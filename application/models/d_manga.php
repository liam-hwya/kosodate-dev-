<?php

 class d_manga extends CI_MODEL {

    public function getData($limit,$year_ago) {

        // getting data from rss_log_channel table
        $query = $this->db->query("select * from D_MANGA where manga_date >= DATE_SUB(NOW(),INTERVAL $year_ago YEAR) limit $limit");

        $data = [];
        foreach($query->result_array() as $row){
            if(date('m',strtotime($row['manga_date']))==04){
                $data[] = $row;
            }
        }
        
        if(count($data) > 0){
            return $data;
        }else{
            return null;
        }
    }

 }

?>