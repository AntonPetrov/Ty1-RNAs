<?php
class Main extends CI_Controller {

    public function __construct()
    {
        parent::__construct();
        $this->labels = array('str1'=>'-199.9','str2'=>'-199.3','str3'=>'-198.1','str4'=>'-195.1','str5'=>'-194.1','str6'=>'-193.8','str7'=>'-188.3',
                              'pseudoknots' => 'pseudoknots',
                              'pseudoknot1'=>'-231.0','pseudoknot2'=>'-231.0','pseudoknot3'=>'-230.7','pseudoknot4'=>'-230.7','pseudoknot5'=>'-230.6','pseudoknot6'=>'-230.6','pseudoknot7'=>'-230.5','pseudoknot8'=>'-230.4','pseudoknot9'=>'-230.3','pseudoknot10'=>'-230.1','pseudoknot11'=>'-230.1','pseudoknot12'=>'-230.1','pseudoknot13'=>'-230.0','pseudoknot14'=>'-229.9','pseudoknot15'=>'-229.8','pseudoknot16'=>'-229.8','pseudoknot17'=>'-229.8','pseudoknot18'=>'-229.8','pseudoknot19'=>'-229.7','pseudoknot20'=>'-229.6','pseudoknot21'=>'-229.5','pseudoknot22'=>'-229.4','pseudoknot23'=>'-229.4','pseudoknot24'=>'-229.4','pseudoknot25'=>'-229.3','pseudoknot26'=>'-229.3','pseudoknot27'=>'-229.3','pseudoknot28'=>'-229.3','pseudoknot29'=>'-229.2','pseudoknot30'=>'-229.2','pseudoknot31'=>'-229.2','pseudoknot32'=>'-229.2','pseudoknot33'=>'-229.2','pseudoknot34'=>'-229.1','pseudoknot35'=>'-229.1','pseudoknot36'=>'-229.1','pseudoknot37'=>'-229.1','pseudoknot38'=>'-229.1','pseudoknot39'=>'-229.1','pseudoknot40'=>'-229.1','pseudoknot41'=>'-229.0','pseudoknot42'=>'-229.0','pseudoknot43'=>'-228.9','pseudoknot44'=>'-228.9','pseudoknot45'=>'-228.8','pseudoknot46'=>'-228.8','pseudoknot47'=>'-228.8','pseudoknot48'=>'-228.8','pseudoknot49'=>'-228.8','pseudoknot50'=>'-228.8','pseudoknot51'=>'-228.8','pseudoknot52'=>'-228.7','pseudoknot53'=>'-228.7','pseudoknot54'=>'-228.7','pseudoknot55'=>'-228.7','pseudoknot56'=>'-228.7','pseudoknot57'=>'-228.7','pseudoknot58'=>'-228.7','pseudoknot59'=>'-228.7','pseudoknot60'=>'-228.6','pseudoknot61'=>'-228.6','pseudoknot62'=>'-228.6','pseudoknot63'=>'-228.6','pseudoknot64'=>'-228.6','pseudoknot65'=>'-228.5','pseudoknot66'=>'-228.5','pseudoknot67'=>'-228.5','pseudoknot68'=>'-228.5','pseudoknot69'=>'-228.4','pseudoknot70'=>'-228.4','pseudoknot71'=>'-228.4','pseudoknot72'=>'-228.3','pseudoknot73'=>'-228.3','pseudoknot74'=>'-228.3','pseudoknot75'=>'-228.2','pseudoknot76'=>'-228.2','pseudoknot77'=>'-228.2','pseudoknot78'=>'-228.2','pseudoknot79'=>'-228.2','pseudoknot80'=>'-228.2','pseudoknot81'=>'-228.2','pseudoknot82'=>'-228.2','pseudoknot83'=>'-228.2','pseudoknot84'=>'-228.2','pseudoknot85'=>'-228.1','pseudoknot86'=>'-228.1','pseudoknot87'=>'-228.1','pseudoknot88'=>'-228.0','pseudoknot89'=>'-228.0','pseudoknot90'=>'-228.0','pseudoknot91'=>'-228.0','pseudoknot92'=>'-228.0','pseudoknot93'=>'-227.9','pseudoknot94'=>'-227.8','pseudoknot95'=>'-227.8',
                              'pseudo1'=>'-231.0','pseudo2'=>'-231.0','pseudo3'=>'-230.7','pseudo4'=>'-230.7','pseudo5'=>'-230.6','pseudo6'=>'-230.6','pseudo7'=>'-230.5','pseudo8'=>'-230.4','pseudo9'=>'-230.3','pseudo10'=>'-230.1','pseudo11'=>'-230.1','pseudo12'=>'-230.1','pseudo13'=>'-230.0','pseudo14'=>'-229.9','pseudo15'=>'-229.8','pseudo16'=>'-229.8','pseudo17'=>'-229.8','pseudo18'=>'-229.8','pseudo19'=>'-229.7','pseudo20'=>'-229.6','pseudo21'=>'-229.5','pseudo22'=>'-229.4','pseudo23'=>'-229.4','pseudo24'=>'-229.4','pseudo25'=>'-229.3','pseudo26'=>'-229.3','pseudo27'=>'-229.3','pseudo28'=>'-229.3','pseudo29'=>'-229.2','pseudo30'=>'-229.2','pseudo31'=>'-229.2','pseudo32'=>'-229.2','pseudo33'=>'-229.2','pseudo34'=>'-229.1','pseudo35'=>'-229.1','pseudo36'=>'-229.1','pseudo37'=>'-229.1','pseudo38'=>'-229.1','pseudo39'=>'-229.1','pseudo40'=>'-229.1','pseudo41'=>'-229.0','pseudo42'=>'-229.0','pseudo43'=>'-228.9','pseudo44'=>'-228.9','pseudo45'=>'-228.8','pseudo46'=>'-228.8','pseudo47'=>'-228.8','pseudo48'=>'-228.8','pseudo49'=>'-228.8','pseudo50'=>'-228.8','pseudo51'=>'-228.8','pseudo52'=>'-228.7','pseudo53'=>'-228.7','pseudo54'=>'-228.7','pseudo55'=>'-228.7','pseudo56'=>'-228.7','pseudo57'=>'-228.7','pseudo58'=>'-228.7','pseudo59'=>'-228.7','pseudo60'=>'-228.6','pseudo61'=>'-228.6','pseudo62'=>'-228.6','pseudo63'=>'-228.6','pseudo64'=>'-228.6','pseudo65'=>'-228.5','pseudo66'=>'-228.5','pseudo67'=>'-228.5','pseudo68'=>'-228.5','pseudo69'=>'-228.4','pseudo70'=>'-228.4','pseudo71'=>'-228.4','pseudo72'=>'-228.3','pseudo73'=>'-228.3','pseudo74'=>'-228.3','pseudo75'=>'-228.2','pseudo76'=>'-228.2','pseudo77'=>'-228.2','pseudo78'=>'-228.2','pseudo79'=>'-228.2','pseudo80'=>'-228.2','pseudo81'=>'-228.2','pseudo82'=>'-228.2','pseudo83'=>'-228.2','pseudo84'=>'-228.2','pseudo85'=>'-228.1','pseudo86'=>'-228.1','pseudo87'=>'-228.1','pseudo88'=>'-228.0','pseudo89'=>'-228.0','pseudo90'=>'-228.0','pseudo91'=>'-228.0','pseudo92'=>'-228.0','pseudo93'=>'-227.9','pseudo94'=>'-227.8','pseudo95'=>'-227.8');
    }

	public function gallery()
	{
//         $this->output->cache(1000000);

	    $this->load->model('Main_model');

        $data['title']   = 'Alternative Secondary Structures';
        $data['baseurl'] = base_url();
        $data['graphs']  = $this->Main_model->get_ss_diagrams();
        $data['labels']  = $this->labels;

        $this->load->view('header_view', $data);
        $this->load->view('menu_view', $data);
        $this->load->view('main_ss_view', $data);
        $this->load->view('footer_view');

	}

    public function index()
    {
//         $this->output->cache(1000000);

	    $this->load->model('Main_model');

        $data['title']   = 'Alternative Secondary Structures';
        $data['baseurl'] = base_url();
        $data['loops']   = $this->Main_model->get_all_loops();
        $data['labels']  = $this->labels;
        $data['graphs']  = $this->Main_model->get_ss_diagrams();

        $this->table->set_heading('#','id','Energy','Internal loops','Hairpins');
        $tmpl = array( 'table_open'  => '<table class="condensed-table bordered-table" id="sortable">' );
        $this->table->set_template($tmpl);
        $data['loops'] = $this->table->generate($data['loops']);

        $this->load->view('header_view', $data);
        $this->load->view('menu_view', $data);
        $this->load->view('main_all_view', $data);
        $this->load->view('footer_view');

    }

	public function results($id)
	{
//         $this->output->cache(1000000);

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

	public function loops($type=NULL)
	{
	    if ($type == NULL) {
            $data['title']   = 'Loops grouped by structure';
            $data['baseurl'] = base_url();
            $this->load->view('header_view', $data);
            $this->load->view('menu_view', $data);
            echo '<div class="container"><div class="content">';
            echo anchor(array('main','loops','rnastructure'), 'Pseudoknot removed with RNAStructure');
            echo '<br>';
            echo anchor(array('main','loops','k2n'), 'Pseudoknot removed with K2N');
            echo '</div>';
            $this->load->view('footer_view');
	    } else {
            $this->load->model('Main_model');

            $data['title']   = 'Loops grouped by structure';
            $data['baseurl'] = base_url();
            $data['results'] = $this->Main_model->get_loops($type);
            $data['labels']  = $this->labels;

            $this->load->view('header_view', $data);
            $this->load->view('menu_view', $data);
            $this->load->view('main_loops_view', $data);
            $this->load->view('footer_view');
        }

	}

}

/* End of file main.php */
/* Location: ./application/controllers/main.php */