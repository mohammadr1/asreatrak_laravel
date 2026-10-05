<?php

namespace App\Support;

final class AparatVideo
{
    public static function id(?string $input): ?string
    {
        $input = trim($input ?? '');
        if (preg_match('/\A[a-zA-Z0-9]{1,100}\z/', $input)) {
            return $input;
        }

        $url = parse_url($input);
        if (! is_array($url)
            || ! in_array(strtolower($url['scheme'] ?? ''), ['http', 'https'], true)
            || ! in_array(strtolower($url['host'] ?? ''), ['aparat.com', 'www.aparat.com'], true)
            || isset($url['user']) || isset($url['pass']) || isset($url['port'])
            || ! preg_match('~\A/v/([a-zA-Z0-9]{1,100})/?\z~', $url['path'] ?? '', $matches)) {
            return null;
        }

        return $matches[1];
    }

    public static function embedUrl(?string $input): ?string
    {
        $id = self::id($input);

        return $id ? "https://www.aparat.com/video/video/embed/videohash/{$id}/vt/frame" : null;
    }
}
