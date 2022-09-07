<?php

namespace App\Http\Controllers;

use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use App\Repositories\Contracts\Interface\UserRepositoryInterface;

class UserController extends Controller {
    /**
     * @var userRepository
     */
    protected $userRepository;

    /**
     * @param UserRepository $userRepository
     */
    public function __construct(UserRepositoryInterface $userRepository)
    {
        $this->userRepository = $userRepository;
    }

    /**
     * @param Request $request
     * 
     * @return JsonResponse
     */
    public function index(Request $request) : JsonResponse
    {
        try {
            $user = $this->userRepository->getAll();
            
            return response()->json([
                'error' => 0,
                'message' => 'ok',
                'user' => $user
            ], Response::HTTP_OK);
        } catch (\Throwable $th) {
            return response()->json([
                'error' => 2,
                'message' => 'exeption'
            ], Response::HTTP_BAD_REQUEST);
        }
    }
}