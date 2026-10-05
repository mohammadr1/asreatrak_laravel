<?php

namespace App\Support;

use DOMDocument;
use Illuminate\Support\Str;

final class NewsContent
{
    public static function render(?string $content): string
    {
        $html = Str::sanitizeHtml($content ?? '');
        if ($html === '') return '';

        $document = new DOMDocument('1.0', 'UTF-8');
        $previous = libxml_use_internal_errors(true);
        try {
            $document->loadHTML('<?xml encoding="UTF-8"><html><body>'.$html.'</body></html>', LIBXML_NONET);
        } finally {
            libxml_clear_errors();
            libxml_use_internal_errors($previous);
        }
        foreach (iterator_to_array($document->getElementsByTagName('a')) as $link) {
            $id = AparatVideo::id($link->getAttribute('href'));
            if (! $id || trim($link->textContent) !== 'ویدیوی آپارات: '.$id) continue;

            $wrapper = $document->createElement('span');
            $wrapper->setAttribute('class', 'inline-aparat-player');
            $frame = $document->createElement('iframe');
            foreach ([
                'src' => AparatVideo::embedUrl($id), 'title' => 'ویدیوی آپارات: '.$id,
                'loading' => 'lazy', 'referrerpolicy' => 'strict-origin-when-cross-origin',
                'sandbox' => 'allow-scripts allow-same-origin allow-presentation',
                'allow' => 'fullscreen; picture-in-picture', 'allowfullscreen' => '',
            ] as $name => $value) $frame->setAttribute($name, $value);
            $wrapper->appendChild($frame);
            $link->parentNode->insertBefore($wrapper, $link);
            $link->textContent = 'تماشا در آپارات';
            $link->setAttribute('target', '_blank');
            $link->setAttribute('rel', 'noopener noreferrer');
        }
        $body = $document->getElementsByTagName('body')->item(0);
        $output = '';
        foreach ($body->childNodes as $child) $output .= $document->saveHTML($child);
        return $output;
    }
}
