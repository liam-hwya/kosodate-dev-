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

        $rss_channel_data = $this->RSS_log_channel_model->select_channel();
        $data = [
            'rss_output' => $rss_channel_data->RSS_XML
        ];
        // echo "<pre>";var_dump($rss_channel_data->RSS_XML);echo "</pre>";die();
        $this->output->set_header('X-CONTENT-RETURN: YES');
        $this->output->set_header('Content-Type: text/xml');
        $this->load->view('rss',$data);

    }
}

?>
