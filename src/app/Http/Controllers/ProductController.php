<?php

namespace App\Http\Controllers;


use App\Models\Product;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class ProductController extends Controller
{
    //
    public function addProduct(Request $request)
    {
        // Validate the incoming request data
        $validator = Validator::make($request->all(), [
            'name' => 'required|string|min:10|max:100',
            'price' => 'required|numeric',
        ]);

        if ($validator->fails()) {
            return response()->json($validator->errors(), 422);
        }

        // Create a new product instance
        Product::create([
            'name' => $request->input('name'),
            'price' => $request->input('price'),
        ]);

        // Return a success response
        return response()->json(['message' => 'Product added successfully'], 201);
    }

    public function getProducts()
    {
        // Retrieve all products from the database
        $products = Product::all();

        // Return the products as a JSON response
        return $products->isEmpty()
            ? response()->json(['message' => 'No products found'], 404)
            :
            response()->json($products,200);
    }

    public function getProduct($id)
    {
        // Find the product by ID
        $product = Product::find($id);
        // Return the product as a JSON response
        return !$product ? response()->json(['message' => 'Product not found'], 404) : response()->json($product,200);
    }

    /**
     * Undocumented function
     *
     * @param Request $request
     * @param [type] $id
     * @return void
     */
    public function updateProduct(Request $request, $id)
    {
        // Find the product by ID
        $product = Product::find($id);

        // Check if the product exists
        if (!$product) {
            return response()->json(['message' => 'Product not found'], 404);
        }

        // Validate the incoming request data
        $validator = Validator::make($request->all(), [
            'name' => 'sometimes|string|min:10|max:100',
            'price' => 'sometimes|numeric',
        ]);

        if ($validator->fails()) {
            return response()->json($validator->errors(), 422);
        }

        // Update the product with the new data
        $product->update($request->all());

        // Return a success response
        return response()->json(['message' => 'Product updated successfully'], 200);
    }

    /**
     * Undocumented function
     *
     * @param [type] $id
     * @return void
     */
    public function deleteProduct($id)
    {
        // Find the product by ID
        $product = Product::find($id);

        // Check if the product exists
        if (!$product) {
            return response()->json(['message' => 'Product not found'], 404);
        }

        // Delete the product
        $product->delete();

        // Return a success response
        return response()->json(['message' => 'Product deleted successfully'], 200);
    }


}
