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
<!--                     <?=$results['hl']?> -->
                    Coming soon.
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
            function appletLoaded (){
                var groupNum = $('.exemplar:first').next().attr("href").match(/(Group_.+?).html/)[1];
                $('.exemplar:first').attr('checked','checked');
                show_first_instance_in_jmol(groupNum);
            }

            function show_first_instance_in_jmol(id) {
                jmolScript('zap;');
                jmolScript('load "http://rna.bgsu.edu/research/anton/share/iljun6/PDBDatabase/'+id+'/'+id+'_1.pdb";');
                apply_jmol_styling();
            }

            function apply_jmol_styling() {
                    jmolScript('spacefill off;');
                    jmolScript('select [U];color navy;');
                    jmolScript('select [G]; color chartreuse;');
                    jmolScript('select [C]; color gold;');
                    jmolScript('select [A]; color red;');
                    jmolScript('select 1.2; color grey; color translucent 0.8;');
                    jmolScript('select protein; color purple; color translucent 0.8;');
                    jmolScript('select 1.0;spacefill off;center 1.1;');
                    jmolScript('frame *;display displayed and not 1.2;');
                    jmolScript('select hetero;color pink;');
            }

          $(function () {

//             $(".pdb").click(LookUpPDBInfo);
            $(".sortable").tablesorter();

            $('.exemplar').click(function() {

                var groupNum = $(this).next().attr("href").match(/(Group_.+?).html/)[1];
//                 var loop_id = t.next().html();
                show_first_instance_in_jmol(groupNum);
            });

//             jmol_neighborhood_button_click('neighborhood');
//             jmol_show_nucleotide_numbers_click('showNtNums');

            $('#jmol').css('position','fixed');
            var offset_left = $('#left_content').offset().left + 500; // 530 = span9 width
            var offset_top  = $('#left_content').offset().top;
            $('#jmol').css('left',offset_left);
            $('#jmol').css('top', offset_top);
        });
      </script>

