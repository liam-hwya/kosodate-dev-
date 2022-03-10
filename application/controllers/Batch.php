<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Batch extends CI_Controller {

    /**
     * Running this script from shell
     * 
     *
     */


    public function __construct() {

        parent::__construct();

        $this->load->helper('xml');
        $this->load->helper('text');
        $this->load->model('Manga_model','Manga_model');
        $this->load->model('RSS_log_channel_model','RSS_log_channel_model');
        $this->load->model('RSS_log_item_model','RSS_log_item_model');
        $this->load->model('RSS_insta_model','RSS_insta_model');
    }

    public function index()
    {
        // there is no action here yet
    }

    public function insertRSS()
    {
        $rss_data = $this->prepareRSS();

        if($rss_data == null){
          
            echo 'There is no manga for last year ago of this month';

        }else{

            $channel_id = $this->RSS_log_channel_model->create_rss($rss_data['channel_data']);
    
            if($this->RSS_log_item_model->create_rss($rss_data['item_data'],$channel_id)){

                echo "Inserted successfully<br>";
                echo "See the feed <a href=".RSS_FEED_LINK." >Here</a>";
            };

            if(isset($_SESSION['admin_control'])) {
                unset($_SESSION['admin_control']);
            }

        }

    }

    public function prepareRSS()
    {   
        $d_manga_col = $this->Manga_model->select_manga();
        
        if(count($d_manga_col) == 0){
          
            return null;

        }else{
            // Preparing XML  Channel data
            $channel_data['title'] = $this->config->item('rss_site_title');
            $channel_data['link'] = $this->config->item('rss_site_link');
            $channel_data['description'] = CONST_HEADER_META_DESCRIPTION;
            $channel_data['pubDate'] = nad_jp_date();
            $channel_data['language'] = "ja";
            $channel_data['copyright'] = "©2017 EvolvedInfo. All Rights Reserved.";

            //For skipping the same manga id
            $uniq_manga_id = 0;
            
            // Preparing XML item data
            foreach($d_manga_col as $key=>$manga_item){
                if($uniq_manga_id != $manga_item['id']){
                    $item_data[$key]['title'] = $manga_item['title'];
                    $item_data[$key]['link'] = MANGA_URL.$manga_item['id'];
                    $item_data[$key]['guid'] = $manga_item['id'];
                    $item_data[$key]['category'] = '<![CDATA[ママコマ漫画]]>';
                    $item_data[$key]['description'] = "こそだてDAYS（こそだてデイズ）は子育てママと作る0～6歳児ママのためのWebメディアです。ママ達の子育て体験談を無料で漫画化し、赤ちゃん期から入学までに必要な育児情報を配信しています。".$manga_item['author']."～「".$manga_item['title']."」をお楽しみください。";
                    $item_data[$key]['pubDate'] = nad_jp_date();
                    $item_data[$key]['modifiedDate'] = null;
                    $item_data[$key]['delete'] = null;
                    $item_data[$key]['enclosure'] = KOSODATE_IMG_URL.$manga_item['img_url'];
                    $item_data[$key]['thumbnail'] = KOSODATE_IMG_URL.$manga_item['img_url'];
                    $item_data[$key]['encoded'] = null;
                    $item_data[$key]['relatedlink'] = null;
                    $item_data[$key]['author'] = $manga_item['author'];
                    $item_data[$key]['story_name'] = $manga_item['story_name'];
                    $item_data[$key]['story_mama_year_old'] = $manga_item['story_mama_year_old'];
                    $item_data[$key]['story_childs_year_old'] = $manga_item['story_childs_year_old'];
                    $item_data[$key]['intro'] = $manga_item['intro'];
                    $uniq_manga_id = $manga_item['id'];
                }
                else{
                    continue;
                }
            
            }

            // Selecting instagram tags
            $insta = $this->RSS_insta_model->select_insta();
            $insta_tag = (is_null($insta)? '': $insta->tag);
            
            // Adding encoded data
            foreach($item_data as $key=>$item){
            
                $manga_img_url_col = $this->Manga_model->select_manga_media($item['guid']);

                $item_data[$key]['encoded'] = '<![CDATA[<p>体験談投稿</p><p>'.$item_data[$key]['story_name'].'</p>';

                if(!empty($item_data[$key]['story_mama_year_old'])){
                    $item_data[$key]['encoded'] .= '<p>'.$item_data[$key]['story_mama_year_old'].'</p>';
                }

                if(!empty($item_data[$key]['story_childs_year_old'])){
                    $item_data[$key]['encoded'] .= '<p>お子さん</p><p>'.$item_data[$key]['story_childs_year_old'].'</p>';
                }

                $item_data[$key]['encoded'] .= '<p>'.$item_data[$key]['title'].'</p>';          
                $item_data[$key]['encoded'] .= '<p>'.$item_data[$key]['intro'].'</p>';
                $item_data[$key]['encoded'] .= '<p>'.date('Y.m.d',strtotime($item_data[$key]['pubDate'])).'</p>';
                
                $item_data[$key]['encoded'] .= '<p>';
                foreach($manga_img_url_col as $manga_img){
                    $item_data[$key]['encoded'] .= '<img src="'.KOSODATE_IMG_URL.$manga_img['img_url'].'"/>';
                }
                $item_data[$key]['encoded'] .= '</p>';
            
                $item_data[$key]['encoded'] .= $insta_tag."]]>";
            }
            
            /**
             * 
             * Select all related manga for manga's tag 
             * [manga_id]
             * --[tags_id]
             * --[tags_name]
             * 
             */
            
            foreach($d_manga_col as $key=>$item){
                $manga_id_tags[$item['id']][$key]['tags_id'] = $item['tags_id']; 
                $manga_id_tags[$item['id']][$key]['tags_name'] = $item['tags_name'];
            }

            foreach($manga_id_tags as $manga_id=>$manga_tags){

                $tags = manga_tag_sort($manga_id,$manga_tags); //Sort the manga tags
                $tags_condition = manga_tag_condition($manga_id,$tags); //Get the condition for manga tags.
                
                unset($tags); //Clear for another manga

                $related_manga_tags_col = $this->Manga_model->select_related_manga_for_tags($tags_condition); //Manga collection of the related tag

                $item_by_manga_id[$manga_id] = $this->search_item_by_manga_id($item_data,['guid'=>$manga_id]); //Create new item data by related manga id 
                
                // Adding Related Link data
                foreach($related_manga_tags_col as $related_manga) {

                    $item_by_manga_id[$manga_id]['relatedlink'] .= '<relatedlink title="'.$related_manga['manga_title'].'" link="'.MANGA_URL.$related_manga['manga_id'].'" thumbnail="'.KOSODATE_IMG_URL.$related_manga['img_url'].'"/>';

                    //Remove unnecessary data
                    unset($item_by_manga_id[$manga_id]['author']);
                    unset($item_by_manga_id[$manga_id]['story_name']);
                    unset($item_by_manga_id[$manga_id]['story_mama_year_old']);
                    unset($item_by_manga_id[$manga_id]['story_childs_year_old']);
                    unset($item_by_manga_id[$manga_id]['intro']);
                
                }

            } 

            // Preparing RSS-XML
            $xml='<?xml version="1.0" encoding="UTF-8" ?>';
            $xml.='<channel>';
            $xml.='<title>'.$channel_data['title'].'</title>';
            $xml.='<link>'.$channel_data['link'].'</link>';
            $xml.='<description>'.$channel_data['description'].'</description>';
            $xml.='<pubDate>'.nad_jp_date($channel_data['pubDate'],$format='RFC822').'</pubDate>';
            $xml.='<language>'.$channel_data['language'].'</language>';
            $xml.='<copyright>'.$channel_data['copyright'].'</copyright>';
            foreach($item_by_manga_id as $item)
            {
              $xml.='<item>';
                $xml.='<title>'.$item['title'].'</title>';
                    $xml.='<link>'.$item['link'].'</link>';
                $xml.='<guid>'.$item['guid'].'</guid>';
                $xml.='<category>'.$item['category'].'</category>';
                $xml.='<description>'.$item['description'] .'</description>';
                $xml.='<pubDate>'.nad_jp_date($item['pubDate'],$format='RFC822').'</pubDate>';
                $xml.='<modifiedDate>'.nad_jp_date($item['modifiedDate'],$format='RFC822').'</modifiedDate>';
                $xml.='<encoded>'.$item['encoded'] .'</encoded>';
                $xml.='<delete>'.$item['delete'] .'</delete>';
                $xml.='<enclosure url="'.$item['enclosure'] .'"/>';
                $xml.='<thumbnail url="'.$item['thumbnail'] .'"/>';
                $xml.=$item['relatedlink'];
              $xml.='</item>';
            }
            $xml.='</channel>';
            $channel_data['RSS_XML'] = $xml;
            return [
                'channel_data' => $channel_data,
                'item_data' => $item_by_manga_id
            ];

        }      

    }

    private function search_item_by_manga_id($item_data,$manga_id) {

        $result = [];

        foreach($item_data as $item) {
            foreach($manga_id as $key=>$val) {
                if(!isset($item[$key]) || $item[$key] != $val) {
                    continue 2;
                }
            }
            $result[] = $item;
        }

        return $result[0]; // Return the only one manga which is matched for passing manga id
    }

    
}

