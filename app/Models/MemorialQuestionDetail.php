<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class MemorialQuestionDetail extends Model
{
    use HasFactory;

    protected $table = 'mm_memorial_question_details';

    public $timestamps = true;

    protected $fillable = [
        'question_type',
        'question_title',
    ];

    public function question()
    {
        return $this->hasOne(MemorialQuestion::class, 'detail_id', 'id');
    }
}
