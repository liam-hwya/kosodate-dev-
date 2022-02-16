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
        // var_dump($rss_channel_data->RSS_XML);die();
        $this->output->set_header('Content-Type: text/xml');
        $this->load->view('rss',$data);

    }
}

?>

<!-- <encoded>
<![CDATA[
    <h2>お試し</h2>
    <img src="manga/5f43603169de4/manga_5f43603169de0.png"/>
    <img src="manga/5f43603819785/manga_5f43603819781.png"/>
    <img src="manga/5f43603fcd8ca/manga_5f43603fcd8c5.png"/>
    <img src="manga/5f436046ca4d7/manga_5f436046ca4d2.png"/>
    ]]>
    </encoded> -->
