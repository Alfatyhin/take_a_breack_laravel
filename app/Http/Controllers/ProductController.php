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

            $validate_array = [
                'slag' => 'required|unique:products,slag',
            ];

            $this->validate($request, $validate_array);

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
//            dd($post);
            $variables  = false;
            $old_options  = false;
            if ($product->variables) {
                $variables = json_decode($product->variables, true);
            }
            if ($product->options) {
                $old_options = json_decode($product->options, true);
            }
            foreach ($post['options'] as $k => &$option) {
                foreach ($option['choices'] as $ko => $choice) {
                    if (!$choice['priceModifier']) {
                        $option['choices'][$ko]['priceModifier'] = 0;
                    }
                    if (empty($choice['text'])) {
                        unset($option['choices'][$ko]);
                    }
                }
            }
            foreach ($post['options'] as $k => &$option) {

                if ($variables && $old_options) {

                    foreach ($variables as $kv => &$variant) {

                        foreach ($variant['options'] as &$variant_option) {

                            if ($variant_option['name'] == $old_options[$k]['name']) {

                                $variant_option['name'] = $option['name'];
                                $variant_option['nameTranslated'] = $option['nameTranslated'];

                                foreach ($old_options[$k]['choices'] as $kc => $old_choise) {
                                    if ($variant_option['value'] == $old_choise['text']) {
                                        $variant_option['valueTranslated'] = $option['choices'][$kc]['textTranslated'];
                                        $variant_option['value'] = $option['choices'][$kc]['text'];
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

        if ($mode == 'add-variable') {
            $variables = $post['variables'];
            $kv = $post['kv'];
            $variables[$kv]['combinationNumber'] = $kv;
            $variables[$kv]['id'] = time();
            $variables[$kv]['unlimited'] = 0;
            $variables[$kv]['quantity'] = 0;
            $variables[$kv]['defaultDisplayedPrice'] = 0;

            if ($product->variables) {
                $old_variables = json_decode($product->variables, true);
                $old_variables[$kv] = $variables[$kv];
                $variables = $old_variables;
            }
            $post['variables'] = $variables;

            $mode = 'variables';

        }

        if ($mode == 'variables') {
            if (!empty($post['variables'])) {
                if (isset($product->options)) {
                    $options = json_decode($product->options, true);

                    foreach ($post['variables'] as $kv => &$variable) {
                        foreach ($variable['options'] as &$var_option) {
                            $var_opt_name = $var_option['name'];
                            foreach ($options as &$option) {
                                if ($option['name'] == $var_opt_name) {
                                    $var_option['nameTranslated'] = $option['nameTranslated'];
                                    $var_opt_value = $var_option['value'];
                                    foreach ($option['choices'] as &$choice) {
                                        if ($choice['text'] == $var_opt_value) {
                                            $var_option['valueTranslated'] = $choice['textTranslated'];
                                            $choice['variant_number'] = $kv;
                                        }
                                    }
                                }
                            }
                        }
                    }

                    $product->options = json_encode($options);
                }
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
}
