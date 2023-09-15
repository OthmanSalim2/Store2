<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Resources\ProductResource;
use App\Models\Product;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Response;

class ProductsController extends Controller
{

    public function __construct()
    {
        $this->middleware('auth:sanctum')->except('index', 'show');
    }

    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        // $request->all() this's will return all values in body.
        // $request->query() this will return all parameters from request.
        $products = Product::filter($request->query())
            ->with('store:id,name', 'tags:id,name', 'category:id,name')
            ->paginate();

        // this way for multiple product.
        return  ProductResource::collection($products);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'description' => ['nullable', 'string', 'max:255'],
            'category_id' => ['required', 'exists:categories,id'],
            'status' => ['in:active, active'],
            'price' => ['required', 'numeric', 'min:0'],
            'compare_price' => ['nullable', 'numeric', 'min:0'],
        ]);

        $user = request()->user();

        if (!$user->tokenCan('products.create')) {
            abort(403, 'Not Allowed');
        }

        $product = Product::create($request->all());

        return Response::json($product, 201, [
            // Location here it represent extra header in request.
            'Location' => route('products.show', $product->id),
        ]);
    }

    /**
     * Display the specified resource.
     */
    public function show(Product $product)
    {
        // this way for single product.
        return new ProductResource($product);

        // load this for display the extra data from relation.
        // return $product->load('category:id,name', 'store:id,name', 'tags:id,name');
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Product $product)
    {
        $request->validate([
            // sometimes this mean if was returned in request value it will be required but if not returned won't returned.
            'name' => 'sometimes|required|string|max:255',
            'description' => 'nullable|string|max:255',
            'category_id' => 'sometimes|required|exists:categories,id',
            'status' => 'in:active,inactive',
            'price' => 'sometimes|required|numeric|min:0',
            'compare_price' => 'nullable|numeric|gt:price',
        ]);

        $user = $request->user();

        if (!$user->tokenCan('products.update')) {
            abort(403, 'Not Allowed');
        }

        $product->update($request->all());

        return Response::json($product);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy($id)
    {

        $user = Auth::guard('sanctum')->user();

        if (!$user->tokenCan('products.delete')) {
            return Response::json([
                'message' => 'Not Allowed',
            ], 403);
        }

        // this's way to delete product when using api.
        Product::destroy($id);

        return [
            'message' => 'Product deleted successfully',
        ];
    }
}
