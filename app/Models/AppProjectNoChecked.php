<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class AppProjectNoChecked extends Model
{
    //指定表名
    protected $table = 'app_project_no_checked';
    protected $primaryKey = 'project_id';

    const CREATED_AT = null;
    const UPDATED_AT = null;

}
