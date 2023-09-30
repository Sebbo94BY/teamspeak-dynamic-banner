<?php

namespace App\Jobs;

use App\Models\Template;
use Exception;
use GdImage;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldBeUnique;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Storage;

class DrawGridSystemOnTemplate implements ShouldQueue, ShouldBeUnique
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    /**
     * Class properties
     *
     * @var Template
     */
    protected Template $template;

    /**
     * The spacing between each horizontal and vertical line for the grid system.
     */
    protected int $grid_spacing = 25;

    /**
     * The number of seconds after which the job's unique lock will be released.
     *
     * @var int
     */
    public int $uniqueFor = 10;

    /**
     * The unique ID of the job.
     */
    public function uniqueId(): string
    {
        return $this->template->id;
    }

    /**
     * Delete the job if its models no longer exist.
     *
     * @var bool
     */
    public $deleteWhenMissingModels = true;

    /**
     * Create a new job instance.
     */
    public function __construct(Template $template)
    {
        $this->template = $template;
    }

    /**
     * Draw grid system on static image.
     */
    protected function draw_grid_on_static_image(GdImage $gd_image, int $spacing): GdImage
    {
        if ($gd_image === false) {
            throw new Exception('Failed to load the source template as image object.');
        }

        // Define line color and transparency
        $line_color = imagecolorallocatealpha($gd_image, 0, 0, 0, 80);

        // Draw vertical lines
        for ($iw = 0; $iw < $this->template->width / $spacing; $iw++) {
            if (! imageline($gd_image, $iw * $spacing, 0, $iw * $spacing, $this->template->width, $line_color)) {
                throw new Exception("Failed to draw the vertical line at the x-position `$iw*$spacing` for the grid system.");
            }
        }

        // Draw horizontal lines
        for ($ih = 0; $ih < $this->template->height / $spacing; $ih++) {
            if (! imageline($gd_image, 0, $ih * $spacing, $this->template->width, $ih * $spacing, $line_color)) {
                throw new Exception("Failed to draw the horizontal line at the y-position `$ih*$spacing` for the grid system.");
            }
        }

        return $gd_image;
    }

    /**
     * Draw grid system on (animated) GIF image.
     */
    protected function draw_grid_on_animated_image(string $source_image_filepath, string $target_image_filepath, int $spacing): void
    {
        shell_exec("ffmpeg -hide_banner -loglevel error -nostdin -y -i $source_image_filepath -vf 'drawgrid=width=$spacing:height=$spacing:thickness=1:color=black@0.8' $target_image_filepath 2>&1");
    }

    /**
     * Execute the job.
     */
    public function handle(): void
    {
        // Ensure necessary target directory exists
        $target_directory = $this->template->file_path_drawed_grid;
        if (! Storage::disk('public')->exists($target_directory)) {
            if (! Storage::disk('public')->makeDirectory($target_directory, 0755, true, true)) {
                throw new Exception("Failed to create the necessary target directory `$target_directory`.");
            }
        }

        $target_directory = public_path($this->template->file_path_drawed_grid);

        // Identify file type and handle respectively the drawing of the grid system
        $source_image_filepath = public_path($this->template->file_path_original).'/'.$this->template->filename;

        switch (strtolower(pathinfo($source_image_filepath, PATHINFO_EXTENSION))) {
            case 'png':
                // Save the generated image as file
                if (! imagepng($this->draw_grid_on_static_image(imagecreatefrompng($source_image_filepath), $this->grid_spacing), $target_directory.'/'.$this->template->filename, 0)) {
                    throw new Exception("Could not save the template with the drawed grid system to `$target_directory`.");
                }

                break;

            case 'jpg':
            case 'jpeg':
                // Save the generated image as file
                if (! imagejpeg($this->draw_grid_on_static_image(imagecreatefromjpeg($source_image_filepath), $this->grid_spacing), $target_directory.'/'.$this->template->filename, 100)) {
                    throw new Exception("Could not save the template with the drawed grid system to `$target_directory`.");
                }

                break;

            case 'gif':
                $this->draw_grid_on_animated_image($source_image_filepath, $target_directory.'/'.$this->template->filename, $this->grid_spacing);

                break;

            default:
                throw new Exception("The uploaded file `$source_image_filepath` is not supported by this application and can not be used.");
        }
    }
}
