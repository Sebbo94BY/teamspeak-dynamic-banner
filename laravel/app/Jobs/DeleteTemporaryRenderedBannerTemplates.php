<?php

namespace App\Jobs;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldBeUnique;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Symfony\Component\HttpFoundation\File\Exception\FileException;

class DeleteTemporaryRenderedBannerTemplates implements ShouldQueue, ShouldBeUnique
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    /**
     * The absolute file path, which should get deleted.
     *
     * @var string
     */
    protected string $absolute_file_path_to_delete;

    /**
     * The number of seconds after which the job's unique lock will be released.
     *
     * @var int
     */
    public $uniqueFor = 60;

    /**
     * The unique ID of the job.
     */
    public function uniqueId(): string
    {
        return $this->absolute_file_path_to_delete;
    }

    /**
     * Create a new job instance.
     */
    public function __construct(string $absolute_file_path_to_delete)
    {
        $this->absolute_file_path_to_delete = $absolute_file_path_to_delete;
    }

    /**
     * Execute the job.
     */
    public function handle(): void
    {
        if (file_exists($this->absolute_file_path_to_delete)) {
            if (! unlink($this->absolute_file_path_to_delete)) {
                throw new FileException('Failed to delete the following file: '.$this->absolute_file_path_to_delete);
            }
        }
    }
}
