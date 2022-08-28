<?php

namespace App\Http\Controllers;

use App\Models\Categories;
use App\Models\Product;
use App\Services\AppServise;
use App\Services\EcwidService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;

class ProductController extends Controller
{



    public function ProductEnabled(Request $request)
    {
        $id = $request->post('id');
        $enabled = $request->post('enabled');
        $product = Product::where('id', $id)->first();
        if ($enabled == 1) {
            $product->enabled = true;
        } else {
            $product->enabled = false;
        }
        $product->save();


        ShopController::sitemapGenerate();

        return "true";
    }

    public function RedactProduct(Request $request, Product $product)
    {
        $message = false;
        if (session()->has('message')) {
            $message = session('message');
        }
        $shop_langs = AppServise::getLangs();

        $options_select = ['SELECT' => 'список', 'SIZE' => 'размер', 'RADIO' => 'выбор', 'CHECKBOX' => 'флажки', 'TEXT' => 'текстовое поле'];

        $categories = Categories::all()->sortBy('index_num')->keyBy('id');

        $prod_categories = json_decode($product->categories, true);
        $prod_categories_map = [];
        if ($prod_categories) {
            $prod_categories_map = array_flip($prod_categories);
        }
        $product_data = json_decode($product->data, true);

//        dd($product->toArray());

        $cat_id = $product->category_id;
        $products = Product::where('category_id', $cat_id)->get()->sortBy('index_num');


        return view('shop-settings.product-redact', [
            'message'    => $message,
            'categories' => $categories,
            'product'    => $product,
            'products'   => $products,
            'prod_categories_map' => $prod_categories_map,
            'options_select' => $options_select,
            'product_data' => $product_data,
            'shop_langs' => $shop_langs
        ]);
    }

    public function ProductSave(Request $request, Product $product)
    {
        $mode = $request->get('mode');
        $post = $request->post();

        if ($mode == 'general') {
            $categories = Categories::all()->sortBy('index_num')->keyBy('id');

            $product_category_old_id = $product->category_id;

            if (empty($post['slag'])) {
                $slag = preg_replace('/\\W/', '_', $post['name']);
                $slag = strtolower($slag);

            } else {
                $slag = $post['slag'];
            }

            $product->name = $post['name'];
            $product->slag = $slag;
            $product->sku = $post['sku'];
            $product->price = $post['price'];
            $product->compareToPrice = $post['compareToPrice'];
            $product->unlimited = $post['unlimited'];
            $product->count = $post['count'];
            $product->category_id = $post['category_id'];
            $product->translate = json_encode($post['translate']);

            if (!empty($post['enabled'])) {
                $product->enabled = true;
            } else {

                $product->enabled = false;
            }

            if ($product_category_old_id != $product->category_id) {

                if ($product_category_old_id) {
                    $product_category_old = $categories[$product_category_old_id];
                    $product_category_old->deleteProductId($product->id);
                }
                if (isset($categories[$product->category_id])) {
                    $product_category = $categories[$product->category_id];
                    $product_category->addProductId($product->id);
                }

            } else {
                $product_category = $categories[$product->category_id];
                $product_category->addProductId($product->id);
            }



            $catigory_ids_unset = json_decode($product->categories);
            if (!empty($catigory_ids_unset)) {
                foreach ($catigory_ids_unset as $cat_id) {
                    $category = $categories[$cat_id];
                    $category->deleteProductId($product->id);
                }
                $product->categories = null;
            }

            if (!empty($post['categories'])) {
                $catigory_ids_add = $post['categories'];
                foreach ($catigory_ids_add as $cat_id) {
                    $category = $categories[$cat_id];
                    $category->addProductId($product->id);
                }
                $product->categories = json_encode($catigory_ids_add);
            }
            $product->save();

            session()->flash('message', ["product {$product->name} save"]);
        }


        if ($mode == 'options') {
            $variables  = false;
            $old_options  = false;
            if ($product->variables) {
                $variables = json_decode($product->variables, true);
            }
            if ($product->options) {
                $old_options = json_decode($product->options, true);
            }
            foreach ($post['options'] as $k => $option) {
                foreach ($option['choices'] as $ko => $choice ) {
                    if (!isset($choice['text'])) {
                        unset($option['choices'][$ko]);
                    }
                    if ($variables && $old_options) {
                        foreach ($old_options as $old_option) {
                            if ($old_option['name'] == $option['name']) {
                                foreach ($option['choices'] as $kc => $choice) {
                                    if ($choice['text'] != $old_option['choices'][$kc]['text']) {
                                        $new_choice_text = $choice['text'];
                                        foreach ($variables as &$variant) {
                                            foreach ($variant['options'] as &$variant_option) {
                                                if ($variant_option['name'] == $option['name']
                                                    && $variant_option['value'] == $old_option['choices'][$kc]['text']) {
                                                    $variant_option['value'] = $new_choice_text;
                                                }
                                            }
                                        }
                                    }
                                }
                            }
                        }
                    }
                }
                if (sizeof($option['choices']) == 0) {
                    unset($post['options'][$k]);
                }

            }

            if ($variables) {
                $product->variables = json_encode($variables);
            }
            if (!empty($post['options'])) {
                $product->options = json_encode($post['options']);
            } else {
                $product->options = null;
            }

            $product->save();
            session()->flash('message', ["product {$product->name} save"]);
        }
        if ($mode == 'option_add') {


            $option = $post['option'];
            $options = json_decode($product->options, true);
            $options[] = $option;
            $product->options = json_encode($options);

            $product->save();
            session()->flash('message', ["product {$product->name} save"]);
        }


        if ($mode == 'variables') {
            if (!empty($post['variables'])) {
                $product->variables = json_encode($post['variables']);
            } else {
                $product->variables = null;
            }

            $product->save();
            session()->flash('message', ["product {$product->name} save"]);
        }

        if ($mode == 'data') {

            $data = json_decode($product->data, true);
            foreach ($post['data'] as $name => $item) {
                $data[$name] = $item;
            }

            $product->data = json_encode($data);

            $product->save();
            session()->flash('message', ["product {$product->name} save"]);
        }

        ShopController::sitemapGenerate();

        return redirect(route('product_redact', ['product' => $product]));
    }

    public function createProduct(Request $request)
    {
        $product = new Product();
        $product->name = 'New Product';
        $product->slag = 'new_product';
        $product->sku = time();
        $product->save();

        $product->save();
        session()->flash('message', ["product {$product->name} create"]);

        return redirect(route('product_redact', ['product' => $product]));

    }

    public function deleteProduct(Request $request, Product $product)
    {
        $categories = Categories::all()->sortBy('index_num')->keyBy('id');
        if (!empty($product->category_id)) {
            $product_category = $categories[$product->category_id];
            $product_category->deleteProductId($product->id);
        }
        if (!empty($product->categories)) {
            $product_categoryes = json_decode($product->categories, true);

            foreach ($product_categoryes as $cat_id) {
                $category = $categories[$cat_id];
                $category->deleteProductId($product->id);
            }
        }

        session()->flash('message', ["product $product->name delete"]);
        $product->delete();

        return back();
    }


    public function clone(Request $request, Product $product)
    {
        $new_product = $product->replicate();
        $new_product->name = $product->name . ' clone';
        $new_product->sku = $product->sku . '-1';
        $new_product->slag = $product->slag . '_clone';
        $new_product->save();


        session()->flash('message', ["product $product->name copy"]);
        return redirect(route('product_redact', ['product' => $new_product]));
    }
}
