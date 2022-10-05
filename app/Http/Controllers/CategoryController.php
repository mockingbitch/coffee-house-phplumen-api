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
                    return $this->errorResponse('Resource not found');
                }
            }

            $categories = $this->categoryRepository->getAll();

            return response()->json([
                'error' => 0,
                'message' => 'ok',
                'categories' => $categories
            ], Response::HTTP_OK);
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
            $validator = Validator::make($request->all(), [
                'name' => 'required|string|max:1000',
                'description' => 'required|string'
            ]);

            if ($validator->fails()) :
                return $this->exceptionResponse($validator->errors(), 422);
            endif;

            if (! $this->categoryRepository->create($validator->validated())) :
                return $this->errorResponse('Failed to create product');
            endif;

            return $this->successResponse('Ok', Response::HTTP_CREATED);
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
            $category_id = $request->query('id');

            if (! $this->categoryRepository->find($category_id)) :
                return $this->errorResponse('Resource not found');
            endif;

            $validator = Validator::make($request->all(), [
                'name' => 'required|string|max:1000',
                'description' => 'required|string'
            ]);

            if ($validator->fails()) :
                return $this->exceptionResponse($validator->errors(), 422);
            endif;

            if (! $this->categoryRepository->update($category_id, $validator->validated())) :
                return $this->errorResponse('Failed to update category');
            endif;

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
            $category_id = $request->query('id');
            
            if (! $this->categoryRepository->find($category_id)) :
                return $this->errorResponse('Resource not found');
            endif;

            $products = $this->productRepository->getProductsByCategory($category_id);

            if (count($products) > 0) :
                return $this->exceptionResponse('Danh mục hiện đang có sản phẩm, vui lòng xoá sản phẩm trước');
            endif;

            if (! $this->categoryRepository->delete($category_id)) :
                return $this->errorResponse('Failed to delete category');
            endif;

            return $this->successResponse('Ok');
        } catch (\Throwable $th) {
            return $this->catchErrorResponse();
        }
    }
}