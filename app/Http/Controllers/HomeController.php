<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use \App\Response\Response;

use App\Test;

class HomeController extends Controller
{
    private $response;

    public function __construct() 
    {
        $this->response = new Response();
    }

    public function index()
    {
        return response()->json($this->response->toString("S", config('api.connect')));
    }

    public function store(Request $request)
    {
        $user = Test::create($request->all()); // Automatically generate a uuid
        return response()->json(['teste' => $user]);
    }

}
