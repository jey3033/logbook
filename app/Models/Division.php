<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Division extends Model
{
    use HasFactory;

    protected $Fillable = [
        "name",
        'uuid',
        "supervisor"
    ];

    public function member(): HasMany {
        return $this->hasMany(User::class, 'division');
    }
}
