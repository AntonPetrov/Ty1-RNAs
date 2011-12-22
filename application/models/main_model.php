<?php
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

    function process_results($file_list)
    {
        $results = '';

        // open html file
        $count = 1;
        foreach ($file_list as $htmlfile) {

            // open fasta file
            $file = fopen($this->location . str_replace('.html','.fasta',$htmlfile), "r") or exit("Unable to open file!");
            $variants = array();
            while(!feof($file)) {
                $line = fgets($file);
                if (preg_match('/^(A|C|G|U)/',$line)) {
                    $variants[] = $line;
                }
            }
            fclose($file);
            $vars_count = count($variants);
            $variants = implode(', ',$variants);
            $results .= "<strong>#{$count}</strong> <a href='{$this->data_url}/{$htmlfile}' target='_blank'>$htmlfile</a> $vars_count variants <br> $variants";
            $count++;

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

                    $data[$i][] = $cell;
                }
            }

            $this->table->set_heading($this->header);
            $tmpl = array( 'table_open'  => "<table class='condensed-table zebra-striped bordered-table sortable'>" );
            $this->table->set_template($tmpl);
            $results .= $this->table->generate($data);

            unset($tds);
        }

        return $results;
    }

    function get_results($id)
    {
        // get files, sort by il and hl
        if ($handle = opendir($this->location)) {
            while (false !== ($entry = readdir($handle))) {
                if (preg_match("/^{$id}.+?html$/",$entry)) {
                    if (substr_count($entry,'_') == 2) {
                        $html['hl'][] = $entry;
                    } else {
                        $html['il'][] = $entry;
                    }
                }
            }
            closedir($handle);
        }

        $results['il'] = $this->process_results($html['il']);
        $results['hl'] = $this->process_results($html['hl']);

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
                  <a href="{$baseurl}main/results/$file">$this->labels[$file]</a>
                </a>
              </li>
EOT;
        }
        return $text;
    }


}

/* End of file main_model.php */
/* Location: ./application/model/main_model.php */