<?php

namespace App\Http\Controllers;

use App\Models\Notification;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Illuminate\Foundation\Bus\DispatchesJobs;
use Illuminate\Foundation\Validation\ValidatesRequests;
use Illuminate\Routing\Controller as BaseController;
use Illuminate\Support\Facades\Auth;

class Controller extends BaseController
{
    use AuthorizesRequests, DispatchesJobs, ValidatesRequests;

    public function getNotif() {
        $notif = Notification::where('receiver', Auth::user()->id);
        return response([
            'count' => $notif->count() ? $notif->count() : 0,
            'shortlist' => $notif->limit(3)->get(),
        ]);
    }
}
