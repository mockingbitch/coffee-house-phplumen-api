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
            if ($request->query('id')) :
                if ($table = $this->tableRepository->find($request->query('id'))) {
                    return response()->json([
                        'error' => 0,
                        'message' =>'ok',
                        'table' => $table
                    ], Response::HTTP_OK);
                } else {
                    return $this->errorResponse('Resource not found');
                }
            endif;

            $tables = $this->tableRepository->getAll();

            return response()->json([
                'error' => 0,
                'message' => 'ok',
                'tables' => $tables
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

            if (! $this->tableRepository->create($validator->validated())) :
                return $this->errorResponse('Failed to create table');
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
            $table_id = $request->query('id');

            if (! $this->tableRepository->find($table_id)) :
                return $this->errorResponse('Resource not found');
            endif;

            $validator = Validator::make($request->all(), [
                'name' => 'required|string|max:1000',
                'description' => 'required|string'
            ]);

            if ($validator->fails()) :
                return $this->exceptionResponse($validator->errors(), 422);
            endif;

            if (! $this->tableRepository->update($table_id, $validator->validated())) :
                return $this->errorResponse('Failed to create table');
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
            $table_id = $request->query('id');

            if (! $this->tableRepository->find($table_id)) :
                return $this->errorResponse('Resource not found');
            endif;

            if (! $this->tableRepository->delete($table_id)) :
                return $this->errorResponse('Failed to delete table');
            endif;

            return $this->successResponse('Ok');
        } catch (\Throwable $th) {
            return $this->catchErrorResponse();
        }
    }
}