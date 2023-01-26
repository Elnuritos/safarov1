<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class InsalesBonusSystem extends Model
{
    use HasFactory;
    protected $table = 'insales_bonus_system';
    protected $fillable = ['id', 'order_id', 'client_id', 'bonus', 'exported','in_order_id'];
}
