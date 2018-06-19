<?php
namespace App\Service;

use Illuminate\Http\Request;
use App\User;
use App\Address;
use App\Phone;

class UserService extends Service
{
    private $user;
    private $address;
    private $phone;

    /**
     * Construct
     */
    public function __construct() 
    {
        $this->user     = new User();
        $this->address  = new Address();
        $this->phone    = new Phone();
    }

    /**
     * Create a user \App\User
     * @param  \Illuminate\Http\Request  $request
     * @return object $user or false
     */
    public function createUser(Request $request)
    {  
        try
        {
            $returnUser = $this->user->create([
                'name'              => $request->get('name'),
                'username'          => $request->get('username'),
                'profile_picture'   => $request->get('profile_picture'),
                'email'             => $request->get('email'),
                'password'          => bcrypt($request->get('password')),
                'user_type'         => $request->get('user_type'),
            ]);
        }
        catch(Exception $e)
        {
            throw new Exception("Error to create a user", 0, $e);
        }

        return $returnUser;
    }

    /**
     * Create address to user
     * @param int $userId
     * @param  \Illuminate\Http\Request  $request
     * @return object
     */
    public function createAddressUser($userId, Request $request) 
    {
        $returnAddressUser = [];
        try 
        {
            foreach ($request->get('addresses') as $key => $value)
            {
                $returnAddressUser[] = $this->address->create([
                    'street'    => $value['street'],
                    'city'      => $value['city'],
                    'state'     => $value['state'],
                    'zip_code'  => $value['zip_code'],
                    'country'   => $value['country'],
                    'user_id'   => $userId,
                ]);
            }
        }
        catch(Exception $e)
        {
            throw new Exception("Error to create a address", 0, $e);
        }

        return $returnAddressUser;
    }

    /**
     * Create phone to user
     * @param int $userId
     * @param  \Illuminate\Http\Request  $request
     * @return object
     */
    public function createPhoneUser($userId, Request $request)
    {
        $returnPhoneUser = [];
        try 
        {
            foreach ($request->get('phones') as $key => $value)
            {
                $returnPhoneUser[] = $this->phone->create([
                    'country_code'  => $value['country_code'],
                    'number'        => $value['number'],
                    'user_id'       => $userId,
                ]);
            }
        }
        catch(Exception $e)
        {
            throw new Exception("Error to create a phone", 0, $e);
        }

        return $returnPhoneUser;
    }
}
?>