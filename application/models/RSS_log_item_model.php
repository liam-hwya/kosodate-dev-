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

    public function select_count($channel_id) {

        $this->db->select('count(*)');
        $this->db->from('RSS_LOG_ITEM');
        $this->db->where('log_channel_id',$channel_id);

        return $this->db->get()->result_array();

    }

    public function select_items($channel_id) {

        $this->db->select('*');
        $this->db->from('RSS_LOG_ITEM');
        $this->db->where('log_channel_id',$channel_id);

        return $this->db->get()->result_array();
    }

    public function select_latest_items($channel_id) {

        $this->db->select('*');
        $this->db->from('RSS_LOG_ITEM');
        $this->db->where('log_channel_id',$channel_id);
        $this->db->order_by('log_item_id','DESC');
        $this->db->limit(1);

        return $this->db->get()->row();
    }

    public function search_manga_from_rss($channel_id,$manga) {

        if(empty($manga)){
            return NULL;
        }

        $this->db->like('title',$manga);
        $this->db->where('log_channel_id',$channel_id);
        $sql = $this->db->get('RSS_LOG_ITEM');

        return $sql->result_array();


    }

    
 }