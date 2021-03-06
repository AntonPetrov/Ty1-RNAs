    <div class="topbar" data-dropdown="dropdown">
      <div class="fill">
        <div class="container">
          <a class="brand" href="<?php echo $baseurl;?>">Ty1 Analysis</a>
          <ul class="nav">

            <li><a href="<?php echo $baseurl;?>">Alternative structures</a></li>

            <li class="dropdown">
                <a href="#" class="dropdown-toggle">Sans pseudoknot</a>
                <ul class="dropdown-menu">
                    <?php
                        $labels = array('str1'=>'-199.9',
                        'str2'=>'-199.3',
                        'str3'=>'-198.1',
                        'str4'=>'-195.1',
                        'str5'=>'-194.1',
                        'str6'=>'-193.8',
                        'str7'=>'-188.3');
                        foreach ($labels as $k => $v) {
                            echo "<li><a href='{$baseurl}main/results/{$k}'>$v</a></li>";
                        }
                    ?>
                </ul>
            </li>

            <li class="dropdown">
                <a href="#" class="dropdown-toggle">Loops</a>
                <ul class="dropdown-menu">
                    <li><a href='<?=$baseurl?>main/loops/rnastructure'>RNAStructure</a></li>
                    <li><a href='<?=$baseurl?>main/loops/k2n'>K2N</a></li>
                </ul>
            </li>

            <li><a href="http://rna.bgsu.edu/research/jar3d" target="_blank">JAR3D</a></li>
            <li><a href="http://goo.gl/aYyy4" target="_blank">Google Doc</a></li>
            <li><a href="http://rna.bgsu.edu/research/ty1_dev">Dev</a></li>

          </ul>
        </div>
      </div>
    </div>