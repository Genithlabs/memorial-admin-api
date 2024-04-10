<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Memorial extends Model
{
    use HasFactory;

    protected $table = 'mm_memorials';

    public $timestamps = true;

    public function attachmentProfileImage() {
        return $this->hasOne(Attachment::class, 'id', 'profile_attachment_id')->where('is_delete', 0);
    }

    public function attachmentBgm() {
        return $this->hasOne(Attachment::class, 'id', 'bgm_attachment_id')->where('is_delete', 0);
    }
}
