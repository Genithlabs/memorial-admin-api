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

    public function story() {
        return $this->hasMany(Story::class, 'memorial_id', 'id')->where('is_visible', 1);
    }

    public function visitComments() {
        return $this->hasMany(VisitorComment::class, 'memorial_id', 'id')
            ->join('mm_users as user', 'mm_visitor_comments.user_id', 'user.id')
            ->select('mm_visitor_comments.id', 'mm_visitor_comments.user_id', 'user.user_name', 'mm_visitor_comments.memorial_id', 'mm_visitor_comments.message', 'mm_visitor_comments.is_visible', 'mm_visitor_comments.created_at', 'mm_visitor_comments.updated_at')
            ->where('mm_visitor_comments.is_visible', 1)
            ->orderBy('mm_visitor_comments.created_at', 'desc');
    }
}
