<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class InsalesProducts extends Model
{
    use HasFactory;
    protected $fillable = ['id', 'article', 'product_id', 'title', 'price',"variant_id"];
}
