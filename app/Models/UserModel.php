<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class UserModel extends Model
{
    protected $primarykeys='id';
    protected $table='user';
    public $timestamps=false;
   	protected $guarded=[];
}
