<?php

namespace App\Http\Controllers\Helpers;

use App\Http\Controllers\Controller;
use App\Jobs\DeleteTemporaryRenderedBannerTemplates;
use App\Models\BannerTemplate;
use Carbon\Carbon;
use Exception;
use GdImage;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Redis;
use Predis\Connection\ConnectionException;
use Symfony\Component\HttpFoundation\BinaryFileResponse;

class DrawTextOnTemplateController extends Controller
{
    /**
     * Class properties
     */
    protected string $upload_directory = 'uploads/fonts';

    protected ?GdImage $gd_image = null;

    /**
     * The class destructor
     */
    public function __destruct()
    {
        // Destroy generated image to free up the memory
        if (($this->gd_image !== null) and ($this->gd_image !== false)) {
            imagedestroy($this->gd_image);
        }
    }

    /**
     * Draws the configured text on the template(s) (image(s)) and either writes it to the storage or returns it from the memory without saving it.
     */
    public function draw_text_to_image(BannerTemplate $banner_template, bool $write_to_filesystem, bool $only_original, string $ip_address): BinaryFileResponse
    {
        if ($only_original) {
            $files_to_update = [
                $banner_template->template->file_path_original => $banner_template->file_path_drawed_text,
            ];
        } else {
            $files_to_update = [
                $banner_template->template->file_path_original => $banner_template->file_path_drawed_text,
                $banner_template->template->file_path_drawed_grid => $banner_template->file_path_drawed_grid_text,
            ];
        }

        // Get current variables
        $variables_and_values = [];
        try {
            $variables_and_values = array_merge($variables_and_values, Redis::hgetall('instance_'.$banner_template->banner->instance->id.'_datetime'));
            $variables_and_values = array_merge($variables_and_values, Redis::hgetall('instance_'.$banner_template->banner->instance->id.'_servergrouplist'));
            $variables_and_values = array_merge($variables_and_values, Redis::hgetall('instance_'.$banner_template->banner->instance->id.'_virtualserver_info'));
        } catch (ConnectionException $connection_exception) {
            Log::error('Redis connection error: '.$connection_exception->getMessage());
        }

        $banner_variable_helper = new BannerVariableController(null);
        $variables_and_values = array_merge($variables_and_values, $banner_variable_helper->get_client_specific_info_from_cache($banner_template->banner->instance, $ip_address));

        foreach ($files_to_update as $source_path => $target_path) {
            // Load template as GDImage to be able to modify it
            $source_image_filepath = $source_path.'/'.$banner_template->template->filename;
            $source_image_file_extension = strtolower(pathinfo($source_image_filepath, PATHINFO_EXTENSION));
            $this->gd_image = ($source_image_file_extension == 'png') ? imagecreatefrompng(public_path($source_image_filepath)) : imagecreatefromjpeg(public_path($source_image_filepath));

            if ($this->gd_image === false) {
                throw new Exception('Failed to load the source template as image object.');
            }

            // Write all configured texts to the image
            foreach ($banner_template->configurations as $configuration) {
                // Convert HEX to RGB color code(s) and set it as text font color
                list($red, $green, $blue) = sscanf($configuration->font_color_in_hexadecimal, '#%02x%02x%02x');
                $font_color = imagecolorallocate($this->gd_image, $red, $green, $blue);

                if ($font_color === false) {
                    throw new Exception('Failed to allocate the color for the text.');
                }

                // Search for variables (`%SAMPLE_VARIABLE%`) and replace it with their current value, if possible
                $text = $configuration->text;

                if (preg_match_all("/%[A-Z0-9\_?]+%/", $text, $text_variables)) {
                    foreach ($text_variables[0] as $variable) {
                        $variable_name = strtoupper(str_replace('%', '', $variable));

                        if (array_key_exists($variable_name, $variables_and_values)) {
                            $text = str_replace("$variable", $variables_and_values[$variable_name], $text);
                        } else {
                            $text = str_replace("$variable", 'Unknown', $text);
                        }
                    }
                }

                // Calculate required text width in pixel
                $text_bounding_box = imagettfbbox($configuration->font_size, $configuration->font_angle, public_path($this->upload_directory.DIRECTORY_SEPARATOR.$configuration->font->filename), $text);
                $total_text_width_in_pixel = $text_bounding_box[2];

                // Avoid writing text outside of the template. Instead, automatically wrap the text.
                if ($configuration->x_coordinate + $total_text_width_in_pixel >= $banner_template->template->width) {
                    $word_wrap_width = intval(($banner_template->template->width - $configuration->x_coordinate) / $total_text_width_in_pixel * 100);
                    $text = wordwrap($text, $word_wrap_width, "\n", false);
                }

                if (! imagefttext($this->gd_image,
                    $configuration->font_size,
                    $configuration->font_angle,
                    $configuration->x_coordinate,
                    $configuration->y_coordinate,
                    $font_color,
                    public_path($this->upload_directory.DIRECTORY_SEPARATOR.$configuration->font->filename),
                    $text
                )) {
                    throw new Exception("Failed to write the text `$text` to the image.");
                }
            }

            // Return the generated image or write it to the storage
            if ($write_to_filesystem) {
                $image_file_path = public_path($target_path.'/'.$banner_template->template->filename);
            } else {
                $image_file_path = tempnam(sys_get_temp_dir(), 'DynamicBanner_');
                DeleteTemporaryRenderedBannerTemplates::dispatch($image_file_path)->delay(now()->addMinutes(1));
            }

            if ($source_image_file_extension == 'png') {
                imagepng($this->gd_image, $image_file_path, 0);
            } else {
                imagejpeg($this->gd_image, $image_file_path, 100);
            }
        }

        // To avoid caching of the image(s), we explicitly set some headers with an expiry in the past.
        $current_rfc7231_datetime = Carbon::now()->subSeconds(5)->toRfc7231String();

        return response()->file($image_file_path, [
            'Cache-Control' => 'no-cache, private',
            'Expires' => '-1',
            'ETag' => md5($current_rfc7231_datetime),
            'Last-Modified' => $current_rfc7231_datetime,
            'Content-Type' => 'image/'.$source_image_file_extension,
        ]);
    }
}
