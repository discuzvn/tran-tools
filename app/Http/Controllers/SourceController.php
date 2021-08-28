<?php

namespace App\Http\Controllers;

use Illuminate\Http\JsonResponse;
use Illuminate\Support\Str;

const LANG_EXT = '.php.json';

class SourceController extends Controller
{
    public function getTranslations($code): JsonResponse
    {
        $dir = base_path() . '/source_' . $code;
        $translationFiles = scandir($dir);
        return response()->json(collect($translationFiles)
            ->filter(function ($value) {
                return Str::endsWith($value, '.json');
            })
            ->map(function ($value) {
                return Str::substr($value, 0, strlen($value) - strlen(LANG_EXT));
            })
            ->flatten());
    }

    public function getTranslation($code, $file): JsonResponse
    {
        $fullPath = base_path() . '/source_'. $code . '/' . $file . LANG_EXT;
        $plainText = file_get_contents($fullPath);
        return response()->json(json_decode($plainText));
    }
}
