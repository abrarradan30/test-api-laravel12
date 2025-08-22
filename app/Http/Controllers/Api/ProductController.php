<?php

namespace App\Http\Controllers\Api;

// import model
use App\Models\Product;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

// resource API
use App\Http\Resources\ProductResource;

// import facade validator
use Illuminate\Support\Facades\Validator;

// import facade Storage
use Illuminate\Support\Facades\Storage;

class ProductController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        // get data product
        $products = Product::latest()->paginate(5);

        return new ProductResource(true, 'List Data Products', $products);
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'image' => 'required|image|mimes:jpeg,png,jpg,gif,svg|max:2048',
            'title' => 'required',
            'description' => 'required',
            'price' => 'required|numeric',
            'stock' => 'required|numeric',
        ]);

        // if validation fails
        if($validator->fails()) {
            return response()->json($validator->errors(), 422);
        }

        $image = $request->file('image');
        $image->storeAs('products', $image->hashName());

        // create data
        $product = Product::create([
            'image' => $image->hashName(),
            'title' => $request->title,
            'description' => $request->description,
            'price' => $request->price,
            'stock' => $request->stock,
        ]);

        return new ProductResource(true, 'Data Product Berhasil Ditambahkan!', $product);
    }

    /**
     * Display the specified resource.
     */
    public function show($id)
    {
        // find product by ID
        $product = Product::find($id);

        return new ProductResource(true, 'Detail Data Product!', $product);
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(string $id)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, $id)
    {
        // validation rules
        $validator = Validator::make($request->all(), [
            'image' => 'required|image|mimes:jpeg,png,jpg,gif,svg|max:2048',
            'title' => 'required',
            'description' => 'required',
            'price' => 'required|numeric',
            'stock' => 'required|numeric',
        ]);

        // if validation fails
        if ($validator->fails()) {
            return response()->json($validator->errors(), 422);
        }

        // find product by ID
        $product = Product::find($id);

        // check if image is not empty
        if ($request->hasFile('image')) {

            // delete old image
            Storage::delete('products/' . basename($product->image));

            // upload image
            $image = $request->file('image');
            $image->storeAs('products', $image->hashName());

            $product->update([
                'image' => $image->hashName(),
                'title' => $request->title,
                'description' => $request->description,
                'price' => $request->price,
                'stock' => $request->stock,
            ]);
        } else {
            $product->update([
                'title' => $request->title,
                'description' => $request->description,
                'price' => $request->price,
                'stock' => $request->stock,
            ]);
        }

        return new ProductResource(true, 'Data Product Berhasil Diubah!', $product);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy($id)
    {
        $product = Product::find($id);

        // delete image
        Storage::delete('product/'.basename($product->image));

        $product->delete();

        return new ProductResource(true, 'Data Product Berhasi Dihapus', null);
    }
}
