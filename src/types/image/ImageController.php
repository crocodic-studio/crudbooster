<?php
/**
 * Created by PhpStorm.
 * User: User
 * Date: 5/10/2019
 * Time: 10:38 PM
 */

namespace crocodicstudio\crudbooster\types\image;

use Illuminate\Routing\Controller as BaseController;

class ImageController extends BaseController
{
    public function postUploadImage()
    {
        $file = null;
        try {

            cb()->validation([
                'userfile' => 'required|mimes:' . implode(",",config('crudbooster.UPLOAD_IMAGE_EXTENSION_ALLOWED'))
            ]);

            $file = cb()->uploadFile('userfile', request("encrypt")?true:false, request("resize_width"), request("resize_height"));

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