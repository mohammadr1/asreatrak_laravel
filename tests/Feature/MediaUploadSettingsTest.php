<?php

namespace Tests\Feature;

use App\Livewire\Media\FeaturedPicker;
use App\Services\Media\Validation\ImageValidationRule;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Validator;
use Tests\TestCase;

class MediaUploadSettingsTest extends TestCase
{
    public function test_uploaded_images_and_crop_sources_are_real_webp_files(): void
    {
        config(['database.default' => 'media_testing', 'database.connections.media_testing' => [
            'driver' => 'sqlite', 'database' => ':memory:', 'prefix' => '', 'foreign_key_constraints' => true,
        ]]);
        \Illuminate\Support\Facades\DB::purge('media_testing');
        $this->artisan('migrate', ['--database' => 'media_testing', '--force' => true])->assertSuccessful();
        \Illuminate\Support\Facades\Storage::fake('public');
        $disk = \Illuminate\Support\Facades\Storage::disk('public');
        $processor = app(\App\Services\Media\ImageProcessor::class);
        foreach (['jpg', 'png', 'webp'] as $extension) {
            $file = UploadedFile::fake()->image('sample.'.$extension, 1280, 960);
            $media = app(\App\Services\Media\MediaService::class)->upload($file);
            $this->assertSame('webp', $media->extension);
            $this->assertSame('image/webp', $media->mime_type);
            $this->assertSame('image/webp', getimagesize($disk->path($media->original_path))['mime']);
            $this->assertSame($disk->size($media->original_path), $media->size);
            $this->assertSame(md5($disk->get($media->original_path)), $media->hash);
            $this->assertSame(1280, $media->width);
            $destination = $disk->path('crop-'.$extension.'.webp');
            $processor->prepareForCrop($disk->path($media->original_path), $destination);
            $this->assertSame('image/webp', getimagesize($destination)['mime']);
        }
    }

    public function test_picker_and_service_share_configurable_per_image_limit(): void
    {
        foreach ([2, 5] as $megabytes) {
            config(['media.image.max_size' => $megabytes * 1024 * 1024]);
            $picker = new FeaturedPicker();
            $rules = $picker->getRules();
            $this->assertSame($megabytes * 1024 * 1024, (new ImageValidationRule())->maxSize);
            $atLimit = UploadedFile::fake()->image('boundary.jpg')->size($megabytes * 1024);
            $overLimit = UploadedFile::fake()->image('too-large.jpg')->size($megabytes * 1024 + 1);
            $this->assertTrue(Validator::make(['uploads' => [$atLimit, $atLimit]], $rules)->passes());
            $messages = (new \ReflectionMethod($picker, 'messages'))->invoke($picker);
            $validator = Validator::make(['uploads' => [$overLimit]], $rules, $messages);
            $this->assertTrue($validator->fails());
            $this->assertStringContainsString('حجم هر تصویر', $validator->errors()->first('uploads.0'));
        }
    }
}
