<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Log extends Model
{
    use HasFactory;

    protected $Fillable = [
        "title",
        "log",
        "user_id",
        'next_approver'
    ];

    public function author() {
        return User::where("id", $this->user_id)->first();
    }
}
