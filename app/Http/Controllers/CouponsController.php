<?php

namespace App\Http\Controllers;

use App\Models\Coupons;
use Illuminate\Http\Request;

class CouponsController extends Controller
{

    public function couponsDiscounts(Request $request)
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


        $coupons = Coupons::orderBy('id', 'desc')->get();


        return view('shop-settings.coupons', [
            'error_log'      => $request->error_log,
            'message' => $request->message,
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
}
