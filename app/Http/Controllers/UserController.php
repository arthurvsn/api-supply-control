<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\User;
use App\Address;
use App\Phone;
use JWTAuth;
use JWTAuthException;
use \App\Response\Response;
use \App\Service\UserService;

class UserController extends Controller
{
    private $user;
    private $address;
    private $phone;
    private $response;
    private $userService;

    public function __construct(User $user) 
    {
        $this->user         = new User;
        $this->address      = new Address;
        $this->phone        = new Phone;
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
            return response()->json($this->response->toString(), 500);
        }
        
        $user = JWTAuth::toUser($token);
        
        $this->response->setType("S");
        $this->response->setMessages("Login successfully!");
        $this->response->setDataSet("token", $token);
        
        $this->response->setDataSet("user", $user);
        return response()->json($this->response->toString(), 200);
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
     *
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

            return response()->json($this->response->toString(), 500);
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
        $address = $this->address->getAddressUser($id);
        $phone = $this->phone->getPhoneUser($id);

        if(!$user)
        {
            $this->response->setType("N");
            $this->response->setMessages("User not found!");
            return response()->json($this->response->toString(), 404);
        }
        else 
        {
            $user->addresses = $address;
            $user->phones = $phone;
            
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
    
                return response()->json($this->response->toString(), 404);
            }
    
            $user->fill([
                $request->all(),
                'password' => bcrypt($request->get('password')),
            ]);
            $user->save();
            $this->response->setType("S");
            $this->response->setDataSet("user", $user);
            $this->response->setMessages("User updated successfully !");
    
            return response()->json($this->response->toString());
        }
        catch (\Exception $e)
        {
            $this->response->setType("N");
            $this->response->setMessages($e->getMessage());

            return response()->json($this->response->toString(), 500);
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
            $user = User::find($id);

            if(!$user) 
            {
                $this->response->setType("N");
                $this->response->setMessages("Record not found!");

                return response()->json($this->response->toString(), 404);
            }

            $user->delete();

        }
        catch (\Exception $e)
        {
            $this->response->setType("N");
            $this->response->setMessages($e->getMessage());

            return response()->json($this->response->toString(), 500);
        }
        
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

            return response()->json($this->response->toString(), 500);
        }
        
        return response()->json($this->response->toString());
    }
}
