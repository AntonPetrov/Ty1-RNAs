<?php
class Main extends CI_Controller {

    public function __construct()
    {
        parent::__construct();
    }

    public function index()
    {
//         $this->output->cache(1000000);

	    $this->load->model('Main_db_model', '', TRUE);

        $data['title']   = 'Results';
        $data['baseurl'] = base_url();
        $data['loops']   = $this->Main_db_model->get_list_of_results();

        $this->load->view('header_view', $data);
        $this->load->view('menu_view', $data);
        $this->load->view('main_all_view', $data);
        $this->load->view('footer_view');
    }

	public function results($id)
	{
//         $this->output->cache(1000000);

	    $this->load->model('Main_db_model', '', TRUE);

        $data['title']   = $this->Main_db_model->get_job_description($id);
        $data['baseurl'] = base_url();
        $data['results'] = $this->Main_db_model->get_results($id);

        $this->load->view('header_view', $data);
        $this->load->view('menu_view', $data);
        $this->load->view('main_results_view', $data);
        $this->load->view('footer_view');

	}

}

/* End of file main.php */
/* Location: ./application/controllers/main.php */