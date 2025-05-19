<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use App\Models\User;

class SgdGroup extends Model
{
    protected $table = 'sgd_groups';
    protected $fillable = [
        'title',
        'topic',
        'meeting_address',
        'is_done',
        'category'
    ];
    public function members()
    {
        return $this->hasMany(User::class);
    }
}
