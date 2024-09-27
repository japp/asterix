<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

use App\Models\Target;

class TargetController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        return view('targets.index');
    }
     
    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        return view('targets.create');
    }
    
    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        $request->validate([
            'name' => ['required', 'max:64'],
        ]);

        $request->merge([
            'user_id' => auth()->id(),
        ]);

        Target::create($request->all());

     
        return redirect()->route('targets.index')
                        ->with('success','Target has been created successfully.');
    }
     
    /**
     * Display the specified resource.
     *
     * @param  \App\Target  $target
     * @return \Illuminate\Http\Response
     */
    public function show(Target $target)
    {
        return view('targets.show',compact('target'));
    } 
     
    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\Target  $target
     * @return \Illuminate\Http\Response
     */
    public function edit(Target $target)
    {
        return view('targets.edit', compact('target'));
    }
    
    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Target  $target
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, Target $target)
    {
        $request->validate([
            'name' => ['required', 'max:64'],
        ]);
        
        $target->update($request->only('name', 'radeg', 'decdeg','vmag'));

        return redirect()->route('targets.index')
                        ->with('success','Target has been updated');
    }
    
    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Target  $target
     * @return \Illuminate\Http\Response
     */
    public function destroy(Target $target)
    {
        $target->delete();
    
        return redirect()->route('targets.index')
                        ->with('success','Target has been deleted successfully');
    }
}
