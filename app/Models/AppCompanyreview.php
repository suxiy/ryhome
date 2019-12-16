<?php

namespace App\Models;

use Illuminate\Support\Facades\DB;
use Illuminate\Database\Eloquent\Model;
use Think\Exception;

class AppCompanyreview extends Model
{
    //指定表名
    protected $table = 'app_companyreview';

    const CREATED_AT = null;
    const UPDATED_AT = null;

    public static function approved($id){
        DB::beginTransaction();
        try{
            $query = DB::table('app_companyreview')
                ->where('id',$id);
            $first = (array)$query->get()->first();
            unset($first['id']);
            if(DB::table('app_company')->insert($first)
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
