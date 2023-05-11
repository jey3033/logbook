<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Routing\Controller as BaseController;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use OTPHP\TOTP;

class UserController extends BaseController
{
    public function login() {
        return view('login');
    }

    public function auth(Request $request) {
        $email = $_POST['email'];
        $password = $_POST['password'];
        $result = [];
        
        if (Auth::attempt(['email' => $email, 'password' => $password])) {
            if(Auth::user()->activated == 2) return response(json_encode(["message" => "Your account is deactivated, please contact your supervisor"]), 401);
            $result['message'] = 'Succesfull Login';
            $result['TOTP'] = Auth::user()->TOTPEnable;
            $OTP = TOTP::create();
            if (!Auth::user()->secret) {
                $user = User::where('uuid', Auth::user()->uuid)->first();
                $user->secret = $OTP->getSecret();
                $user->save();
            }
            $OTP = TOTP::create(Auth::user()->secret);
            $OTP->setLabel("Laravel Logbook({$email})");
            $grCodeUri = $OTP->getQrCodeUri('https://api.qrserver.com/v1/create-qr-code/?data=[DATA]&size=300x300&ecc=M','[DATA]');
            $result['uri'] = $grCodeUri;    
            return response(json_encode($result),200);
        }
        $result['message'] = 'Email atau Password anda salah, silahkan dicek kembali';
        return response(json_encode($result), 404);
    }

    public function get_logged_user() {
        if (Auth::user()) {
            return response(json_encode(Auth::user()), 200);
        }
        else {
            $result['message'] = "User Not Found";
            return response(json_encode($result), 404);
        }
    }

    public function logout() {
        if (!Auth::user()) return response(json_encode(["Message" => "You're not logged in"]), 401);
        Auth::logout();

        $result['message'] = "User Logged Out";
        return response(json_encode($result), 200);
    }

    public function create_user() {
        try {
            $name = $_POST['Name'];            
            $email = $_POST['Email'];
            $uuid = md5($email);
            $password = $_POST['Password'];

            $new_user = [];
            $new_user['name'] = $name;
            $new_user['uuid'] = $uuid;
            $new_user['email'] = $email;
            $new_user['activated'] = 1;
            $new_user['password'] = bcrypt($password);

            User::create($new_user);

            return response(json_encode(['message' => "User {$name} Created"]), 201);
        } catch (\Throwable $th) {
            return response(json_encode($th->getMessage()), 404);
        }
    }

    public function get_list_user() {
        $user = DB::table("users")->select('users.name', 'users.profile_path', 'users.email', 'users.activated', 'users.uuid', 'users.supervisor');
        if (isset($_REQUEST['filter'])) {
            foreach ($_REQUEST['filter'] as $key => $value) {
                if($value['value'] != null) {
                    $filterValue = $value['value'];
                    $user = $user->where($value['name'],'like',"%{$filterValue}%");
                }
            }
        }

        $user = $user->get();
        if ($user->isEmpty()) {
            return response(json_encode(["Message" => "USer List is Empty"]), 204);
        }
        return response(json_encode(["Data" => $user]));
    }

    public function update_user() {
        if (!Auth::user()) return response(json_encode(["Message" => "You're not logged in"]), 401);
        try {
            $name = $_POST['Name'];
            $uuid = $_POST['UUID'];
            $email = $_POST['Email'];
            $password = (isset($_POST['Password'])) ? $_POST['Password'] : "";
            $supervisor = (isset($_POST['Supervisor'])) ? $_POST['Supervisor'] : "";

            $user = User::where("uuid", $uuid)->first();
            $user->name = $name;
            $user->email = $email;
            if($password) $user->password = $password;
            if($supervisor) $user->supervisor = $supervisor;
            $user->save();

            return back();
        } catch (\Throwable $th) {
            return response(json_encode($th->getMessage()), 404);
        }
        
    }

    public function deactivate_user($id) {
        if (!Auth::user()) return response(json_encode(["Message" => "You're not logged in"]), 401);

        try {
            $user = User::where("uuid", $id)->first();
            $user->activated = 2;
            $user->save();

            return back();
        } catch (\Throwable $th) {
            return response(json_encode($th->getMessage()), 404);
        }
    }

    public function activate_user($id) {
        if (!Auth::user()) return response(json_encode(["Message" => "You're not logged in"]), 401);

        try {
            $user = User::where("uuid", $id)->first();
            $user->activated = 1;
            $user->save();

            return back();
        } catch (\Throwable $th) {
            return response(json_encode($th->getMessage()), 404);
        }
    }

    public function change_pass_bypass() {
        $uuid = $_POST['id'];
        $password = $_POST['password'];
        try {
            $user = User::where("uuid", $uuid)->first();
            $user->password = bcrypt($password);
            $user->save();

            return response(json_encode(['Message' => "User {$user->name}'s password is changed"]), 202);
        } catch (\Throwable $th) {
            return response(json_encode($th->getMessage()), 404);
        }
    }

    public function set_supervisor() {
        $uuid = $_POST['id'];
        $supervisor_id = $_POST['supervisor_id'];

        try {
            $user = User::where("uuid", $uuid)->first();
            $supervisor = User::where("uuid", $supervisor_id)->first();
            if ($user->id == $supervisor->id) return response(json_encode(["Message" => "cannot set own user as own supervisor"]), 406);
            $user->supervisor = $supervisor->id;
            $user->save();

            return response(json_encode(["Message" => "user {$user->name}'s supervisor is set to {$supervisor->name}"]), 202);
        } catch (\Throwable $th) {
            return response(json_encode($th->getMessage()), 500);
        }
    }

    public function check_password(Request $request) {
        $pass = $_GET['oldpass'];
        $check = Hash::check($pass, $request->user()->password);
        if ($check) {
            return response(json_encode(['Message' => "Password match"]));
        }
        return response(json_encode(['Message' => "Password mismatch"]));
    }
    
    public function edit_profile(Request $request) {
        $request->validate([
            'image' => 'image|mimes:jpeg,png,jpg,gif,svg',
        ]);

        $user = User::where("uuid", $_POST['uuid'])->first();
    
        if ($request->image) {
            $imageName = time().'.'.$request->image->extension();  
            $request->image->move(public_path('images'), $imageName);
            $user->profile_path = "/images/{$imageName}";
        }
        $user->name = $_POST['name'];
        $user->email = $_POST['email'];
        if (isset($_POST['TOTP'])) {
            $user->TOTPEnable = 1;
        }else{
            $user->TOTPEnable = 0;
        }
        if ($_POST['supervisor']) {
            $user->division = $_POST['supervisor'];
        }
        $user->save();

        return back()->with(['status' => "success", 'message' => "Profile Updated"]);
    }

    public function otp_verification() {
        $dataUser = User::where('uuid', Auth::user()->uuid)->first();
        $OTP = TOTP::create($dataUser->secret);
        if ($OTP->verify($_POST['verification'])) {
            return 200;
        } else {
            return 400;
        }
    }

    public function change_password() {
        $uuid = Auth::user()->uuid;
        $password = $_POST['newPass'];
        try {
            $user = User::where("uuid", $uuid)->first();
            $user->password = bcrypt($password);
            $user->save();

            return response(json_encode(['Message' => "User {$user->name}'s password is changed"]), 202);
        } catch (\Throwable $th) {
            return response(json_encode($th->getMessage()), 404);
        }
    }

    public function get_profilepicture() {
        $uuid = $_GET['uuid'];
        $profile_path = User::where("uuid", $uuid)->first()->profile_path;
        return $profile_path;
    }

    public function storeToken(Request $request) {
                Auth::user()->update(['device_key'=>$request->token]);
                return response()->json(['Token successfully stored.']);
    }
  
    
}
