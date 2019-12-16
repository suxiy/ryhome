<?php

namespace App\Http\Controllers\API;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;

class UploadController extends Controller
{
    public function uploadbussinessimage(Request $request){
        try{
            $filename = $this->uploadImage(__FUNCTION__);
            if($filename){
                return $filename;
            }
            throw new \Exception('error');
        }catch (\Exception $e){
            return response()->json();
        }
    }

    public function uploadadimage(Request $request){
        try{
            $filename = $this->uploadImage(__FUNCTION__);
            if($filename){
                return $filename;
            }
            throw new \Exception('error');
        }catch (\Exception $e){
            return response()->json();
        }
    }

    protected function uploadImage($type){
        try{
            $file = $_FILES['file']??null;
            if($file){
                $filename = $file['name'];
                $tmp = $file['tmp_name'];
                $extension = pathinfo($filename)['extension'];
                $allow_extension = ['jpg', 'jpeg', 'gif', 'bmp', 'png'];
                if(!in_array($extension,$allow_extension)){
                    throw new \Exception('图片类型错误');
                }
                $uri = '/upload/'.$type;
                $path = base_path().$uri;
                if(!is_dir($path)){
                    mkdir($path,0777,true);
                }
                $path .= ('/'.$filename);
                $http = $this->is_https()?'https://':'http://';
                $uri = $http.$_SERVER['HTTP_HOST'].'/'.$uri.'/'.$filename;
                if(move_uploaded_file($tmp,$path)){
                    return $uri;
                }
            }
        }catch (\Exception $e){
            return false;
        }
        return false;
    }

    protected function is_https() {
        if ( !empty($_SERVER['HTTPS']) && strtolower($_SERVER['HTTPS']) !== 'off') {
            return true;
        } elseif ( isset($_SERVER['HTTP_X_FORWARDED_PROTO']) && $_SERVER['HTTP_X_FORWARDED_PROTO'] === 'https' ) {
            return true;
        } elseif ( !empty($_SERVER['HTTP_FRONT_END_HTTPS']) && strtolower($_SERVER['HTTP_FRONT_END_HTTPS']) !== 'off') {
            return true;
        }
        return false;
    }
}
