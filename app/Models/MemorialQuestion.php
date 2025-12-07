<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class MemorialQuestion extends Model
{
    use HasFactory;

    protected $table = 'mm_memorial_questions';

    public $timestamps = true;

    protected $fillable = [
        'detail_id',
        'display_order',
        'is_active',
    ];

    public function detail()
    {
        return $this->belongsTo(MemorialQuestionDetail::class, 'detail_id', 'id');
    }
}
