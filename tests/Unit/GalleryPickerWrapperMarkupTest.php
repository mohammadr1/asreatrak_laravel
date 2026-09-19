<?php

namespace Tests\Unit;

use DOMDocument;
use DOMXPath;
use PHPUnit\Framework\TestCase;

class GalleryPickerWrapperMarkupTest extends TestCase
{
    public function test_alpine_controller_stays_inside_the_x_data_attribute(): void
    {
        $markup = file_get_contents(
            dirname(__DIR__, 2) . '/resources/views/filament/media/gallery-picker-wrapper.blade.php'
        );

        $document = new DOMDocument();

        libxml_use_internal_errors(true);
        $document->loadHTML($markup);
        libxml_clear_errors();

        $xpath = new DOMXPath($document);
        $wrapper = $xpath->query('//*[@x-data]')->item(0);

        $this->assertNotNull($wrapper);
        $this->assertStringContainsString(
            'async syncGallery',
            $wrapper->getAttribute('x-data'),
        );
        $this->assertStringContainsString(
            'wire.set("data.gallery_media", items)',
            $wrapper->getAttribute('x-data'),
        );
        $this->assertSame(
            'syncGallery($event, $wire, $el)',
            $wrapper->getAttribute('x-on:media-multiple-selected.window'),
        );
        $this->assertSame(
            0,
            $xpath->query('./text()[normalize-space()]', $wrapper)->length,
            'JavaScript leaked out of the Alpine attribute as visible modal text.',
        );
    }
}
