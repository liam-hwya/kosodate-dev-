<?php

 class d_media extends CI_MODEL {

    public function getData($limit,$col) {

        // getting data from rss_log_channel table
        $query = $this->db->query("select * from D_MANGA where manga_date >= DATE_SUB(NOW(),INTERVAL $year_ago YEAR) limit $limit");
        return $query->result_array();
        
    }

    public function getMangaRelatedData($limit,$col) {

        $query = $this->db->query("select * from ");
    }

 }

?>