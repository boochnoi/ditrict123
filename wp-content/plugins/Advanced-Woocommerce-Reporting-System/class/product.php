<?php
	global $pw_rpt_main_class;
?>
<div class="wrap">
	<div class="row">
        <div class="col-xs-12">
            <div class="awr-box">
                <div class="awr-title">
                    <h3>
                        <i class="fa fa-apple"></i>
                        <span><?php _e('Configuration',__PW_REPORT_WCREPORT_TEXTDOMAIN__);?></span>
                    </h3>
                    
                </div><!--awr-title -->
                <div class="awr-box-content-form">
                    <?php
                        $table_name='product';
                        $pw_rpt_main_class->search_form_html($table_name);
                    ?>	  
                </div><!--awr-box-content -->
            </div><!--awr-box -->
        </div>
    </div>
    
    <div class="row">
        <div class="col-md-12" id="target">
		 <?php
            $table_name='product';
            $pw_rpt_main_class->table_html($table_name);
        ?>	  
        </div>
    </div>
    
</div>