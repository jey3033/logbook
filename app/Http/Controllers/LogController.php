<?php

namespace App\Http\Controllers;

use App\Models\Log;
use App\Http\Requests\StoreLogRequest;
use App\Http\Requests\UpdateLogRequest;
use App\Models\User;
use ETC_Class\Custom_Filter\Custom_Filter as Custom_Filter;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

use function PHPUnit\Framework\isEmpty;

class LogController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        if (!Auth::user()) return response(json_encode(["Message" => "You're not logged in"]), 401);
        $log = Log::where("user_id", Auth::user()->id)->get();
        $log = DB::table("logs")->join('users', "logs.user_id", "=", "users.id", 'inner')->where("logs.user_id", Auth::user()->id)->orWhere("users.supervisor",Auth::user()->id)->select('logs.id', 'logs.uuid', 'users.name', 'users.profile_path', 'logs.title', 'logs.log', 'logs.status', 'logs.updated_at');
        if (isset($_REQUEST['filter'])) {
            foreach ($_REQUEST['filter'] as $key => $value) {
                if($value['value'] != null) {
                    $filterValue = $value['value'];
                    $log = $log->where($value['name'],'like',"%{$filterValue}%");
                }
            }
        }
        $log = $log->get();
        foreach ($log as $key => $value) {
            $value->log = Str::words($value->log, 10, '...');
        }
        if ($log->isEmpty()) {
            return response(json_encode(["Message" => "Log is Empty"]), 204);
        }
        return response(json_encode(["Data" => $log]));
    }

    public function store()
    {
        if (!Auth::user()) return response(json_encode(["Message" => "You're not logged in"]), 401);
        try {
            // var_dump('aa');die();
            $title = $_POST['title'];
            $log = $_POST['log'];
            $new_log = new Log();
            $new_log->title = $title;
            $new_log->log = $log;
            $new_log->user_id = Auth::user()->id;
            $new_log->save();

            $new_log->uuid = md5($new_log->id.$log);
            $new_log->save();

            return response(json_encode(["Message" => "log {$log} is created", "ID" => $new_log->id]));
        } catch (\Throwable $th) {
            return response(json_encode($th->getMessage()), 500);
        }
    }

    public function view($id) {
        $uuid = $id;
        try {
            $log = Log::where("uuid", $uuid)->first();
            return response(json_encode($log));
        } catch (\Throwable $th) {
            return response(json_encode($th->getMessage()), 500);
        }
    }

    public function edit() {}

    public function amend($id) {
        $uuid = $id;
        $text = $_REQUEST['log'];
        try {
            $log = Log::where("uuid", $uuid)->first();
            $author = User::where("id", $log->user_id)->first();
            if ($log->status != 0) return response(json_encode(["Message" => "Log has been Responsed"]), 405);
            if ($author->id != Auth::user()->id) return response(json_encode(["Message" => 'You\'re not authorized to edit this log']), 403);
            $log->log = $text;
            $log->save();

            return response(json_encode(["Message" => "Log {$log->uuid} has been updated"]), 202);
        } catch (\Throwable $th) {
            return response(json_encode($th->getMessage()), 500);
        }
    }

    public function response($id) {
        $uuid = $id;
        $status = $_POST['status'];
        try {
            $log = Log::where("uuid", $uuid)->first();
            if (!$log) return response(json_encode(["Message" => "Log Not Found"]), 400);
            $author = User::where("id", $log->user_id)->first();
            if ($author->id == Auth::user()->id) return response(json_encode(["Message" => 'You\'re not authorized to approved your own log']), 403);
            elseif ($author->supervisor_id == Auth::user()->id)  return response(json_encode(["Message" => 'You\'re not this user\'s supervisor']), 401);
            $log->status = $status;
            $log->save();

            return response(json_encode(["Message" => "Log Responsed"]));
        } catch (\Throwable $th) {
            return response(json_encode($th->getMessage()), 500);
        }
    }

    public function getLogOutstanding() {
        if (!Auth::user()) return response(json_encode(["Message" => "You're not logged in"]), 401);
        try {
            $data = DB::table("logs")->join('users', "logs.user_id", "=", "users.id", 'inner')->Where("users.supervisor",Auth::user()->id)->where("logs.status",0)->select('logs.id', 'logs.uuid', 'users.name', 'users.profile_path', 'logs.title', 'logs.log', 'logs.status', 'logs.updated_at')->get();
            foreach ($data as $key => $value) {
                $value->log = Str::words($value->log, 10, '...');
            }
            if ($data->isEmpty()) {
                return response(json_encode(["Message" => "Log is Empty"]), 204);
            }
            return response(json_encode(["Data" => $data]));
        } catch (\Throwable $th) {
            return response(json_encode($th->getMessage()), 500);
        }
    }

    public function getLogPersonal() {
        if (!Auth::user()) return response(json_encode(["Message" => "You're not logged in"]), 401);
        try {
            $data = DB::table("logs")->join('users', "logs.user_id", "=", "users.id", 'inner')->Where("logs.user_id", Auth::user()->id)->select('logs.id', 'logs.uuid', 'users.name', 'users.profile_path', 'logs.title', 'logs.log', 'logs.status', 'logs.updated_at')->get();
            foreach ($data as $key => $value) {
                $value->log = Str::words($value->log, 10, '...');
            }
            if ($data->isEmpty()) {
                return response(json_encode(["Message" => "Log is Empty"]), 204);
            }
            return response(json_encode(["Data" => $data]));
        } catch (\Throwable $th) {
            return response(json_encode($th->getMessage()), 500);
        }
    }
}
