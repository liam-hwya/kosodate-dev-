<?php

 class RSS_log_item_model extends CI_MODEL {

    public function create_rss($data,$channel_id) {
        
        foreach($data as $rss_item){

            $rss_item['log_channel_id'] = $channel_id; //insert channel id
            $this->db->insert('RSS_LOG_ITEM',$rss_item);  
        }
        return true;
    }

    public function select_items_by_channel_id($channel_id) {

        $this->db->select('*');
        $this->db->from('RSS_LOG_ITEM');
        $this->db->where('log_channel_id',$channel_id);

        return $this->db->get()->result_array();
    }

    public function select_modified_date($channel_id) {

        $this->db->select('modified_date');
        $this->db->from('RSS_LOG_ITEM');
        $this->db->where('log_channel_id',$channel_id);
        
        return $this->db->get()->result_array();
    }

    
 }