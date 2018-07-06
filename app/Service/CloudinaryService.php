<?php
namespace App\Service;

use Illuminate\Http\Request;

class CloudinaryService extends Service
{
    /**
     * Construct
    */
    public function __construct() 
    {   }

    public function uploadFile(Request $request)
    {
        try 
        {
            if($request->hasFile('profile_picture'))
            {
                \Cloudder::upload($request->file('profile_picture'));
                $result = \Cloudder::getResult();
                
                return $result;
            }
        }
        catch(Exception $e)
        {
            throw new Exception("Error to save a profile picture in Cloudinary", 0, $e);
        }
    }
}