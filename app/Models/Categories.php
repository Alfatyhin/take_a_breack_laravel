<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Categories extends Model
{
    use HasFactory;

    public function addProductId($product_id)
    {
        $products = json_decode($this->products, true);
        if (!empty($products)) {
            if (!in_array($product_id, $products)) {
                $products[] = $product_id;
                $products = array_slice($products, 0);
                $this->products = json_encode($products);
                $this->save();
            }
        } else {
            $products[] = $product_id;
            $this->products = json_encode($products);
            $this->save();
        }

        return true;
    }

    public function deleteProductId($product_id)
    {
        $products = json_decode($this->products, true);
        if (!empty($products)) {
            if (in_array($product_id, $products)) {
                $key = array_search($product_id, $products);
                unset($products[$key]);
                $products = array_slice($products, 0);
                $this->products = json_encode($products);
                $this->save();
            }
        }


        return true;
    }
}
