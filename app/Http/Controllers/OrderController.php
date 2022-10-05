<?php

namespace App\Http\Controllers;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use App\Repositories\Contracts\Interface\OrderRepositoryInterface;
use App\Repositories\Contracts\Interface\OrderDetailRepositoryInterface;
use App\Repositories\Contracts\Interface\ProductRepositoryInterface;
use App\Repositories\Contracts\Interface\TableRepositoryInterface;
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
     * @var tableRepository
     */
    protected $tableRepository;

    /**
     * @param OrderRepositoryInterface $orderRepository
     * @param OrderDetailRepositoryInterface $orderDetailRepository
     * @param ProductRepositoryInterface $productRepository
     * @param TableRepositoryInterface $tableRepository
     */
    public function __construct(
        OrderRepositoryInterface $orderRepository,
        OrderDetailRepositoryInterface $orderDetailRepository,
        ProductRepositoryInterface $productRepository,
        TableRepositoryInterface $tableRepository
    )
    {
        $this->orderRepository = $orderRepository;
        $this->orderDetailRepository = $orderDetailRepository;
        $this->productRepository = $productRepository;
        $this->tableRepository = $tableRepository;
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
        try {
            $user = auth()->user();

            if (! $this->tableRepository->find($request->table_id)) :
                return $this->errorResponse('Could not find table');
            endif;

            if (null !== $user) :
                $data = [
                    'guest_id' => $user->role == 'ROLE_GUEST' ? $user->id : null,
                    'waiter_id' => $user->role == 'ROLE_WAITER' || $user->role == 'ROLE_MANAGER' || $user->role == 'ROLE_ADMIN' ? $user->id : null,
                    'table_id' => $request->table_id,
                    'total' => 0,
                    'note' => $request->note,
                ];
            else :
                $data = [
                    'table_id' => $request->table_id,
                    'total' => 0,
                    'note' => $request->note,
                ];
            endif;

            if (! $order = $this->orderRepository->create($data)) :
                return $this->errorResponse('Failed to create order');
            endif;

            $total = $this->createOrderDetail($order->id, $request);
            
            if (! $this->orderRepository->update($order->id, ['total' => $total])) :
                return $this->errorResponse('Failed to update order total');
            endif;

            return $this->successResponse('Ok');
        } catch (\Throwable $th) {
            return $this->catchErrorResponse();
        }
    }

    /**
     * @param integer $order_id
     * @param object $request
     * 
     * @return float
     */
    public function createOrderDetail(int $order_id, object $request) : float
    {
        try {
            $list_data = $request->data;
            $total = 0;

            //foreach list product from request
            foreach ($list_data as $data) :
                $product = $this->productRepository->find($data['product_id']);
                
                if (! $product || null == $product) :
                    return $this->errorResponse('Resource not found');
                endif;
                
                $data = [
                    'order_id' => $order_id,
                    'product_id' => $product->id,
                    'quantity' => $data['quantity'],
                    'total' => $product->price * (int) $data['quantity'],
                    'note' => 'test'
                ];

                if (! $this->orderDetailRepository->create($data)) :
                    return $this->errorResponse('Failed to create order detail');
                endif;

                $total += $product->price * (int) $data['quantity'];
            endforeach;

            return $total;
        } catch (\Throwable $th) {
            return $this->catchErrorResponse();
        }
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
