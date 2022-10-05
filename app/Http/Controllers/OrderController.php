<?php

namespace App\Http\Controllers;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use App\Repositories\Contracts\Interface\OrderRepositoryInterface;
use App\Repositories\Contracts\Interface\OrderDetailRepositoryInterface;
use App\Repositories\Contracts\Interface\ProductRepositoryInterface;
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
     * @var productDetailRepository
     */
    protected $productRepository;

    /**
     * @param OrderRepositoryInterface $orderRepository
     * @param OrderDetailRepositoryInterface $orderDetailRepository
     * @param ProductRepositoryInterface $productRepository
     */
    public function __construct(
        OrderRepositoryInterface $orderRepository,
        OrderDetailRepositoryInterface $orderDetailRepository,
        ProductRepositoryInterface $productRepository
    )
    {
        $this->orderRepository = $orderRepository;
        $this->orderDetailRepository = $orderDetailRepository;
        $this->productRepository = $productRepository;
    }

    /**
     * @param Request $request
     * 
     * @return JsonResponse
     */
    public function showOrder(Request $request) : JsonResponse
    {
        try {
            if ($request->query('id')) :
                if ($order = $this->orderRepository->find($request->query('id'))) :
                    return response()->json([
                        'error' => 0,
                        'message' =>'ok',
                        'order' => $order
                    ], Response::HTTP_OK);
                else :
                    return $this->errorResponse('Resource not found');
                endif;
            endif;

            if (null !== $request->query('status')) :
                $orders = $this->orderRepository->findByStatus($request->query('status'));
            else :
                $orders = $this->orderRepository->getAll();
            endif;

            return response()->json([
                'error' => 0,
                'message' => 'ok',
                'orders' => $orders
            ], Response::HTTP_OK);
        } catch (\Throwable $th) {
            return $this->catchErrorResponse();
        }
    }

    public function createOrder(Request $request) : JsonResponse
    {
        //define user id
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
        
        $data = [
            'guest_id' => $guest_id,
            'waiter_id' => $waiter_id,
            'table_id' => $request->table_id,
            'total' => 0,
            'note' => $request->note,
        ];

        if (! $order = $this->orderRepository->create($data)) :
            return $this->errorResponse('Failed to create order');
        endif;

        $total = $this->createOrderDetail($order->id, $request);
        
        if (! $this->orderRepository->update($order->id, ['total' => $total])) :
            return $this->errorResponse('Failed to update order total');
        endif;

        return $this->successResponse('Ok');
    }

    /**
     * @param integer $order_id
     * @param object $request
     * 
     * @return number
     */
    public function createOrderDetail(int $order_id, object $request) : number
    {
        $list_products_id = $request->data;
        $quantity = 1;
        $total = 0;

        //foreach list product from request
        foreach ($list_products_id as $product_id) :
            $product = $this->productRepository->find($product_id);
            
            if (! $product || null == $product) :
                return $this->errorResponse('Resource not found');
            endif;

            $data = [
                'order_id' => $order_id,
                'product_id' => $product->id,
                'quantity' => $quantity,
                'note' => 'test'
            ];

            if (! $this->orderRepository->create($data)) :
                return $this->errorResponse('Failed to create order detail');
            endif;

            $total = $product->price * $quantity;
        endforeach;

        return $total;
    }

    /**
     * @param Request $request
     * 
     * @return JsonResponse
     */
    public function updateOrder(Request $request) : JsonResponse
    {
        try {
            $order_id = $request->query('id');

            if (! $this->orderRepository->find($order_id)) :
                return $this->errorResponse('Resource not found');
            endif;

            if (! $this->orderRepository->update($order_id, ['status' => $request->status])) :
                return $this->errorResponse('Failed to update');
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
    public function checkOut(Request $request) : JsonResponse
    {
        try {
            $order_id = $request->query('id');

            if (! $this->orderRepository->find($order_id)) :
                return $this->errorResponse('Resource not found');
            endif;

            if (! $this->orderRepository->update($order_id, ['status' => 'CHECKEDOUT'])) :
                return $this->errorResponse('Failed to check out');
            endif;

            return $this->successResponse('Ok');
        } catch (\Throwable $th) {
            return $this->catchErrorResponse();
        }
    }
}
