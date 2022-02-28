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
          return  'admin/ad_instagram';
        }
        
    }

}