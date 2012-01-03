    <div class="container main_results_view">

      <div class="content">
        <div class="page-header">
          <h1><?php echo $title;?></h1>
        </div>
        <div class="row">
          <div class="span8" id="left_content">

            <ul class="tabs" data-tabs="tabs">
                <li class="active"><a href="#ils">Internal Loops</a></li>
                <li><a href="#hls">Hairpin Loops</a></li>
            </ul>


            <div class="tab-content">

                <div class="tab-pane active" id="ils">
                    <?=$results['il']?>
                </div>

                <div class="tab-pane" id="hls">
                    <?=$results['hl']?>
<!--                     Coming soon. -->
                </div>

            </div>

          </div>

            <div class="span6 offset1" id="jmol" >
                <div class="block jmolheight">
                    <script type="text/javascript">
                        jmolInitialize("/jmol");
                        jmolSetAppletColor("#ffffff");
                        jmolApplet(340, "javascript appletLoaded()");
                    </script>
                </div>
           </div>



        </div>
      </div>

      <script>
          $(function () {
            position_jmol_applet();
//             $(".pdb").click(LookUpPDBInfo);
            $(".sortable").tablesorter();
            $('.exemplar').click(function() {
                var groupNum = $(this).next().attr("href").match(/(Group_.+?).html/)[1];
                var loopType = $(this).next().html().substring(0,2);
                show_first_instance_in_jmol(groupNum,loopType);
            });
        });
      </script>