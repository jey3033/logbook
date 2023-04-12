<?php

namespace App\Http\Controllers;

use App\Models\Division;
use App\Http\Requests\StoreDivisionRequest;
use App\Http\Requests\UpdateDivisionRequest;
use App\Models\User;
use Illuminate\Support\Facades\Auth;
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
        $division = DB::table('divisions')->join('users', "divisions.supervisor", "=", "users.id")->select("divisions.name", "users.name as supervisor", "divisions.active", "divisions.uuid");
        if (isset($_REQUEST['filter'])) {
            foreach ($_REQUEST['filter'] as $key => $value) {
                if($value['value'] != null) {
                    $filterValue = $value['value'];
                    $division = $division->where($value['name'],'like',"%{$filterValue}%");
                }
            }
        }

        $division = $division->get();
        
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
            $division->save();

            $division->uuid = md5($division->id . $division->name);
            $division->save();

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
    public function edit(Division $division)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \App\Http\Requests\UpdateDivisionRequest  $request
     * @param  \App\Models\Division  $division
     * @return \Illuminate\Http\Response
     */
    public function update(UpdateDivisionRequest $request, Division $division)
    {
        //
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
