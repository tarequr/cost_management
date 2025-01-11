<?php

namespace App\Http\Controllers\Backend;

use Illuminate\Http\Request;
use App\Models\BudgetEstimate;
use Illuminate\Support\Facades\Log;
use App\Http\Controllers\Controller;

class BudgetEstimateController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $budgetEstimates = BudgetEstimate::where('user_id', auth()->user()->id)->orderBy('id', 'desc')->get();
        return view('backend.budget_estimate.index', compact('budgetEstimates'));
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        return view('backend.budget_estimate.create');
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        // $request->validate([
        //     'name' => 'required|unique:sections,name'
        // ]);

        try {
            BudgetEstimate::create([
                'user_id' => auth()->user()->id,
                'project_name' => $request->project_name,
                'client_name' => $request->client_name,
                'start_date' => $request->start_date,
                'end_date' => $request->end_date,
            ]);

            notify()->success('Section Created Successfully', 'Success');
            return redirect()->route('budget-estimate.index');
        } catch (\Throwable $th) {
            Log::error($th->getMessage());
            notify()->error('Section Create Failed', 'Error');
            return back();
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
