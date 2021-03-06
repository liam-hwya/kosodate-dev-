<?php

 class Manga_model extends CI_MODEL {

    public function select_manga($manga=null) {

        $this->db->select('D_TAGS_MANGA.tags_id');
        $this->db->select('D_TAGS.tags_name');
        $this->db->select('D_MANGA.manga_id AS id');
        $this->db->select('D_MANGA.manga_title AS title');
        $this->db->select('D_MANGA.manga_url AS link');
        $this->db->select('D_MANGA.manga_intro AS intro');
        $this->db->select('D_MANGA.manga_date AS date');
        $this->db->select('D_MANGA.manga_detail AS detail');
        $this->db->select('D_MANGA.story_name');
        $this->db->select('D_MANGA.story_mama_year_old');
        $this->db->select('D_MANGA.story_childs_year_old');
        $this->db->select('D_MEDIA_1.media_url AS img_url');
        $this->db->select('D_MANGA.manga_deleted');
        $this->db->select('D_MANGA.manga_state_code');
        $this->db->select('D_MANGAKA.mangaka_state_code ');
        $this->db->select('D_MANGAKA.mangaka_nickname AS author');
        $this->db->select('D_MANGAKA.mangaka_icon_url');
        $this->db->from('D_MANGA');
        $this->db->join('D_MANGAKA','D_MANGA.mangaka_id = D_MANGAKA.mangaka_id','left');
        $this->db->join('D_MEDIA AS D_MEDIA_1','D_MANGA.manga_icon_media_id = D_MEDIA_1.media_id','left');
        $this->db->join('D_TAGS_MANGA','D_MANGA.manga_id = D_TAGS_MANGA.manga_id','left');
        $this->db->join('D_TAGS','D_TAGS_MANGA.tags_id = D_TAGS.tags_id','left');
        $this->db->where('D_MANGA.manga_deleted',NO_DELETE_FLAG);
        $this->db->where('D_MANGA.manga_state_code',CONST_MANGA_STATE_CODE_PUBLIC);
        $this->db->where('D_MANGA.manga_date >=',date("Y-m-01 00:00:00",strtotime("-1 year")));
        $this->db->where('D_MANGA.manga_date <=',date("Y-m-t 23:59:59",strtotime("-1 year")));
        $this->db->where('D_MANGAKA.mangaka_state_code',CONST_MANGAKA_STATE_CODE_SHOW);
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

    public function tags_like_age($like_tags='???') {

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

    public function select_all_age_manga() {
        
        $manga_for_tags = $this->select_all_manga_for_tags(); 
        $age_manga = $this->organize_manga_for_tags($manga_for_tags);
        
        return $age_manga;
        foreach($tags_condition as $condition) {
            for($manga_count=0;$manga_count < $condition['manga_limit'];$manga_count++){
                if($condition['manga_id'] != $organized_manga[$condition['tags_id']][$manga_count]) {
                    
                    $manga_condition = [
                        'tags_id' => $condition['tags_id'],
                        'manga_id' => $organized_manga[$condition['tags_id']][$manga_count]
                    ];
                    
                    $result[] = $this->select_manga_by_tags_condition($manga_condition);
                }
            }
        } 
        
        return $result;
    }

    public function select_related_manga_for_tags($tags_condition,$new_manga = false) {
        if ($tags_condition) {
            $manga_result = [];
            foreach ($tags_condition as $condition) {
                
                $this->db->select('D_MANGA.manga_id');
                $this->db->select('D_MANGA.manga_title');
                $this->db->select('D_MEDIA_1.media_url AS img_url');
                $this->db->select('D_TAGS.tags_id');
                $this->db->from('D_MANGA');
                $this->db->join('D_MEDIA AS D_MEDIA_1', 'D_MANGA.manga_icon_media_id = D_MEDIA_1.media_id', 'left');
                $this->db->join('D_TAGS_MANGA', 'D_MANGA.manga_id = D_TAGS_MANGA.manga_id', 'left');
                $this->db->join('D_TAGS', 'D_TAGS_MANGA.tags_id = D_TAGS.tags_id', 'left');
                $this->db->where('D_TAGS.tags_id', $condition['tags_id']);
                $this->db->where('D_MANGA.manga_id !=', $condition['manga_id']);
                $this->db->group_by('D_MANGA.manga_id');
                $this->db->order_by('D_TAGS_MANGA.tags_id', 'DESC');
                $this->db->order_by('D_MANGA.manga_date', 'DESC');
                if(isset($condition['offset'])){
                    $this->db->limit($condition['manga_limit'],$condition['offset']);
                }else{
                    $this->db->limit($condition['manga_limit']);
                }
                
                if ($new_manga) {
                    $result = $this->db->get()->result_array();
                    return $result;
                }

                $result[] = $this->db->get()->result_array();
            }
        }

        $manga_result = $this->group_manga($result);

        
        $manga_result = $this->check_unique_manga($manga_result,$tags_condition);

        return $manga_result;

    }

    private function group_manga($manga_result) {

        // consolidation of the result as an array
        foreach($manga_result as $row){
          
            if(count($row) != count($row,COUNT_RECURSIVE)) {
                foreach($row as $nested_row){
                    $result[] = $nested_row;
                }
            }else{
                $result[] = $row;
            }
        }
        
        return $result;
    }
        

    public function check_unique_manga($manga_result,$tags_condition) {

        $uniq_manga_id = [];
        $offset_add = 0;
        
        // var_dump($manga_result);die();
        foreach($manga_result as $key=>$manga){
            

            if(!in_array($manga['manga_id'],$uniq_manga_id)){
                $uniq_manga_id[] = $manga['manga_id'];
            }else{
                $offset = $this->get_related_manga_limit($tags_condition,['tags_id' => $manga['tags_id']]);
                
                do {
                    $offset_add ++;
                    $another_manga = $this->select_related_manga_for_tags($condition = [
                                        ['manga_id'=> $manga['manga_id'],
                                        'tags_id' => $manga['tags_id'],
                                        'manga_limit' => 1,
                                        'offset' => $offset+$offset_add
                                        ]
                                    ], $new_manga = true);
                                    
                }while(in_array($another_manga[0]['manga_id'],$uniq_manga_id));
                
                unset($manga_result[$key]);
                $manga_result[$key] = $another_manga[0];
            }
        }
        
        return $manga_result;

    }

    public function select_manga_by_tags_condition($condition) {
    
        $this->db->select('D_MANGA.manga_id');
        $this->db->select('D_MANGA.manga_title');
        $this->db->select('D_MEDIA_1.media_url AS img_url');
        $this->db->select('D_TAGS.tags_id');
        $this->db->from('D_MANGA');
        $this->db->join('D_MEDIA AS D_MEDIA_1', 'D_MANGA.manga_icon_media_id = D_MEDIA_1.media_id', 'left');
        $this->db->join('D_TAGS_MANGA', 'D_MANGA.manga_id = D_TAGS_MANGA.manga_id', 'left');
        $this->db->join('D_TAGS', 'D_TAGS_MANGA.tags_id = D_TAGS.tags_id', 'left');
        $this->db->where('D_TAGS.tags_id', $condition['tags_id']);
        $this->db->where('D_MANGA.manga_id', $condition['manga_id']);
        $this->db->order_by('D_TAGS_MANGA.tags_id', 'DESC');
        $this->db->order_by('D_MANGA.manga_date', 'DESC');

        return $this->db->get()->row_array();
    }

    private function organize_manga_for_tags($manga_for_tags) {

        $organized_manga = [];
        foreach($manga_for_tags as $key=>$manga){
            $organized_manga[$manga['tags_id']][] = $manga['manga_id']; 
        }
        
        return $organized_manga;
    }

    private function select_all_manga_for_tags() {

        $tags_id = $this->tags_id();

        $this->db->select('D_TAGS.tags_id');
        $this->db->select('D_TAGS.tags_name');
        $this->db->select('D_MANGA.manga_id');
        $this->db->from('D_MANGA');
        $this->db->join('D_MANGAKA','D_MANGA.mangaka_id = D_MANGAKA.mangaka_id','left');
        $this->db->join('D_TAGS_MANGA', 'D_MANGA.manga_id = D_TAGS_MANGA.manga_id', 'left');
        $this->db->join('D_TAGS', 'D_TAGS_MANGA.tags_id = D_TAGS.tags_id', 'left');
        $this->db->where('D_MANGA.manga_deleted',NO_DELETE_FLAG);
        $this->db->where('D_MANGA.manga_state_code',CONST_MANGA_STATE_CODE_PUBLIC);
        $this->db->where('D_MANGAKA.mangaka_state_code',CONST_MANGAKA_STATE_CODE_SHOW);
        $this->db->where_in('D_TAGS_MANGA.tags_id',$tags_id);
        $this->db->order_by('D_TAGS.tags_id', 'DESC');
        
        return $this->db->get()->result_array();
    }

    public function search_manga($manga) {
        
        if(empty($manga)){
            return NULL;
        }

        $this->db->like('manga_title',$manga);
        $this->db->join('D_MANGAKA','D_MANGA.mangaka_id = D_MANGAKA.mangaka_id','left');
        $this->db->join('D_MEDIA AS D_MEDIA_1','D_MANGA.manga_icon_media_id = D_MEDIA_1.media_id','left');
        $this->db->where('D_MANGA.manga_deleted',NO_DELETE_FLAG);
        $this->db->where('D_MANGA.manga_state_code',CONST_MANGA_STATE_CODE_PUBLIC);
        $this->db->where('D_MANGAKA.mangaka_state_code',CONST_MANGAKA_STATE_CODE_SHOW);
        $this->db->group_by('D_MANGA.manga_id');
        $this->db->order_by('D_MANGA.manga_date','DESC');
        $this->db->limit(CONST_RSS_MANGA_ITEM_NUM);

        $sql = $this->db->get('D_MANGA');

        return $sql->result_array();
    }

    public function select_manga_detail($manga_id) {

        $this->db->select('D_MANGA.manga_id AS id');
        $this->db->select('D_MANGA.manga_title AS title');
        $this->db->select('D_MANGA.manga_url AS link');
        $this->db->select('D_MANGA.manga_intro AS intro');
        $this->db->select('D_MANGA.manga_date AS date');
        $this->db->select('D_MANGA.manga_detail AS detail');
        $this->db->select('D_MANGA.story_name');
        $this->db->select('D_MANGA.story_mama_year_old');
        $this->db->select('D_MANGA.story_childs_year_old');
        $this->db->select('D_MANGA.manga_deleted');
        $this->db->select('D_MANGA.manga_state_code');
        $this->db->select('D_MANGAKA.mangaka_nickname AS author');
        $this->db->select('D_MANGAKA.mangaka_icon_url');
        $this->db->from('d_manga');
        $this->db->join('D_MANGAKA','D_MANGA.mangaka_id = D_MANGAKA.mangaka_id','left');
        $this->db->where('D_MANGA.manga_deleted',NO_DELETE_FLAG);
        $this->db->where('D_MANGA.manga_state_code',CONST_MANGA_STATE_CODE_PUBLIC);
        $this->db->where('D_MANGAKA.mangaka_state_code',CONST_MANGAKA_STATE_CODE_SHOW);
        $this->db->where('manga_id',$manga_id);

        return $this->db->get()->row_array();
    }

    public function select_tags_for_manga($manga_id) {

        $tags_id = $this->tags_id();

        $this->db->select('D_TAGS_MANGA.tags_id');
        $this->db->select('D_TAGS.tags_name');
        $this->db->from('D_MANGA');
        $this->db->join('D_TAGS_MANGA','D_MANGA.manga_id = D_TAGS_MANGA.manga_id','left');
        $this->db->join('D_TAGS','D_TAGS_MANGA.tags_id = D_TAGS.tags_id','left');
        $this->db->where('D_MANGA.manga_id',$manga_id);
        $this->db->where_in('D_TAGS_MANGA.tags_id',$tags_id);
        $this->db->group_by('D_TAGS_MANGA.tags_id');
        $this->db->order_by('D_TAGS_MANGA.tags_id','DESC');

        return $this->db->get()->result_array();
    }

    public function select_mangaka_for_manga($manga_id) {

        $this->db->select('D_MANGAKA.mangaka_nickname AS author');
        $this->db->from('D_MANGA');
        $this->db->join('D_MANGAKA','D_MANGA.mangaka_id = D_MANGAKA.mangaka_id','left');
        $this->db->where('D_MANGAKA.mangaka_state_code',CONST_MANGAKA_STATE_CODE_SHOW);
        $this->db->where('D_MANGA.manga_id',$manga_id);

        return $this->db->get()->row()->author;

    }

 }

?>






