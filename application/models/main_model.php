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

        $this->location     = '/Servers/rna.bgsu.edu/img/ty1/data_new/';
        $this->img_location = '/Servers/rna.bgsu.edu/img/ty1/ss/';
//         $this->location['pseudoknot'] = '/Servers/rna.bgsu.edu/img/ty1/pseudoknot/';

        $this->img_url    = 'http://rna.bgsu.edu/img/ty1/ss';
        $this->data_url   = 'http://rna.bgsu.edu/img/ty1/data';

        $this->header = array('Group',
                              'Mean Log Probability',
                              'Median Log Probability',
                              'Mean Percentile',
                              'Median Percentile',
                              'Mean Min Edit Distance',
                              'Median Min Edit Distance',
                              'Signature');
        $this->labels = array('str1'=>'-199.9','str2'=>'-199.3','str3'=>'-198.1','str4'=>'-195.1','str5'=>'-194.1','str6'=>'-193.8','str7'=>'-188.3',
                              'pseudoknots' => 'pseudoknots',
                              'pseudoknot1'=>'-231.0','pseudoknot2'=>'-231.0','pseudoknot3'=>'-230.7','pseudoknot4'=>'-230.7','pseudoknot5'=>'-230.6','pseudoknot6'=>'-230.6','pseudoknot7'=>'-230.5','pseudoknot8'=>'-230.4','pseudoknot9'=>'-230.3','pseudoknot10'=>'-230.1','pseudoknot11'=>'-230.1','pseudoknot12'=>'-230.1','pseudoknot13'=>'-230.0','pseudoknot14'=>'-229.9','pseudoknot15'=>'-229.8','pseudoknot16'=>'-229.8','pseudoknot17'=>'-229.8','pseudoknot18'=>'-229.8','pseudoknot19'=>'-229.7','pseudoknot20'=>'-229.6','pseudoknot21'=>'-229.5','pseudoknot22'=>'-229.4','pseudoknot23'=>'-229.4','pseudoknot24'=>'-229.4','pseudoknot25'=>'-229.3','pseudoknot26'=>'-229.3','pseudoknot27'=>'-229.3','pseudoknot28'=>'-229.3','pseudoknot29'=>'-229.2','pseudoknot30'=>'-229.2','pseudoknot31'=>'-229.2','pseudoknot32'=>'-229.2','pseudoknot33'=>'-229.2','pseudoknot34'=>'-229.1','pseudoknot35'=>'-229.1','pseudoknot36'=>'-229.1','pseudoknot37'=>'-229.1','pseudoknot38'=>'-229.1','pseudoknot39'=>'-229.1','pseudoknot40'=>'-229.1','pseudoknot41'=>'-229.0','pseudoknot42'=>'-229.0','pseudoknot43'=>'-228.9','pseudoknot44'=>'-228.9','pseudoknot45'=>'-228.8','pseudoknot46'=>'-228.8','pseudoknot47'=>'-228.8','pseudoknot48'=>'-228.8','pseudoknot49'=>'-228.8','pseudoknot50'=>'-228.8','pseudoknot51'=>'-228.8','pseudoknot52'=>'-228.7','pseudoknot53'=>'-228.7','pseudoknot54'=>'-228.7','pseudoknot55'=>'-228.7','pseudoknot56'=>'-228.7','pseudoknot57'=>'-228.7','pseudoknot58'=>'-228.7','pseudoknot59'=>'-228.7','pseudoknot60'=>'-228.6','pseudoknot61'=>'-228.6','pseudoknot62'=>'-228.6','pseudoknot63'=>'-228.6','pseudoknot64'=>'-228.6','pseudoknot65'=>'-228.5','pseudoknot66'=>'-228.5','pseudoknot67'=>'-228.5','pseudoknot68'=>'-228.5','pseudoknot69'=>'-228.4','pseudoknot70'=>'-228.4','pseudoknot71'=>'-228.4','pseudoknot72'=>'-228.3','pseudoknot73'=>'-228.3','pseudoknot74'=>'-228.3','pseudoknot75'=>'-228.2','pseudoknot76'=>'-228.2','pseudoknot77'=>'-228.2','pseudoknot78'=>'-228.2','pseudoknot79'=>'-228.2','pseudoknot80'=>'-228.2','pseudoknot81'=>'-228.2','pseudoknot82'=>'-228.2','pseudoknot83'=>'-228.2','pseudoknot84'=>'-228.2','pseudoknot85'=>'-228.1','pseudoknot86'=>'-228.1','pseudoknot87'=>'-228.1','pseudoknot88'=>'-228.0','pseudoknot89'=>'-228.0','pseudoknot90'=>'-228.0','pseudoknot91'=>'-228.0','pseudoknot92'=>'-228.0','pseudoknot93'=>'-227.9','pseudoknot94'=>'-227.8','pseudoknot95'=>'-227.8');


        parent::__construct();
    }

    function get_all_loops()
    {
        $html = $this->list_html_files();
        $ils = array();
        $hls = array();
        foreach ($html['il'] as $loop) {
            $str = substr($loop,0,strpos($loop,'_'));
            if (array_key_exists($str,$ils)) { $ils[$str]++; } else { $ils[$str] = 1; }
        }
        foreach ($html['hl'] as $loop) {
            $str = substr($loop,0,strpos($loop,'_'));
            if (array_key_exists($str,$hls)) { $hls[$str]++; } else { $hls[$str] = 1; }
        }

        $i = 1;
//         natsort($ils);
        foreach ($ils as $k => $v) {
            $table[] = array($i,
                             anchor(array('main','results',$k),$k),
                             $v,
                             $hls[$k]);
            $i++;
        }
        return $table;
    }

    function get_loops()
    {
        $html = $this->list_html_files();
        $results['il'] = $this->group_loops_by_structure($html['il']);
        $results['hl'] = $this->group_loops_by_structure($html['hl']);
        return $results;
    }

    function group_loops_by_structure($loops)
    {
        $combined_counts = array();
        foreach ($loops as $loop) {
            $pos = strpos($loop,'_');
            $k   = substr($loop,$pos+1,-5); // 12_15_20_25
            $v   = substr($loop,0,$pos);    // str1 or pseudoknot1
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

        $str_number = 1000; // much greater number
        foreach ($combined_counts as $k => $v) {
            // show a header for 7 structures, 6 etc
            if ($v < $str_number) {
                if ($v == count($loops)) {
                    $results .= "<h3>All $v structures</h3>";
                } elseif ($v == 1) {
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
            $found_in = '<div class="found_in">Found in ' . $str . '</div><br>';

            $htmlfile = $combined[$k][0] . '_' . $k . '.html';
            $results .= $this->get_loop_description($htmlfile,$loop_count,$found_in);
            $loop_count++;
        }

        return $results;
    }

    function get_loop_description($htmlfile, $loop_count,$found_in)
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
        $results .= $found_in;
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

                if (in_array($j,array(1,2,3,4))) {
                    $cell = round($cell,2);
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
            $results .= $this->get_loop_description($htmlfile,$loop_count,'');
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
        $results['hl'] = $this->process_results($html['hl']);

        return $results;
    }

    function get_ss_diagrams()
    {
        if ($handle = opendir($this->img_location)) {
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
            $file = substr($img,0,strpos($img,'.'));
            if ($file != 'pseudoknots') {
            $text .= <<<EOT
              <li>
                <a href="{$this->img_url}/{$img}" class='fancybox' rel='ss'>
                  <img class="thumbnail span4" src="$this->img_url/{$img}" alt="">
                  <a href="{$baseurl}main/results/$file">{$this->labels[$file]}</a>
                </a>
              </li>
EOT;
            } else {
            $text .= <<<EOT
              <li>
                <a href="{$this->img_url}/{$img}" class='fancybox' rel='ss'>
                  <img class="thumbnail span4" src="$this->img_url/{$img}" alt="">
                </a>
              </li>
EOT;
            }

        }
        return $text;
    }


}

/* End of file main_model.php */
/* Location: ./application/model/main_model.php */