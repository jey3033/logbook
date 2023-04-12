<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Foundation\Inspiring;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class PageController extends Controller
{
    //
    public function dashboard() {
        if (!Auth::user()) return redirect('/');

        $quote = Inspiring::quote();
        return view("dashboard", ["username" => Auth::user()->name, "quote" => $quote]);
    }

    public function logList() {
        if (!Auth::user()) return redirect('/');
        $list_user = User::get();
        return view("list-log", ["username" => Auth::user()->name, 'list_user' => $list_user]);
    }

    public function profile() {
        if (!Auth::user()) return redirect('/');
        $list_user = User::where("activated",1)->get();
        return view("profile", ["user_data" => Auth::user(), 'list_user' => $list_user]);
    }
    public function logout() {
        Auth::logout();

        return redirect("/");
    }
    public function user() {
        $list_user = User::where("activated",1)->get();
        return view("list-user",['list_user' => $list_user]);
    }
    public function edituser($uuid) {
        $data_user = User::where("uuid", $uuid)->first();
        $list_user = User::where("activated",1)->get();
        return view("edit-user",['user_data' => $data_user, 'list_user' => $list_user]);
    }

    public function divisionlist() {
        if (!Auth::user()) return redirect('/');

        $listsupervisor = DB::table('users')->join("divisions", "users.id","=","divisions.supervisor")->select("users.name", "users.uuid")->get();
        $list_user = User::where("activated",1)->get();
        return view('list-division', ['supervisorlist' => $listsupervisor, 'userlist' => $list_user]);
    }

    public function divisioncreate() {
        $list_user = User::where("activated",1)->get();
        return view('create-division', ['userlist' => $list_user]);
    }
}
