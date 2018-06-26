<?php

namespace App\Http\Controllers;

use JWTAuth;
use JWTAuthException;
use App\User;
use App\Car;
use \App\Response\Response;
use \App\Service\CarService;
use Illuminate\Http\Request;

class CarController extends Controller
{   
    private $car;
    private $user;
    private $carService;
    private $response;

    /**
     * Construct
     */
    public function __construct()
    {
        $this->car         = new Car();
        $this->user        = new User();
        $this->carService  = new CarService();
        $this->response    = new Response();
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

            if(!$userLogged)
            {
                $this->response->setType('N');
                $this->response->setMessage('User not authenticate');

                return response()->json($this->response->toString());
            }

            $returnCar = $this->carService->createCar($userLogged->id, $request);

            $this->response->setType("S");
            $this->response->setDataSet("Car", $returnCar);            
            $this->response->setMessages("Created car successfully!");
        }
        catch (\Exception $e)
        {
            $this->response->setType("N");
            $this->response->setMessages($e->getMessage());

            return response()->json($this->response->toString());
        }

        return response()->json($this->response->toString());
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
            $this->response->setType("N");
            $this->response->setMessages("Car not found!");
            return response()->json($this->response->toString());
        }
        else 
        {
            $this->response->setType("S");
            $this->response->setDataSet("car", $car);
            $this->response->setMessages("Show car successfully!");
            
            return response()->json($this->response->toString());
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
                $this->response->setType("N");
                $this->response->setMessages("Record not found!");
    
                return response()->json($this->response->toString());
            }
    
            $car->fill([
                $request->all(),
            ]);
            $car->save();
            $this->response->setType("S");
            $this->response->setDataSet("car", $car);
            $this->response->setMessages("User updated successfully !");
    
            return response()->json($this->response->toString());
        }
        catch (\Exception $e)
        {
            $this->response->setType("N");
            $this->response->setMessages($e->getMessage());

            return response()->json($this->response->toString());
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

            if(!$user)
            {
                $this->response->setType("N");
                $this->response->setMessages("Record not found!");

                return response()->json($this->response->toString());
            }

            $user->delete();

        }
        catch (\Exception $e)
        {
            $this->response->setType("N");
            $this->response->setMessages($e->getMessage());

            return response()->json($this->response->toString());
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
            $cars = $this->car->getAllCarsByUser($userID);

            if(!$cars)
            {
                $this->response->setType("N");
                $this->response->setMessages("Cars not found");
            }
            else 
            {
                $this->response->setType("S");
                $this->response->setMessages("Cars founded!");
                $this->response->setDataSet("cars", $cars);
            }
            
        }
        catch (\Exception $e)
        {
            $this->response->setType("N");
            $this->response->setMessages($e->getMessage());

            return response()->json($this->response->toString());
        }
        
        return response()->json($this->response->toString());
    }
}
