<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Sber extends Model
{
    use HasFactory;
    protected $table = 'payments_statuses';
    protected $fillable = ['id', 'payment_id', 'task_id', 'status', 'exported'];
}
