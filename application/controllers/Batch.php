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
        $this->load->model('d_media','d_media');
        $this->load->model('d_manga','Manga_model');

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
        $d_manga_col = $this->Manga_model->select_manga_rss();
        
            // Preparing XML data
            $channel_data['title'] = "こそだてDAYS";
            $channel_data['link'] = "https://www.kosodatedays.com/";
            $channel_data['description'] = "こそだてDAYS（こそだてデイズ）は子育てママと作る0～6歳児ママのためのWebメディアです。ママ達の子育て体験談を無料で漫画化し、赤ちゃん期から入学までに必要な育児情報を配信しています。";
            $channel_data['pubDate'] = nad_jp_date();
            $channel_data['language'] = "ja";
            $channel_data['copyright'] = "©2017 EvolvedInfo. All Rights Reserved.";

            $item_data = [];
            foreach($d_manga_col as $key=>$manga_item){
                $item_data[$key]['title'] = $manga_item['title'];
                $item_data[$key]['link'] = $manga_item['img_url'];
                $item_data[$key]['guid'] = $manga_item['id'];
                $item_data[$key]['category'] = htmlspecialchars('<![CDATA[ママコマ漫画]]>');
                $item_data[$key]['description'] = "こそだてDAYS（こそだてデイズ）は子育てママと作る0～6歳児ママのためのWebメディアです。ママ達の子育て体験談を無料で漫画化し、赤ちゃん期から入学までに必要な育児情報を配信しています。".$manga_item['author']."～「".$manga_item['title']."」をお楽しみください。";
                $item_data[$key]['pubDate'] = nad_jp_date();
                $item_data[$key]['modifiedDate'] = null;
                $item_data[$key]['encoded'] = htmlspecialchars('<![CDATA[><h2>'.$manga_item['title'].'</h2><img src="'.$manga_item['img_url'].'"/>]]');
                $item_data[$key]['delete'] = $manga_item['manga_deleted'];
                $item_data[$key]['enclosure'] = $manga_item['img_url'];
                $item_data[$key]['thumbnail'] = $manga_item['img_url'];
                $item_data[$key]['related'] = "sample";
            }

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
                $xml.=htmlspecialchars('<related>'.$item['related'] .'</related>');
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
