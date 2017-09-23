<?php
	global $pw_rpt_main_class;
?>
<div class="wrap">
    <div class="row">
		<div class="col-xs-12">
        	<div class="awr-box">
            	<div class="awr-title">
                	<h3><i class="fa fa-desktop"></i><?php _e('Configuration',__PW_REPORT_WCREPORT_TEXTDOMAIN__);?></h3>
                    <div class="awr-title-icons">
                    	<div class="awr-title-icon awr-toggle-icon"><i class="fa fa-arrow-up"></i></div>
                        <div class="awr-title-icon awr-setting-icon"><i class="fa fa-cog"></i></div>
                        <div class="awr-title-icon awr-close-icon"><i class="fa fa-times"></i></div>
                    </div>
                </div><!--awr-title -->
                <div class="awr-box-content-form">
					<?php
						$table_name='variation_per_month';
						$pw_rpt_main_class->search_form_html($table_name);
					?>    				
				</div>
			</div>
		</div>
	</div>
	
    <div class="row">
        <div class="col-md-12" id="target">
            <?php
                $table_name='variation_per_month';
                $pw_rpt_main_class->table_html($table_name);
            ?>	
        </div>
    </div>
</div>