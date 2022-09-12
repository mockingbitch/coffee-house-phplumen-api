<?php
namespace App\Services;

use App\Repositories\Contracts\Interface\ProductRepositoryInterface;

class CartSessionService
{
    /**
     * @var productRepository
     */
    protected $productRepository;

    /**
     * @param ProductRepositoryInterface $productRepository
     */
    public function __construct(ProductRepositoryInterface $productRepository)
    {
        $this->productRepository = $productRepository;
    }

    /**
     * @param integer $product_id
     * @return void
     */
    public function createCartSession(int $product_id)
    {
        $product = $this->productRepository->find($product_id);
        $cart = session()->get('cart');

        if(isset($cart[$product_id])){
            $cart[$product_id]['quantity'] = $cart[$product_id]['quantity'] + 1;
        }else{
            $cart[$product_id]=[
                'id' => $product->id,
                'name' => $product->name,
                'price' => $product->price,
                'quantity' => 1,
                'image' => $product->image
            ];
        }

        session()->put('cart', $cart);
    }

    public function update($id,$quantity){
        if($id && $quantity){
            $cart = session()->get('cart');
            $cart[$id]['quantity'] = (int)$quantity ;
        }
        session()->put('cart',$cart);
        return response()->json(['code'=>200],200);
    }
    public function delete($id){
        if($id){
            $cart = session()->get('cart');
            unset($cart[$id]);
            session()->put('cart',$cart);
            return response()->json(['code'=>200],200);
        }
    }
    public function checkOut($carts){
            $customer = Auth::guard('customer')->user();
            $mail = $customer['email'];
            $name = $customer['name'];
            Mail::send('home.mail.mail-cart',compact('customer','carts'),function($email) use($mail,$name){
                $email->subject('Techshop - Xác nhận đơn hàng');
                $email->to($mail,$name);
            });
    }
}