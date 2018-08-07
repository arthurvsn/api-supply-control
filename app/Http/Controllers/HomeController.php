<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use \App\Response\Response;

class HomeController extends Controller
{
    private $response;
    private $messages;

    public function __construct() 
    {
        $this->messages = \Config::get('messages');
        $this->response = new Response();
    }

    public function index()
    {
        return response()->json($this->response->toString("S", $this->messages['api']['connect']));
    }
}
