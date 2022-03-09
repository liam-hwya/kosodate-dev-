<?php

if (!function_exists('get_channel_id')) {

    /**
     * 
     *
     * @access public
     * @param object $channel 
     * @return string
     */
    function get_channel_id($channel) {

        return $channel->logid;
    }
}
if (!function_exists('get_channel_modified_date')) {

    /**
     * 
     *
     * @access public
     * @param object $channel 
     * @return string
     */
    function get_channel_modified_date($channel) {

        return $channel->last_time2;
    }
}
