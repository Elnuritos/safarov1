<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class OzonFboOrdersAnaliticsData extends Model
{
    use HasFactory;
    protected $table = 'ozon_fbo_orders_analitics_data';
    protected $primaryKey = 'id';
  protected $connection = 'backups';
    protected $fillable = ['id', 'warehouse_id',  'order_id', 'region','city','payment_type_group_name','warehouse_name','delivery_type'];
}
