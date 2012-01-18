<?php

class Main_db_model extends CI_Model {

    function __construct()
    {
        $this->TOP   = 3; // top 3 matches are listed

        $this->header = array('Group',
                              'Mean Log Probability',
                              'Median Log Probability',
                              'Mean Percentile',
                              'Median Percentile',
                              'Mean Min Edit Distance',
                              'Median Min Edit Distance',
                              'Signature');

        parent::__construct();
    }

    function get_list_of_results()
    {
        $this->db->select()
                 ->from('job_description');
        $query = $this->db->get();
        $table = array();
        $i = 1;
        foreach ($query->result() as $row) {
            $table[] = array($i,
                             anchor(array('main','results', $row->job_id), 'view'),
                             $row->description);
            $i++;
        }
        $tmpl = array( 'table_open'  => "<table class='zebra-striped bordered-table'>" );
        $this->table->set_template($tmpl);
        $this->table->set_heading(array('#', 'Results', 'Description'));
        return $this->table->generate($table);
    }

    function get_job_description($job_id)
    {
        $query = $this->db->select('description')
                          ->from('job_description')
                          ->where('job_id', $job_id)
                          ->get();
        $row = $query->row();
        return $row->description;
    }

    function get_results($job_id)
    {
        $this->db->select('*')
                 ->select("group_concat(`ss_id` SEPARATOR ', ') AS ss_ids", FALSE)
                 ->select("count(*) as total", FALSE)
                 ->from('loop_locations')
                 ->where('job_id', $job_id)
                 ->group_by(array('location','seq'))
                 ->order_by('count(*) DESC, loop_type, location ASC');
        $query = $this->db->get();

        foreach ($query->result() as $row) {
            $results[$row->loop_type][$row->location]['ss_id'][] = $row->ss_ids;
            $results[$row->loop_type][$row->location]['seq'][] = $row->seq . ' (' . $row->count . ')';
            $results[$row->loop_type][$row->location]['sum'] = $row->total;
        }

        $html['il'] = '';
        $html['hl'] = '';
        foreach ($results as $loop_type => $result) {
            $loop_i = 1;
            $MAXSTR = 1000000;
            foreach ($result as $location => $data) {

                $numstruct = $data['sum'];
                if ($numstruct < $MAXSTR) {
                    $html[$loop_type] .= sprintf('<h4>%s structures</h4>', $numstruct);
                    $MAXSTR = $numstruct;
                }

                $seq_variants = implode(', ', $data['seq']);
                $ss_ids = implode(', ', $data['ss_id']);
                $html[$loop_type] .= sprintf("<strong>#%s</strong> %s (%s variant(s))<br>",
                                             $loop_i, $location, count($data['seq']));
                $html[$loop_type] .= sprintf('%s<br>', $seq_variants);
//                 $html[$loop_type] .= sprintf('%s<br><div class="found_in">%s</div><br>', $seq_variants, $ss_ids);
                $loop_i++;


                $table = array();
                $this->db->select()
                         ->from('jar3d_results')
                         ->where('job_id', $job_id)
                         ->where('location', $location)
                         ->order_by('result_id asc')
                         ->limit(3);
                $query = $this->db->get();

                foreach ($query->result() as $jresult) {
                    $handle = str_replace(array('IL','HL'), 'Group', $jresult->group);
                    $aa = "<input type='radio' class='exemplar' name='r'><a href='http://rna.bgsu.edu/research/anton/share/iljun6/{$handle}.html'>" . $jresult->group . '</a>';
                    $table[] = array($aa,
                                     $jresult->mean_log_probability,
                                     $jresult->median_log_probability,
                                     $this->beautify_percentile($jresult->mean_percentile),
                                     $this->beautify_percentile($jresult->median_percentile),
                                     $this->beautify_edit_distance($jresult->mean_min_edit_distance),
                                     $this->beautify_edit_distance($jresult->median_min_edit_distance),
                                     $jresult->signature);
                }
                $tmpl = array( 'table_open'  => "<table class='condensed-table zebra-striped bordered-table sortable'>" );
                $this->table->set_template($tmpl);
                $this->table->set_heading($this->header);

                $html[$loop_type] .= $this->table->generate($table);

            }
        }

        return $html;
    }

    function beautify_percentile($percentile)
    {
        if ($percentile >= 90) {
            return "<label class='label success'>$percentile</label>";
        } else {
            return $percentile;
        }
    }

    function beautify_edit_distance($distance)
    {
        if ($distance == 0) {
            return "<label class='label success'>$distance</label>";
        } else {
            return $distance;
        }
    }

}

/* End of file main_model.php */
/* Location: ./application/model/main_model.php */