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

    public function history() {
        return Backlog::where('log_id', $this->id)->get();
    }

    function get_status() {
        if ($this->status == 0) {
            return 'Menunggu approval head departemen';
        }
        if ($this->status == 1) {
            return 'Menunggu approval head departemen tujuan';
        }
        if ($this->status == 2) {
            return 'Ditolak';
        }
        if ($this->status == 3) {
            return 'Dalam pengerjaan departemen tujuan';
        }
        if ($this->status == 4) {
            return 'Hasil dalam review head departemen';
        }
        if ($this->status == 5) {
            return 'Selesai';
        }
    }
}
