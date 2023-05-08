<?php

namespace App\Http\Controllers;

use App\Models\GlobalSetting;
use App\Http\Requests\StoreGlobalSettingRequest;
use App\Http\Requests\UpdateGlobalSettingRequest;

class GlobalSettingController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        //
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
     * @return \Illuminate\Http\Response
     */
    public function store() {
        $data = $_POST;

        try {
            $setting = GlobalSetting::first();
            $setting->default_date_acceptance = $data['default_date_acceptance'];

            $setting->save();
            return response("Global Setting Updated");
        } catch (\Throwable $th) {
            //throw $th;
            return response($th->getMessage(), 401);
        }
    }
}
