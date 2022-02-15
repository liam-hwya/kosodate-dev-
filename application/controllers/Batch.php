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

        define('MAX_POST_LIMIT',30);
        define('POST_YEARS_AGO',1);
    }

    public function index()
    {
        // there is no action here yet
    }

    public function insertRSS()
    {
        echo "<pre>";
        $this->prepareRSS();

        
        // echo $xml;
    }

    public function prepareRSS()
    {   
        $d_manga_col = $this->Manga_model->select_manga_for_rss();

            // Preparing XML  Channel data
            $channel_data['title'] = "こそだてDAYS";
            $channel_data['link'] = "https://www.kosodatedays.com/";
            $channel_data['description'] = "こそだてDAYS（こそだてデイズ）は子育てママと作る0～6歳児ママのためのWebメディアです。ママ達の子育て体験談を無料で漫画化し、赤ちゃん期から入学までに必要な育児情報を配信しています。";
            $channel_data['pubDate'] = nad_jp_date();
            $channel_data['language'] = "ja";
            $channel_data['copyright'] = "©2017 EvolvedInfo. All Rights Reserved.";

            // Preparing XML item data
            $item_data = [];
            $manga_id_start = true;
            foreach($d_manga_col as $key=>$manga_item){
                $item_data[$key]['title'] = $manga_item['title'];
                $item_data[$key]['link'] = $manga_item['link'];
                $item_data[$key]['guid'] = $manga_item['id'];
                $item_data[$key]['tags_id'] = $manga_item['tags_id'];
                $item_data[$key]['category'] = htmlspecialchars('<![CDATA[ママコマ漫画]]>');
                $item_data[$key]['description'] = "こそだてDAYS（こそだてデイズ）は子育てママと作る0～6歳児ママのためのWebメディアです。ママ達の子育て体験談を無料で漫画化し、赤ちゃん期から入学までに必要な育児情報を配信しています。".$manga_item['author']."～「".$manga_item['title']."」をお楽しみください。";
                $item_data[$key]['pubDate'] = nad_jp_date();
                $item_data[$key]['modifiedDate'] = null;
                $item_data[$key]['delete'] = $manga_item['manga_deleted'];
                $item_data[$key]['enclosure'] = $manga_item['img_url'];
                $item_data[$key]['thumbnail'] = $manga_item['img_url'];
                $item_data[$key]['relatedlink'] = null;
            }
            
            // Select all image url for manga
            for($manga_counter = 0; $manga_counter<count($item_data);$manga_counter++){

                $manga_img_url_col = $this->Manga_model->select_manga_media($item_data[$manga_counter]['guid']);
                $item_data[$manga_counter]['encoded'] = htmlspecialchars('<![CDATA[<h2>').$item_data[$manga_counter]['title'].htmlspecialchars('</h2>');
                foreach($manga_img_url_col as $manga_img){
                    $item_data[$manga_counter]['encoded'] .= htmlspecialchars('<img src="').$manga_img['img_url'].htmlspecialchars('"/>');
                }
                $item_data[$manga_counter]['encoded'] .= htmlspecialchars(']]>');

            }

            // Select all related mangas for manga's tag 
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

                if(count($tags_names) == 1){ // Checkpoint for deciding manga limit

                    $tags_condition = [
                        [
                            'manga_id' => $manga_id,
                            'tags_id' => $tags_names[0],
                            'manga_limit' => 3
                        ]
                    ];
                }

                if(count($tags_names) == 2){

                    $tags_condition = [
                        [
                            'manga_id' => $manga_id,
                            'tags_id' => $tags_names[0],
                            'manga_limit' => 2
                        ],
                        [
                            'manga_id' => $manga_id,
                            'tags_id' => $tags_name[1],
                            'manga_limit' => 1
                        ]
                    ];
                }

                if(count($tags_names) >= 3){

                    $tags_condition = [
                        [
                            'manga_id' => $manga_id,
                            'tags_id' => $tags_names[0],
                            'manga_limit' => 1
                        ],
                        [
                            'manga_id' => $manga_id,
                            'tags_id' => $tags_names[1],
                            'manga_limit' => 1
                        ],
                        [
                            'manga_id' => $manga_id,
                            'tags_id' => $tags_names[2],
                            'manga_limit' => 1
                        ]
                    ];
                }
                $related_manga_tags_col = $this->Manga_model->select_related_manga_for_tags($tags_condition); //Manga collection of the related tag
                
            }
            
            // Preparing RSS item data relatedlink
            for($manga_counter = 0; $manga_counter<count($item_data);$manga_counter++){

                foreach($related_manga_tags_col as $each_item){

                    foreach($each_item as $each_item_manga){
                      
                        $item_data[$manga_counter]['relatedlink'] .= htmlspecialchars('<relatedlink title="').$each_item_manga['manga_title'].htmlspecialchars('" link="').$each_item_manga['manga_url'].htmlspecialchars('" thumbnail="').$item_data[$manga_counter]['thumbnail'].htmlspecialchars('"/>');
                    }
                  
                }
            }
            
            // Preparing RSS-XML
            $xml=htmlspecialchars('<?xml version="1.0" encoding="UTF-8" ?>');
            $xml.=htmlspecialchars('<channel>');
            $xml.=htmlspecialchars('<title>'.$channel_data['title'].'</title>');
            $xml.=htmlspecialchars('<link>'.$channel_data['link'].'</link>');
            $xml.=htmlspecialchars('<description>'.$channel_data['description'].'</description>');
            $xml.=htmlspecialchars('<pubDate>'.$channel_data['pubDate'].'</pubDate>');
            $xml.=htmlspecialchars('<language>'.$channel_data['language'].'</language>');
            $xml.=htmlspecialchars('<copyright>'.$channel_data['copyright'].'</copyright>');
            foreach($item_data as $item)
            {
              $xml.=htmlspecialchars('<item>');
                $xml.=htmlspecialchars('<title>'.$item['title'].'</title>');
                $xml.=htmlspecialchars('<link>'.$item['link'].'</link>');
                $xml.=htmlspecialchars('<guid>'.$item['guid'].'</guid>');
                $xml.=htmlspecialchars('<category>').$item['category'].htmlspecialchars('</category>');
                $xml.=htmlspecialchars('<description>'.$item['description'] .'</description>');
                $xml.=htmlspecialchars('<pubDate>'.$item['pubDate'] .'</pubDate>');
                $xml.=htmlspecialchars('<modifiedDate>'.$item['modifiedDate'] .'</modifiedDate>');
                $xml.=htmlspecialchars('<encoded>').$item['encoded'] .htmlspecialchars('</encoded>');
                $xml.=htmlspecialchars('<delete>'.$item['delete'] .'</delete>');
                $xml.=htmlspecialchars('<enclosure>'.$item['enclosure'] .'</enclosure>');
                $xml.=htmlspecialchars('<thumbnail>'.$item['thumbnail'] .'</thumbnail>');
                $xml.=htmlspecialchars('<related>'.$item['relatedlink'] .'</related>');
              $xml.=htmlspecialchars('</item>');
            }
            $xml.=htmlspecialchars('</channel>');
            $channel_data['RSS-XML'] = $xml;

            var_dump($channel_data);
            var_dump($item_data);die();
        return [
            'channel_data' => $channel_data,
            'item_data' => $item_data
        ];

    }
}
