<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\User;
use App\Address;
use App\Phone;
use App\Car;
use App\Supply;
use JWTAuth;
use JWTAuthException;
use \App\Response\Response;
use \App\Service\UserService;

class UserController extends Controller
{
    private $user;
    private $address;
    private $phone;
    private $car;
    private $supply;
    private $response;
    private $userService;

    /**
     * construct
     */
    public function __construct()
    {
        $this->user         = new User;
        $this->address      = new Address;
        $this->phone        = new Phone;
        $this->car          = new Car;
        $this->supply       = new Supply;
        $this->response     = new Response();
        $this->userService  = new UserService();
    }

    /**
     * Login user
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function login(Request $request)
    {
        $credentials = $request->only('username', 'password');
        $token = null;
        try 
        {
           if (!$token = JWTAuth::attempt($credentials)) 
           {
               $this->response->setType("N");
               $this->response->setMessages("Invalid username or password");
               return response()->json($this->response->toString());
           }
        } 
        catch (JWTAuthException $e) 
        {
            $this->response->setType("N");
            $this->response->setMessages("Failed to create token");
            return response()->json($this->response->toString());
        }
        
        $user = JWTAuth::toUser($token);
        
        $this->response->setType("S");
        $this->response->setMessages("Login successfully!");
        $this->response->setDataSet("token", $token);
        
        $this->response->setDataSet("user", $user);
        return response()->json($this->response->toString());
    }

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $users = User::get();
        
        $this->response->setType("S");
        $this->response->setDataSet("user", $users);
        $this->response->setMessages("Sucess!");

        return response()->json($this->response->toString());
    }

    /**
     * 
     */
    public function ping(Request $request) 
    {
        $user_logged = $this->userService->getAuthUser($request);

        if(!$user_logged) 
        {
            $this->response->setType("N");
            $this->response->setMessages("Sucess!");
        } 
        else 
        {
            $this->response->setType("S");
            $this->response->setMessages("User logged!");
            $this->response->setDataSet("user", $user_logged);
        }

        return response()->json($this->response->toString());
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {    }

    /**
     * Store a newly created resource in storage.
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        try
        {
            \DB::beginTransaction();
            
            $returnUser = $this->userService->createUser($request);

            $returnUser->address = $this->userService->createAddressUser($returnUser->id, $request);
            $returnUser->phone = $this->userService->createPhoneUser($returnUser->id, $request);
            
            $this->response->setType("S");
            $this->response->setDataSet("user", $returnUser);            
            $this->response->setMessages("Created user successfully!");
            
        }
        catch (\Exception $e)
        {
            \DB::rollBack();
            $this->response->setType("N");
            $this->response->setMessages($e->getMessage());

            return response()->json($this->response->toString());
        }

        \DB::commit();
        return response()->json($this->response->toString());
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Re  sponse
     */
    public function show($id)
    {
        $user = $this->user->find($id);

        if(!$user)
        {
            
            $this->response->setType("N");
            $this->response->setMessages("User not found!");
            return response()->json($this->response->toString());
        }
        else 
        {
            $user->addresses = $user->addresses()->get();
            $user->phones = $user->phones()->get();
            $car = $user->cars()->get();

            $objectCar = [];
            foreach ($car as $key => $value) 
            {                
                $carValue = $this->car->find($value->id);
                $supply = $carValue->supplies()->get();
                $carValue->supply = $carValue->supplies()->get();
                $objectCar[] = $carValue;
            }

            $user->cars = $objectCar;
            
            $this->response->setType("S");
            $this->response->setDataSet("user", $user);
            $this->response->setMessages("Show user successfully!");
            
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
    {    }

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
            $user = User::find($id);
            
            if(!$user) 
            {
                $this->response->setType("N");
                $this->response->setMessages("Record not found!");
                
                return response()->json($this->response->toString());
            }

            /**
             * Ainda é gambiarra, organizar isso
             */
            \DB::beginTransaction();            
            $user->fill([
                $request->all(),
                'password' => bcrypt($request->get('password')),
            ]);
            $user->save();
            
            $address = $request->get('addresses');
            $phones = $request->get('phones');

            $user->addresses()->update($address[0]);
            $user->phones()->update($phones[0]);
            /**
             * Fim da Gambiarra
             */
            
            $this->response->setType("S");
            $this->response->setDataSet("user", $user);
            $this->response->setMessages("User updated successfully !");    
            
        }
        catch (\Exception $e)
        {
            \DB::rollBack();
            $this->response->setType("N");
            $this->response->setMessages($e->getMessage());

            return response()->json($this->response->toString());
        }

        \DB::commit();
        return response()->json($this->response->toString());
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
            \DB::beginTransaction();
            $user = User::find($id);

            if(!$user) 
            {
                $this->response->setType("N");
                $this->response->setMessages("Record not found!");

                return response()->json($this->response->toString());
            }

            $car = $user->cars()->get();
            
            /**
             * Delete all supply dependencies of a user's cars
             */
            foreach($car as $key => $value) 
            {
                $cars = $this->car->find($value->id);
                $cars->supplies()->delete();
            }
            
            /**
             * Delete all dependencies of a user
             */
            $user->cars()->delete();
            $user->addresses()->delete();
            $user->phones()->delete();
            $user->delete();

            $this->response->setType("S");
            $this->response->setMessages("User and your dependencies has been deleted");

        }
        catch (\Exception $e)
        {
            \DB::rollBack();
            $this->response->setType("N");
            $this->response->setMessages($e->getMessage());

            return response()->json($this->response->toString());
        }

        \DB::commit();
        return response()->json($this->response->toString());
    }

    /**
     * 
     */
    public function getUserLogged(Request $resquest)
    {
        try
        {
            $user = $this->userService->getAuthUser($resquest);

            $this->response->setType("S");
            $this->response->setMessages("Show user successfully!");
            $this->response->setDataSet("user", $user);

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
