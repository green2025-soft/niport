<?php

use Illuminate\Support\Facades\Storage;

use function Illuminate\Log\log;

if (!function_exists('uploadImage')) {
    /**
     * Upload image to given folder, delete old image if exists.
     *
     * @param \Illuminate\Http\UploadedFile $file
     * @param string $folder
     * @param string|null $oldPath
     * @return string URL of uploaded file
     */
    function uploadImage($file, $folder, $oldPath = null): string
    {
        // Delete old file if exists
        if ($oldPath) {
            $oldPathRelative = str_replace('/storage/', '', parse_url($oldPath, PHP_URL_PATH));
            if (Storage::disk('public')->exists($oldPathRelative)) {
                Storage::disk('public')->delete($oldPathRelative);
            }
        }

        // Store new file
        $path = $file->store($folder, 'public');

        // Return public url
        return Storage::url($path);
    }



}


if (!function_exists('dbDateFormat')) {
    function dbDateFormat($date='')
    {
        if (empty($date)) {
            return null;
        }

        $date = str_replace(['/', '.'], ['-'], $date);

        if (validateDate($date)) {
            return date('Y-m-d', strtotime($date));
        }

        return null;
    }
}



if (!function_exists('validateDate')) {
    function validateDate($date)
    {
        $timestamp = strtotime($date);
        return $timestamp ? $date : null;
    }
}


if (!function_exists('api_dd')) {
    function api_dd($data, $stop = true)
    {
        if (request()->header('X-Debug') == 'true') {
            $response = response()->json([
                'debug' => true,
                'data' => $data,
            ]);

            if ($stop) {
                $response->send();
                exit; // dd() এর মতো stop করবে
            }

            return $response;
        }

        return null; // debug header না থাকলে কিছুই করবে না
    }
}
