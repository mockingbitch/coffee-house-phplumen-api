<?php

namespace App\Http\Controllers;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use App\Repositories\Contracts\Interface\CartRepositoryInterface;
use App\Repositories\Contracts\Interface\CartDetailRepositoryInterface;
use App\Repositories\Contracts\Interface\ProductRepositoryInterface;
use Validator;

class CartController extends Controller
{
    /**
     * @var cartRepository
     */
    protected $cartRepository;

    /**
     * @var cartDetailRepository
     */
    protected $cartDetailRepository;

    /**
     * @var productDetailRepository
     */
    protected $productRepository;

    /**
     * @param CartRepositoryInterface $cartRepository
     * @param CartDetailRepositoryInterface $cartDetailRepository
     * @param ProductRepositoryInterface $productRepository
     */
    public function __construct(
        CartRepositoryInterface $cartRepository,
        CartDetailRepositoryInterface $cartDetailRepository,
        ProductRepositoryInterface $productRepository
    )
    {
        $this->cartRepository = $cartRepository;
        $this->cartDetailRepository = $cartDetailRepository;
        $this->productRepository = $productRepository;
    }

    public function createCart(Request $request) : JsonResponse
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

        $cart = $this->cartRepository->create($data);
    }

    /**
     * undone quantity
     *
     * @param Request $request
     * @return void
     */
    public function createCartDetail(Request $request) : JsonResponse
    {
        $list_products_id = $request->data;

        foreach ($list_products_id as $product_id) :
            $product = $this->productRepository->find($product_id);
            
            if ($product && $product != null) :
                $cart = [
                    'id' => $product->id,
                    'name' => $product->name,
                    'price' => $product->price,
                    'quantity'=> 1,
                    'image' => $product->image
                ];
            endif;
        endforeach;
    }
}
