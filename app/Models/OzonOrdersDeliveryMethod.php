<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class OzonOrdersDeliveryMethod extends Model
{
    use HasFactory;
    protected $table = 'ozon_fbs_rfbs_orders_delivery_methods';
    protected $primaryKey = 'id';
    protected $connection = 'backups';
    protected $fillable = ['id', 'delivery_method_id', 'warehouse_id', 'order_id', 'tpl_provider_id','name','warehouse','tpl_provider'];
}
