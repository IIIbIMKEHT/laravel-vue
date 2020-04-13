<?php

namespace App\Http\Controllers\API;

use App\Category;
use App\Http\Controllers\Controller;
use App\Product;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Gate;

class ProductController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        if (Gate::allows('isAdmin') || Gate::allows('isModer')){
            $products = Product::with('category')->paginate(3);
            return response()->json($products);
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
            'category_id' => 'required',
            'description' => 'required',
            'price' => 'required|numeric'
        ], [
            'required' => 'Поле :attribute обязательно!',
            'max' => 'Наименование не может быть более 191 символов',
            'numeric' => 'Только цифры!'
        ], [
            'title' => 'наименование',
            'category_id' => 'категория',
            'description' => 'описание',
            'price' => 'цена'
        ]);

        Product::updateImg($request, null);
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
        $this->validate($request, [
            'title' => 'required|string|max:191',
            'category_id' => 'required',
            'description' => 'required',
            'price' => 'required'
        ], [
            'required' => 'Поле :attribute обязательно!',
            'max' => 'Наименование не может быть более 191 символов',
        ], [
            'title' => 'наименование',
            'category_id' => 'категория',
            'description' => 'описание',
            'price' => 'цена'
        ]);
        $product = Product::findOrFail($id);
        Product::updateImg($request, $product);
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        $product = Product::findOrFail($id);
        $userPhoto = public_path('img/products/') . $product->img;
        if (file_exists($userPhoto)) {
            @unlink($userPhoto);
        }
        $product->delete();
    }
}
