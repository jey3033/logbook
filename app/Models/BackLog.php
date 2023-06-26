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

    function get_user() {
        return User::where('id', $this->user_id)->first();
    }

    function get_status() {
        if ($this->status == 0) {
            if(Backlog::where("log_id", $this->log_id)->where("user_id", $this->user_id)->where("status",0)->where("created_at",">",$this->created_at)->count() > 0) {
                return 'Mengedit dokumen';
            }
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
