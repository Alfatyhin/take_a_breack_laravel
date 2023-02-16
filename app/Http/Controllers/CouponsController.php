<?php

namespace App\Http\Controllers;

use App\Models\Categories;
use App\Models\Coupons;
use App\Models\CouponsGroups;
use App\Models\Product;
use App\Services\AppServise;
use Illuminate\Http\Request;

class CouponsController extends Controller
{

    public function couponsDiscounts(Request $request, $name = false)
    {
        $coupons = false;
        $post = $request->post();
        if (!empty($post)) {
            if (!empty($post['name']) && !empty($post['code']) && !empty($post['discount']['value'])) {
                if (!empty($post['add'])) {
                    $coupon = new Coupons();
                }
                $coupon->name = $post['name'];
                $coupon->code = $post['code'];
                $coupon->discount = json_encode($post['discount']);
                $coupon->data = json_encode($post['data']);
                $coupon->status = $post['status'];
                $coupon->save();

                session()->flash('message', ["coupon save"]);


            }if (!empty($request->post('delete'))) {
                $coupon = Coupons::find($request->get('delete'));
                $coupon->delete();
                session()->flash('message', ["coupon delete"]);
                return back();
            } else {
                session()->flash('message', ["error data - empty values"]);
            }

            return redirect(route('coupons_discount'));
        }

        $categories = Categories::get()->sortBy('index_num')->keyBy('id');
        $products = Product::get()->sortBy('index_num')->keyBy('id');
        $products = AppServise::ProductsShopPrepeare($products, $categories);

        if ($name) {
            $coupons = Coupons::leftJoin('coupons_groups', 'coupons.group_id', '=', 'coupons_groups.id')
                ->select('coupons.*', 'coupons_groups.name', 'coupons_groups.discount', 'coupons_groups.data')
                ->where('coupons_groups.name', $name)
                ->orderBy('id', 'desc')
                ->paginate(100);


        } else {

            $coupons = Coupons::where('group_id', null)
                ->orderBy('id', 'desc')
                ->paginate(100);
        }




//        dd($coupons->toArray());


        return view('shop-settings.coupons', [
            'error_log'      => $request->error_log,
            'message' => $request->message,
            'categories' => $categories,
            'products' => $products,
            'coupons' => $coupons
        ]);
    }


    public function couponStatusSave(Request $request)
    {
        $id = $request->post('id');
        $status = $request->post('enabled');
        $coupon = Coupons::find($id);
        if ($status == 1) {
            $coupon->status = 'active';
        } else {
            $coupon->status = "disable";
        }
        $coupon->save();


        return "true";
    }


    public function dataSave(Request $request)
    {
        $id = $request->post('id');
        $coupon = Coupons::find($id);
        $data = $request->post('data');
        $discount_data = $request->post('discount');
        $coupons_data = json_decode($coupon->data, true);
        $coupons_discount = json_decode($coupon->discount, true);

        foreach ($data as $k => $v) {
            $coupons_data[$k] = $v;
        }
        foreach ($discount_data as $k => $v) {
            $coupons_discount[$k] = $v;
        }
        $coupon->name = $request->post('name');
        $coupon->code = $request->post('code');
        $coupon->data = json_encode($coupons_data);
        $coupon->discount = json_encode($coupons_discount);
        $coupon->save();


        session()->flash('message', ["coupon change"]);

        return back();
    }

    public function CouponsGroups(Request $request)
    {
        $coupons = [];

        $category = Categories::where('slag', 'promo')->first();
        if (!$category) {
            dd('create category url promo');
        }

        $cat_id = $category->id;

        $products = Product::where('category_id', $cat_id)
            ->where('variables', null)->get()->keyBy('id');
        if (!$products) {
            dd('add products to promo category');
        }

        $post = $request->post();

        if ($post) {

            if (isset($post['add']) && !empty($post['discount']['value'])) {


                if (empty($post['name'])) {
                    $post['name'] = 'new group';
                }
                $new_group = new CouponsGroups();
                $new_group->name = $post['name'];
                $new_group->discount = json_encode($post['discount']);
                if (isset($post['data'])) {
                    $new_group->data = json_encode($post['data']);
                } else {
                    $new_group->data = '{"count_limit": "1"}';
                }

                $new_group->save();

                session()->flash('message', ["coupon group add"]);
            }
        }

        $coupons_grop_id = Coupons::where('group_id', '!=', null)->distinct()->pluck('group_id')->toArray();

        $groups_data = false;
        foreach ($coupons_grop_id as $id) {
            $groups_data[$id]['count'] = Coupons::where('group_id', $id)
                ->where('status', 'active')->count();
        }

        $coupons = CouponsGroups::orderBy('id', 'desc')->get();
//        dd($coupons->toArray());



        return view('shop-settings.coupons_groups', [
            'error_log' => $request->error_log,
            'message' => $request->message,
            'products' => $products,
            'groups_data' => $groups_data,
            'coupons' => $coupons
        ]);
    }

    public function CouponsGenerate(Request $request, CouponsGroups $coupon_group)
    {
        $post = $request->post();

        if (isset($post['add']) && isset($post['count'])) {
            $stop = $post['count'];

            for ($x=0; $x < $stop; $x++) {
                $coupon = new Coupons();
                $coupon->code = AppServise::generateCouponCode();
                $coupon->name = AppServise::generateCouponCode();
                $coupon->group_id = $coupon_group->id;
                $coupon->status = 'active';
                $coupon->discount = $coupon_group->discount;

                $coupon->save();
            }

            session()->flash('message', ["coupon group add $x coupons"]);
        }



        return redirect(route('coupons_groups'));

    }

    public function CouponsGroupChange(Request $request, CouponsGroups $coupon_group)
    {
        $post = $request->post();

        if(isset($post['product_id'])) {
            $post['discount']['prod_id'] = $post['product_id'];
        }

        if ($post['discount']['type_mod'] == 'PRODUCT' && empty($post['product_id']) && !empty($post['discount']['old_prod_id'])) {

            $post['discount']['prod_id'] = $post['discount']['old_prod_id'];
        }

        $coupon_group->name = $post['name'];
        $coupon_group->discount = json_encode($post['discount']);


        if (isset($post['data'])) {
            $coupon_group->data = json_encode($post['data']);
        } else {
            $coupon_group->data = '{"count_limit": "1"}';
        }

        $coupon_group->save();
        session()->flash('message', ["coupon group save"]);


        return redirect(route('coupons_groups'));
    }

    public function CouponsGroupList(Request $request, CouponsGroups $coupon_group)
    {
        $post = $request->post();

        $coupons = Coupons::where('group_id', $coupon_group->id)
            ->distinct()->pluck('code')->toArray();

        foreach ($coupons as $item)
        {
            print_r("$item<br>");
        }


    }


}
