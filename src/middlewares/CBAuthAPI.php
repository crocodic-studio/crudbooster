<?php

namespace crocodicstudio\crudbooster\middlewares;

use Closure;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Request;
use Illuminate\Support\Facades\Response;
use Illuminate\Support\Facades\Validator;

class CBAuthAPI
{
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure  $next
     * @return mixed
     */
    public function handle($request, Closure $next)
    {


        $screetkey = Cache::get('screetkey');
        $useragent = $_SERVER['HTTP_USER_AGENT'];                

        if( get_setting('api_debug_mode') == 'false') {                     

            $result    = array();
            $validator = Validator::make(
                [   
                'screetkey'             =>$screetkey,
                'useragent'             =>$useragent,
                'X-Authorization-Token' =>Request::header('X-Authorization-Token'),
                'X-Authorization-Time'  =>Request::header('X-Authorization-Time')   
                ],          
                [
                'screetkey'             =>'required',
                'useragent'             =>'required',
                'X-Authorization-Token' =>'required',
                'X-Authorization-Time'  =>'required',                   
                ]
            );      
            
            if ($validator->fails()) 
            {
                $message               = $validator->errors()->all();           
                $result['api_status']  = 0;
                $result['api_message'] = implode(', ',$message);            
                response()->json($result,400)->send();
                exit;
            }

            //verifikasi trust token
            $token_md5 = md5($screetkey.Request::header('X-Authorization-Time').$useragent);

            $your_data = array('screetkey'=>$screetkey,'token'=>Request::header('X-Authorization-Token'),'time'=>Request::header('X-Authorization-Time'));

            //Check token is expired or not 
            if(Cache::has('api_token_'.$token_md5)) {
                response()->json(['api_status'=>0,'api_message'=>'TOKEN_EXPIRED','YOUR_DATA'=>$your_data],401)->send();
                exit;
            }

            if($token_md5 != Request::header('X-Authorization-Token')) {            
                $result['api_status']   = 0;
                $result['api_message']  = "INVALID_TOKEN";
                $result['YOUR_DATA'] = $your_data;
                $res = response()->json($result,401);
                $res->send();
                exit;
            }else{

                //Save token to cache
                Cache::put('api_token_'.$token_md5,$token_md5,3600);                
            }

        }   //end debug 

        return $next($request);
    }
}
