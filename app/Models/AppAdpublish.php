<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;

class AppAdpublish extends Model
{
    //指定表名
    protected $table = 'app_adpublish';

    const CREATED_AT = null;
    const UPDATED_AT = null;

    public static function approved($id){
        DB::beginTransaction();
        try{
            $query = DB::table('app_adpublish')
                ->where('id',$id);
            $first = (array)$query->get()->first();
            unset($first['id']);
            if(DB::table('app_ad')->insert($first)
                and $query->delete()) {
                DB::commit();
                return true;
            }
            throw new \Exception('error');
        }catch (\Exception $e){
            DB::rollBack();
            return false;
        }
    }
}
