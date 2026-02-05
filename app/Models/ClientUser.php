<?php


namespace App\Models;


use Illuminate\Database\Eloquent\Model;


class ClientUser extends Model
{
    protected $guarded = [];
    //protected $connection = 'clientdb'; 
    protected $table = 'user'; 
    protected $primaryKey = 'userID';
}
