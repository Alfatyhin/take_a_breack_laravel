<?php

namespace App\Http\Controllers;

use App\Services\EcwidService;
use Illuminate\Http\Request;

class EcwidStore extends Controller
{

    private $ecwidService;
    public function __construct(EcwidService $service) {
        $this->ecwidService = $service;
    }

    public function index()
    {
        var_dump($this->ecwidService);
    }

    public function getEcwidProducts(Request $request)
    {
        $products = $this->ecwidService
            ->getAllProducts();

//        var_dump($products['items'][0]);

        return view('ecwid.products', [
            'products' => $products,
        ]);
    }

    public function EcwidShop(Request $request)
    {

        $categories = $this->ecwidService
            ->getCategories();

        foreach ($categories['items'] as $item) {
            $categoryId = $item['id'];

            if ($item['enabled']) {
                $categoryName = $item['name'];
                $products = $this->ecwidService
                    ->getProductsByCategoryId($categoryId);
                $productList[$categoryName]['description'] = $item;
                $productList[$categoryName]['catalog']  = $products;
            }
        }
//        var_dump($products['items'][0]);

        return view('ecwid.shop', [
            'productList' => $productList
        ]);
    }


    public function getEcwidProductById(Request $request)
    {
        echo "<pre>";
        $productId = $request->get('id');
        $product = $this->ecwidService->getProduct($productId);

        print_r($product);

    }

    public function getOrderById(Request $request)
    {

        $orderId = $request->get('id');
        $ecwidOrder = false;
        $order = false;

        if ($orderId) {

            $ecwidOrder = $this->ecwidService->getOrderBuId($orderId);
            $order = \App\Models\Orders::where('ecwidId', $orderId)->first();

        }

        if (!empty($ecwidOrder['couponDiscount'])) {
            $discount = $ecwidOrder['couponDiscount'];
            $total = $ecwidOrder['subtotal'];
            $rateDiscount = 100 / ($total / $discount);
            var_dump("скидка - $discount ($rateDiscount%)");
        } else {
            echo 'no discount';
        }

        return view('ecwid.order', [
            'ecwidOrder' => $ecwidOrder,
            'order'      => $order,
        ]);

    }

    public function allCategories()
    {
        $categories = $this->ecwidService->getCategories();

        echo "<pre>";
       foreach ($categories['items'] as $item) {

           $img = $item['thumbnailUrl'];
           $name = $item['name'];
           echo "<img src='$img'><br> $name <br>";

           foreach ($item['nameTranslated'] as $k => $v) {
               echo "$k - $v <br>";
           }

           $desc = $item['description'];

           echo "<hr> Описание <br> $desc <br>";

           foreach ($item['descriptionTranslated'] as $k => $v) {
               echo "$k - $v <br>";
           }


       }

    }



}
