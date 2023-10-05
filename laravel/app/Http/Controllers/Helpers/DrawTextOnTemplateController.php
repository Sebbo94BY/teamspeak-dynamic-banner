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

    /**
     * Replace variables with actual values.
     */
    protected function replace_variables_with_actual_values(string $text, array $variables_and_values): string
    {
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

        return $text;
    }

    /**
     * Draws text on a static image.
     */
    protected function draw_text_on_static_image(GdImage $gd_image, BannerTemplate $banner_template, array $variables_and_values): GdImage
    {
        if ($gd_image === false) {
            throw new Exception('Failed to load the source template as image object.');
        }

        // Write all configured texts to the image
        foreach ($banner_template->configurations as $configuration) {
            // Convert HEX to RGB color code(s) and set it as text font color
            list($red, $green, $blue) = sscanf($configuration->font_color_in_hexadecimal, '#%02x%02x%02x');
            $font_color = imagecolorallocate($gd_image, $red, $green, $blue);

            if ($font_color === false) {
                throw new Exception('Failed to allocate the color for the text.');
            }

            // Search for variables (`%SAMPLE_VARIABLE%`) and replace it with their current value, if possible
            $text = $this->replace_variables_with_actual_values($configuration->text, $variables_and_values);

            // Calculate required text width in pixel
            $text_bounding_box = imagettfbbox($configuration->font_size, $configuration->font_angle, public_path($this->upload_directory.DIRECTORY_SEPARATOR.$configuration->font->filename), $text);
            $total_text_width_in_pixel = $text_bounding_box[2];

            // Avoid writing text outside of the template. Instead, automatically wrap the text.
            if ($configuration->x_coordinate + $total_text_width_in_pixel >= $banner_template->template->width) {
                $word_wrap_width = intval(($banner_template->template->width - $configuration->x_coordinate) / $total_text_width_in_pixel * 100);
                $text = wordwrap($text, $word_wrap_width, "\n", false);
            }

            if (! imagefttext($gd_image,
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

        return $gd_image;
    }

    /**
     * Draw text on a(n) (animated) GIF image.
     */
    protected function draw_text_on_animated_image(string $source_image_filepath, string $target_image_filepath, BannerTemplate $banner_template, array $variables_and_values): void
    {
        $text_configs = [];
        foreach ($banner_template->configurations as $configuration) {
            // Search for variables (`%SAMPLE_VARIABLE%`) and replace it with their current value, if possible
            $text = $this->replace_variables_with_actual_values($configuration->text, $variables_and_values);

            $text_configs[] = "drawtext=text='$text':fontsize=$configuration->font_size:x=$configuration->x_coordinate:y=$configuration->y_coordinate:fontcolor=$configuration->font_color_in_hexadecimal:fontfile=".public_path($this->upload_directory.DIRECTORY_SEPARATOR.$configuration->font->filename);
        }

        shell_exec("ffmpeg -hide_banner -loglevel error -nostdin -y -i $source_image_filepath -vf \"".implode(',', $text_configs)."\" $target_image_filepath 2>&1");
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
            // Permanently or temporary write it to the storage
            if ($write_to_filesystem) {
                $image_file_path = public_path($target_path).'/'.$banner_template->template->filename;
            } else {
                $image_file_path = tempnam(sys_get_temp_dir(), 'DynamicBanner_');
                DeleteTemporaryRenderedBannerTemplates::dispatch($image_file_path)->delay(now()->addMinutes(1));
            }

            // Identify file type and handle respectively the drawing of the grid system
            $source_image_filepath = public_path($source_path).'/'.$banner_template->template->filename;
            $source_image_file_extension = strtolower(pathinfo($source_image_filepath, PATHINFO_EXTENSION));

            switch ($source_image_file_extension) {
                case 'png':
                    // Save the generated image as file
                    $gd_image = imagecreatefrompng($source_image_filepath);
                    if (! imagepng($this->draw_text_on_static_image($gd_image, $banner_template, $variables_and_values), public_path($target_path).'/'.$banner_template->template->filename, 0)) {
                        throw new Exception("Could not save the template with the drawed text to `$target_path`.");
                    }

                    imagepng($gd_image, $image_file_path, 0);

                    // Destroy the generated image to free up the memory again
                    imagedestroy($gd_image);

                    break;

                case 'jpg':
                case 'jpeg':
                    // Save the generated image as file
                    $gd_image = imagecreatefromjpeg($source_image_filepath);
                    if (! imagejpeg($this->draw_text_on_static_image($gd_image, $banner_template, $variables_and_values), public_path($target_path).'/'.$banner_template->template->filename, 100)) {
                        throw new Exception("Could not save the template with the drawed text to `$target_path`.");
                    }

                    imagejpeg($gd_image, $image_file_path, 100);

                    // Destroy the generated image to free up the memory again
                    imagedestroy($gd_image);

                    break;

                case 'gif':
                    // For the API the temporary file must be suffixed with `.gif` as the browser will otherwise not show any GIF.
                    // Instead you see a white page and HTTP 200 response with Content-Type `image/gif`.
                    $image_file_path = (str_ends_with($image_file_path, '.gif')) ? $image_file_path : $image_file_path.'.gif';
                    $this->draw_text_on_animated_image($source_image_filepath, $image_file_path, $banner_template, $variables_and_values);

                    break;

                default:
                    throw new Exception("The uploaded file `$source_image_filepath` is not supported by this application and can not be used.");
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
