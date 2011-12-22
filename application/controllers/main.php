<?php
class Main extends CI_Controller {

    public function __construct()
    {
        parent::__construct();
        $this->labels = array('str1'=>'-199.9',
                              'str2'=>'-199.3',
                              'str3'=>'-198.1',
                              'str4'=>'-195.1',
                              'str5'=>'-194.1',
                              'str6'=>'-193.8',
                              'str7'=>'-188.3');
    }


	public function index()
	{
	    $this->load->model('Main_model');

        $data['title']   = 'Alternative Secondary Structures';
        $data['baseurl'] = base_url();
        $data['graphs']  = $this->Main_model->get_ss_diagrams();
        $data['labels']  = $this->labels;

        $this->load->view('header_view', $data);
        $this->load->view('menu_view', $data);
        $this->load->view('main_ss_view', $data);
        $this->load->view('footer_view');

//         $this->output->enable_profiler(TRUE);

	}

	public function results($id)
	{
	    $this->load->model('Main_model');

        $data['title']   = $this->labels[$id];
        $data['baseurl'] = base_url();
        $data['results'] = $this->Main_model->get_results($id);
        $data['labels']  = $this->labels;

        $this->load->view('header_view', $data);
        $this->load->view('menu_view', $data);
        $this->load->view('main_results_view', $data);
        $this->load->view('footer_view');


	}
}

/* End of file main.php */
/* Location: ./application/controllers/main.php */