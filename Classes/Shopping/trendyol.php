<?php

/**
 * 
 * @author 237mrt <237mrtt@gmail.com>
 * @version 1.0.0
 * @license MIT
 * @link https://github.com/237mrt
 * 
 */

namespace Mrt237\Classes\Get\Shopping;


use DOMDocument;
use DOMXPath;

class Trendyol
{

    /** Variables */
    private static $url;
    private static $response = array(
        "author" => [
            'message' => 'Bu api 237mrt tarafından geliştirilmiştir.',
            "instagram" => "https://instagram.com/mertjson",
            "github" => "https://github.com/237mrt",
            'version' => '1.0.0'
        ]
    );


    private static $price = null;


    public static function product($url)
    {
        self::$url = $url;
        return new self;
    }

    private static function editinfo($info)
    {
        $info = explode(',', $info);
        return ($info);
    }

    private static function request()
    {
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, self::$url);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_USERAGENT, 'Mozilla/5.0 (Windows NT 6.1; WOW64; rv:40.0) Gecko/20100101 Firefox/40.1');
        $result = curl_exec($ch);
        curl_close($ch);

        $doc = new DOMDocument();
        @$doc->loadHTML($result);
        $xpath = new DOMXPath($doc);

        /** Price */
        $trendPrice = $xpath->query('//*[@id="product-detail-app"]/div/div[2]/div[1]/div[2]/div[2]/div[2]/div/div/div[4]/div/div/span')->item(0)->nodeValue;
        $name = $xpath->query('//*[@id="product-detail-app"]/div/div[2]/div[1]/div[2]/div[2]/div[2]/div/div/div[1]/h1/span')->item(0)->nodeValue;
        $info = $xpath->query('//*[@id="product-detail-app"]/div/section/div/div/div/ul')->item(0)->nodeValue;


        $price = explode(' ', $trendPrice);
        $price = str_replace(',', '.', $price[0]);

        $info = self::editinfo(utf8_decode($info));

        self::$price = (float)  $price;

        self::$response['data'] = [
            'title' => utf8_decode($name),
            'price' => $trendPrice,
            'category' =>  utf8_decode($xpath->query('//*[@id="marketing-product-detail-breadcrumb"]/div/a[2]/span')->item(0)->nodeValue . " > " . $xpath->query('//*[@id="marketing-product-detail-breadcrumb"]/div/a[3]/span')->item(0)->nodeValue),
            'productInfo' => $info,
        ];
        return new self;
    }




    public static function get()
    {   
        self::request();
        print_r(json_encode(self::$response));
    }
}
