<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class OzonFboOrdersProducts extends Model
{
    use HasFactory;
    protected $table = 'ozon_fbo_orders_products';
    protected $primaryKey = 'id';
   protected $connection = 'backups';
    protected $fillable = ['id', 'price', 'ProductTotal','article', 'order_id', 'name','sku','quantity','offer_id'];
}
