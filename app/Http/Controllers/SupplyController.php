<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

use App\Supply;
use App\TypeFuel;
use App\Service\SupplyService;
use App\Response\Response;

class SupplyController extends Controller
{
    private $messages;
    private $supply;
    private $supplyService;
    private $typeFuel;
    
    /**
     * Construct
     */
    public function __construct()
    {
        $this->supply         = new Supply();
        $this->supplyService  = new SupplyService();
        $this->typeFuel       = new TypeFuel();
        $this->response       = new Response();
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
            $supplies = $this->supplyService->createSupply($request);

            $this->response->setDataSet('supply', $supplies);
            return response()->json($this->response->toString("S", config('supply.sucess')));
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
    {   }

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
    {   }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {   }

    /**
     * search and sum values expenses between dates of params
     * @param date $dateStart
     * @param date $dateEnd
     * @param int $carID
     * @return \Illuminate\Http\Response
     */
    public function expensesMounth($dateStart, $dateEnd, $carID)
    {
        try 
        {
            $valueExpense = $this->supply->getExpenses($dateStart, $dateEnd, $carID);

            $this->response->setDataSet("supply", $valueExpense);
            return response()->json($this->response->toString("S", config('supply.search')));
    
        }
        catch (\Exception $e)
        {
            return response()->json($this->response->toString("N", $e->getMessage()));
        }
    }

    /**
     * Get type fuels on database
     * @return \Illuminate\Http\Response
     */
    public function getTypeFuel()
    {
        try
        {
            $typeFuels = $this->typeFuel->get();

            $this->response->setDataSet("typeFuels", $typeFuels);
            return response()->json($this->response->toString("S", config('supply.search')));

        }
        catch (\Exception $e)
        {
            return response()->json($this->response->toString("N", $e->getMessage()));
        }
    }
}
