<?php

namespace Tests\Feature;

use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Config;
use Illuminate\Support\Facades\Storage;
use Tests\TestCase;

class CloudflareR2FilesystemConfigTest extends TestCase
{
    public function test_s3_disk_configuration_is_mapped_to_r2_specifications(): void
    {
        $diskConfig = Config::get('filesystems.disks.s3');

        $this->assertIsArray($diskConfig, 'S3 disk configuration must be declared in config/filesystems.php');
        $this->assertSame('s3', $diskConfig['driver'] ?? null);
        $this->assertFalse(
            $diskConfig['use_path_style_endpoint'] ?? true,
            'Cloudflare R2 requires use_path_style_endpoint=false'
        );
        $this->assertArrayHasKey('endpoint', $diskConfig);
        $this->assertArrayHasKey('bucket', $diskConfig);
    }

    public function test_s3_storage_can_store_and_retrieve_student_attachments(): void
    {
        Storage::fake('s3');

        $file = UploadedFile::fake()->image('student_avatar.jpg', 200, 200);
        $path = Storage::disk('s3')->putFile('students/photos', $file);

        $this->assertNotEmpty($path);
        Storage::disk('s3')->assertExists($path);

        $contents = Storage::disk('s3')->get($path);
        $this->assertNotEmpty($contents);
    }

    public function test_s3_storage_can_write_and_stream_generated_reports(): void
    {
        Storage::fake('s3');

        $reportContent = 'PDF-1.7 mock assessment report stream';
        $reportPath = 'reports/2026/kspk_milestone_cohort_biruni.pdf';

        Storage::disk('s3')->put($reportPath, $reportContent);

        Storage::disk('s3')->assertExists($reportPath);
        $this->assertSame($reportContent, Storage::disk('s3')->get($reportPath));
        $this->assertSame(strlen($reportContent), Storage::disk('s3')->size($reportPath));
    }
}
