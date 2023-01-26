<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class OzonFboOrders extends Model
{
    use HasFactory;
    protected $table = 'ozon_fbo_orders';
    protected $primaryKey = 'id';
 protected $connection = 'backups';
    protected $fillable = ['id', 'posting_number', 'status', 'order_id', 'order_number','in_process_at','created_at_ozon'];
}
