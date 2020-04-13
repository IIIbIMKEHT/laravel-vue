<?php

namespace App\Http\Controllers\API;

use App\Category;
use App\Http\Controllers\Controller;
use App\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Gate;

class CategoryController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function __construct()
    {
        $this->middleware('auth:api');
    }

    public function index()
    {
        if (Gate::allows('isAdmin') || Gate::allows('isModer')){
            return Category::latest()->paginate(3);
        }
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        $this->validate($request, [
            'title' => 'required|string|max:191',
        ], [
            'required' => 'Поле :attribute обязательно!',
            'max' => 'Наименование не может быть более 191 символов',
        ], [
            'title' => 'наименование',
        ]);

        return Category::create([
           'title' => $request['title']
        ]);
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
        $user = Category::findOrFail($id);
        $this->validate($request, [
            'title' => 'required|string|max:191',
        ], [
            'required' => 'Поле :attribute обязательно!',
            'max' => 'Наименование не может быть более 191 символов',
        ], [
            'title' => 'наименование',
        ]);

        $user->update($request->all());
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        $category = Category::findOrFail($id);
        $category->products()->delete();
        $category->delete();
    }

    public function search(Request $request){

        if ($search = $request->get('q')) {
            $categories = Category::where(function($query) use ($search){
                $query->where('title','LIKE',"%$search%");
            })->paginate(10);
        }else{
            $categories = Category::latest()->paginate(3);
        }

        return $categories;
    }
}
