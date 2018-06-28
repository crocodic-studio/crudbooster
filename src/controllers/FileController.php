<?php

namespace Crocodicstudio\Crudbooster\Controllers;

use Crocodicstudio\Crudbooster\CBCoreModule\FileUploader;
use Crocodicstudio\Crudbooster\Controllers\Helpers\IndexImport;
use Crocodicstudio\Crudbooster\Modules\ModuleGenerator\ControllerGenerator\FieldDetector;
use Storage;
use Response;
use Image;
use File;
use Illuminate\Support\Facades\Request;

class FileController extends Controller
{
    public function uploadSummernote()
    {
        echo asset(app( FileUploader::class)->uploadFile('userfile'));
    }

    public function uploadFile()
    {
        echo app(FileUploader::class)->uploadFile('userfile');
    }

    public function doUploadImportData()
    {
        $import = app(IndexImport::class);
        //$this->cbLoader();
        if (! Request::hasFile('userfile')) {
            return redirect()->back();
        }
        $file = Request::file('userfile');
        $validator = $import->validateForImport($file);
        if ($validator->fails()) {
            backWithMsg(implode('<br/>', $validator->errors()->all()), 'warning');
        }
        $url = $import->uploadImportData($file);

        return redirect($url);
    }

    public function getPreview($one, $two = null, $three = null, $four = null, $five = null)
    {
        // array_filter() filters out the falsy values from array.
        $params = array_filter([$one, $two, $three, $four, $five]);        
        $filename = array_pop($params);
        $fullFilePath = implode(DIRECTORY_SEPARATOR, array_filter(['uploads', $one, $two, $three, $four, $five]));

        $fullStoragePath = storage_path('app/'.$fullFilePath);


        if (! Storage::exists($fullFilePath)) {
            abort(404);
        }
        $hasImageExtension = $this->isImage($fullStoragePath);
        $imageFileSize = 0;
        $imgRaw = '';
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

        if (request('download')) {
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
        $w = request('w', cbConfig('DEFAULT_THUMBNAIL_WIDTH', 300));
        $h = request('h', $w);
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

        return FieldDetector::isWithin($extension, 'IMAGE_EXTENSIONS');;
    }

    /**
     * @param $imageFileSize
     * @param $fullFilePath
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

    /**
     * @return mixed
     */
    public function doneImport()
    {
        return app(IndexImport::class)->doneImport();
    }
}
