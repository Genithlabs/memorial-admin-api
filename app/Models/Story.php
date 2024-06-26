<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Story extends Model
{
    use HasFactory;
    protected $table = "mm_stories";
    public $timestamps = true;

    public function attachment() {
        return $this->hasOne(Attachment::class, 'id', 'attachment_id')->where('is_delete', 0);
    }
}
