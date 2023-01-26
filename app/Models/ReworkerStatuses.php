<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ReworkerStatuses extends Model
{
    use HasFactory;
    protected $table = 'reworker_statuses';
    //  protected $table = 'reworker_statusesel';
    protected $fillable = ['id', 'order_status', 'task_id'];
}
