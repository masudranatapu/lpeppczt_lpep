<?php

namespace App\Http\Controllers\App;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;

class BioEnergyController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        return view('app-bioenergy.index');
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create(Request $request)
    {
        $cmId = $request->get('cm_id');

        $agent = Auth::user()->id;
        $customers = AppCustomer::where('agent_id', $agent)->get();
        $agents = User::whereHas('userPermission', function ($q) {
            $q->where('role_id', ROLE_AGENT);
        })
            ->where('status', 1)
            ->get();


        return view('visit-info.create', compact('customers', 'cmId'));



    }

    

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
         $bioenergy = new BioEnergy();

        $bioenergy->visit_date = $request->visit_date;
        $bioenergy->client_name=$request->client_name;
        $bioenergy->client_number=$request->client_number;
        $bioenergy->district=$request->district;
        $bioenergy->upazila=$request->upazila;
        $bioenergy->union=$request->union;
        $bioenergy->village=$request->village;
        $bioenergy->livestockdetails=$request->livestockdetails;
        $bioenergy->size=$request->size;
        $bioenergy->start_date=$request->start_date;
        $bioenergy->end_date=$request->end_date;
        $bioenergy->organizer_name=$request->organizer_name;
        $bioenergy->condition=$request->condition;

    

        if ($bioenergy->save()) {
            return back()->with('message', "Bioenergy  Added Successfully");
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
        //
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        //
    }
}
