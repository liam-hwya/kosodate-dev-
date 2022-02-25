<?php

defined('BASEPATH') OR exit('No direct script access allowed');

class Ad_instagram extends CI_Controller{

    private $_page_name = 'マンガの登録・編集';

    public function __construct() {

        parent::__construct();

        $this->load->helper('xml');
        $this->load->helper('text');
        $this->load->model('RSS_log_channel_model','RSS_log_channel_model');
        $this->load->model('RSS_log_item_model','RSS_log_item_model');
        $this->load->model('RSS_insta_model','RSS_insta_model');
    }

    public function index() {

        $view_data['page_name'] = $this->_page_name;

        $this->load->view('instagram/register',$view_data);
    }

    public function register() {

        $instagram_tag['tag'] = $this->input->post('tag');
        
        if($this->RSS_insta_model->insert_insta($instagram_tag)){
                
            $latest_channel_id = $this->RSS_log_channel_model->select_latest_channel_id();
            $rss_items = $this->RSS_log_item_model->select_items_by_channel_id($latest_channel_id);
            
            foreach($rss_items as $key=>$item){        
                $new_rss_items[] = substr_replace($item['encoded'],$instagram_tag['tag'],-3,0);
                
                $this->RSS_log_item_model->update_items_encoded($latest_channel_id,['encoded' => $new_rss_items[$key]],$item);
            }
            
            $updated_rss_items = $this->RSS_log_item_model->select_items_by_channel_id($latest_channel_id);
            if($this->_update_channel_xml($latest_channel_id,$updated_rss_items)){
                return 'admin/ad_instagram';
            };
        }
    }

    private function _update_channel_xml($latest_channel_id,$updated_rss_items){

        $latest_channel = $this->RSS_log_channel_model->select_latest_channel();
        // var_dump($latest_channel);
        $xml='<?xml version="1.0" encoding="UTF-8" ?>';
            $xml.='<channel>';
            $xml.='<title>'.$latest_channel->title.'</title>';
            $xml.='<link>'.$latest_channel->link.'</link>';
            $xml.='<description>'.$latest_channel->description.'</description>';
            $xml.='<pubDate>'.nad_jp_date($latest_channel->pubDate,$format='RFC822').'</pubDate>';
            $xml.='<language>'.$latest_channel->language.'</language>';
            $xml.='<copyright>'.$latest_channel->copyright.'</copyright>';
            foreach($updated_rss_items as $item)
            {
              $xml.='<item>';
                $xml.='<title>'.$item['title'].'</title>';
                $xml.='<link>'.$item['link'].'</link>';
                $xml.='<guid>'.$item['guid'].'</guid>';
                $xml.='<category>'.$item['category'].'</category>';
                $xml.='<description>'.$item['description'] .'</description>';
                $xml.='<pubDate>'.nad_jp_date($item['pubDate'],$format='RFC822') .'</pubDate>';
                $xml.='<modifiedDate>'.nad_jp_date($item['modifiedDate'],$format='RFC822').'</modifiedDate>';
                $xml.='<encoded>'.$item['encoded'] .'</encoded>';
                $xml.='<delete>'.$item['delete'] .'</delete>';
                $xml.='<enclosure url="'.$item['enclosure'] .'"/>';
                $xml.='<thumbnail url="'.$item['thumbnail'] .'"/>';
                $xml.=$item['relatedlink'];
              $xml.='</item>';
            }
            $xml.='</channel>';
            
        $update_data = [
            'RSS_XML' => $xml,
            'last_time2' => nad_jp_date('', 'RFC822')
        ];

        return $this->RSS_log_channel_model->update_channel($latest_channel_id,$update_data);

    }
}