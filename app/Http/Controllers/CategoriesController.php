<?php

namespace App\Http\Controllers;

use App\Models\Categories;
use App\Models\Product;
use App\Services\EcwidService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;

class CategoriesController extends Controller
{
    public function index(Request $request)
    {
        $categories = Categories::all();

        dd($categories);
    }



    public function CategoryProductsSave(Request $request)
    {
        $prod_ids = $request->post('prod_ids');
        $category_id = $request->post('id');
        if (!empty($category_id && $prod_ids)) {
            $category = Categories::where('id', $category_id)->first();
            if (!empty($category)) {
                $category->products = json_encode($prod_ids);
                $category->save();

                foreach ($prod_ids as $num => $prod_id) {
                    $product = Product::find($prod_id);
                    $product->index_num = $num;
                    $product->save();
                }

                session()->flash('message', ["category {$category->name}  products sortable save"]);
            }
        }
        return redirect(route('shop_settings_categories'));
    }

    public function CategoryDataSave(Request $request, Categories $category)
    {
        $data = json_decode($category->data, true);

        foreach ($request->post('data') as $name => $value) {
            $data[$name] = $value;
        }
        $category->data = json_encode($data);
        $category->save();

        session()->flash('message', ["category {$category->name}  products data save"]);

        return redirect(route('shop_settings_categories'));
    }

    public function CategorySave(Request $request)
    {
        $category_id = $request->post('id');
        $post = $request->post();
        $category = Categories::where('id', $category_id)->first();

        if (!empty($category->id)) {

            $validate_array = [
                'slag' => 'required|unique:categories,slag',
            ];

            $this->validate($request, $validate_array);

            if (!empty($post['name'])) {
                $category->name = $post['name'];
            }
            if (!empty($post['name'])) {
                $category->slag = $post['slag'];
            }
            if (!empty($post['enabled'])) {
                $category->enabled = true;
            } else {
                $category->enabled = false;
            }
            if (!empty($post['parent_id'])) {
                $category->parent_id = $post['parent_id'];
            } else {
                $category->parent_id = null;
            }
            if (empty($post['slag'])) {
                $slag = preg_replace('/\\W/', '_', $category['name']);
                $slag = strtolower($slag);

            } else {
                $slag = strtolower($post['slag']);
                $slag = preg_replace('/\\W/', '_', $slag);
            }

            $category->slag = $slag;
            $category->translate = json_encode($post['translate']);
            $category->save();


            session()->flash('message', ["category {$post['name']} save"]);

        }


        ShopController::sitemapGenerate();

        return redirect(route('shop_settings_categories'));
    }

    public function create(Request $request)
    {
        $category = new Categories();
        $category->name = 'New Category';
        $category->slag = 'new_category';
        $category->save();

        session()->flash('message', ["new category create"]);

        return back();
    }

    public function delete(Request $request, Categories $category)
    {
        $cat_products = json_decode($category->products);
        $products = Product::whereIn('id', $cat_products)->get();

        foreach ($products as $product) {
            if ($product->category_id == $category->id) {
                $product->category_id = 0;
            }
            if (!empty($product->categories)) {
                $prod_categories = json_decode($product->categories, true);
                if (in_array($category->id, $prod_categories)) {
                    $key = array_search($category->id, $prod_categories);
                    unset($prod_categories[$key]);
                    $prod_categories = array_slice($prod_categories, 0);
                    $product->categories = json_encode($prod_categories);
                }
            }
            $product->save();
        }
        $cat_name = $category->name;
        $category->delete();

        session()->flash('message', ["category $cat_name delete"]);

        return back();
    }
}
