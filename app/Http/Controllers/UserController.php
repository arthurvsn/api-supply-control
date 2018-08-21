<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use JWTAuth;
use JWTAuthException;
use Validator;
use Mail;
use App\User;
use App\Address;
use App\Phone;
use App\Car;
use App\Supply;
use \App\Response\Response;
use \App\Service\UserService;
use \App\Service\CloudinaryService;

class UserController extends Controller
{
    private $address;
    private $car;
    private $cloudinary;
    private $messages;
    private $phone;
    private $response;
    private $supply;
    private $user;
    private $userService;

    /**
     * construct
     */
    public function __construct()
    {
        $this->address      = new Address;
        $this->car          = new Car;
        $this->cloudinary   = new CloudinaryService();
        $this->messages     = \Config::get('messages');
        $this->phone        = new Phone;
        $this->response     = new Response();
        $this->supply       = new Supply;
        $this->user         = new User;
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
               return response()->json($this->response->toString("N", $this->messages['login']['credentials']));
           }

            $user = JWTAuth::toUser($token);

            $this->response->setDataSet("token", $token);
            $this->response->setDataSet("user", $user);

            return response()->json($this->response->toString("S", $this->messages['login']['sucess']));
        } 
        catch (JWTAuthException $e) 
        {
            return response()->json($this->response->toString("N", $e->getMessage()));
        }
    }

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $users = User::get();
        
        $this->response->setDataSet("user", $users);
        return response()->json($this->response->toString("S", $this->messages['user']['show']));
    }

    /**
     * 
     */
    public function ping(Request $request) 
    {
        $user_logged = $this->userService->getAuthUser($request);

        if(!$user_logged) 
        {
            return response()->json($this->response->toString("N", $this->messages['error']));
        } 
        else 
        {
            $this->response->setDataSet("user", $user_logged);
            return response()->json($this->response->toString("S", $this->messages['login']['logged']));
        }

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
            
            \DB::commit();
            $this->response->setDataSet("user", $returnUser);            
            return response()->json($this->response->toString("S", $this->messages['user']['create']));
        }
        catch (\Exception $e)
        {
            \DB::rollBack();
            return response()->json($this->response->toString("N", $e->getMessage()));
        }
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
            return response()->json($this->response->toString("N", $this->messages['error']));
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
            
            $this->response->setDataSet("user", $user);
            return response()->json($this->response->toString("S", $this->messages['user']['show']));
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
                return response()->json($this->response->toString("N", $this->messages['error']));
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
            
            \DB::commit();
            return response()->json($this->response->toString("S", $this->messages['user']['save']));
        }
        catch (\Exception $e)
        {
            \DB::rollBack();
            $this->response->setType("N");
            $this->response->setMessages($e->getMessage());

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
            \DB::beginTransaction();
            $user = User::find($id);

            if(!$user) 
            {
                return response()->json($this->response->toString("N", $this->messages['error']));
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

            \DB::commit();
            return response()->json($this->response->toString("S", $this->messages['user']['delete']));

        }
        catch (\Exception $e)
        {
            \DB::rollBack();
            return response()->json($this->response->toString("N", $e->getMessage()));
        }
    }

    /**
     * Get user loogged
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function getUserLogged(Request $resquest)
    {
        try
        {
            $user = $this->userService->getAuthUser($resquest);

            return response()->json($this->response->toString("S", $this->messages['login']['logged']));
        }
        catch (\Exception $e)
        {
            return response()->json($this->response->toString("N", $e->getMessage()));
        }
    }

    /**
     * Get token from email to reset password
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function getTokenResetPassword(Request $request) 
    {
        try 
        {
            $user = $this->user->where('email', $request->get('email'))->first();

            if (!$user)
            {
                return response()->json($this->response->toString("N", $this->messages['reset']['nomail']));
            }

            $userToken = JWTAuth::fromUser($user);
            
            $data = array(
                'name' => $user->name,
                'link' => "http://localhost:4200/password/reset/".$userToken
            );

            Mail::send('mail', $data, function ($message) {

                $message->from('noreplay@supplycontrol.com', 'SUPPLY CONTROL');

                $message->to('arthurvsn@gmail.com')->subject('Password Recoverys');

            });

            return response()->json($this->response->toString("N", $this->messages['reset']['sucess']));

        }
        catch (\Exception $e)
        {
            return response()->json($this->response->toString("N", $e->getMessage()));
        }
    }

    /**
     * Reset Password from token
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function resetPassword($token, Request $request)
    {
        try
        {
            //$token = JWTAuth::toUser($request->get('token')) == "" ? $token : JWTAuth::toUser($request->get('token')); //: $request->input('token');
            $userToken = JWTAuth::toUser($token);

            if (!$token)
            {
                return response()->json($this->response->toString("N", $this->messages['login']['invalid']));
            }

            $user = $this->user->find($userToken->id);
            
            $newUser = $user->fill([
                $request->all(),
                'password' => bcrypt($request->get('password')),
            ]);
            
            $user->save();
            
            //JWTAuth::setToken($token)->invalidate();
            JWTAuth::invalidate($token);

            return response()->json($this->response->toString("S", $this->messages['login']['change']));
        }
        catch (\Exception $e)
        {
            return response()->json($this->response->toString("N", $e->getMessage()));
        }
    }

    /**
     * Save a profile picture to user 
     * @param  int  $id
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function saveProfilePicture($id, Request $request)
    {
        try 
        {
            $picutre = $this->cloudinary->uploadFile($request);
            $user = $this->user->find($id);

            if (!$picutre || !$user)
            {
                return response()->json($this->response->toString("N", $this->messages['error']));
            }

            $user->fill([
                'profile_picture' => $picutre['url'],
            ]);

            $user->save();

            $this->response->setDataSet("picture", $picutre);
            return response()->json($this->response->toString("S", $this->messages['user']['picture']));
        }

        catch (\Exception $e)
        {
            return response()->json($this->response->toString("N", $e->getMessage()));
        }
    }
}
