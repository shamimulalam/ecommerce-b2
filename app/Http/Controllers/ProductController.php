<?php

namespace App\Http\Controllers;

use App\Brand;
use App\Category;
use App\Product;
use Illuminate\Http\Request;

class ProductController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {
        $data['title'] = 'Product List';
        $product = new Product();
        $product = $product->withTrashed();
        if($request->has('search') && $request->search != null){
            $product = $product->where('name','like','%'.$request->search.'%');
        }
        if($request->has('status') && $request->status != null){
            $product = $product->where('status',$request->status);
        }
        $product = $product->orderBy('id','DESC')->paginate(3);
        $data['products'] = $product;

        if (isset($request->status) || $request->search) {
            $render['status'] = $request->status;
            $render['search'] = $request->search;
            $product = $product->appends($render);
        }

        $data['serial'] = managePagination($product);
        return view('admin.product.index',$data);
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        $data['title'] = 'Create new product';
        $data['categories'] = Category::orderBy('name')->pluck('name','id');
        $data['brands'] = Brand::orderBy('name')->pluck('name','id');
        return view('admin.product.create',$data);
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
            'name'=>'required',
            'category_id'=>'required',
            'brand_id'=>'required',
            'price'=>'required|numeric',
            'stock'=>'required|numeric',
            'status'=>'required',
        ]);
        $product = $request->except('_token');
        $product['created_by'] = 1;
        Product::create($product);
        session()->flash('message','Product created successfully');
        return redirect()->route('product.index');
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\Product  $product
     * @return \Illuminate\Http\Response
     */
    public function show(Product $product)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\Product  $product
     * @return \Illuminate\Http\Response
     */
    public function edit(Product $product)
    {
        $data['title'] = 'Edit product';
        $data['product'] = $product;
        $data['categories'] = Category::orderBy('name')->pluck('name','id');
        $data['brands'] = Brand::orderBy('name')->pluck('name','id');
        return view('admin.product.edit',$data);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Product  $product
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, Product $product)
    {
        $request->validate([
            'name'=>'required',
            'category_id'=>'required',
            'brand_id'=>'required',
            'price'=>'required|numeric',
            'stock'=>'required|numeric',
            'status'=>'required',
        ]);
        $product_data = $request->except('_token');
        $product_data['updated_by'] = 1;
        $product->update($product_data);
        session()->flash('message','Product updated successfully');
        return redirect()->route('product.index');
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Product  $product
     * @return \Illuminate\Http\Response
     */
    public function destroy(Product $product)
    {
        //
    }
}