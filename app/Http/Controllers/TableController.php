<?php

namespace App\Http\Controllers;

use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use App\Repositories\Contracts\Interface\TableRepositoryInterface;
use Validator;

class TableController extends Controller {
    /**
     * @var tableRepository
     */
    protected $tableRepository;

    /**
     * @param TableRepository $tableRepository
     */
    public function __construct(
        TableRepositoryInterface $tableRepository,
        )
    {
        $this->tableRepository = $tableRepository;
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
                if ($table = $this->tableRepository->find($request->query('id'))) {
                    return response()->json([
                        'error' => 0,
                        'message' =>'ok',
                        'table' => $table
                    ], Response::HTTP_OK);
                } else {
                    return response()->json([
                        'error' => 1,
                        'message' => 'Resource not found',
                        'table' => null
                    ], Response::HTTP_GATEWAY_TIMEOUT);
                }
            }

            $tables = $this->tableRepository->getAll();

            return response()->json([
                'error' => 0,
                'message' => 'ok',
                'tables' => $tables
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

            if (! $this->tableRepository->create($validator->validated())) {
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
            $table_id = $request->query('id');
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

            if (! $this->tableRepository->update($table_id, $validator->validated())) {
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
            $table_id = $request->query('id');
            if (! $this->tableRepository->find($table_id)) {
                return response()->json([
                    'error' => 1,
                    'message' => 'Resource not found'
                ], Response::HTTP_GATEWAY_TIMEOUT);
            }

            if (! $this->tableRepository->delete($table_id)) {
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