<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class OzonOrders extends Model
{
    use HasFactory;
    protected $table = 'ozon__fbs_rfbs_orders';
    protected $primaryKey = 'id';
    protected $connection = 'backups';
    protected $fillable = ['id', 'posting_number', 'status', 'order_id', 'order_number','tracking_number','tpl_integration_type','in_process_at','shipment_date','delivering_date'];
}
