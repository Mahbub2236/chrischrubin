<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Transaction extends Model
{
    protected $table = 'transaction';
    protected $primaryKey = 'transID';

    protected $fillable = [
        'userID',
        'benId',
        'type',
        'amount',
        'status',
        'reference',
        'date'
    ];
}