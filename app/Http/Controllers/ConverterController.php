<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreConverter;
use App\Support\HttpCall;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Artisan;

class ConverterController extends Controller
{
    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        return view('home');
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(StoreConverter $request)
    {
        $curl = $request->input('curl');

        // TODO: catch any "input" errors as validation errors
        Artisan::call($curl);

        $data = json_decode(trim(Artisan::output()), true);

        $request = \App\Models\Request::create($data);
        $code = HttpCall::format($request);

        return view('home', ['curl' => $curl, 'http' => $code]);
    }
}
