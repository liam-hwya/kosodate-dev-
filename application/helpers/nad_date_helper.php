<?php

if (!function_exists('nad_jp_date')) {

    /**
     * 渡された時間(utc)を日本時間に変換します
     *
     * @access public
     * @param string Date関数のフォーマット文字列
     * @return string
     */
    function nad_jp_date($date = '', $format = 'Y-m-d H:i:s', $end_date = false) {

        if (empty($date)) {
            $date = NULL;
        }
        $timezone = 'Asia/Tokyo';

        $t = new DateTime($date);
        $t->setTimezone(new DateTimeZone($timezone));

        if($end_date) {
            return $t->format('Y-m-t 23:59:59');
        }else{
            return $t->format($format);
        }
        
    }
}