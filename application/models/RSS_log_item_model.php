<?php

 class RSS_log_item_model extends CI_MODEL {

    public function create_rss($data,$channel_id) {
        echo "<pre>";
        // var_dump($data[0]);die();
        foreach($data as $rss_item){

            $rss_item['log_channel_id'] = $channel_id;

            $this->db->insert('RSS_lOG_ITEM',$rss_item);
          
        }

        return true;

        
    }

    public function select_rss() {

        return $this->db->get('RSS_lOG_ITEM')->result_array();
    }


    
 }

?>