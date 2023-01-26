<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Insales extends Model
{
    use HasFactory;
    protected $table = 'insales_pyrus_ids';
    protected $fillable = ['id', 'order_id', 'task_id'];
}
