<?php

namespace App\Http\Controllers;

use App\Http\Requests\ProductRequest;
use App\Http\Resources\ProductResource;
use App\Models\Product;
use App\Services\FileUploadService;
use Illuminate\Http\JsonResponse;
use OpenApi\Annotations as OA;
use Symfony\Component\HttpFoundation\Response;

/**
 * @OA\Info(title="Product API", version="1.0")
 *
 * ProductController handles all the actions related to product listings.
 */
class ProductController extends Controller
{
    /**
     * @OA\Post(
     *     path="/api/v1/products",
     *     summary="Create a new product",
     *     description="Creates a new product listing",
     *     tags={"Products"},
     *     @OA\RequestBody(
     *         required=true,
     *         @OA\JsonContent(
     *             required={"name", "description", "price", "category"},
     *             @OA\Property(property="name", type="string", example="Product 1"),
     *             @OA\Property(property="description", type="string", example="Description of the product"),
     *             @OA\Property(property="price", type="number", format="float", example="29.99"),
     *             @OA\Property(property="category", type="string", example="Electronics"),
     *             @OA\Property(property="image", type="string", format="binary")
     *         )
     *     ),
     *     @OA\Response(
     *         response=201,
     *         description="Product created successfully",
     *         @OA\JsonContent(
     *             @OA\Property(property="id", type="integer", example=1),
     *             @OA\Property(property="name", type="string", example="Product 1"),
     *             @OA\Property(property="description", type="string", example="Description of the product"),
     *             @OA\Property(property="price", type="number", format="float", example="29.99"),
     *             @OA\Property(property="category", type="string", example="Electronics"),
     *             @OA\Property(property="image", type="string", example="/storage/products/product1.jpg")
     *         )
     *     ),
     *     @OA\Response(
     *         response=422,
     *         description="Invalid input",
     *         @OA\JsonContent(
     *             @OA\Property(property="message", type="string", example="Validation failed")
     *         )
     *     )
     * )
     */
    public function store(ProductRequest $request, FileUploadService $uploadService): JsonResponse
    {
        $requestedData = $request->validated();
        if ($request->hasFile('image')) {
            $requestedData['image_url'] = $uploadService->upload($request->file('image'), 'products');;
        }

        $product = Product::query()->create($requestedData);

        return response()->json(
            new ProductResource($product),
            Response::HTTP_CREATED
        );
    }


    /**
     * @OA\Get(
     *     path="/api/v1/products/{id}",
     *     summary="Get a product by ID",
     *     description="Retrieve a product listing by its ID",
     *     tags={"Products"},
     *     @OA\Parameter(
     *         name="id",
     *         in="path",
     *         required=true,
     *         @OA\Schema(type="integer")
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Product found",
     *         @OA\JsonContent(
     *             @OA\Property(property="id", type="integer", example=1),
     *             @OA\Property(property="name", type="string", example="Product 1"),
     *             @OA\Property(property="description", type="string", example="Description of the product"),
     *             @OA\Property(property="price", type="number", format="float", example="29.99"),
     *             @OA\Property(property="category", type="string", example="Electronics"),
     *             @OA\Property(property="image", type="string", example="/storage/products/product1.jpg")
     *         )
     *     ),
     *     @OA\Response(
     *         response=404,
     *         description="Product not found",
     *         @OA\JsonContent(
     *             @OA\Property(property="message", type="string", example="Product not found")
     *         )
     *     )
     * )
     */
    public function show(Product $product): JsonResponse
    {
        return response()->json(
            new ProductResource($product),
            Response::HTTP_OK
        );
    }

    /**
     * @OA\Put(
     *     path="/api/v1/products/{id}",
     *     summary="Update a product",
     *     description="Update an existing product listing by its ID",
     *     tags={"Products"},
     *     @OA\Parameter(
     *         name="id",
     *         in="path",
     *         required=true,
     *         @OA\Schema(type="integer")
     *     ),
     *     @OA\RequestBody(
     *         required=true,
     *         @OA\JsonContent(
     *             required={"name", "description", "price", "category"},
     *             @OA\Property(property="name", type="string", example="Updated Product"),
     *             @OA\Property(property="description", type="string", example="Updated description of the product"),
     *             @OA\Property(property="price", type="number", format="float", example="39.99"),
     *             @OA\Property(property="category", type="string", example="Home Appliances"),
     *             @OA\Property(property="image", type="string", format="binary", description="Product image (optional, can be null)")
     *         )
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Product updated successfully",
     *         @OA\JsonContent(
     *             @OA\Property(property="id", type="integer", example=1),
     *             @OA\Property(property="name", type="string", example="Updated Product"),
     *             @OA\Property(property="description", type="string", example="Updated description of the product"),
     *             @OA\Property(property="price", type="number", format="float", example="39.99"),
     *             @OA\Property(property="category", type="string", example="Home Appliances"),
     *             @OA\Property(property="image", type="string", example="/storage/products/product_updated.jpg")
     *         )
     *     ),
     *     @OA\Response(
     *         response=422,
     *         description="Invalid input data",
     *         @OA\JsonContent(
     *             @OA\Property(property="message", type="string", example="Validation failed")
     *         )
     *     ),
     *     @OA\Response(
     *         response=404,
     *         description="Product not found",
     *         @OA\JsonContent(
     *             @OA\Property(property="message", type="string", example="Product not found")
     *         )
     *     )
     * )
     */
    public function update(ProductRequest $request, Product $product, FileUploadService $uploadService): JsonResponse
    {
        $requestedData = $request->validated();
        if ($request->hasFile('image')) {
            $uploadService->delete($product->image_url);
            $requestedData['image_url'] = $uploadService->upload($request->file('image'), 'products');;
        } else if ($request->get('image') == null) {
            $requestedData['image_url'] = null;
            $uploadService->delete($product->image_url);
        }

        $product->update($requestedData);

        return response()->json(
            new ProductResource($product),
            Response::HTTP_OK
        );
    }

    /**
     * @OA\Delete(
     *     path="/api/v1/products/{id}",
     *     summary="Delete a product",
     *     description="Delete a product listing by its ID",
     *     tags={"Products"},
     *     @OA\Parameter(
     *         name="id",
     *         in="path",
     *         required=true,
     *         @OA\Schema(type="integer")
     *     ),
     *     @OA\Response(
     *         response=204,
     *         description="Product deleted successfully"
     *     ),
     *     @OA\Response(
     *         response=404,
     *         description="Product not found",
     *         @OA\JsonContent(
     *             @OA\Property(property="message", type="string", example="Product not found")
     *         )
     *     )
     * )
     */
    public function destroy(Product $product, FileUploadService $uploadService): JsonResponse
    {
        $uploadService->delete($product->image_url);
        $product->delete();

        return response()->json([
            'message' => 'Product deleted successfully'
        ], Response::HTTP_NO_CONTENT);
    }
}

