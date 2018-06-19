<?php
namespace App\Service;

use Illuminate\Http\Request;
use App\Car;

class CarService extends Service
{
    private $car;

    /**
     * Construct
    */
    public function __construct() 
    {
        $this->car = new Car();
    }

    /**
     * Create a car
     * @param int $userId
     * @param Request $request
     * @return object $returnCar
     */
    public function createCar($userId, Request $request)
    {  
        try
        {
            $returnCar = $this->car->create([
                'board'             => $request->get('board'),
                'model'             => $request->get('model'),
                'manufacturer'      => $request->get('manufacturer'),
                'color'             => $request->get('color'),
                'year_manufacture'  => $request->get('year_manufacture'),
                'capacity'          => $request->get('capacity'),
                'user_id'           => $userId
            ]);
        }
        catch(Exception $e)
        {
            throw new Exception("Error to create a user", 0, $e);
        }

        return $returnCar;
    }
}