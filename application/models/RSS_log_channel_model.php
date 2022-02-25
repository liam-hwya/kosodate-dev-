<?php

 class RSS_log_channel_model extends CI_MODEL {

    public function create_rss($data) {

        $this->db->insert('RSS_LOG_CHANNEL',$data);

        $channel_id = $this->select_latest_channel_id();

        return $channel_id;
    }

    public function select_latest_channel() {

        $this->db->select('*');
        $this->db->from('RSS_LOG_CHANNEL');
        $this->db->order_by('logid','DESC');
        $this->db->limit(1);
        return $this->db->get()->row();
    }

    public function select_latest_channel_id() {

        $this->db->select('logid');
        $this->db->from('RSS_LOG_CHANNEL');
        $this->db->order_by('logid','DESC');
        $this->db->limit(1);

        $result = $this->db->get()->row();

        return $result->logid;
    }

    public function select_modified_date($channel_id) {

        $this->db->select('last_time2');
        $this->db->from('RSS_LOG_CHANNEL');
        $this->db->where('logid',$channel_id);

        return $this->db->get()->row();

    }

    public function update_channel($channel_id,$update_data) {

        $this->db->where('logid',$channel_id);
        $this->db->update('RSS_LOG_CHANNEL',$update_data);

        return true;

    }

    
 }

?>