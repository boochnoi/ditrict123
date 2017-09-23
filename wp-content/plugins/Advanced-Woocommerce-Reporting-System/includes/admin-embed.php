<?php
	// FONTAWESOME
	wp_register_style(__PW_REPORT_WCREPORT_FIELDS_PERFIX__.'font-awesome', __PW_REPORT_WCREPORT_CSS_URL__. 'back-end/font-awesome/font-awesome.min.css', true);
	wp_enqueue_style(__PW_REPORT_WCREPORT_FIELDS_PERFIX__.'font-awesome');
	
	// BOOTSTRAP
	wp_register_style(__PW_REPORT_WCREPORT_FIELDS_PERFIX__.'bootstrap-css', __PW_REPORT_WCREPORT_CSS_URL__. 'back-end/bootstrap/bootstrap.min.css', true);
	wp_enqueue_style(__PW_REPORT_WCREPORT_FIELDS_PERFIX__.'bootstrap-css');
	
	
	/////////////////////////CSS CHOSEN///////////////////////
	wp_register_style( __PW_REPORT_WCREPORT_FIELDS_PERFIX__.'chosen_css_report', __PW_REPORT_WCREPORT_CSS_URL__.'/back-end/chosen/chosen.css', false, '1.0.0' );
	wp_enqueue_style( __PW_REPORT_WCREPORT_FIELDS_PERFIX__.'chosen_css_report' );
	
	
	/////////////////////////CSS Loading///////////////////////
	wp_register_style( __PW_REPORT_WCREPORT_FIELDS_PERFIX__.'loading_css', __PW_REPORT_WCREPORT_CSS_URL__.'/back-end/loading/main.css', false, '1.0.0' );
	wp_enqueue_style( __PW_REPORT_WCREPORT_FIELDS_PERFIX__.'loading_css' );
	
	if(isset($_GET['parent']) && $_GET['parent']=='setting')
	{
		/////////////////////////Two Side Multi Select///////////////////////
		wp_enqueue_style( __PW_REPORT_WCREPORT_FIELDS_PERFIX__.'two-side-multi-select', __PW_REPORT_WCREPORT_CSS_URL__.'back-end/two-side-multiselect/jquerysctipttop.css');
		wp_enqueue_style( __PW_REPORT_WCREPORT_FIELDS_PERFIX__.'two-side-multi-select-style', __PW_REPORT_WCREPORT_CSS_URL__.'back-end/two-side-multiselect/style.css');
		wp_enqueue_style( __PW_REPORT_WCREPORT_FIELDS_PERFIX__.'two-side-multi-select-bootstrap', __PW_REPORT_WCREPORT_CSS_URL__.'back-end/two-side-multiselect/bootstrap.min.css');
	}
	
	/*wp_register_style(__PW_REPORT_WCREPORT_FIELDS_PERFIX__.'bootstrap-min', __PW_REPORT_WCREPORT_JS_URL__. 'back-end/dashboard/bootstrap/css/bootstrap.min.css', true);
	wp_enqueue_style(__PW_REPORT_WCREPORT_FIELDS_PERFIX__.'bootstrap-min');*/
	
	// JQUERY UI DATE PICKER
	//wp_enqueue_style('jquery-style', 'https://ajax.googleapis.com/ajax/libs/jqueryui/1.8.2/themes/smoothness/jquery-ui.css');
	wp_enqueue_style(__PW_REPORT_WCREPORT_FIELDS_PERFIX__.'jquery-style-ui', __PW_REPORT_WCREPORT_CSS_URL__. 'back-end/jquery-ui.min.css');
	wp_enqueue_script('jquery-ui-datepicker');
	wp_enqueue_script('jquery');

	//NEW DATATABLE
	
	wp_register_style(__PW_REPORT_WCREPORT_FIELDS_PERFIX__.'css-export',__PW_REPORT_WCREPORT_CSS_URL__. '/back-end/Datagrid/jquery.dataTables.min.css', true);
	wp_enqueue_style(__PW_REPORT_WCREPORT_FIELDS_PERFIX__.'css-export');	
	
	wp_register_style(__PW_REPORT_WCREPORT_FIELDS_PERFIX__.'css-export1', __PW_REPORT_WCREPORT_CSS_URL__.'/back-end/Datagrid/buttons.dataTables.min.css', true);
	wp_enqueue_style(__PW_REPORT_WCREPORT_FIELDS_PERFIX__.'css-export1');	
	
	
	wp_register_script(__PW_REPORT_WCREPORT_FIELDS_PERFIX__.'datatable-datatable',__PW_REPORT_WCREPORT_JS_URL__. '/back-end/Datagrid/jquery.dataTables.min.js', true);
	wp_enqueue_script(__PW_REPORT_WCREPORT_FIELDS_PERFIX__.'datatable-datatable');
    
    wp_register_script(__PW_REPORT_WCREPORT_FIELDS_PERFIX__.'datatable-btn', __PW_REPORT_WCREPORT_JS_URL__.'/back-end/Datagrid/dataTables.buttons.min.js', true);
	wp_enqueue_script(__PW_REPORT_WCREPORT_FIELDS_PERFIX__.'datatable-btn');
    
    wp_register_script(__PW_REPORT_WCREPORT_FIELDS_PERFIX__.'datatable-zip', __PW_REPORT_WCREPORT_JS_URL__.'/back-end/Datagrid/jszip.min.js', true);
	wp_enqueue_script(__PW_REPORT_WCREPORT_FIELDS_PERFIX__.'datatable-zip');
    
    wp_register_script(__PW_REPORT_WCREPORT_FIELDS_PERFIX__.'datatable-pdfmake', __PW_REPORT_WCREPORT_JS_URL__.'/back-end/Datagrid/pdfmake.min.js', true);
	wp_enqueue_script(__PW_REPORT_WCREPORT_FIELDS_PERFIX__.'datatable-pdfmake');
    
    wp_register_script(__PW_REPORT_WCREPORT_FIELDS_PERFIX__.'datatable-font', __PW_REPORT_WCREPORT_JS_URL__.'/back-end/Datagrid/vfs_fonts.js', true);
	wp_enqueue_script(__PW_REPORT_WCREPORT_FIELDS_PERFIX__.'datatable-font');
    
    wp_register_script(__PW_REPORT_WCREPORT_FIELDS_PERFIX__.'datatable-btn5', __PW_REPORT_WCREPORT_JS_URL__.'/back-end/Datagrid/buttons.html5.min.js', true);
	wp_enqueue_script(__PW_REPORT_WCREPORT_FIELDS_PERFIX__.'datatable-btn5');
	
	wp_register_script(__PW_REPORT_WCREPORT_FIELDS_PERFIX__.'datatable-json', __PW_REPORT_WCREPORT_JS_URL__.'/back-end/Datagrid/jquery.tabletojson.js', true);
	wp_enqueue_script(__PW_REPORT_WCREPORT_FIELDS_PERFIX__.'datatable-json');

	if(isset($_GET['parent']) && $_GET['parent']=='dashboard')
	{
		//amChart
		wp_register_style(__PW_REPORT_WCREPORT_FIELDS_PERFIX__.'amcharts-export', __PW_REPORT_WCREPORT_CSS_URL__.'back-end/amchart/export.css', true);
		wp_enqueue_style(__PW_REPORT_WCREPORT_FIELDS_PERFIX__.'amcharts-export');	
	
		
		wp_register_script(__PW_REPORT_WCREPORT_FIELDS_PERFIX__.'amcharts', 'https://www.amcharts.com/lib/3/amcharts.js', true);
		wp_enqueue_script(__PW_REPORT_WCREPORT_FIELDS_PERFIX__.'amcharts');	
		
		wp_register_script(__PW_REPORT_WCREPORT_FIELDS_PERFIX__.'serial', 'https://www.amcharts.com/lib/3/serial.js', true);
		wp_enqueue_script(__PW_REPORT_WCREPORT_FIELDS_PERFIX__.'serial');	
		
		wp_register_script(__PW_REPORT_WCREPORT_FIELDS_PERFIX__.'none_theme', 'https://www.amcharts.com/lib/3/themes/none.js', true);
		wp_enqueue_script(__PW_REPORT_WCREPORT_FIELDS_PERFIX__.'none_theme');	
		
		wp_register_script(__PW_REPORT_WCREPORT_FIELDS_PERFIX__.'dark_theme', 'https://www.amcharts.com/lib/3/themes/dark.js', true); //dark.js , light.js, chalk.js , patterns.js
		wp_enqueue_script(__PW_REPORT_WCREPORT_FIELDS_PERFIX__.'dark_theme');
		
		wp_register_script(__PW_REPORT_WCREPORT_FIELDS_PERFIX__.'light_theme', 'https://www.amcharts.com/lib/3/themes/light.js', true); //dark.js , light.js, chalk.js , 
		wp_enqueue_script(__PW_REPORT_WCREPORT_FIELDS_PERFIX__.'light_theme');
		
		wp_register_script(__PW_REPORT_WCREPORT_FIELDS_PERFIX__.'patterns_theme', 'https://www.amcharts.com/lib/3/themes/patterns.js', true); //dark.js , light.js, chalk.js , 
		wp_enqueue_script(__PW_REPORT_WCREPORT_FIELDS_PERFIX__.'patterns_theme');
		
		wp_register_script(__PW_REPORT_WCREPORT_FIELDS_PERFIX__.'amstock', 'https://www.amcharts.com/lib/3/amstock.js', true);
		wp_enqueue_script(__PW_REPORT_WCREPORT_FIELDS_PERFIX__.'amstock');	
		
		wp_register_script(__PW_REPORT_WCREPORT_FIELDS_PERFIX__.'ampie', 'https://www.amcharts.com/lib/3/pie.js', true);
		wp_enqueue_script(__PW_REPORT_WCREPORT_FIELDS_PERFIX__.'ampie');	
			
		wp_register_script(__PW_REPORT_WCREPORT_FIELDS_PERFIX__.'amchart-export', 'https://www.amcharts.com/lib/3/plugins/export/export.js', true);
		wp_enqueue_script(__PW_REPORT_WCREPORT_FIELDS_PERFIX__.'amchart-export');	

		//MAP
		wp_register_style(__PW_REPORT_WCREPORT_FIELDS_PERFIX__.'map-style', __PW_REPORT_WCREPORT_CSS_URL__.'/back-end/map/style.css', true);
		wp_enqueue_style(__PW_REPORT_WCREPORT_FIELDS_PERFIX__.'map-style');		
		
		wp_register_script(__PW_REPORT_WCREPORT_FIELDS_PERFIX__.'mousewheel', __PW_REPORT_WCREPORT_JS_URL__.'/back-end/map/jquery.mousewheel.min.js', true);
		wp_enqueue_script(__PW_REPORT_WCREPORT_FIELDS_PERFIX__.'mousewheel');	
	
		wp_register_script(__PW_REPORT_WCREPORT_FIELDS_PERFIX__.'raphael', __PW_REPORT_WCREPORT_JS_URL__.'/back-end/map/raphael-min.js', true);
		wp_enqueue_script(__PW_REPORT_WCREPORT_FIELDS_PERFIX__.'raphael');	
	
		
		wp_register_script(__PW_REPORT_WCREPORT_FIELDS_PERFIX__.'js-mapael', __PW_REPORT_WCREPORT_JS_URL__. '/back-end/map/jquery.mapael.js', true);
		wp_enqueue_script(__PW_REPORT_WCREPORT_FIELDS_PERFIX__.'js-mapael');
		
		wp_register_script(__PW_REPORT_WCREPORT_FIELDS_PERFIX__.'knob', __PW_REPORT_WCREPORT_JS_URL__.'/back-end/map/jquery.knob.js', true);
		wp_enqueue_script(__PW_REPORT_WCREPORT_FIELDS_PERFIX__.'knob');	
		
		/*wp_register_script(__PW_REPORT_WCREPORT_FIELDS_PERFIX__.'js-france_departments', __PW_REPORT_WCREPORT_JS_URL__. 'back-end/map/france_departments.js', true);
		wp_enqueue_script(__PW_REPORT_WCREPORT_FIELDS_PERFIX__.'js-france_departments');*/
		
		wp_register_script(__PW_REPORT_WCREPORT_FIELDS_PERFIX__.'js-world_countries', __PW_REPORT_WCREPORT_JS_URL__. '/back-end/map/world_countries.js', true);
		wp_enqueue_script(__PW_REPORT_WCREPORT_FIELDS_PERFIX__.'js-world_countries');
		
		wp_register_script(__PW_REPORT_WCREPORT_FIELDS_PERFIX__.'js-usa_states', __PW_REPORT_WCREPORT_JS_URL__. '/back-end/map/usa_states.js', true);
		wp_enqueue_script(__PW_REPORT_WCREPORT_FIELDS_PERFIX__.'js-usa_states');
		
	
		wp_enqueue_script(__PW_REPORT_WCREPORT_FIELDS_PERFIX__.'js-googlemap', 'https://maps.google.com/maps/api/js?sensor=false', true);
		
		/*wp_register_script(__PW_REPORT_WCREPORT_FIELDS_PERFIX__.'js-mapael-examples', __PW_REPORT_WCREPORT_JS_URL__. 'back-end/map/examples.js', true);
		wp_enqueue_script(__PW_REPORT_WCREPORT_FIELDS_PERFIX__.'js-mapael-examples');*/
		
	
		wp_register_style(__PW_REPORT_WCREPORT_FIELDS_PERFIX__.'bootstrap-min', __PW_REPORT_WCREPORT_JS_URL__. '/back-end/dashboard/bootstrap/css/bootstrap.min.css', true);
	//	wp_enqueue_style(__PW_REPORT_WCREPORT_FIELDS_PERFIX__.'bootstrap-min');
		
		wp_register_style(__PW_REPORT_WCREPORT_FIELDS_PERFIX__.'daterangepicker', __PW_REPORT_WCREPORT_JS_URL__. '/back-end/dashboard/bootstrap-daterangepicker/daterangepicker.css', true);
		wp_enqueue_style(__PW_REPORT_WCREPORT_FIELDS_PERFIX__.'daterangepicker');
		
		wp_register_script(__PW_REPORT_WCREPORT_FIELDS_PERFIX__.'bootstrap', __PW_REPORT_WCREPORT_JS_URL__. '/back-end/dashboard/bootstrap/js/bootstrap.min.js', true);
		wp_enqueue_script(__PW_REPORT_WCREPORT_FIELDS_PERFIX__.'bootstrap');
		
		wp_register_script(__PW_REPORT_WCREPORT_FIELDS_PERFIX__.'moment', __PW_REPORT_WCREPORT_JS_URL__. '/back-end/dashboard/bootstrap-daterangepicker/moment.js', true);
		wp_enqueue_script(__PW_REPORT_WCREPORT_FIELDS_PERFIX__.'moment');
		
		wp_register_script(__PW_REPORT_WCREPORT_FIELDS_PERFIX__.'daterangepicker', __PW_REPORT_WCREPORT_JS_URL__. '/back-end/dashboard/bootstrap-daterangepicker/daterangepicker.js', true);
		wp_enqueue_script(__PW_REPORT_WCREPORT_FIELDS_PERFIX__.'daterangepicker');
		
		
		wp_register_script(__PW_REPORT_WCREPORT_FIELDS_PERFIX__.'dashboard-custom-js', __PW_REPORT_WCREPORT_JS_URL__. 'back-end/dashboard/dashboard-custom-js.js', true);
		wp_enqueue_script(__PW_REPORT_WCREPORT_FIELDS_PERFIX__.'dashboard-custom-js');
	}


	
	//TAB
	wp_register_style( __PW_REPORT_WCREPORT_FIELDS_PERFIX__.'adminform-tab1-css', __PW_REPORT_WCREPORT_CSS_URL__.'/back-end/Tab/tabs.css' , false, '1.0.0' );
	wp_enqueue_style(__PW_REPORT_WCREPORT_FIELDS_PERFIX__.'adminform-tab1-css');	
	
	wp_register_style( __PW_REPORT_WCREPORT_FIELDS_PERFIX__.'adminform-tab2-css', __PW_REPORT_WCREPORT_CSS_URL__.'/back-end/Tab/tabstyles.css' , false, '1.0.0' );
	wp_enqueue_style(__PW_REPORT_WCREPORT_FIELDS_PERFIX__.'adminform-tab2-css');	
	
	wp_register_script( __PW_REPORT_WCREPORT_FIELDS_PERFIX__.'adminform-tab1-js', __PW_REPORT_WCREPORT_JS_URL__.'/back-end/Tab/modernizr.custom.js' , false, '1.0.0' );
	wp_enqueue_script(__PW_REPORT_WCREPORT_FIELDS_PERFIX__.'adminform-tab1-js');
	
	wp_register_script( __PW_REPORT_WCREPORT_FIELDS_PERFIX__.'adminform-tab2-js', __PW_REPORT_WCREPORT_JS_URL__.'/back-end/Tab/cbpFWTabs.js' , false, '1.0.0' );
	wp_enqueue_script(__PW_REPORT_WCREPORT_FIELDS_PERFIX__.'adminform-tab2-js');

	if(isset($_GET['parent']) && $_GET['parent']=='setting')
	{
		//////////////////MULTISELECT MULTI SIDE//////////////////////////
		wp_enqueue_script(__PW_REPORT_WCREPORT_FIELDS_PERFIX__.'two-side-multi-select-bootstrap-js',__PW_REPORT_WCREPORT_JS_URL__.'back-end/two-side-multiselect/bootstrap.min.js',array( 'jquery' ));
		
		wp_enqueue_script(__PW_REPORT_WCREPORT_FIELDS_PERFIX__.'two-side-multi-select-js',__PW_REPORT_WCREPORT_JS_URL__.'back-end/two-side-multiselect/multiselect.js',array( 'jquery' ));
	}
	
	//PANEL
	
	wp_register_style(__PW_REPORT_WCREPORT_FIELDS_PERFIX__.'panel-demo-css', __PW_REPORT_WCREPORT_CSS_URL__. '/back-end/panel/demo.css', true);
	//wp_enqueue_style(__PW_REPORT_WCREPORT_FIELDS_PERFIX__.'panel-demo-css');
	
	wp_register_style(__PW_REPORT_WCREPORT_FIELDS_PERFIX__.'panel-main-css', __PW_REPORT_WCREPORT_CSS_URL__. '/back-end/panel/component.css', true);
	wp_enqueue_style(__PW_REPORT_WCREPORT_FIELDS_PERFIX__.'panel-main-css');
	
	wp_register_script( __PW_REPORT_WCREPORT_FIELDS_PERFIX__.'panel-modernize-js', __PW_REPORT_WCREPORT_JS_URL__.'/back-end/panel/modernizr-custom.js' , false, '1.0.0' );
	wp_enqueue_script(__PW_REPORT_WCREPORT_FIELDS_PERFIX__.'panel-modernize-js');
	
	wp_register_script( __PW_REPORT_WCREPORT_FIELDS_PERFIX__.'panel-class-js', __PW_REPORT_WCREPORT_JS_URL__.'/back-end/panel/classie.js' , false, '1.0.0' );
	wp_enqueue_script(__PW_REPORT_WCREPORT_FIELDS_PERFIX__.'panel-class-js');
	
	wp_register_script( __PW_REPORT_WCREPORT_FIELDS_PERFIX__.'panel-main-js', __PW_REPORT_WCREPORT_JS_URL__.'/back-end/panel/main.js' , false, '1.0.0' );
	wp_enqueue_script(__PW_REPORT_WCREPORT_FIELDS_PERFIX__.'panel-main-js');
	
	
	
	//////////////////CHOSEN//////////////////////////
	wp_register_script( __PW_REPORT_WCREPORT_FIELDS_PERFIX__.'chosen_js1', __PW_REPORT_WCREPORT_JS_URL__.'/back-end/chosen/chosen.jquery.min.js' , false, '1.0.0' );
	wp_enqueue_script( __PW_REPORT_WCREPORT_FIELDS_PERFIX__.'chosen_js1' );


	// PLUGIN MAIN
	wp_register_style(__PW_REPORT_WCREPORT_FIELDS_PERFIX__.'css-custom', __PW_REPORT_WCREPORT_CSS_URL__. 'back-end/plugin-style.css', true);
	wp_enqueue_style(__PW_REPORT_WCREPORT_FIELDS_PERFIX__.'css-custom');
	
	wp_register_script(__PW_REPORT_WCREPORT_FIELDS_PERFIX__.'js-custom', __PW_REPORT_WCREPORT_JS_URL__. 'back-end/custom-js.js', true);
	wp_enqueue_script(__PW_REPORT_WCREPORT_FIELDS_PERFIX__.'js-custom');	
	
	$ajax_url=admin_url( 'admin-ajax.php');
	//IF HOST BLOCK admin-ajax.php
	//$ajax_url=__PW_REPORT_WCREPORT_URL__.'/admin-ajax.php';
	
	/*$parsed = parse_url($ajax_url);

	if(strpos($parsed['host'], "www.") === 0)
	{
		if(isset($_SERVER['HTTPS']))
			$ajax_url=admin_url( 'admin-ajax.php',"https");
		else
			$ajax_url=admin_url( 'admin-ajax.php',"http");
		$ajax_url=str_replace("www.","",$ajax_url);				
	}*/
		
	$currency_format=get_option('woocommerce_price_thousand_sep',',');
	
	wp_localize_script(__PW_REPORT_WCREPORT_FIELDS_PERFIX__.'js-custom','params',
		array(
			'nonce' =>wp_create_nonce( 'pw_livesearch_nonce' ),
			'address' =>__PW_REPORT_WCREPORT_URL__,
			'woo_currency' => get_woocommerce_currency_symbol(),
			'currency_format' => $currency_format,
			'ajaxurl' => $ajax_url,
		)
	);	
	
	
	wp_register_script( __PW_REPORT_WCREPORT_FIELDS_PERFIX__.'dependency', __PW_REPORT_WCREPORT_JS_URL__.'back-end/dependency/dependsOn-1.0.1.min.js' , false, '1.0.0' );
	wp_enqueue_script( __PW_REPORT_WCREPORT_FIELDS_PERFIX__.'dependency' );
	
	if(!function_exists('pw_report_dependency'))
	{
		function pw_report_dependency($element_id,$args)
		{
			$output='';
			$output.='
			<script type="text/javascript">
				jQuery(document).ready(function(jQuery){
				
				jQuery("."+"'.$element_id.'_field").dependsOn({';		
					foreach($args['parent_id'] as $parent)
					{
						$element_type=$args[$parent][0];
						unset($args[$parent][0]);
						switch($element_type)
						{
							case "select":
							{
								$output.= '
								"#'.$parent.'": {
										values: [\''.(is_array($args[$parent]) ? implode("','", $args[$parent]) : $args[$parent]).'\']
								},';
							}
							break;
							
							case "checkbox":
							{
								if($args[$parent])
									$output.= '
									"#'.$parent.'": {
										checked: true
									},';
								else
									$output.= '
									"#'.$parent.'": {
										checked: false
									},';

							}
							break;
						}
					}
			$output.='
					});
				});
			 </script>';
			 return $output;
		}
	}
	
?>