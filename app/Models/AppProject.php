<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class AppProject extends Model
{
    //指定表名
    protected $table = 'app_project';
    protected $primaryKey = 'project_id';

    const CREATED_AT = null;
    const UPDATED_AT = null;

}
