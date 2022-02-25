<?php

 class RSS_insta_model extends CI_MODEL {

    public function insert_insta($data) {
        
        $this->db->insert('RSS_INSTA',$data);

        return true;

    }
 }