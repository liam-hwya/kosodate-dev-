<?php

 class Manga_model extends CI_MODEL {

    public function select_manga_for_rss() {
        
        $tags_id = $this->tags_id();

        $this->db->select('D_TAGS_MANGA.tags_id');
        $this->db->select('D_TAGS.tags_name');
        $this->db->select('D_MANGA.manga_id AS id');
        $this->db->select('D_MANGA.manga_title AS title');
        $this->db->select('D_MANGA.manga_url AS link');
        $this->db->select('D_MANGA.manga_intro AS intro');
        $this->db->select('D_MANGA.manga_date AS date');
        $this->db->select('D_MEDIA_1.media_url AS img_url');
        $this->db->select('D_MANGA.manga_deleted');
        $this->db->select('D_MANGA.manga_state_code');
        $this->db->select('D_MANGAKA.mangaka_state_code ');
        $this->db->select('D_MANGAKA.mangaka_nickname AS author');
        $this->db->from('D_MANGA');
        $this->db->join('D_MANGAKA','D_MANGA.mangaka_id = D_MANGAKA.mangaka_id','left');
        $this->db->join('D_MEDIA AS D_MEDIA_1','D_MANGA.manga_icon_media_id = D_MEDIA_1.media_id','left');
        $this->db->join('D_TAGS_MANGA','D_MANGA.manga_id = D_TAGS_MANGA.manga_id','left');
        $this->db->join('D_TAGS','D_TAGS_MANGA.tags_id = D_TAGS.tags_id','left');
        $this->db->where('D_MANGA.manga_deleted',NO_DELETE_FLAG);
        $this->db->where('D_MANGA.manga_state_code',CONST_MANGA_STATE_CODE_PUBLIC);
        // $this->db->where('D_MANGA.manga_date >=',nad_jp_date());
        // $this->db->where('D_MANGA.manga_date <=',nad_jp_date('','',$end_date=true));
        $this->db->where('D_MANGA.manga_date >=','2020-07-01 0:00:00');
        $this->db->where('D_MANGA.manga_date <=','2020-07-31 23:59:59');
        $this->db->where('D_MANGAKA.mangaka_state_code',CONST_MANGAKA_STATE_CODE_SHOW);
        $this->db->where_in('D_TAGS_MANGA.tags_id',$tags_id);
        $this->db->group_by('D_TAGS_MANGA.tags_id');
        $this->db->group_by('D_MANGA.manga_id');
        $this->db->order_by('D_TAGS_MANGA.tags_id','DESC');
        $this->db->order_by('D_MANGA.manga_date','DESC');
        $this->db->limit(CONST_RSS_MANGA_ITEM_NUM);

        return $this->db->get()->result_array();
    }

    private function tags_id() {

        $result = $this->tags_like_age();

        foreach($result as $tag) {
            $tags_id[] = $tag['tags_id'];
        }

        return $tags_id;
    }

    public function tags_like_age($like_tags='歳') {

        $this->db->select('*');
        $this->db->from('D_TAGS');
        
        if($like_tags){
          $this->db->like('tags_name',$like_tags,'both');
          $this->db->where('tags_state_code',CONST_COMMON_STATE_CODE_VALID);
          $this->db->where('tags_deleted',NO_DELETE_FLAG);
        }
        
        return $this->db->get()->result_array();

    }

    public function select_manga_media($manga_id) {
        
        $this->db->select('D_MEDIA_1.media_url AS img_url');
        $this->db->from('D_MANGA');
        $this->db->join('D_MANGA_MEDIA','D_MANGA.manga_id = D_MANGA_MEDIA.manga_id','left');
        $this->db->join('D_MEDIA AS D_MEDIA_1','D_MANGA_MEDIA.media_id = D_MEDIA_1.media_id','left');
        $this->db->where('D_MANGA.manga_id',$manga_id);

        return $this->db->get()->result_array();
    }

    public function select_related_manga_for_tags($tags_condition) {

        if($tags_condition){

            $manga_result = array();
            foreach($tags_condition as $condition){
                // var_dump($condition);
                $this->db->select('D_MANGA.manga_id');
                $this->db->select('D_MANGA.manga_title');
                $this->db->select('D_MANGA.manga_url');
                $this->db->select('D_MEDIA_1.media_url AS img_url');
                $this->db->from('D_MANGA');
                $this->db->join('D_MEDIA AS D_MEDIA_1','D_MANGA.manga_icon_media_id = D_MEDIA_1.media_id','left');
                $this->db->join('D_TAGS_MANGA','D_MANGA.manga_id = D_TAGS_MANGA.manga_id','left');
                $this->db->join('D_TAGS','D_TAGS_MANGA.tags_id = D_TAGS.tags_id','left');
                $this->db->where('D_TAGS.tags_id',$condition['tags_id']);
                $this->db->order_by('D_TAGS_MANGA.tags_id','DESC');
                $this->db->order_by('D_MANGA.manga_date','DESC');
                $this->db->limit($condition['manga_limit']);

                $result[] = $this->db->get()->result_array();

            }
        }
        // var_dump($result);


        foreach($result as $row){
          
            if(count($row) != count($row,COUNT_RECURSIVE)) {

                foreach($row as $nested_row){

                    $manga_result[] = $nested_row;
                  
                }
            }else{

                $manga_result[] = $row;

            }

            
        }

        return $manga_result;

    }

 }

?>






