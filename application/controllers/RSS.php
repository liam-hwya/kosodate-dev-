<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class RSS extends CI_Controller
{

    /**
     * Running this script from shell
     *
     *
     */

    public function __construct()
    {
        parent::__construct();

        $this->load->helper('xml');
        $this->load->helper('text');
        $this->load->model('RSS_log_channel_model', 'RSS_log_channel_model');
        $this->load->model('RSS_log_item_model', 'RSS_log_item_model');
    }

    public function feed() {

        $rss_channel_data = $this->RSS_log_channel_model->select_latest_channel();
        $data = [
            'rss_output' => $rss_channel_data->RSS_XML
        ];


        $latest_channel_id = $this->RSS_log_channel_model->select_latest_channel_id();
        $modified_date = $this->RSS_log_item_model->select_modified_date($latest_channel_id->logid);
        $latest_date = 0;
        foreach($modified_date as $date){

            //filter date
            if($date['modifiedDate'] != null) {

                $cur_date = strtotime($date['modifiedDate']);

                if($cur_date > $latest_date){
                    $latest_date = $cur_date;
                }
            }
            
        }

        $this->output->set_header('Last-Modified: '.$latest_date);
        if(isset($this->input->request_headers()['If-Modified-Since'])) {
            if($latest_date == $this->input->request_headers()['If-Modified-Since']) {
                http_response_code(304);
                $this->output->set_header('X-MODIFIED_SINCE: MATCH');
                die();
            }
        }

        http_response_code(200);
        // echo "<pre>";var_dump($rss_channel_data->RSS_XML);echo "</pre>";die();
        $this->output->set_header('X-CONTENT-RETURN: YES');
        $this->output->set_header('Content-Type: text/xml');
        $this->load->view('rss',$data);

    }
}

?>
