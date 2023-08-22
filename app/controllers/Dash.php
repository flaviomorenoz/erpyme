<?php 
//defined('BASEPATH') OR exit('No direct script access allowed');

class Dash extends MY_Controller
{

    function __construct() {
        parent::__construct();

        session_start();
        $this->load->model('dash_model');
    }

    function index(){
        $this->data['page_title'] = "DDashboard";
        $this->data['Admin'] = $this->Admin;
        
        $bc     = array(array('link' => '#', 'page' => "dash"));
        $meta   = array('page_title' => "Dashboard", 'bc' => $bc);
        $this->page_construct('dash/index', $this->data, $meta);    
        //$this->load->view(site_url('dash/index'), $this->data);
        //echo (base_url('dash/index'));
    }

    function index2(){
        // ->view() ya sabe que la ruta de vista empieza en themes
        $this->load->view($this->theme . 'dash/index');
    }

}