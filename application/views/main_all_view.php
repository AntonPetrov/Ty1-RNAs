    <div class="container main_ss_view">

      <div class="content">
        <div class="page-header">
          <h1>
            <?php echo $title;?>
          </h1>
        </div>

        <div class="row">
            <div class="span6">
                <?=$loops?>
            </div>

            <div class="span6 offset2">
                <ul class="media-grid">
                    <?=$graphs?>
                </ul>
            </div>
        </div>

      </div>

    <script>
        $(function () {
    		$(".fancybox").fancybox();
    		$("#sortable").tablesorter();
        })
    </script>
