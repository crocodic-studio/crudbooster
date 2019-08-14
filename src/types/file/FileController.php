<?php
/**
 * Created by PhpStorm.
 * User: User
 * Date: 5/10/2019
 * Time: 10:38 PM
 */

namespace crocodicstudio\crudbooster\types\file;

use Illuminate\Routing\Controller as BaseController;

class FileController extends BaseController
{
    public function postUploadFile()
    {

        $file = null;
        try {

            cb()->validation([
                'userfile' => 'required|mimes:' . implode(",",config('crudbooster.UPLOAD_FILE_EXTENSION_ALLOWED'))
            ]);

            $file = cb()->uploadFile('userfile', request("encrypt"));

        } catch (CBValidationException $e) {
            return response()->json(['status'=>false,'message'=>$e->getMessage()]);
        } catch (\Exception $e) {
            return response()->json(['status'=>false,'message'=>$e->getMessage()]);
        }

        return response()->json([
            'status'=>true,
            'filename'=>basename($file),
            'full_url'=>asset($file),
            'url'=>$file
        ]);
    }
}