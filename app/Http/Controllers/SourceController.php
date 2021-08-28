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
        try {
          $fullPath = base_path() . '/source_'. $code . '/' . $file . LANG_EXT;
          $plainText = file_get_contents($fullPath);
          return response()->json(json_decode($plainText));
        } catch (\Exception $e) {
          return response()->json(['ok' => false], 404);
        }
    }

    public function saveTranslation($file): JsonResponse
    {
      $fullPath = base_path() . '/source_translated/' . $file . LANG_EXT;
      file_put_contents($fullPath, json_encode(request()->all(), JSON_UNESCAPED_UNICODE | JSON_PRETTY_PRINT));
      return response()->json(['ok' => 'true']);
    }
}
