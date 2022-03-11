<?php

if (!function_exists('manga_tag_sort')) {

    /**
     * 
     *
     * @access public
     * @param string Manga ID and its tags
     * @return string
     */
    function manga_tag_sort($manga_id,$manga_tags) {

        foreach($manga_tags as $key=>$manga){

            switch ($manga['tags_name']){
                
                case ONE_YEAR_OLD_TAG_AGE_NAME:
                    $tags[$key]['id'] = $manga['tags_id'];  
                    $tags[$key]['name'] = ONE_YEAR_OLD_TAG_AGE;
                    break;
                case TWO_YEAR_OLD_TAG_AGE_NAME:
                    $tags[$key]['id'] = $manga['tags_id'];
                    $tags[$key]['name'] = TWO_YEAR_OLD_TAG_AGE;
                    break;
                case THREE_YEAR_OLD_TAG_AGE_NAME:
                    $tags[$key]['id'] = $manga['tags_id'];
                    $tags[$key]['name'] = THREE_YEAR_OLD_TAG_AGE;
                    break;
                case FOUR_YEAR_OLD_TAG_AGE_NAME:
                    $tags[$key]['id'] = $manga['tags_id'];
                    $tags[$key]['name'] = FOUR_YEAR_OLD_TAG_AGE;
                    break;
                case FIVE_YEAR_OLD_TAG_AGE_NAME:
                    $tags[$key]['id'] = $manga['tags_id'];
                    $tags[$key]['name'] = FIVE_YEAR_OLD_TAG_AGE;
                    break;
                case SIX_YEAR_OLD_TAG_AGE_NAME:
                    $tags[$key]['id'] = $manga['tags_id'];
                    $tags[$key]['name'] = SIX_YEAR_OLD_TAG_AGE;
                    break;
                default:
                    $tags[] = null;
                    break;
                  
              }
            
        }

        // Sort the age order
        usort($tags,function($original,$sorted){
            return $original['name'] > $sorted['name'];
        });

        return $tags;
        
    }
}

if (!function_exists('manga_tag_condition')) {

    /**
     * 
     *
     * @access public
     * @param string Manga id ,Manga Tags
     * @return string
     */
    function manga_tag_condition($manga_id,$tags) {

        if(count($tags) == ONE_CASE_FOR_AGE){ // Checkpoint for deciding manga limit

            $tags_condition = [
                [
                    'manga_id' => $manga_id,
                    'tags_id' => $tags[0]['id'],
                    'manga_limit' => MANGA_LIMITATION_THREE
                ]
            ];
        }
    
        if(count($tags) == TWO_CASE_FOR_AGE){
    
            $tags_condition = [
                [
                    'manga_id' => $manga_id,
                    'tags_id' => $tags[0]['id'],
                    'manga_limit' => MANGA_LIMITATION_TWO
                ],
                [
                    'manga_id' => $manga_id,
                    'tags_id' => $tags[1]['id'],
                    'manga_limit' => MANGA_LIMITATION_ONE
                ]
            ];
        }
    
        if(count($tags) >= THREE_CASE_FOR_AGE){
    
            $tags_condition = [
                [
                    'manga_id' => $manga_id,
                    'tags_id' => $tags[0]['id'],
                    'manga_limit' => MANGA_LIMITATION_ONE
                ],
                [
                    'manga_id' => $manga_id,
                    'tags_id' => $tags[1]['id'],
                    'manga_limit' => MANGA_LIMITATION_ONE
                ],
                [
                    'manga_id' => $manga_id,
                    'tags_id' => $tags[2]['id'],
                    'manga_limit' => MANGA_LIMITATION_ONE
                ]
            ];
        }
        
        return $tags_condition;
    }
}

if (!function_exists('get_related_manga')) {

    /**
     *
     *
     * @access public
     * @param string Age Manga,Tags condition
     * @return string
     */
    function get_related_manga($age_manga,$tags_condition) {

        $CI = get_instance();
        $CI->load->model('Manga_model','Manga_model');

        foreach($tags_condition as $condition) {
            for($manga_count=0;$manga_count < $condition['manga_limit'];$manga_count++){
                if($condition['manga_id'] != $age_manga[$condition['tags_id']][$manga_count]) {
                    
                    $manga_condition = [
                        'tags_id' => $condition['tags_id'],
                        'manga_id' => $age_manga[$condition['tags_id']][$manga_count]
                    ];
                    
                    $result[] = $CI->Manga_model->select_manga_by_tags_condition($manga_condition);
                }
            }
        } 
        
        return $result;
    }
}

