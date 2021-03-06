<?php

defined('BASEPATH') OR exit('No direct script access allowed');

class Ad_modify_rss extends CI_Controller{

    private $_page_name = 'マンガの登録・編集';

    public function __construct() {

        parent::__construct();

        $this->load->model('RSS_log_channel_model','RSS_log_channel_model');
        $this->load->model('RSS_log_item_model','RSS_log_item_model');
        $this->load->model('RSS_insta_model','RSS_insta_model');
        $this->load->model('Manga_model','Manga_model');


    }

    public function index() {

        $manga_detail_url_str = "/admin/ad_modify_rss/detail/";
        $manga_detail_url = site_url($manga_detail_url_str);

        $manga_execute_url_str = "/admin/ad_modify_rss/execute";
        $manga_execute_url = site_url($manga_execute_url_str);

        $manga_signup_url_str = "/admin/ad_modify_rss/sign_up";
        $manga_signup_url = site_url($manga_signup_url_str);

        $view_data['release_date'] = nad_jp_date('','Y-m-d').' '.nad_jp_date('','Y-m-d',$end_date=true);
        $view_data['manga_detail_url'] = $manga_detail_url;
        $view_data['manga_execute_url'] = $manga_execute_url;
        $view_data['manga_signup_url'] = $manga_signup_url;
        
        $this->load->view('rss/item_lists',$view_data);
    }

    public function modify() {

        $get_data['manga'] = $this->input->get('manga');
        $manga = !empty($get_data['manga']) ? $get_data['manga'] : '';

        $base_url_str = "/admin/ad_modify_rss/modify";
        $base_url_str .= "?search=" . $manga;
        $base_url = site_url($base_url_str);

        $manga_detail_url_str = "/admin/ad_modify_rss/detail/";
        $manga_detail_url = site_url($manga_detail_url_str);

        $channel = $this->RSS_log_channel_model->select_latest_channel();
        $channel_id = get_channel_id($channel);
        $manga_list = $this->RSS_log_item_model->search_manga_from_rss($channel_id, $manga);
        
        $view_data['search'] = $manga;
        $view_data['base_url'] = $base_url;
        $view_data['manga_list'] = $manga_list;
        $view_data['manga_detail_url'] = $manga_detail_url;

        $this->load->view('rss/modify',$view_data);
    }

    public function sign_up() {

        $get_data['manga'] = $this->input->get('manga');
        $manga = !empty($get_data['manga']) ? $get_data['manga'] : '';

        $base_url_str = "/admin/ad_modify_rss/sign_up";
        $base_url_str .= "?search=" . $manga;
        $base_url = site_url($base_url_str);

        $manga_detail_url_str = "/admin/ad_modify_rss/detail/";
        $manga_detail_url = site_url($manga_detail_url_str);

        $manga_list = $this->Manga_model->search_manga($manga);
        
        $view_data['search'] = $manga;
        $view_data['base_url'] = $base_url;
        $view_data['manga_list'] = $manga_list;
        $view_data['manga_detail_url'] = $manga_detail_url;

        $this->load->view('rss/sign_up',$view_data);
    }

    public function detail($index = FALSE) {
        
        if(!$index){
            redirect('/ad_modify_rss','refresh');
        }

        $manga_media = $this->Manga_model->select_manga_media($index);
        $manga_tags = $this->Manga_model->select_tags_for_manga($index);
        

        if(!empty($manga_tags)){
            $tags = manga_tag_sort($index,$manga_tags); //Sort the manga tags
            $tags_condition = manga_tag_condition($index,$tags); //Get the condition for manga tags.
            $age_manga = $this->Manga_model->select_all_age_manga();
            $related_manga = get_related_manga($age_manga,$tags_condition); 
        }else{
            $related_manga = null;
        }

        $insta = $this->RSS_insta_model->select_insta();
        $insta_tag = (is_null($insta)? '': $insta->tag);

        $action = $this->input->get('action');
        if(is_null($action) || empty($action)){
            redirect('/ad_modify_rss','refresh');
        }
        
        if($action == 'register'){
            $manga = $this->Manga_model->select_manga_detail($index);
            
            if(!is_null($manga)){
                $data = [
                    'manga' => $manga,
                    'manga_media' => $manga_media,
                    'related_manga' => $related_manga,
                    'insta_tag' => $insta_tag
                ];
                $rss_manga = $this->get_rss_manga($data);
                $manga['description'] = $rss_manga['description'];
                $manga['encoded'] = $rss_manga['encoded'];
            }
            
        }elseif($action == 'update'){

            $manga = $this->RSS_log_item_model->select_items_by_id($index);
            // echo '<pre>'; var_dump($manga['pubDate']); die();
        }
        
        // echo '<pre>'; var_dump($manga); die();
        $register_url_str = "/admin/ad_modify_rss/register";
        $register_url = site_url($register_url_str);
        $update_url_str = "/admin/ad_modify_rss/update";
        $update_url = site_url($update_url_str);

        $view_data['manga'] = $manga;
        $view_data['manga_media'] = $manga_media;
        $view_data['manga_id'] = $index;
        $view_data['related_manga'] = $related_manga;
        $view_data['register_url'] = $register_url;
        $view_data['update_url'] = $update_url;
        $view_data['action'] = $action;
        
        $this->load->view('rss/detail',$view_data);

    }

    public function register() {
        
        if(!$this->input->post('manga_register')){
            echo "You don't have access to this route";
            die();
        }

        $manga_id = $this->input->post('manga_id');
        if(!empty($manga_id)) {
            
            $manga = $this->Manga_model->select_manga_detail($manga_id);
            $author = $this->Manga_model->select_mangaka_for_manga($manga_id);
            $manga_media = $this->Manga_model->select_manga_media($manga_id);
            $manga_tags = $this->Manga_model->select_tags_for_manga($manga_id);
            
            if(!empty($manga_tags)){
                $tags = manga_tag_sort($manga_id,$manga_tags); //Sort the manga tags
                $tags_condition = manga_tag_condition($manga_id,$tags); //Get the condition for manga tags.
                $related_manga = $this->Manga_model->select_related_manga_for_tags($tags_condition);
            }else{
                $related_manga = null;
            }
            

            $insta = $this->RSS_insta_model->select_insta();
            $insta_tag = (is_null($insta)? '': $insta->tag);
            
            $data = [
                'manga' => $manga,
                'manga_media' => $manga_media,
                'related_manga' => $related_manga,
                'insta_tag' => $insta_tag
            ];
            $rss_manga = $this->get_rss_manga($data);

            $channel = $this->RSS_log_channel_model->select_latest_channel();
            $channel_id = get_channel_id($channel);
            $items_from_log = $this->RSS_log_item_model->select_items_by_channel_id($channel_id);
            
            if(count($items_from_log) <= CONST_RSS_MANGA_ITEM_NUM && $this->is_unique_manga_id($items_from_log,$manga_id)) {
                if(isset($_SESSION['register_manga'])) {
                    if($this->is_manga_in_session($session=$_SESSION['register_manga'],$manga_id)) {
                        $_SESSION['register_manga'][$manga_id] = $rss_manga;
                    }
                }else {
                    $register_manga[$manga_id] = $rss_manga;
                    $this->session->set_userdata('register_manga',$register_manga); 
                }
            }else{
                echo "Can't insert the manga. Manga is full or the manga with this id is already exists.";
                die();
            }

        }
    }

    public function update(){

        if(!$this->input->post('manga_update')){
            echo "You don't have access to this route";
            die();
        }
        $manga = $this->input->post('manga');
        $related_manga = $this->input->post('related_manga');
        // echo '<pre>'; var_dump($manga); die();
        $manga_id = $manga['guid'];
        $rss_manga['guid'] = $manga['guid'];
        $rss_manga['title'] = $manga['title'];
        $rss_manga['link'] = $manga['link'];
        $rss_manga['category'] = $manga['category'];
        $rss_manga['description'] = $manga['description'];
        $rss_manga['encoded'] = $manga['encoded'];
        $rss_manga['thumbnail'] = $manga['img_url']['thumbnail'];
        $rss_manga['enclosure'] = $manga['img_url']['enclosure'][0];
        $rss_manga['delete'] = $manga['delete'];
        $rss_manga['pubDate'] = $manga['pubDate'];

        $relatedlink = '';
        if (!is_null($related_manga)) {
            foreach ($related_manga as $manga) {
                $relatedlink .= '<relatedlink title="'.$manga['manga_title'].'" link="'.MANGA_URL.$manga['manga_id'].'"/>';
            }
        }
        $rss_manga['relatedlink'] = $relatedlink;
        
        $rss_manga['modifiedDate'] = nad_jp_date('','RFC822');
        $rss_manga['last_time'] = nad_jp_date('','RFC822');

        // Set data in session
        if(isset($_SESSION['update_manga'])) {
            if($this->is_manga_in_session($session=$_SESSION['update_manga'],$manga_id)) {
                $_SESSION['update_manga'][$manga_id] = $rss_manga;
            }
            $_SESSION['update_manga'][$manga_id] = $rss_manga;
        }else {
            $update_manga[$manga_id] = $rss_manga;
            $this->session->set_userdata('update_manga',$update_manga); 
        }

    }

    public function execute() {

        if(!$this->input->post('execute_manga')){
            echo "You don't have access to this route";
            die();
        }
        echo '<pre>';

        if(isset($_SESSION['register_manga'])) {
            $register_manga = $_SESSION['register_manga'];
        }else{
            $register_manga = [];
        }

        if(isset($_SESSION['update_manga'])) {
            $update_manga = $_SESSION['update_manga'];
        }else{
            $update_manga = [];
        }

        $item_data = array_merge($register_manga,$update_manga);

        $channel = $this->RSS_log_channel_model->select_latest_channel();
        $channel_id = get_channel_id($channel);
        $channel_data = $this->new_channel($channel_id,$item_data);
        $new_channel_id = $this->RSS_log_channel_model->create_rss($channel_data);

        $this->RSS_log_item_model->create_rss($register_manga, $new_channel_id);
    
        foreach($update_manga as $id=>$manga) {
            $update_manga[$id]['log_channel_id'] = $new_channel_id;
        }
        $this->RSS_log_item_model->update_manga($update_manga);
        
        unset($_SESSION['register_manga']);
        unset($_SESSION['update_manga']);

    }

    private function get_rss_xml($channel_id,$channel,$items=null) {
        
        if (is_null($items)) {
            $items = $this->RSS_log_item_model->select_items_by_channel_id($channel_id, $delete=false);
        }

        $xml='<?xml version="1.0" encoding="UTF-8" ?>';
        $xml.='<channel>';
        $xml.='<title>'.$channel->title.'</title>';
        $xml.='<link>'.$channel->link.'</link>';
        $xml.='<description>'.$channel->description.'</description>';
        $xml.='<pubDate>'.nad_jp_date($channel->pubDate,$format='RFC822').'</pubDate>';
        $xml.='<language>'.$channel->language.'</language>';
        $xml.='<copyright>'.$channel->copyright.'</copyright>';
        foreach($items as $item)
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

        return $xml;
    }

    private function new_channel($channel_id,$data) {

        $channel_data['title'] = "こそだてDAYS";
        $channel_data['link'] = "https://www.kosodatedays.com/";
        $channel_data['description'] = "こそだてDAYS（こそだてデイズ）は子育てママと作る0～6歳児ママのためのWebメディアです。ママ達の子育て体験談を無料で漫画化し、赤ちゃん期から入学までに必要な育児情報を配信しています。";
        $channel_data['pubDate'] = nad_jp_date();
        $channel_data['language'] = "ja";
        $channel_data['copyright'] = "©2017 EvolvedInfo. All Rights Reserved.";
        $xml = $this->get_rss_xml($channel_id,(object) $channel_data,$data);
        $channel_data['RSS_XML'] = $xml;
        return $channel_data;

    }

    private function is_unique_manga_id($items_from_log,$manga_id) {
        $id = [];

        foreach($items_from_log as $item) {
            $id[] = $item['guid'];
        }

        if(in_array($manga_id,$id)){
            return false;
        }

        return true;
    }

    private function get_rss_manga($data) {

        $manga = $data['manga'];
        $manga_media = $data['manga_media'];
        $related_manga = $data['related_manga'];
        $insta_tag = $data['insta_tag'];

        $rss_manga['title'] = $manga['title'];
        $rss_manga['link'] = MANGA_URL.$manga['id'];
        $rss_manga['guid'] = $manga['id'];
        $rss_manga['category'] = '<![CDATA[ママコマ漫画]]>';
        $rss_manga['description'] = "<![CDATA[こそだてDAYS（こそだてデイズ）は子育てママと作る0～6歳児ママのためのWebメディアです。ママ達の子育て体験談を無料で漫画化し、赤ちゃん期から入学までに必要な育児情報を配信しています。".$manga['author']."～「".$manga['title']."」をお楽しみください。]]>";
        $rss_manga['pubDate'] = nad_jp_date('','RFC822');
        $rss_manga['modifiedDate'] = null;
        $rss_manga['delete'] = 0;
        $rss_manga['enclosure'] = (empty($manga_media[0]['img_url']))? '':KOSODATE_IMG_URL.$manga_media[0]['img_url']; //first image of the manga media
        $rss_manga['thumbnail'] = (empty($manga_media[0]['img_url']))? '':KOSODATE_IMG_URL.$manga_media[0]['img_url']; //first image of the manga media

        // Encoded tags
        $rss_manga['encoded'] = '<![CDATA[<p>体験談投稿'.$manga['story_name'];
        if(!empty($manga['story_mama_year_old'])){
            $rss_manga['encoded'] .= $manga['story_mama_year_old'].'<br>';
        }
        if(!empty($manga['story_childs_year_old'])){
            $rss_manga['encoded'] .= 'お子さん'.$manga['story_childs_year_old'].'</p>';
        }
        $rss_manga['encoded'] .= '<p>'.$manga['title'].'</p>';          
        $rss_manga['encoded'] .= '<p>'.$manga['intro'].'</p>';
        $rss_manga['encoded'] .= '<p>'.nad_jp_date().'</p><p>';

        foreach($manga_media as $media) {
            $rss_manga['encoded'] .= '<img src="'.KOSODATE_IMG_URL.$media['img_url'].'"/>';
        }

        $rss_manga['encoded'] .= '</p>';
            
        $rss_manga['encoded'] .= '<p>イラスト・'.$manga['author'].'さん<img src="'.KOSODATE_T_URL.$manga['mangaka_icon_url'].'"/></p>';
        $rss_manga['encoded'] .= '<p>'.$manga['detail'].'</p>';

        $rss_manga['encoded'] .= $insta_tag."]]>";

        // Related Tags
        $rss_manga['relatedlink'] = '';

        if(!is_null($related_manga)){
            foreach($related_manga as $manga){
                $rss_manga['relatedlink'] .= '<relatedlink title="'.$manga['manga_title'].'" link="'.MANGA_URL.$manga['manga_id'].'"/>';
            }
        }

        return $rss_manga;
    }

    private function is_manga_in_session($session,$manga_id) {

        foreach($session as $element){
            if($element['guid'] == $manga_id) {
                return false;
            }
        }
        return true;
    }

}