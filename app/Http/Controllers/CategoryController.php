<?php

namespace App\Http\Controllers;

use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use App\Repositories\Contracts\Interface\CategoryRepositoryInterface;
use App\Repositories\Contracts\Interface\ProductRepositoryInterface;
use Validator;

class CategoryController extends Controller {
    /**
     * @var categoryRepository
     */
    protected $categoryRepository;

    /**
     * @var productRepository
     */
    protected $productRepository;

    /**
     * @param CategoryRepository $categoryRepository
     */
    public function __construct(
        CategoryRepositoryInterface $categoryRepository,
        ProductRepositoryInterface $productRepository
        )
    {
        $this->categoryRepository = $categoryRepository;
        $this->productRepository = $productRepository;
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
                if ($category = $this->categoryRepository->find($request->query('id'))) {
                    return response()->json([
                        'error' => 0,
                        'message' =>'ok',
                        'category' => $category
                    ], Response::HTTP_OK);
                } else {
                    return response()->json([
                        'error' => 0,
                        'message' => 'Resource not found',
                        'category' => null
                    ], Response::HTTP_GATEWAY_TIMEOUT);
                }
            }

            $categories = $this->categoryRepository->getAll();

            return response()->json([
                'error' => 0,
                'message' => 'ok',
                'categories' => $categories
            ], Response::HTTP_OK);
        } catch (\Throwable $th) {
            return response()->json([
                'error' => 3,
                'message' => 'Catch error',
            ], Response::HTTP_GATEWAY_TIMEOUT);
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
            $validator = Validator::make($request->all(), [
                'name' => 'required|string|max:1000',
                'description' => 'required|string'
            ]);

            if ($validator->fails()) {
                return response()->json([
                    'error' => 1,
                    'message' => $validator->errors()
                ], 422);
            }

            if (! $this->categoryRepository->create($validator->validated())) {
                return response()->json([
                    'error' => 2,
                    'message' => 'Something went wrong'
                ], Response::HTTP_GATEWAY_TIMEOUT);
            }

            return response()->json([
                'error' => 0,
                'message' => 'ok'
            ], Response::HTTP_CREATED);
        } catch (\Throwable $th) {
            return response()->json([
                'error' => 3,
                'message' => 'Catch error',
            ], Response::HTTP_GATEWAY_TIMEOUT);
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
            $category_id = $request->query('id');
            $validator = Validator::make($request->all(), [
                'name' => 'required|string|max:1000',
                'description' => 'required|string'
            ]);

            if ($validator->fails()) {
                return response()->json([
                    'error' => 1,
                    'message' => $validator->errors()
                ], 422);
            }

            if (! $this->categoryRepository->update($category_id, $validator->validated())) {
                return response()->json([
                    'error' => 2,
                    'message' => 'Something went wrong'
                ], Response::HTTP_GATEWAY_TIMEOUT);
            }

            return response()->json([
                'error' => 0,
                'message' => 'ok'
            ], Response::HTTP_OK);
        } catch (\Throwable $th) {
            return response()->json([
                'error' => 3,
                'message' => 'Catch error',
            ], Response::HTTP_GATEWAY_TIMEOUT);
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
            $category_id = $request->query('id');
            
            if (! $this->categoryRepository->find($category_id)) {
                return response()->json([
                    'error' => 1,
                    'message' => 'Resource not found'
                ], Response::HTTP_GATEWAY_TIMEOUT);
            }

            $products = $this->productRepository->getProductsByCategory($category_id);

            if (count($products) > 0) {
                return response()->json([
                    'error' => 1,
                    'message' => 'Danh mục hiện đang có sản phẩm, vui lòng xoá sản phẩm trước'
                ], Response::HTTP_OK);
            }

            if (! $this->categoryRepository->delete($category_id)) {
                return response()->json([
                    'error' => 2,
                    'message' => 'Something went wrong'
                ], Response::HTTP_GATEWAY_TIMEOUT);
            }

            return response()->json([
                'error' => 0,
                'message' => 'ok'
            ], Response::HTTP_OK);
        } catch (\Throwable $th) {
            return response()->json([
                'error' => 3,
                'message' => 'Catch error',
            ], Response::HTTP_GATEWAY_TIMEOUT);
        }
    }
}