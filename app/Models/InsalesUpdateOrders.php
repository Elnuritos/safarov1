<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class InsalesUpdateOrders extends Model
{
    use HasFactory;
    protected $fillable = ['id', 'article', 'title', 'price', 'exported'];
}
