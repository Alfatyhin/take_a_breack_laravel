<?php

namespace App\Http\Controllers;

use App\Models\ProductOptions;
use Illuminate\Http\Request;

class ProductOptionsController extends Controller
{
    public function index(Request $request)
    {
        $product_options = ProductOptions::all()->keyBy('id')->toArray();

        dd($product_options);

    }

    public function add(Request $request)
    {
        $post = $request->post();

        $new_option = new ProductOptions();
        $new_option->name = $post['name'];
        $new_option->type = $post['name'];
        $new_option->nameTranslate = json_encode($post['nameTranslate']);
        $new_option->save();

        session()->flash('message', ["option {$new_option->name} save"]);

        return back();
    }

    public function save(Request $request, ProductOptions $option)
    {
        $post = $request->post();

        if (isset($post['options'])) {
            $test = end($post['options']);
            if (empty($test['text'])) {
                $post['options'] = array_slice($post['options'], 0, -1);
            }

            $option->options = json_encode($post['options']);

        }

        $option->name = $post['name'];
        $option->type = $post['type'];
        $option->nameTranslate = json_encode($post['nameTranslate']);
        $option->save();

        session()->flash('message', ["option {$option->name} save"]);

        return back();
    }
}
