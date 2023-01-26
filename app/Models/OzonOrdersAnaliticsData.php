<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class OzonOrdersAnaliticsData extends Model
{
    use HasFactory;
    protected $table = 'ozon_fbs_rfbs_orders_analitics_data';
    protected $primaryKey = 'id';
    protected $connection = 'backups';
    protected $fillable = ['id', 'warehouse_id', 'tpl_provider_id', 'order_id', 'region','city','payment_type_group_name','warehouse','tpl_provider','delivery_date_begin','delivery_date_end'];
}
