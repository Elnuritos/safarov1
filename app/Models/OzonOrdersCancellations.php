<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class OzonOrdersCancellations extends Model
{
    use HasFactory;
    protected $table = 'ozon_fbs_rfbs_orders_cancellations';
    protected $primaryKey = 'id';
    protected $connection = 'backups';
    protected $fillable = ['id', 'cancel_reason_id', 'cancel_reason', 'order_id', 'cancellation_type','cancellation_initiator'];
}
