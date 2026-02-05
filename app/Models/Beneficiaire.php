<?php


namespace App\Models;


use Illuminate\Database\Eloquent\Model;


class Beneficiaire extends Model
{
    //protected $connection = 'clientdb'; 
    protected $table = 'beneficiaire'; 
    protected $primaryKey = 'beneficiaireId';
}
