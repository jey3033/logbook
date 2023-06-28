<?php

namespace App\Http\Controllers;

use App\Models\Log;
use App\Http\Requests\StoreLogRequest;
use App\Http\Requests\UpdateLogRequest;
use App\Mail\NotifMail;
use App\Mail\StatusMail;
use App\Models\BackLog;
use App\Models\Division;
use App\Models\Notification;
use App\Models\User;
use Carbon\Carbon;
use ETC_Class\Custom_Filter\Custom_Filter as Custom_Filter;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Str;

use function PHPUnit\Framework\isEmpty;

class LogController extends Controller {
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index() {
        if (!Auth::user()) return response(json_encode(["Message" => "You're not logged in"]), 401);
        $log = Log::where("user_id", Auth::user()->id)->get();
        $log = DB::table("logs")->join('users', "logs.user_id", "=", "users.id", 'inner')->select('logs.id', 'logs.uuid', 'users.name', 'users.profile_path', 'logs.title', 'logs.log', 'logs.status', 'logs.updated_at');
        if (isset($_REQUEST['filter'])) {
            foreach ($_REQUEST['filter'] as $key => $value) {
                if ($value['name'] == 'users.id' && $value['value'] != null) {
                    $filterValue = $value['value'];
                    $log = $log->where($value['name'],$filterValue);
                }

                if ($value['name'] == 'tgl-update-min' && $value['value'] != null) {
                    $filterValue = $value['value'];
                    $log = $log->where("logs.updated_at",'>',$filterValue.' 00:00:00');
                }else if ($value['name'] == 'tgl-update-max' && $value['value'] != null) {
                    $filterValue = $value['value'];
                    $log = $log->where("logs.updated_at",'<=',$filterValue.' 23:59:59');
                }else 
                if($value['value'] != null) {
                    $filterValue = $value['value'];
                    $log = $log->where($value['name'],'like',"%{$filterValue}%");
                }
            }
        }else {
            $log = $log->where("logs.user_id", Auth::user()->id)->orWhere("users.supervisor",Auth::user()->id);
        }
        $log = $log->latest()->get();
        foreach ($log as $key => $value) {
            $value->shortenlog = Str::words(strip_tags($value->log), 10, '...');
        }
        if ($log->isEmpty()) {
            return response(json_encode(["Message" => "Log is Empty"]), 204);
        }
        return response(json_encode(["Data" => $log]));
    }

    public function store() {
        if (!Auth::user()) return response(json_encode(["Message" => "You're not logged in"]), 401);
        try {
            // var_dump('aa');die();
            $title = $_POST['title'];
            $log = $_POST['log'];
            $division = $_POST['division'];
            $divisionObj = Division::where("uuid", $division)->first();
            $user_id = Auth::user()->id;
            $user = User::where('id', $user_id)->first();
            $userDiv = Division::where("id", $user->division)->first();
            
            $new_log = new Log();
            $new_log->title = $title;
            $new_log->log = $log;
            $new_log->user_id = $user->id;
            $new_log->division_id = $divisionObj->id;
            $new_log->save();

            $new_log->uuid = md5($new_log->id . $log);
            $new_log->next_approver = $userDiv->supervisor;

            $notification = new Notification();
            $notification->header = "New Log Created";
            $notification->notification = "{$user->name} created new log for {$divisionObj->name}";
            $notification->sender = $user->id;
            $notification->receiver = $userDiv->supervisor;

            $notification->save();

            if($user->supervisor) {
                Mail::to(User::where('id', $user->supervisor)->first()->email)->send(new NotifMail($new_log));
            }
            $new_log->save();

            $backlog = new Backlog();
            $backlog->user_id = $user->id;
            $backlog->log_id = $new_log->id;

            $backlog->save();

            $url = 'POST https://fcm.googleapis.com/v1/projects/logbook-2516b/messages:send';
            $FcmToken = User::whereNotNull('device_key')->pluck('device_key')->all();
            
            $serverKey = 'BPTwkZ2F-B4-GdUma0lrQL94mzw3KzzDJAoUkiQbDQ2_7xvYB1w3T-zPDC5MFbnU7TMMxQPkpn4xy81CQGL3UNA';
    
            $data = [
                "registration_ids" => $FcmToken,
                "notification" => [
                    "title" => $title,
                    "body" => $log,  
                ]
            ];
            $encodedData = json_encode($data);
        
            $headers = [
                'Authorization:key=' . $serverKey,
                'Content-Type: application/json',
            ];
        
            $ch = curl_init();
        
            curl_setopt($ch, CURLOPT_URL, $url);
            curl_setopt($ch, CURLOPT_POST, true);
            curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
            curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
            curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, 0);
            curl_setopt($ch, CURLOPT_HTTP_VERSION, CURL_HTTP_VERSION_1_1);
            // Disabling SSL Certificate support temporarly
            curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);        
            curl_setopt($ch, CURLOPT_POSTFIELDS, $encodedData);
            // Execute post
            $result = curl_exec($ch);
            if ($result === FALSE) {
                die('Curl failed: ' . curl_error($ch));
            }        
            // Close connection
            curl_close($ch);
            // FCM response
            // dd($result);

            return response(json_encode(["Message" => "log {$log} is created", "ID" => $new_log->id]));
        } catch (\Throwable $th) {
            return response(json_encode($th->getMessage()), 500);
        }
    }

    public function view($id) {
        $uuid = $id;
        try {
            $log = Log::where("uuid", $uuid)->first();
            $workerlist = Division::where('id', $log->division_id)->first()->member()->latest()->get();
            // dd($workerlist);
            return view('log-view', ['log' => $log, 'list_worker' => $workerlist]);
        } catch (\Throwable $th) {
            return response(json_encode($th->getMessage()), 500);
        }
    }

    public function edit() {}

    public function amend($id) {
        $uuid = $id;
        $title = $_REQUEST['title'];
        $text = $_REQUEST['log'];
        try {
            $log = Log::where("uuid", $uuid)->first();
            $author = User::where("id", $log->user_id)->first();
            $divisionObj = Division::where("uuid", $log->division)->first();
            $authorDiv = Division::where("id", $author->division)->first();
            if ($log->status != 0) return response(json_encode(["Message" => "Log has been Responsed"]), 405);
            if ($author->id != Auth::user()->id) return response(json_encode(["Message" => 'You\'re not authorized to edit this log']), 403);
            $log->title = $title;
            $log->log = $text;
            $log->save();

            $notification = new Notification();
            $notification->header = "Log Updated";
            $notification->notification = "{$author->name} created new log for {$divisionObj->name}";
            $notification->sender = $author->id;
            $notification->receiver = ($author->division) ? $authorDiv->supervisor : $divisionObj->supervisor;

            $notification->save();

            $backlog = new Backlog();
            $backlog->user_id = Auth::user()->id;
            $backlog->log_id = $log->id;

            $backlog->save();

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
            $divisionObj = Division::where("id", $log->division_id)->first();
            if(isset($_POST['worker'])) { $worker = User::where("uuid", $_POST['worker'])->first(); }
            if ($author->id == Auth::user()->id) return response(json_encode(["Message" => 'You\'re not authorized to approved your own log']), 403);
            elseif ($author->supervisor_id == Auth::user()->id)  return response(json_encode(["Message" => 'You\'re not this user\'s supervisor']), 401);
            $log->status = $status;

            $notification = new Notification();
            $notification->header = "Log Responsed";
            $notification->notification = "Log {$log->title}'s status changed";
            $notification->sender = Auth::user()->id;

            if ($status == 1) {
                $log->next_approver = $divisionObj->supervisor;
                $notification->receiver = $log->next_approver;
            }else if($status == 3) {
                $date = $log->updated_at->addDays($_POST['date']);
                $log->due_date = $date->toDateString();
                $log->next_approver = $worker->id;
                $notification->receiver = $log->next_approver;
            }else if($status == 4) {
                $divID = User::where('id', $log->next_approver)->first()->division;
                $division = Division::where('id', $divID)->first();
                $log->next_approver = $division->supervisor;
                $notification->receiver = $log->next_approver;
            }else{
                $log->next_approver = 0;
                $notification->receiver = $log->user_id;
            }

            $notification->save();
            
            Mail::to($author->email)->send(new StatusMail($log));

            $log->save();

            $backlog = new Backlog();
            $backlog->user_id = Auth::user()->id;
            $backlog->log_id = $log->id;

            $backlog->save();

            return response(json_encode(["Message" => "Log Responsed"]));
        } catch (\Throwable $th) {
            return response(json_encode($th->getMessage()), 500);
        }
    }

    public function getLogOutstanding() {
        if (!Auth::user()) return response(json_encode(["Message" => "You're not logged in"]), 401);
        try {
            $data = DB::table("logs")->join('users', "logs.user_id", "=", "users.id", 'inner')->where("logs.next_approver",Auth::user()->id)->select('logs.id', 'logs.uuid', 'users.name', 'users.profile_path', 'logs.title', 'logs.log', 'logs.status', 'logs.updated_at', 'logs.due_date')->oldest()->get();
            foreach ($data as $key => $value) {
                $value->shortenlog = Str::words(strip_tags($value->log), 10, '...');
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
            $data = DB::table("logs")->join('users', "logs.user_id", "=", "users.id", 'inner')->Where("logs.user_id", Auth::user()->id)->select('logs.id', 'logs.uuid', 'users.name', 'users.profile_path', 'logs.title', 'logs.log', 'logs.status', 'logs.updated_at', 'logs.due_date')->latest()->get();
            foreach ($data as $key => $value) {
                $value->shortenlog = Str::words(strip_tags($value->log), 10, '...');
            }
            if ($data->isEmpty()) {
                return response(json_encode(["Message" => "Log is Empty"]), 204);
            }
            return response(json_encode(["Data" => $data]));
        } catch (\Throwable $th) {
            return response(json_encode($th->getMessage()), 500);
        }
    }

    public function sendWebNotification(Request $request)
    {
                
    }
}
