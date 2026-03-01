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
        'user_id',
        'status',
        'admin_memo',
        'processed_at',
        'processed_by',
    ];

    public function user()
    {
        return $this->belongsTo(User::class, 'user_id', 'id');
    }

    public function processor()
    {
        return $this->belongsTo(User::class, 'processed_by', 'id');
    }
}
