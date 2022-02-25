<?php

 class RSS_log_item_model extends CI_MODEL {

    public function create_rss($data,$channel_id) {
        foreach($data as $rss_item){

            $rss_item['log_channel_id'] = $channel_id;
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

    public function select_items_encoded_by_channel_id($channel_id) {

        $this->db->select('encoded');
        $this->db->from('RSS_LOG_ITEM');
        $this->db->where('log_channel_id',$channel_id);
        $this->db->order_by('pub_date');

        return $this->db->get()->result_array();
    }

    public function update_items_encoded($channel_id,$update_data,$original_data) {
        // var_dump($channel_id);
        // var_dump($update_data);
        // var_dump($original_data);die();
        $this->db->where('log_channel_id',$channel_id);
        $this->db->where('log_item_id',$original_data['log_item_id']);
        $this->db->update('RSS_LOG_ITEM',$update_data);

        return true;
    }

    public function select_modified_date($channel_id) {

        $this->db->select('modified_date');
        $this->db->from('RSS_LOG_ITEM');
        $this->db->where('log_channel_id',$channel_id);
        
        return $this->db->get()->result_array();
    }

    
 }