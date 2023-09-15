<?php

namespace App\Http\Controllers;

use App\Models\Division;
use App\Http\Requests\StoreDivisionRequest;
use App\Http\Requests\UpdateDivisionRequest;
use App\Models\User;
use Illuminate\Support\Facades\Auth;
// use Illuminate\Support\Facades\Date;
use Illuminate\Support\Facades\DB;

class DivisionController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        //
        if (!Auth::user()) return response(json_encode(["Message" => "You're not logged in"]), 401);
        $division = DB::table('divisions')->join('users', "divisions.supervisor", "=", "users.id")->select("divisions.name", "users.name as supervisor", "divisions.due_date_acceptance", "divisions.active", "divisions.uuid");
        if (isset($_REQUEST['filter'])) {
            foreach ($_REQUEST['filter'] as $key => $value) {
                if($value['value'] != null) {
                    $filterValue = $value['value'];
                    $division = $division->where($value['name'],'like',"%{$filterValue}%");
                }
            }
        }

        $division = $division->orderBy('divisions.created_at', 'desc')->get();
        
        if ($division->isEmpty()) {
            return response(json_encode(["Message" => "No Division Yet"]), 204);
        }
        return response(json_encode(["Data" => $division]));
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \App\Http\Requests\StoreDivisionRequest  $request
     * @return \Illuminate\Http\Response
     */
    public function store() {
        //
        try {
            $division = new Division();
            $division->name = $_POST['name'];
            $supervisorID = User::where('uuid', $_POST['supervisor'])->first()->id;
            $division->supervisor = $supervisorID;
            $division->due_date_acceptance = $_POST['due_date_acceptance'];
            $division->active = 0;
            if (isset($_POST['status'])) {
                $division->active = 1;
            }
            $division->save();

            $division->uuid = md5($division->id . $division->name);
            $division->save();

            foreach ($_POST['member'] as $value) {
                $user = User::where('uuid', $value)->first();
                $user->division = $division->id;
                $user->save();
            }

            return response("Division {$division->name} Created");
        } catch (\Throwable $th) {
            //throw $th;
            return response($th->getMessage(), 401);
        }
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\Models\Division  $division
     * @return \Illuminate\Http\Response
     */
    public function show(Division $division)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\Models\Division  $division
     * @return \Illuminate\Http\Response
     */
    public function edit($uuid) {
        $division = Division::where('uuid', $uuid)->first();
        $list_user = User::where("activated",1)->get();
        return view('edit-division', ['division_data' => $division, 'userlist' => $list_user]);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \App\Http\Requests\UpdateDivisionRequest  $request
     * @param  \App\Models\Division  $division
     * @return \Illuminate\Http\Response
     */
    public function update($uuid) {
        try {
            $division = Division::where('uuid', $uuid)->first();
            $division->name = $_POST['name'];
            $supervisorID = User::where('uuid', $_POST['supervisor'])->first()->id;
            $division->due_date_acceptance = $_POST['due_date_acceptance'];
            $division->supervisor = $supervisorID;
            $division->active = 0;
            if (isset($_POST['status'])) {
                $division->active = 1;
            }
            $division->save();

            foreach (User::all() as $key => $value) {
                $value->division = null;
                $value->save();
            }
            foreach ($_POST['member'] as $value) {
                $user = User::where('uuid', $value)->first();
                $user->division = $division->id;
                $user->save();
            }
        } catch (\Throwable $th) {
            //throw $th;
            return response($th->getMessage(), 401);
        }
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Models\Division  $division
     * @return \Illuminate\Http\Response
     */
    public function destroy(Division $division)
    {
        //
    }
}
