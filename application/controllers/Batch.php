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
    
            if($this->RSS_log_item_model->create_rss($rss_data['item_data'],$channel_id[0])){

                echo "Inserted successfully<br>";
                echo "See the feed <a href='http://localhost:8080/kosodate-dev/rss-mamatena'>http://localhost:8080/kosodate-dev/rss-mamatena</a>";
            };

        }

    }

    public function prepareRSS()
    {   
        $d_manga_col = $this->Manga_model->select_manga_for_rss();
            
        if(count($d_manga_col) == 0){
          
            return null;

        }else{

            // Preparing XML  Channel data
            $channel_data['title'] = "こそだてDAYS";
            $channel_data['link'] = "https://www.kosodatedays.com/";
            $channel_data['description'] = "こそだてDAYS（こそだてデイズ）は子育てママと作る0～6歳児ママのためのWebメディアです。ママ達の子育て体験談を無料で漫画化し、赤ちゃん期から入学までに必要な育児情報を配信しています。";
            $channel_data['pubDate'] = nad_jp_date();
            $channel_data['language'] = "ja";
            $channel_data['copyright'] = "©2017 EvolvedInfo. All Rights Reserved.";

            //For skipping the same manga
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
                    $item_data[$key]['delete'] = $manga_item['manga_deleted'];
                    $item_data[$key]['enclosure'] = KOSODATE_IMG_URL.$manga_item['img_url'];
                    $item_data[$key]['thumbnail'] = KOSODATE_IMG_URL.$manga_item['img_url'];
                    $item_data[$key]['encoded'] = null;
                    $item_data[$key]['relatedlink'] = null;

                    $uniq_manga_id = $manga_item['id'];
                }
                else{
                    continue;
                }
            
            }

            // Adding encoded data
            foreach($item_data as $key=>$item){
            
                $manga_img_url_col = $this->Manga_model->select_manga_media($item['guid']);
                $item_data[$key]['encoded'] = '<![CDATA[<h2>'.$item_data[$key]['title'].'</h2>';
                foreach($manga_img_url_col as $manga_img){
                    $item_data[$key]['encoded'] .= '<img src="'.KOSODATE_IMG_URL.$manga_img['img_url'].'"/>';
                }
                $item_data[$key]['encoded'] .= ']]>';
            }

            // Select all related manga for manga's tag 
            foreach($d_manga_col as $key=>$item){
                // $manga_id_tags[$key]['manga__id'] = $item['id'];
                $manga_id_tags[$item['id']][$key]['manga_tags_id'] = $item['tags_id'];
                $manga_id_tags[$item['id']][$key]['manga_tags_name'] = $item['tags_name'];
            }

            foreach($manga_id_tags as $manga_id=>$manga_item){
          
                foreach($manga_item as $manga){
            
                    switch ($manga['manga_tags_name']){
                
                      case ONE_YEAR_OLD_TAG_AGE_NAME:
                        $tags_names[] = ONE_YEAR_OLD_TAG_AGE;
                        break;
                      case TWO_YEAR_OLD_TAG_AGE_NAME:
                        $tags_names[] = TWO_YEAR_OLD_TAG_AGE;
                        break;
                      case THREE_YEAR_OLD_TAG_AGE_NAME:
                        $tags_names[] = THREE_YEAR_OLD_TAG_AGE;
                        break;
                      case FOUR_YEAR_OLD_TAG_AGE_NAME:
                        $tags_names[] = FOUR_YEAR_OLD_TAG_AGE;
                        break;
                      case FIVE_YEAR_OLD_TAG_AGE_NAME:
                        $tags_names[] = FIVE_YEAR_OLD_TAG_AGE;
                        break;
                      case SIX_YEAR_OLD_TAG_AGE_NAME:
                        $tags_names[] = SIX_YEAR_OLD_TAG_AGE;
                        break;
                      default:
                        $tags_names[] = null;
                        break;
                
                    }
                }
                
                asort($tags_names); // sorting ages from smallest to biggest

                if(count($tags_names) == ONE_CASE_FOR_AGE){ // Checkpoint for deciding manga limit

                    $tags_condition = [
                        [
                            'manga_id' => $manga_id,
                            'tags_id' => $tags_names[0],
                            'manga_limit' => MANGA_LIMITATION_THREE
                        ]
                    ];
                }

                if(count($tags_names) == TWO_CASE_FOR_AGE){

                    $tags_condition = [
                        [
                            'manga_id' => $manga_id,
                            'tags_id' => $tags_names[0],
                            'manga_limit' => MANGA_LIMITATION_TWO
                        ],
                        [
                            'manga_id' => $manga_id,
                            'tags_id' => $tags_names[1],
                            'manga_limit' => MANGA_LIMITATION_ONE
                        ]
                    ];
                }

                if(count($tags_names) >= THREE_CASE_FOR_AGE){

                    $tags_condition = [
                        [
                            'manga_id' => $manga_id,
                            'tags_id' => $tags_names[0],
                            'manga_limit' => MANGA_LIMITATION_ONE
                        ],
                        [
                            'manga_id' => $manga_id,
                            'tags_id' => $tags_names[1],
                            'manga_limit' => MANGA_LIMITATION_ONE
                        ],
                        [
                            'manga_id' => $manga_id,
                            'tags_id' => $tags_names[2],
                            'manga_limit' => MANGA_LIMITATION_ONE
                        ]
                    ];
                }

                unset($tags_names); //Clear for another manga
                
                $related_manga_tags_col = $this->Manga_model->select_related_manga_for_tags($tags_condition); //Manga collection of the related tag
            
                $item_by_manga_id[$manga_id] = $this->search_item_by_manga_id($item_data,['guid'=>$manga_id]); //Create new item data by related manga id 
                
                // Adding Related Link data
                foreach($related_manga_tags_col as $key=>$related_manga) {

                    $item_by_manga_id[$manga_id]['relatedlink'] .= '<relatedlink title="'.$related_manga['manga_title'].'" link="'.MANGA_URL.$related_manga['manga_id'].'" thumbnail="'.KOSODATE_IMG_URL.$item_by_manga_id[$manga_id]['thumbnail'].'"/>';
                
                }

            } 
        
            // Preparing RSS-XML
            $xml='<?xml version="1.0" encoding="UTF-8" ?>';
            $xml.='<channel>';
            $xml.='<title>'.$channel_data['title'].'</title>';
            $xml.='<link>'.$channel_data['link'].'</link>';
            $xml.='<description>'.$channel_data['description'].'</description>';
            $xml.='<pubDate>'.$channel_data['pubDate'].'</pubDate>';
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
                $xml.='<pubDate>'.$item['pubDate'] .'</pubDate>';
                $xml.='<modifiedDate>'.$item['modifiedDate'] .'</modifiedDate>';
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
                'item_data' => $item_data
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

        echo "<h1>Manga item search by id</h1>"; var_dump($result);

        return $result[0]; // Return the only one manga which is matched for passing manga id
    }
    
}

