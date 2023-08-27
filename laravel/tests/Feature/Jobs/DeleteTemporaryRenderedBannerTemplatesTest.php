<?php

namespace Tests\Feature\Jobs;

use App\Jobs\DeleteTemporaryRenderedBannerTemplates;
use Tests\TestCase;

class DeleteTemporaryRenderedBannerTemplatesTest extends TestCase
{
    /**
     * Test that the job can successfully delete an existing file.
     */
    public function test_job_successfully_deletes_an_existing_file(): void
    {
        $image_file_path = tempnam(sys_get_temp_dir(), 'DynamicBanner_');
        $this->assertFileExists($image_file_path);

        DeleteTemporaryRenderedBannerTemplates::dispatchSync($image_file_path);
        $this->assertFileDoesNotExist($image_file_path);
    }
}
