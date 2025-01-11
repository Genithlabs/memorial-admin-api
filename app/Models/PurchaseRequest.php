<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class PurchaseRequest extends Model
{
    use HasFactory;

    protected $table = 'mm_purchase_requests';

    protected $fillable = [
        'id',
        'user_id'
    ];
}
