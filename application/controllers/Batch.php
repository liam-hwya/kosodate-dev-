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
        $this->load->model('d_manga','d_manga');

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
        var_dump($this->prepareRSS());
        var_dump($this->prepareRSS());
        

        
        // echo $channel_data['RSS-XML'];
    }

    public function prepareRSS()
    {
        //Getting D_MEDIA and D_MANGA
        // $d_media_col = $this->d_media->getData(MAX_POST_LIMIT,POST_YEARS_AGO);
        $d_manga_col = $this->d_manga->getData(MAX_POST_LIMIT,POST_YEARS_AGO);

            // Preparing XML data
            $channel_data['title'] = "「こそだてDAYS」、「こそだてDAYS｜ママ達の子育て体験談マンガ」";
            $channel_data['link'] = "https://www.kosodatedays.com/";
            $channel_data['description'] = "こそだてDAYS（こそだてデイズ）は子育てママと作る0～6歳児ママのためのWebメディアです。ママ達の子育て体験談を無料で漫画化し、赤ちゃん期から入学までに必要な育児情報を配信しています。";
            $channel_data['pubDate'] = date("D, d M Y H:i:s O");
            $channel_data['language'] = "ja";
            $channel_data['copyright'] = "©2017 EvolvedInfo. All Rights Reserved.";

            $item_data = [];
            foreach($d_manga_col as $key=>$manga_item){
                $item_data[$key]['title'] = $manga_item['manga_title'];
                $item_data[$key]['link'] = 'media_url_sample';
                $item_data[$key]['guid'] = $manga_item['manga_id'];
                $item_data[$key]['category'] = "<![CDATA[ママコマ漫画]]>";
                $item_data[$key]['description'] = "こそだてDAYS（こそだてデイズ）は子育てママと作る0～6歳児ママのためのWebメディアです。ママ達の子育て体験談を無料で漫画化し、赤ちゃん期から入学までに必要な育児情報を配信しています。author_name_sample～「".$manga_item['manga_title']."」をお楽しみください。";
                $item_data[$key]['pubDate'] = date("D, d M Y H:i:s O");
                $item_data[$key]['modifiedDate'] = null;
                $item_data[$key]['encoded'] = "encoded_sample";
                $item_data[$key]['delete'] = false;
                $item_data[$key]['enclosure'] = 'media-sample-enclosure';
                $item_data[$key]['thumbnail'] = 'media-sample-enclosure';
                $item_data[$key]['related'] = "sample";
            }
            $xml = new SimpleXMLElement('<channel/>');
            
            $new_channel_data = array_flip($channel_data);
            array_walk_recursive($new_channel_data,[$xml,'addChild']);
            // $channel_data['RSS-XML'].='<channel>';
            // $channel_data['RSS-XML'].='<title>'.$channel_data['title'].'</title>';
            // $channel_data['RSS-XML'].='<link>'.$channel_data['link'].'</link>';
            // $channel_data['RSS-XML'].='<description>'.$channel_data['description'].'</description>';
            // $channel_data['RSS-XML'].='<pubDate>'.$channel_data['pubDate'].'</pubDate>';
            // $channel_data['RSS-XML'].='<language>'.$channel_data['language'].'</language>';
            // $channel_data['RSS-XML'].='<copyright>'.$channel_data['copyright'].'</copyright>';
            // foreach($item_data as $item)
            // {
            //   $channel_data['RSS-XML'].='<item>';
            //     $channel_data['RSS-XML'].='<title>'.$item['title'].'</title>';
            //     $channel_data['RSS-XML'].='<link>media sample link</link>';
            //     $channel_data['RSS-XML'].='<guid>'.$item['guid'].'</guid>';
            //     $channel_data['RSS-XML'].='<category>'.$item['category'].'</category>';
            //     $channel_data['RSS-XML'].='<description>'.$item['description'] .'</description>';
            //     $channel_data['RSS-XML'].='<pubDate>'.$item['pubDate'] .'</pubDate>';
            //     $channel_data['RSS-XML'].='<modifiedDate>'.$item['modifiedDate'] .'</modifiedDate>';
            //     $channel_data['RSS-XML'].='<encoded>'.$item['encoded'] .'</encoded>';
            //     $channel_data['RSS-XML'].='<delete>'.$item['delete'] .'</delete>';
            //     $channel_data['RSS-XML'].='<enclosure>'.$item['enclosure'] .'</enclosure>';
            //     $channel_data['RSS-XML'].='<thumbnail>'.$item['thumbnail'] .'</thumbnail>';
            //     $channel_data['RSS-XML'].='<related>'.$item['related'] .'</related>';
            //   $channel_data['RSS-XML'].='</item>';
            // }
            // $channel_data['RSS-XML'].='</channel>';
            var_dump($xml->asXML());die;
        return [
            'channel_data' => $channel_data,
            'item_data' => $item_data
        ];

    }
}
