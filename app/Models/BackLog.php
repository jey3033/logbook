<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Backlog extends Model
{
    use HasFactory;

    protected $Fillable = [
        "log_id",
        "log",
        "user_id",
        'next_approver'
    ];
}
