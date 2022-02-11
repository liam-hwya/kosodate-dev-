<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Welcome extends CI_Controller {

    /**
     * Running this script from shell
     */
    public function index()
    {
        // there is no action here yet
    }

    public function insertRSS()
    {

        //Getting D_MEDIA and D_MANGA
        $d_media_col = [
            ["url" => "sample1"],
            ["url" => "sample2"]
        ];
        $d_manga_col = [
            [
                "id" => "sample1",
                "url" => "sample1"
            ],
            [
                "id" => "sample1",
                "url" => "sample1"
            ]
        ];

        

        // Preparing XML data
        $channel_data['title'] = "「こそだてDAYS」、「こそだてDAYS｜ママ達の子育て体験談マンガ」";
        $channel_data['link'] = "https://www.kosodatedays.com/";
        $channel_data['description'] = "こそだてDAYS（こそだてデイズ）は子育てママと作る0～6歳児ママのためのWebメディアです。ママ達の子育て体験談を無料で漫画化し、赤ちゃん期から入学までに必要な育児情報を配信しています。";
        $channel_data['pubDate'] = date("D, d M Y H:i:s O");
        $channel_data['language'] = "ja";
        $channel_data['copyright'] = "©2017 EvolvedInfo. All Rights Reserved.";

        $item_data['title'] = "sample";
        $item_data['link'] = "sample";
        $item_data['guid'] = "sample";
        $item_data['category'] = "<![CDATA[食品・飲料]]>";
        $item_data['description'] = "こそだてDAYS（こそだてデイズ）は子育てママと作る0～6歳児ママのためのWebメディアです。ママ達の子育て体験談を無料で漫画化し、赤ちゃん期から入学までに必要な育児情報を配信しています。チッチママ～「娘の祈り」をお楽しみください。";
        $item_data['pubDate'] = date("D, d M Y H:i:s O");
        $item_data['modifiedDate'] = NULL;
        $item_data['encoded'] = "<![CDATA[img src/a href/h2/h3/h4/br/p/]]>";
        $item_data['delete'] = false;
        $item_data['enclosure'] = "http://aaaa.com/img/img1.jpg";
        $item_data['thumbnail'] = "http://aaaa.com/img/img1-300x300.jpg";
        $item_data['related'] = "sample";

        $channel_data['RSS-XML']="<?xml version='1.0' encoding='UTF-8' ?>";
        $channel_data['RSS-XML'].='<channel>';
        $channel_data['RSS-XML'].='<title>'.$channel_data['title'].'</title>';
        $channel_data['RSS-XML'].='<link>'.$channel_data['link'].'</link>';
        $channel_data['RSS-XML'].='<description>'.$channel_data['description'].'</description>';
        $channel_data['RSS-XML'].='<pubDate>'.$channel_data['pubDate'].'</pubDate>';
        $channel_data['RSS-XML'].='<language>'.$channel_data['language'].'</language>';
        $channel_data['RSS-XML'].='<copyright>'.$channel_data['copyright'].'</copyright>';
        //Loop goes here
          $channel_data['RSS-XML'].='<item>';
            $channel_data['RSS-XML'].='<title>'.$d_manga->title.'</title>';
            $channel_data['RSS-XML'].='<link>'.$d_media->url.'</link>';
            $channel_data['RSS-XML'].='<guid>'.$d_manga->id.'</guid>';
            $channel_data['RSS-XML'].='<category>'.$item_data['category'].'</category>';
            $channel_data['RSS-XML'].='<description>'.$item_data['description'] .'</description>';
            $channel_data['RSS-XML'].='<pubDate>'.$item_data['description'] .'</pubDate>';
            $channel_data['RSS-XML'].='<modifiedDate>'.$item_data['description'] .'</modifiedDate>';
            $channel_data['RSS-XML'].='<encoded>'.$item_data['description'] .'</encoded>';
            $channel_data['RSS-XML'].='<delete>'.$item_data['description'] .'</delete>';
            $channel_data['RSS-XML'].='<enclosure>'.$item_data['description'] .'</enclosure>';
            $channel_data['RSS-XML'].='<thumbnail>'.$item_data['description'] .'</thumbnail>';
            $channel_data['RSS-XML'].='<related>'.$item_data['description'] .'</related>';
          $channel_data['RSS-XML'].='</item>';
        //Loop ends here
        $channel_data['RSS-XML'].='</channel>';
        echo $channel_data['RSS-XML'];


    }
}
