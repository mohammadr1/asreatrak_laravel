<?php

namespace App\Support;


class ViteAsset
{
    public static function get(string $entry)
    {
        $manifest = json_decode(
            file_get_contents(
                public_path('build/manifest.json')
            ),
            true
        );


        return asset(
            'build/' . $manifest[$entry]['file']
        );
    }
}