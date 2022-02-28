<?php

 class RSS_insta_model extends CI_MODEL {

    public function insert_insta($data) {
        
        $this->db->insert('RSS_INSTA',$data);

        return true;

    }

    public function select_insta() {

        $this->db->select('tag');
        $this->db->from('RSS_INSTA');
        $this->db->order_by('insta_id','DESC');
        $this->db->limit(1);

        return $this->db->get()->row();
    }
 }