<?php

namespace App\Libraries;

use App\Models\InsalesProducts;
use Insales\Library\Insales as ApiInsales;

class GetInsalesProduct
{
    public function getProducts()
    {
        InsalesProducts::truncate();
        $url = "https://323f9baffa77dfd407d9ee42731e3d7a:14aab7f0d7a89a0c0aa52dd4d54fc29c@dimaestri.com";
        $client = new ApiInsales($url);
        $products_count = $client->getProductsCount()->getResponse();
        if ($products_count['data'] != null) {

            $pages_count = intdiv($products_count['data']['count'], 250);

            for ($i = 1; $i <= $pages_count + 1; $i++) {
                $product = $client->getProducts(['per_page' => 250, "page" => $i])->getResponse();
                $products = \SplFixedArray::fromArray($product['data']);
                $iterator_products = $products->getIterator();
                while ($iterator_products->valid()) {

                    $variant_id=$iterator_products->current()['variants'][0]['id'];
                    $title = $iterator_products->current()['title'];
                    $product_id = $iterator_products->current()['variants'][0]['product_id'];
                    $article = $iterator_products->current()['variants'][0]['sku'];
                    $price = $iterator_products->current()['variants'][0]['price'];

                    if (InsalesProducts::where('article', $article)->first() == null) {
                        InsalesProducts::create([
                            "article" => $article,
                            "product_id" => $product_id,
                            "title" => $title,
                            "price" => $price,
                            "variant_id"=>$variant_id
                        ]);
                    }
                    $iterator_products->next();
                }
            }
        }
    }
}
