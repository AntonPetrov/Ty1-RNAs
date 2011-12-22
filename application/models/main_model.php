<?php

function cmp($a, $b) {
    if ($a == $b) {
        return 0;
    }
    return ($a > $b) ? -1 : 1;
}

class Main_model extends CI_Model {

    function __construct()
    {
        $this->TOP   = 3; // top 3 matches are listed
        $this->CELLS = 8; // there are 8 td elements in each row of webjar3d output
        $this->MAXTD = $this->TOP * $this->CELLS; // analyze 24 tds

        $this->location = '/Servers/rna.bgsu.edu/img/ty1/data/';
        $this->img_url  = 'http://rna.bgsu.edu/img/ty1/ss';
        $this->data_url = 'http://rna.bgsu.edu/img/ty1/data';

        $this->header = array('Group',
                              'Mean Log Probability',
                              'Median Log Probability',
                              'Mean Percentile',
                              'Median Percentile',
                              'Mean Min Edit Distance',
                              'Median Min Edit Distance',
                              'Signature');
        $this->labels = array('str1'=>'-199.9',
                              'str2'=>'-199.3',
                              'str3'=>'-198.1',
                              'str4'=>'-195.1',
                              'str5'=>'-194.1',
                              'str6'=>'-193.8',
                              'str7'=>'-188.3');

        parent::__construct();
    }

    function get_loops()
    {
        $html = $this->list_html_files();
        $results['il'] = $this->group_loops_by_structure($html['il']);
//         $results['hl'] = $this->group_loops_by_structure($html['hl']);
        return $results;
    }

    function group_loops_by_structure($loops)
    {
        $combined_counts = array();
        foreach ($loops as $loop) {
            $k = substr($loop,5,-5); // 12_15_20_25
            $v = substr($loop,0,4);  // str1
            $combined[$k][] = $v;
            if (array_key_exists($k, $combined_counts)) {
                $combined_counts[$k] += 1;
            } else {
                $combined_counts[$k] = 1;
            }
        }

        // descending sort
        uasort($combined_counts,'cmp');

        $results = '';
        $loop_count = 1;

        $str_number = 100; // much greater number
        foreach ($combined_counts as $k => $v) {
            // show a header for 7 structures, 6 etc
            if ($v < $str_number) {
                if ($v == 1) {
                    $results .= "<h3>$v structure</h3>";
                } else {
                    $results .= "<h3>$v structures</h3>";
                }
                $str_number = $v;
            }

            $str = '';
            foreach ($combined[$k] as $v) {
                $str = implode(', ', array($str,$this->labels[$v]));
            }
            $str = substr($str,2);
            $results .= 'Present in ' . $str . '<br>';

            $htmlfile = $combined[$k][0] . '_' . $k . '.html';
            $results .= $this->get_loop_description($htmlfile,$loop_count);
            $loop_count++;
        }

        return $results;
    }

    function get_loop_description($htmlfile, $loop_count)
    {

        $results = '';
        // open fasta file
        $file = fopen($this->location . str_replace('.html','.fasta',$htmlfile), "r") or exit("Unable to open file!");
        $variants = array();
        $counts   = array();
        while(!feof($file)) {
            $line = fgets($file);
            if (preg_match('/^(A|C|G|U)/',$line)) {
                $variants[] = $line;
            } elseif (preg_match('/>(\d+)/',$line,$matches)) {
                $counts[] = $matches[1];
            }
        }
        fclose($file);
        $vars_count = count($variants);
        for ($i=0; $i < $vars_count; $i++) {
            $variants[$i] = $variants[$i] . '(' . $counts[$i] . ')';
        }
        $variants = implode(', ',$variants);
        $results .= "<strong>#{$loop_count}</strong> <a href='{$this->data_url}/{$htmlfile}' target='_blank'>$htmlfile</a> $vars_count variants <br> $variants";

        // read top 3*8 <td>
        $file = fopen($this->location . $htmlfile, "r") or exit("Unable to open file!");
        $tds_count = 1;
        while(!feof($file) and $tds_count <= $this->MAXTD) {
            $line = fgets($file);
            if (preg_match('/^<td/',$line)) {
                $tds_count++;
                $tds[] = $line;
            }
        }
        fclose($file);

        // convert to rows
        $data = array();
        for ($i=0; $i < $this->TOP; $i++) {
            for ($j=0; $j < $this->CELLS; $j++) {
                $cell = array_shift($tds);
                $cell = str_replace('<td>','',$cell);
                $cell = str_replace("<td align='right'>",'',$cell);
                $cell = str_replace('</td>','',$cell);

                // add radiobutton to the first td in the row
                if ($j == 0) {
                    $cell = "<input type='radio' class='exemplar' name='r'>" . $cell;
                }
                // color mean percentiles
                if ($j == 3) {
                    if ($cell > 80) {
                        $cell = '<label class="label success">' . $cell . '</label>';
                    }
                }
                // color edit distances 56
                if ($j == 5 or $j == 6) {
                    if ($cell == 0) {
                        $cell = '<label class="label success">' . 0 . '</label>';
                    } else {
                        $cell = round($cell, 0);
                    }
                }

                $data[$i][] = $cell;
            }
        }

        $this->table->set_heading($this->header);
        $tmpl = array( 'table_open'  => "<table class='condensed-table zebra-striped bordered-table sortable'>" );
        $this->table->set_template($tmpl);
        $results .= $this->table->generate($data);
        return $results;
    }

    function process_results($file_list)
    {
        $results = '';

        $loop_count = 1;
        foreach ($file_list as $htmlfile) {
            $results .= $this->get_loop_description($htmlfile,$loop_count);
            $loop_count++;
        }

        return $results;
    }

    function list_html_files($id = NULL)
    {
        if ($handle = opendir($this->location)) {
            while (false !== ($entry = readdir($handle))) {
                // get all files
                if ($id != NULL and !preg_match("/^{$id}.+?html$/",$entry)) {
                    continue;
                }
                if ($entry == "." or $entry == ".." or $entry == ".DS_Store") {
                    continue;
                }
                if (!preg_match('/html/',$entry)) {
                    continue;
                }
                if (substr_count($entry,'_') == 2) {
                    $html['hl'][] = $entry;
                } else {
                    $html['il'][] = $entry;
                }
            }
            closedir($handle);
        }
        return $html;
    }

    function get_results($id)
    {
        // get files, sort by il and hl
        $html = $this->list_html_files($id);

        $results['il'] = $this->process_results($html['il']);
//         $results['hl'] = $this->process_results($html['hl']);

        return $results;
    }

    function get_ss_diagrams()
    {
        if ($handle = opendir('/Servers/rna.bgsu.edu/img/ty1/ss')) {
            while (false !== ($entry = readdir($handle))) {
                if ($entry != "." && $entry != ".." && $entry != ".DS_Store") {
                    $ss[] = $entry;
                }
            }
            closedir($handle);
        }
        $text = '';
        $baseurl = base_url();
        foreach ($ss as $img) {
            $file = substr($img,0,4);
            $text .= <<<EOT
              <li>
                <a href="{$this->img_url}/{$img}" class='fancybox' rel='ss'>
                  <img class="thumbnail span4" src="$this->img_url/{$img}" alt="">
                  <a href="{$baseurl}main/results/$file">{$this->labels[$file]}</a>
                </a>
              </li>
EOT;
        }
        return $text;
    }


}

/* End of file main_model.php */
/* Location: ./application/model/main_model.php */