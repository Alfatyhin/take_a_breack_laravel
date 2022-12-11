<?php

namespace App\Http\Controllers;

use App\Models\Categories;
use App\Models\Product;
use App\Models\ProductOptions;
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
        $products_options = ProductOptions::all()->keyBy('id')->toArray();
        $shop_langs = AppServise::getLangs();


        $categories = Categories::all()->sortBy('index_num')->keyBy('id');

        $prod_categories = json_decode($product->categories, true);
        $prod_categories_map = [];
        if ($prod_categories) {
            $prod_categories_map = array_flip($prod_categories);
        }
        $product_data = json_decode($product->data, true);

//        dd(json_decode($product->options, true),json_decode($product->variables, true), $products_options);


        $cat_id = $product->category_id;
        $products = Product::where('category_id', $cat_id)->get()->sortBy('index_num');


        return view('shop-settings.product-redact', [
            'message'    => $message,
            'categories' => $categories,
            'product'    => $product,
            'products'   => $products,
            'prod_categories_map' => $prod_categories_map,
            'products_options' => $products_options,
            'product_data' => $product_data,
            'shop_langs' => $shop_langs
        ]);
    }

    public function ProductSave(Request $request, Product $product)
    {
        $mode = $request->get('mode');
        $post = $request->post();

//        dd($post);

        if ($mode == 'general') {

            if ($post['slag'] != $product->slag) {
                $validate_array = [
                    'slag' => 'required|unique:products,slag',
                ];


                $this->validate($request, $validate_array);
            }



            $categories = Categories::all()->sortBy('index_num')->keyBy('id');

            $product_category_old_id = $product->category_id;

            if (empty($post['slag'])) {
                $slag = preg_replace('/\\W/', '_', $post['name']);
                $slag = strtolower($slag);

            } else {
                $slag = strtolower($post['slag']);
                $slag = preg_replace('/\\W/', '_', $slag);
            }
            if (empty($post['compareToPrice'])) {
                $post['compareToPrice'] = 0;
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
            if (!empty($post['options'])) {
                if (!empty($post['new_options'])) {
                    foreach ($post['new_options'] as $opt_key => &$option_choices) {
                        foreach ($option_choices['choices'] as &$choice) {
                            $choice['priceModifier'] = 0;
                            $choice['priceModifierType'] = 'ABSOLUTE';
                        }
                        if (!isset($post['options'][$opt_key]['choices'])) {
                            $post['options'][$opt_key]['choices'] = $option_choices['choices'];
                        } else {
                            $next_key = sizeof($post['options'][$opt_key]['choices']);
                            foreach ($option_choices['choices'] as $choice) {
                                $post['options'][$opt_key]['choices'][$next_key] = $choice;
                            }
                        }
                    }
                }
                foreach ($post['options'] as $ko => $option) {
                    if(!isset($option['choices'])) {
                        unset($post['options'][$ko]);
                    }
                }
                $product->options = json_encode($post['options']);
            } else {
                $product->options = null;
            }


            $product->save();
            session()->flash('message', ["product {$product->name} save"]);
        }

        if ($mode == 'option_add') {


            $option['options_id'] = $post['new_option_id'];
            $options = json_decode($product->options, true);
            $options[] = $option;
            $product->options = json_encode($options);

            $product->save();
            session()->flash('message', ["product {$product->name} save"]);
        }

        if ($mode == 'add-variable') {

            $new_variable = $post['variables'];
            $new_variable['id'] = time();
            $new_variable['unlimited'] = 0;
            $new_variable['quantity'] = 0;
            $new_variable['defaultDisplayedPrice'] = 0;

            foreach ($new_variable['options'] as $ko => $optionv) {
                if (!isset($optionv['var_option_id']) || $optionv['var_option_id'] == '') {
                    unset($new_variable['options'][$ko]);
                }
            }
            if (!empty($new_variable['options'])) {
                if ($product->variables) {
                    $variables = json_decode($product->variables, true);
                }
                $variables[] = $new_variable;
            } else {
                dd('add options to variant');
            }


            $post['variables'] = $variables;

            $mode = 'variables';

        }

        if ($mode == 'variables') {
            if (!empty($post['variables'])) {
                $product->variables = json_encode($post['variables']);
                $variables = json_decode($product->variables, true);
            } else {
                $product->variables = null;
                $variables = false;
            }

            if ($product->options) {
                $options = json_decode($product->options, true);
                foreach ($options as $k => &$option) {
                    if (isset($option['choices'])) {
                        foreach ($option['choices'] as $ko => &$choice) {
                            unset($choice['variant_number']);
                            if ($variables) {
                                foreach ($variables as $kv => $variant) {
                                    if (isset($variant['options'][$k])) {
                                        if ($variant['options'][$k]['options_id'] == $option['options_id'] && $variant['options'][$k]['var_option_id'] == $choice['var_option_id']) {
                                            $choice['variant_number'] = $kv;
                                        }
                                    }

                                }
                            }
                        }
                    }
                }
                $product->options = json_encode($options);
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

        $image_galery = json_decode($product->galery, true);

        if (!empty($image_galery)) {
            $flag = false;
            foreach ($image_galery as $ki => &$item) {
                foreach ($item as &$path) {
                    $path_data = explode('/', $path);
                    $path_data = array_slice($path_data,1);
                    $file_names = last($path_data);
                    $filename = $product->id . "_" . $ki . "_product_clone";
                    $new_path_data = array_slice($path_data, 0,-1);
                    $new_path = "/" . implode('/', $new_path_data) . "/$filename.webp";
                    if (preg_match('/webp$/', $file_names)) {
                        if (Storage::disk('public_root')->exists($path)) {
                            $flag = true;
                            if (!Storage::disk('public_root')->exists($new_path)) {
                                Storage::disk('public_root')->copy($path, $new_path);
                            }
                            $path = "$new_path";
                        }
                    }
                }
            }
            if ($flag) {
                $product->galery = json_encode($image_galery);
                $product->image = json_encode($image_galery[0]);
            }
        }
        $new_product->save();


        session()->flash('message', ["product $product->name copy"]);
        return redirect(route('product_redact', ['product' => $new_product]));
    }

    public function fixProducts(Request $request)
    {
        $products = Product::all();

        foreach ($products as $product) {
            if (!empty($product->variables)) {
                $variables = json_decode($product->variables, true);
                $options = json_decode($product->options, true);
                dd($product->toArray());
            }
        }

        dd('done', $products->toArray());
    }
}
