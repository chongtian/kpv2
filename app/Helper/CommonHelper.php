<?php

namespace  App\Helper;

class CommonHelper {

    public static function get_array_value(array  $array, string $key) {
        if(array_key_exists($key, $array)){
            return $array[$key];
        } else {
            return null;
        }
    }

    public static function format_date_time($rawDateTimeString)
    {
        $d = strtotime($rawDateTimeString);
        return date('Y-m-d H:i:s', $d);
    }
}