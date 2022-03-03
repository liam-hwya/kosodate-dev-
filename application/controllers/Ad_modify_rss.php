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

        $view_data = [
            'release_date' => nad_jp_date('','Y-m-d').' '.nad_jp_date('','Y-m-d',$end_date=true)
        ];

        $this->load->view('rss/item_lists',$view_data);
    }

    public function modify() {

        $this->load->view('rss/modify');
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
        
        if($index) {
            $manga_detail = $this->Manga_model->select_manga_detail($index);
            $manga_media = $this->Manga_model->select_manga_media($index);
            $manga_tags = $this->Manga_model->select_tags_for_manga($index);
        }

        $base_url_str = "/admin/ad_modify_rss/register";
        $base_url = site_url($base_url_str);

        $tags = manga_tag_sort($index,$manga_tags); //Sort the manga tags
        $tags_condition = manga_tag_condition($index,$tags); //Get the condition for manga tags.
        $related_manga = $this->Manga_model->select_related_manga_for_tags($tags_condition);

        $view_data['manga_detail'] = $manga_detail;
        $view_data['manga_media'] = $manga_media;
        $view_data['manga_id'] = $index;
        $view_data['related_manga'] = $related_manga;
        $view_data['base_url'] = $base_url;

        $this->load->view('rss/detail',$view_data);

    }

    public function register() {

        $manga_id = $this->input->post('manga_id');
        if(!empty($manga_id)) {

            $manga = $this->Manga_model->select_manga_detail($manga_id);
            $author = $this->Manga_model->select_mangaka_for_manga($manga_id);
            $manga_media = $this->Manga_model->select_manga_media($manga_id);

            $manga_tags = $this->Manga_model->select_tags_for_manga($manga_id);
            $tags = manga_tag_sort($manga_id,$manga_tags); //Sort the manga tags
            $tags_condition = manga_tag_condition($manga_id,$tags); //Get the condition for manga tags.
            $related_manga = $this->Manga_model->select_related_manga_for_tags($tags_condition);

            $insta = $this->RSS_insta_model->select_insta();
            $insta_tag = (is_null($insta)? '': $insta->tag);
            
            $rss_manga['title'] = $manga->manga_title;
            $rss_manga['link'] = MANGA_URL.$manga->manga_id;
            $rss_manga['guid'] = $manga->manga_id;
            $rss_manga['category'] = '<![CDATA[ママコマ漫画]]>';
            $rss_manga['description'] = "こそだてDAYS（こそだてデイズ）は子育てママと作る0～6歳児ママのためのWebメディアです。ママ達の子育て体験談を無料で漫画化し、赤ちゃん期から入学までに必要な育児情報を配信しています。".$author."～「".$manga->manga_title."」をお楽しみください。";
            $rss_manga['pubDate'] = nad_jp_date();
            $rss_manga['modifiedDate'] = NULL;
            $rss_manga['delete'] = $manga->manga_deleted;
            $rss_manga['enclosure'] = $manga_media[0]['img_url'];
            $rss_manga['thumbnail'] = $manga_media[0]['img_url'];

            $rss_manga['encoded'] = '<![CDATA[<p>体験談投稿</p><p>'.$manga->story_name.'</p>';
            if(!empty($manga->story_mama_year_old)){
                $rss_manga['encoded'] .= '<p>'.$manga->story_mama_year_old.'</p>';
            }
            if(!empty($manga->story_childs_year_old)){
                $rss_manga['encoded'] .= '<p>お子さん</p><p>'.$manga->story_childs_year_old.'</p>';
            }
            $rss_manga['encoded'] .= '<p>'.$manga->manga_title.'</p>';          
            $rss_manga['encoded'] .= '<p>'.$manga->manga_intro.'</p>';
            $rss_manga['encoded'] .= '<p>'.nad_jp_date().'</p><p>';

            foreach($manga_media as $media) {
                $rss_manga['encoded'] .= '<img src="'.KOSODATE_IMG_URL.$media['img_url'].'"/>';
            }

            $rss_manga['encoded'] .= '</p>';
            $rss_manga['encoded'] .= $insta_tag."]]>";

            $rss_manga['relatedlink'] = '';

            foreach($related_manga as $manga){
                $rss_manga['relatedlink'] .= '<relatedlink title="'.$manga['manga_title'].'" link="'.MANGA_URL.$manga['manga_id'].'" thumbnail="'.KOSODATE_IMG_URL.$manga['img_url'].'"/>';
            }

            $data[] = $rss_manga;

            $channel_id = $this->RSS_log_channel_model->select_latest_channel_id();
            if($this->RSS_log_item_model->create_rss($data,$channel_id)) {
                return 'inserted';
            }

        }
    }

}