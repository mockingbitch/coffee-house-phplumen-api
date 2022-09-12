<?php

namespace App\Http\Controllers;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use App\Repositories\Contracts\Interface\OrderRepositoryInterface;
use App\Repositories\Contracts\Interface\OrderDetailRepositoryInterface;
use Validator;

class OrderController extends Controller
{
    /**
     * @var orderRepository
     */
    protected $orderRepository;

    /**
     * @var orderDetailRepository
     */
    protected $orderDetailRepository;

    /**
     * @param OrderRepositoryInterface $orderRepository
     * @param OrderDetailRepositoryInterface $orderDetailRepository
     */
    public function __construct(
        OrderRepositoryInterface $orderRepository,
        OrderDetailRepositoryInterface $orderDetailRepository
    )
    {
        $this->orderRepository = $orderRepository;
        $this->orderDetailRepository = $orderDetailRepository;
    }

    public function create(Request $request) : JsonResponse
    {
        $guest_id = null;
        $waiter_id = null;
        switch (auth()->user()->role) {
            case 'ROLE_GUEST':
                $guest_id = auth()->user()->id;
                break;
            case 'ROLE_WAITER':
            case 'ROLE_MANAGER':
            case 'ROLE_ADMIN':
                $waiter_id = auth()->user()->id;
                break;
            default:
            break;
        }
        
        $validator = Validator::make($request->all(), [
            'name' => 'required|string|max:1000',
            'description' => 'required|string',
            'category_id' => 'required|integer',
            'price' => 'required',
            'image' => 'string',
            'status' => 'string'
        ]);
    }
}
