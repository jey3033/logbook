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
        $notif = Notification::where('receiver', Auth::user()->id)->where('read',0);
        return response([
            'count' => $notif->count() ? $notif->count() : 0,
            'shortlist' => $notif->limit(3)->get(),
        ]);
    }

    public function readNotif() {
        $notif = Notification::where('receiver', Auth::user()->id);
        foreach ($notif->get() as $key => $value) {
            $toRead = Notification::where('id', $value['id'])->first();
            // var_dump($toRead);
            $toRead->read = 1;
            $toRead->save();
        }
    }
}
