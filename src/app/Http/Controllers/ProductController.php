<?php

namespace App\Http\Controllers;


use App\Models\Product;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

/**
 * ProductController handles CRUD operations for products. It allows authenticated users with the 'admin' role to add, retrieve, update, and delete products from the database. Each method in this controller is protected by authentication and role-based access control to ensure that only authorized users can perform these actions. The endpoints for managing products return appropriate JSON responses based on the success or failure of the operations.
 * @group 3. Product Management
 */
class ProductController extends Controller
{
    /**
     * Add a new product to the database.
     * 
     * Solo los usuarios con el rol 'admin' pueden acceder a este endpoint.
     * Este endpoint permite a los usuarios con el rol 'admin' agregar un nuevo producto a la base de datos. El producto debe tener un nombre que sea una cadena de texto entre 10 y 100 caracteres, y un precio que sea un valor numérico. Si el producto se agrega correctamente, se devuelve un mensaje de éxito. Si la validación falla, se devuelve un mensaje de error con los detalles de la validación.
     * 
     * @authenticated
     * 
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     * 
     * @bodyParam name string required The name of the product. Example: Laptop
     * @bodyParam price numeric required The price of the product. Example: 999.99
     * @response 201 {
     *   "message": "Product added successfully"
     * }
     * @response 422 {
     *   "message": "Validation error",
     *   "errors": {            
     *    "name": [
     *     "The name field is required."
     *   ],
     *   "price": [
     *     "The price field is required."
     *   ]
     * }     * }
     */
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

    /**
     * Retrieve all products from the database.
     * 
     * Only users with the 'Admin' role can access this endpoint.
     * Permission: Only users with the 'Admin' role can access this endpoint. --- IGNORE ---
     * This endpoint retrieves all products from the database and returns them as a JSON response. If no products are found, it returns a 404 response with a message indicating that no products were found.
     * 
     * @authenticated
     * 
     * @response 200 [
     *   {
     *     "id": 1,
     *     "name": "Laptop",
     *     "price": 999.99,
     *     "created_at": "2024-06-01T12:00:00Z",
     *     "updated_at": "2024-06-01T12:00:00Z"
     *   }
     * ]
     * @response 404 {
     *   "message": "No products found"
     * }
     * 
     * @return \Illuminate\Http\JsonResponse
     */
    public function getProducts()
    {
        // Retrieve all products from the database
        $products = Product::all();

        // Return the products as a JSON response
        return $products->isEmpty()
            ? response()->json(['message' => 'No products found'], 404)
            :
            response()->json($products, 200);
    }

    /**
     * Retrieve a specific product by ID.
     * 
     * Permission: Only users with the 'Admin' role can access this endpoint.
     * This endpoint retrieves a specific product from the database based on the provided ID. If the product is found, it returns the product details as a JSON response. If the product is not found, it returns a 404 response with a message indicating that the product was not found.
     * 
     * @authenticated
     *
     * @response 200 {
     *   "id": 1,
     *   "name": "Laptop",
     *   "price": 999.99,
     *   "created_at": "2024-06-01T12:00:00Z",
     *   "updated_at": "2024-06-01T12:00:00Z"
     * }
     * @response 404 {
     *   "message": "Product not found"
     * }
     */
    public function getProduct(int $id)
    {
        // Find the product by ID
        $product = Product::find($id);
        // Return the product as a JSON response
        return !$product ? response()->json(['message' => 'Product not found'], 404) : response()->json($product, 200);
    }

    /**
     * Update a specific product by ID.
     * 
     * Permission: Only users with the 'Admin' role can access this endpoint.
     * This endpoint updates a specific product in the database based on the provided ID and request data. It validates the incoming request data to ensure that the 'name' field is a string between 10 and 100 characters, and that the 'price' field is a numeric value. If the product is found and the validation passes, it updates the product with the new data and returns a success message. If the product is not found, it returns a 404 response with a message indicating that the product was not found. If the validation fails, it returns a 422 response with the validation errors.
     * 
     * @authenticated
     * 
     * @param Request $request
     * @param int $id
     * @return \Illuminate\Http\JsonResponse
     * @urlParam id integer required The ID of the product. Example: 1
     * @bodyParam name string The name of the product. Example: Laptop
     * @bodyParam price numeric The price of the product. Example: 999.99
     * @response 200 {
     *   "message": "Product updated successfully"
     * }
     * @response 404 {
     *   "message": "Product not found"
     * }
     * @response 422 {
     *   "message": "Validation error",
     *   "errors": {            
     *    "name": [
     *     "The name field must be at least 10 characters."
     *   ],
     *   "price": [
     *     "The price field must be a number."
     *   ]
     * }     * }
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
     * Delete a specific product by ID.
     * 
     * Only users with the 'Admin' role can access this endpoint.
     * This endpoint deletes a specific product from the database based on the provided ID. If the product is found, it deletes the product and returns a success message. If the product is not found, it returns a 404 response with a message indicating that the product was not found.
     * 
     * @authenticated
     * 
     * @param int $id
     * @return \Illuminate\Http\JsonResponse
     * 
     * @urlParam id integer required The ID of the product. Example: 1
     * @response 200 {
     *   "message": "Product deleted successfully"
     * }
     * @response 404 {
     *   "message": "Product not found"
     * }
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
