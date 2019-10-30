<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\DB;

use Config;

use App\Car;
use App\User;

use App\Response\Response;

use App\Service\CarService;

class CarController extends Controller
{   
    private $car;
    private $carService;
    private $messages;
    private $response;
    private $user;

    /**
     * Construct
     */
    public function __construct()
    {
        $this->car         = new Car();
        $this->carService  = new CarService();
        $this->messages    = Config::get('messages');
        $this->response    = new Response();
        $this->user        = new User();
    }

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {   }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {   }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        try
        {
            $userLogged = $this->carService->getAuthUser($request);

            $returnCar = $this->carService->createCar($userLogged->id, $request);

            $this->response->setDataSet("Car", $returnCar);            
            return response()->json($this->response->toString("S", $this->messages['car']['save']));
        }
        catch (\Exception $e)
        {
            return response()->json($this->response->toString("N", $e->getMessage()));
        }
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        $car = $this->car->find($id);

        if(!$car)
        {
            return response()->json($this->response->toString("N", $this->messages['error']));
        }
        else 
        {
            $this->response->setDataSet("car", $car);
            return response()->json($this->response->toString("S", $this->messages['car']['show']));
        }
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {   }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id)
    {
        try
        {
            $car = Car::find($id);
            
            if(!$car) 
            {
                return response()->json($this->response->toString("N", $this->messages['error']));
            }
    
            $car->fill([
                $request->all(),
            ]);

            $car->save();

            $this->response->setDataSet("car", $car);
            return response()->json($this->response->toString("S", $this->messages['car']['save']));

        }
        catch (\Exception $e)
        {
            return response()->json($this->response->toString("N", $e->getMessage()));
        }
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        try
        {
            $car = Car::find($id);
            
            if(!$car)
            {
                return response()->json($this->response->toString("N", $this->messages['error']));
            }

            DB::beginTransaction();
            
            foreach($car as $data) 
            {
                $cars = $this->car->find($data->id);
                $cars->supplies()->delete();
            }
            
            DB::commit();
            return response()->json($this->response->toString("S", $this->messages['car']['delete']));

        }
        catch (\Exception $e)
        {
            DB::rollBack();
            return response()->json($this->response->toString("N", $e->getMessage()));
        }
    }

    /**
     * Get all car by id user
     * @param $userID
     * @return object $cars
     */
    public function getAllCarsByUser($userID)
    {
        try 
        {
            $user = $this->user->find($userID);
            $cars = $user->cars()->get();

            if(!$cars)
            {
                return response()->json($this->response->toString("N", $this->messages['error']));
            }
            else 
            {
                $this->response->setDataSet("cars", $cars);
                return response()->json($this->response->toString("S", $this->messages['car']['show']));
            }            
        }
        catch (\Exception $e)
        {
            return response()->json($this->response->toString("N", $e->getMessage()));
        }
    }
}
