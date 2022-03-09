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

        if(is_null($rss_channel_data)){
            echo "There is no data yet";
            die();
        }else{
            $view_data['rss_output'] = $rss_channel_data->RSS_XML;
        }
        
        $latest_channel_id = get_channel_id($rss_channel_data);
        $latest_date = get_channel_modified_date($rss_channel_data);
        $latest_date = strtotime($latest_date);

        $this->output->set_header('Last-Modified: '.$latest_date);
        if(isset($this->input->request_headers()['If-Modified-Since'])) {
            if($latest_date == $this->input->request_headers()['If-Modified-Since']) {
                http_response_code(304);
                $this->output->set_header('X-MODIFIED_SINCE: MATCH');
                die();
            }
        }

        http_response_code(200);
        $this->output->set_header('X-CONTENT-RETURN: YES');
        $this->output->set_header('Content-Type: text/xml');
        $this->load->view('rss',$view_data);
    }
}

?>
