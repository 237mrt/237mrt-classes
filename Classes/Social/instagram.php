<?php

/**
 * 
 * @author 237mrt <237mrtt@gmail.com>
 * @version 1.0.0
 * @license MIT
 * @link https://github.com/237mrt
 * 
 */

namespace Mrt237\Classes\Get\Social;


class Instagram
{

    /** Variables */
    private static $username;
    private static $photo = "";
    private static $followers = 0;
    private static $postCount = 0;
    private static $posts = [];
    private static $results;
    private static $response;
    private static $array = array(
        "author" => [
            'message' => 'Bu api 237mrt tarafından geliştirilmiştir.',
            "instagram" => "https://instagram.com/mertjson",
            "github" => "https://github.com/237mrt",
            'version' => '1.0.0'
        ]
    );




    /** Header Settings */
    private static function header()
    {
        header("Content-Type: application/json; utf-8;");
    }


    /** Get Username */
    public static function username($username)
    {

        if ($username) {
            $ch = curl_init();
            curl_setopt_array($ch, [
                CURLOPT_URL => 'https://www.instagram.com/' . $username . '/embed/',
                CURLOPT_USERAGENT => 'Mozilla/5.0 (Linux; Android 6.0.1; SM-G935S Build/MMB29K; wv) AppleWebKit/537.36 (KHTML, like Gecko) Version/4.0 Chrome/55.0.2883.91 Mobile Safari/537.36',
                CURLOPT_RETURNTRANSFER => true
            ]);
            $response = curl_exec($ch);
            curl_close($ch);

            $regex = '@\\\"owner\\\":{\\\"id\\\":\\\"([0-9]+)\\\",\\\"profile_pic_url\\\":\\\"(.*?)\\\",\\\"username\\\":\\\"(.*?)\\\",\\\"followed_by_viewer\\\":(true|false),\\\"has_public_story\\\":(true|false),\\\"is_private\\\":(true|false),\\\"is_unpublished\\\":(true|false),\\\"is_verified\\\":(true|false),\\\"edge_followed_by\\\":{\\\"count\\\":([0-9]+)},\\\"edge_owner_to_timeline_media\\\":{\\\"count\\\":([0-9]+)@';
            preg_match($regex, $response, $result);

            self::$username = $username;
            self::$results = $result;
            self::$response = $response;
        } else {
            self::$array['data'] = [
                'success' => false,
                'message' => 'Kullanıcı adı girilmedi!'
            ];
            exit;
        }

        return new self;
    }


    /** Get Photo */
    public static function photo()
    {
        if (isset(self::$results[2])) {
            self::$photo = str_replace('\\\\\\', '', self::$results[2]);
        }

        return new self;
    }

    /** Get Followers */
    public static function followers()
    {
        if (isset(self::$results[9])) {
            self::$followers = self::$results[9];
        }
        return new self;
    }

    /** Post Counts */
    public static function postCount()
    {
        if (isset(self::$results[10])) {
            self::$postCount = self::$results[10];
        }
        return new self;
    }

    /** Posts */
    public static function posts()
    {
        preg_match_all('@\\\"thumbnail_src\\\":\\\"(.*?)\\\"@', self::$response, $result);


        self::$posts = array_map(function ($image) {
            return str_replace('\\\\\\', '', $image);
        }, array_slice($result[1], 0, self::$postCount));

        return new self;
    }

    /** Launch Class ❤ */
    public static function get()
    {
        self::header();

        self::$array['data'] = [
            'success' => true,
            'username' => self::$username,
            'profilePhoto' => self::$photo,
            'followers' => self::$followers,
            'postCount' => self::$postCount,
            'posts' => self::$posts
        ];

        print_r(json_encode(self::$array));
    }
}
