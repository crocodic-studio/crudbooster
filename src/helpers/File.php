<?php
/**
 * Created by PhpStorm.
 * User: User
 * Date: 4/3/2020
 * Time: 6:05 PM
 */

namespace crocodicstudio\crudbooster\helpers;

use Illuminate\Support\Facades\Storage;

trait File
{
    /**
     * To upload a base64
     * @param $input_name
     * @return string
     */
    public function uploadBase64($input_name, $filename = null)
    {
        $filedata = base64_decode(request($input_name));
        $f = finfo_open();
        $mime_type = finfo_buffer($f, $filedata, FILEINFO_MIME_TYPE);
        @$mime_type = explode('/', $mime_type);
        @$mime_type = $mime_type[1];
        if ($mime_type) {
            $filePath = 'uploads/'.date('Y-m');
            Storage::makeDirectory($filePath);
            $filename = ($filename)?$filename.'.'.$mime_type:md5(str_random(5)).'.'.$mime_type;
            if (Storage::put($filePath.'/'.$filename, $filedata)) {
                self::resizeImage($file_path.'/'.$filename, $resize_width, $resize_height);
                return $filePath.'/'.$filename;
            }
        }
    }

    public function uploadFile($name, $encrypt = false, $resize_width = null, $resize_height = null, $id = null)
    {
        if (request()->hasFile($name)) {

            $file = request()->file($name);
            $ext = $file->getClientOriginalExtension();
            $filename = str_slug(pathinfo($file->getClientOriginalName(), PATHINFO_FILENAME));
            $filesize = $file->getClientSize() / 1024;
            $file_path = 'uploads/'.date('Y-m');

            //Create Directory Monthly
            Storage::makeDirectory($file_path);

            if ($encrypt == true) {
                $filename = md5(str_random(5)).'.'.$ext;
            } else {
                $filename = str_slug($filename, '_').'.'.$ext;
            }

            if (Storage::putFileAs($file_path, $file, $filename)) {
                self::resizeImage($file_path.'/'.$filename, $resize_width, $resize_height);

                return $file_path.'/'.$filename;
            } else {
                return null;
            }
        } else {
            return null;
        }
    }

    private function resizeImage($fullFilePath, $resize_width = null, $resize_height = null, $qty = 100, $thumbQty = 75)
    {
        $images_ext = config('crudbooster.IMAGE_EXTENSIONS', 'jpg,png,gif,bmp');
        $images_ext = explode(',', $images_ext);

        $filename = basename($fullFilePath);
        $file_path = trim(str_replace($filename, '', $fullFilePath), '/');

        $file_path_thumbnail = 'uploads_thumbnail/'.date('Y-m');
        Storage::makeDirectory($file_path_thumbnail);

        if (in_array(strtolower($ext), $images_ext)) {

            if ($resize_width && $resize_height) {
                $img = Image::make(storage_path('app/'.$file_path.'/'.$filename));
                $img->fit($resize_width, $resize_height);
                $img->save(storage_path('app/'.$file_path.'/'.$filename), $qty);
            } elseif ($resize_width && ! $resize_height) {
                $img = Image::make(storage_path('app/'.$file_path.'/'.$filename));
                $img->resize($resize_width, null, function ($constraint) {
                    $constraint->aspectRatio();
                });
                $img->save(storage_path('app/'.$file_path.'/'.$filename), $qty);
            } elseif (! $resize_width && $resize_height) {
                $img = Image::make(storage_path('app/'.$file_path.'/'.$filename));
                $img->resize(null, $resize_height, function ($constraint) {
                    $constraint->aspectRatio();
                });
                $img->save(storage_path('app/'.$file_path.'/'.$filename), $qty);
            } else {
                $img = Image::make(storage_path('app/'.$file_path.'/'.$filename));
                if ($img->width() > 1300) {
                    $img->resize(1300, null, function ($constraint) {
                        $constraint->aspectRatio();
                    });
                }
                $img->save(storage_path('app/'.$file_path.'/'.$filename), $qty);
            }

            $img = Image::make(storage_path('app/'.$file_path.'/'.$filename));
            $img->fit(350, 350);
            $img->save(storage_path('app/'.$file_path_thumbnail.'/'.$filename), $thumbQty);
        }
    }
}