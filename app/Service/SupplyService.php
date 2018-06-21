<?php
namespace App\Service;

use Illuminate\Http\Request;
use App\Supply;

class CarService extends Service
{
    private $supply;

    /**
     * Construct
    */
    public function __construct() 
    {
        $this->supply = new Supply();
    }

    /**
     * Create a car
     * @param Request $request
     * @return object $returnSupply
     */
    public function createSupply(Request $request)
    {        
        try
        {
            $returnSupply = $this->supply->create([
                'liters' => $request->get('liters'),
                'amount' => $request->get('amount'),
                'type' => $request->get('type'),
                'date_supply' => $request->get('date_supply'),
                'car_id' => $request->get('car_id'),
            ]);

        }
        catch(Exception $e)
        {
            throw new Exception("Error to create a user", 0, $e);
        }

        return $returnSupply;
    }
}