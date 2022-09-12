<?php

namespace App\Http\Controllers;

use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use App\Repositories\Contracts\Interface\ProductRepositoryInterface;
use App\Repositories\Contracts\Interface\CategoryRepositoryInterface;
use Validator;

class ProductController extends Controller {
    /**
     * @var productRepository
     */
    protected $productRepository;

     /**
     * @var categoryRepository
     */
    protected $categoryRepository;

    /**
     * @param ProductRepository $productRepository
     */
    public function __construct(
        ProductRepositoryInterface $productRepository,
        CategoryRepositoryInterface $categoryRepository
        )
    {
        $this->productRepository = $productRepository;
        $this->categoryRepository = $categoryRepository;
    }

    /**
     * @param Request $request
     * 
     * @return JsonResponse
     */
    public function index(Request $request) : JsonResponse
    {
        try {
            if ($request->query('id')) {
                if ($product = $this->productRepository->find($request->query('id'))) {
                    return response()->json([
                        'error' => 0,
                        'message' => 'ok',
                        'product' => $product
                    ], Response::HTTP_CREATED);
                } else {
                    return $this->errorResponse('Resource not found');
                }
            }

            $products = $this->productRepository->getAll();

            return response()->json([
                'error' => 0,
                'message' => 'ok',
                'products' => $products
            ], Response::HTTP_CREATED);
        } catch (\Throwable $th) {
            return $this->catchErrorResponse();
        }
    }

    /**
     * @param Request $request
     * 
     * @return JsonResponse
     */
    public function create(Request $request) : JsonResponse
    {
        try {
            $category_id = $request->category_id;

            if (! $this->categoryRepository->find($category_id)) {
                return $this->errorResponse('Category not found', 422);
            }

            $validator = Validator::make($request->all(), [
                'name' => 'required|string|max:1000',
                'description' => 'required|string',
                'category_id' => 'required|integer',
                'price' => 'required',
                'image' => 'string',
                'status' => 'string'
            ]);

            if ($validator->fails()) {
                return $this->exceptionResponse($validator->errors(), 422);
            }

            if (! $this->productRepository->create($validator->validated())) {
                return $this->errorResponse('Failed to create product');
            }

            return $this->successReponse('Ok', Response::HTTP_CREATED);
        } catch (\Throwable $th) {
            return $this->catchErrorResponse();
        }
    }

    /**
     * @param Request $request
     * 
     * @return JsonResponse
     */
    public function update(Request $request) : JsonResponse
    {
        try {
            $product_id = $request->query('id');
            $validator = Validator::make($request->all(), [
                'name' => 'required|string|max:1000',
                'description' => 'required|string'
            ]);

            if ($validator->fails()) {
                return $this->exceptionResponse($validator->errors(), 422);
            }

            if (! $this->productRepository->update($product_id, $validator->validated())) {
                return $this->errorResponse('Failed to update product');
            }

            return $this->successResponse('Ok');
        } catch (\Throwable $th) {
            return $this->catchErrorResponse();
        }
    }

    /**
     * @param Request $request
     * 
     * @return JsonResponse
     */
    public function delete(Request $request) : JsonResponse
    {
        try {
            $product_id = $request->query('id');
            if (! $this->productRepository->find($product_id)) {
                
                return $this->errorResponse('Resource not found');
            }

            if (! $this->productRepository->delete($product_id)) {
                
                return $this->errorResponse('Failed to delete product');
            }

            return $this->successResponse('Ok');
        } catch (\Throwable $th) {
            return $this->catchErrorResponse();
        }
    }
}