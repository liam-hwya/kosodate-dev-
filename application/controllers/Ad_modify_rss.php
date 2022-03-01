<?php

defined('BASEPATH') OR exit('No direct script access allowed');

class Ad_modify_rss extends CI_Controller{

    private $_page_name = 'マンガの登録・編集';

    public function __construct() {

        parent::__construct();

        $this->load->model('RSS_log_channel_model','RSS_log_channel_model');
        $this->load->model('RSS_log_item_model','RSS_log_item_model');
        $this->load->model('RSS_insta_model','RSS_insta_model');
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


        $this->load->view('rss/sign_up');
    }

    public function detail($index = FALSE) {
        
        $view_data = [
            'id' => $index
        ];

        $this->load->view('rss/detail',$view_data);

    }

}