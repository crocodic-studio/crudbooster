<?php

namespace crocodicstudio\crudbooster\controllers;

use crocodicstudio\crudbooster\controllers\Controller;

use Storage;
use Response;
use Image;
use File;
use Illuminate\Support\Facades\Request;

class FileController extends Controller
{
    public function getPreview($one, $two = null, $three = null, $four = null, $five = null)
    {
        // array_filter() filters out the falsy values from array.
        $filename = array_pop(array_filter([$one, $two, $three, $four, $five]));
        $fullFilePath = implode(DIRECTORY_SEPARATOR, array_filter(['uploads', $one, $two, $three, $four, $five]));

        $fullStoragePath = storage_path('app/'.$fullFilePath);


        if (! Storage::exists($fullFilePath)) {
            abort(404);
        }
        $hasImageExtension = $this->isImage($fullStoragePath);
        $imageFileSize = 0;

        if ($hasImageExtension) {
            list($imgRaw, $imageFileSize) = $this->resizeImage($fullStoragePath);
        }

        list($headers, $isCachedByBrowser) = $this->prepareHeaders($imageFileSize, $fullFilePath, $filename);

        if ($hasImageExtension) {
            if ($isCachedByBrowser) {
                return Response::make('', 304, $headers); // File (image) is cached by the browser, so we don't have to send it again
            }

            return Response::make($imgRaw, 200, $headers);
        }

        if (Request::get('download')) {
            return Response::download($fullStoragePath, $filename, $headers);
        }

        return Response::file($fullStoragePath, $headers);
    }

    /**
     * @param $fullStoragePath
     * @return array
     */
    private function resizeImage($fullStoragePath)
    {
        $w = Request::get('w', cbConfig('DEFAULT_THUMBNAIL_WIDTH', 300));
        $h = Request::get('h', $w);
        $imgRaw = Image::cache(function ($image) use ($fullStoragePath, $w, $h) {
            $im = $image->make($fullStoragePath);
            if (! $w) {
                return $im;
            }
            if (! $h) {
                $im->fit($w);
            } else {
                $im->fit($w, $h);
            }
        });

        $imageFileSize = mb_strlen($imgRaw, '8bit') ?: 0;

        return [$imgRaw, $imageFileSize];
    }

    /**
     * @param $fullStoragePath
     * @return bool
     */
    private function isImage($fullStoragePath)
    {
        $extension = strtolower(File::extension($fullStoragePath));
        $images_ext = cbConfig('IMAGE_EXTENSIONS', 'jpg,png,gif,bmp');
        $images_ext = explode(',', $images_ext);
        $hasImageExtension = in_array($extension, $images_ext);

        return $hasImageExtension;
    }

    /**
     * @param $fullStoragePath
     * @param $imageFileSize
     * @param $fullFilePath
     * @param $lifetime
     * @param $filename
     * @return array
     */
    private function prepareHeaders($imageFileSize, $fullFilePath, $filename)
    {
        $lifetime = 31556926; // One year in seconds
        $fullStoragePath = storage_path('app/'.$fullFilePath);
        /**
         * Prepare some header variables
         */
        $handler = new \Symfony\Component\HttpFoundation\File\File($fullStoragePath);
        $file_time = $handler->getMTime(); // Get the last modified time for the file (Unix timestamp)

        $header_content_type = $handler->getMimeType();
        $header_content_length = ($imageFileSize) ?: $handler->getSize();
        $header_etag = md5($file_time.$fullFilePath);
        $header_last_modified = gmdate('r', $file_time);
        $header_expires = gmdate('r', $file_time + $lifetime);

        $headers = [
            'Content-Disposition' => 'inline; filename="'.$filename.'"',
            'Last-Modified' => $header_last_modified,
            'Cache-Control' => 'must-revalidate',
            'Expires' => $header_expires,
            'Pragma' => 'public',
            'Etag' => $header_etag,
        ];

        // return Response::download($fullStoragePath, $filename, $headers);

        $headers = array_merge($headers, [
            'Content-Type' => $header_content_type,
            'Content-Length' => $header_content_length,
        ]);

        /**
         * Is the resource cached?
         */
        $h1 = Request::server('HTTP_IF_MODIFIED_SINCE') && Request::server('HTTP_IF_MODIFIED_SINCE') == $header_last_modified;
        $h2 = Request::server('HTTP_IF_NONE_MATCH') && str_replace('"', '', stripslashes(Request::server('HTTP_IF_NONE_MATCH'))) == $header_etag;
        $isCachedByBrowser = ($h1 || $h2);

        return [$headers, $isCachedByBrowser];
    }
}
