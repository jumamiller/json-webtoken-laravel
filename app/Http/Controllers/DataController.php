<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class DataController extends Controller
{
    public function open(){
        $data="This data is not preotected and is accessible by unauthorised users";
        return response()->json(compact('data'),200);
    }
    public function closed(){
        $data="Only authorised users can see this";
        return response()->json(compact('data'),200);
    }
}
