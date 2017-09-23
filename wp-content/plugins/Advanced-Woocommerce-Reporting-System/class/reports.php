<?php
	global $pw_rpt_main_class;
?>
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
						$table_name='products';
						$pw_rpt_main_class->search_form_html($table_name);
					?>    				
				</div>
			</div>
		</div>
	</div>
	
    <div class="row">
    	<div class="col-md-12">
        	<?php
				$table_name='products';
            	$pw_rpt_main_class->table_html($table_name);
			?>	
    	</div>
    </div>

	
    
    <!--CHART-->
    <br />
	<div class="row">
		<div class="col-xs-12">
        	<div class="awr-box">
            	<div class="awr-title">
                	<h3><i class="fa fa-desktop"></i><?php _e('Chart',__PW_REPORT_WCREPORT_TEXTDOMAIN__);?></h3>
                    <div class="awr-title-icons">
                    	<div class="awr-title-icon awr-toggle-icon"><i class="fa fa-arrow-up"></i></div>
                        <div class="awr-title-icon awr-setting-icon"><i class="fa fa-cog"></i></div>
                        <div class="awr-title-icon awr-close-icon"><i class="fa fa-times"></i></div>
                    </div>
                </div><!--awr-title -->
                <div class="awr-box-content-form">
					<div id="chartdiv" style="width: 100%; height: 450px;"></div>				
				</div>
			</div>
		</div>
	</div>
	
	
	<div class="row">
		<div class="col-xs-12">
        	<div class="awr-box">
            	<div class="awr-title">
                	<h3><i class="fa fa-desktop"></i><?php _e('Map',__PW_REPORT_WCREPORT_TEXTDOMAIN__);?></h3>
                    <div class="awr-title-icons">
                    	<div class="awr-title-icon awr-toggle-icon"><i class="fa fa-arrow-up"></i></div>
                        <div class="awr-title-icon awr-setting-icon"><i class="fa fa-cog"></i></div>
                        <div class="awr-title-icon awr-close-icon"><i class="fa fa-times"></i></div>
                    </div>
                </div><!--awr-title -->
                <div class="awr-box-content container5">
					<div class="map">
                        <span>Alternative content for the map</span>
                    </div>
					<div class="rightPanel">
                        <h2>Select a year</h2>
                        <div class="knobContainer">
                            <input class="knob" data-width="80" data-height="80" data-min="2013" data-max="2014" data-cursor=true data-fgColor="#454545" data-thickness=.45 value="2013" data-bgColor="#c7e8ff" /> 
                        </div>
                        <div class="areaLegend">
                            <span>Alternative content for the legend</span>
                        </div>
                        <div class="plotLegend"></div>
                    </div>    
				</div>
			</div>
		</div>
	</div>     

	
    


	<?php
    	$arr=array(
			"2013" => 
				array(	
					"areas" =>
						array(
							"AF" => array(
								"value" => 30428397,
								"href" => "http://en.wikipedia.org/w/index.php?search=Afghanistan",
								"tooltip" => array(
									"content" => "<span style=\"font-weight:bold;\">Afghanistan</span><br />Population : 30428397"
								)
							),
							"ZA" => array(
								"value" => 42385364,
								"href" => "http://en.wikipedia.org/w/index.php?search=South Africa",
								"tooltip" => array(
									"content" => "<span style=\"font-weight:bold;\">South Africa</span><br />Population : 42385364"
								)
							)
						),
					"plots" => 
						array(
						"paris" => array(
							"value" => 1025415,
							"tooltip" => array(
								"content" => "<span style=\"font-weight:bold;\">Paris</span><br />Population: 1025415"
							)
						),
						"newyork" => array(
							"value" => 785175,
							"tooltip" => array(
								"content" => "<span style=\"font-weight:bold;\">New-York</span><br />Population: 785175"
							)
						),
						"sydney" => array(
							"value" => 477087,
							"tooltip" => array(
								"content" => "<span style=\"font-weight:bold;\">Sydney</span><br />Population: 477087"
							)
						),
						"brasilia" => array(
							"value" => 211212,
							"tooltip" => array(
								"content" => "<span style=\"font-weight:bold;\">Brasilia</span><br />Population: 211212"
							)
						),
						"tokyo" => array(
							"value" => 433935,
							"tooltip" => array(
								"content" => "<span style=\"font-weight:bold;\">Tokyo</span><br />Population: 433935"
							)
						),
						"tehran" => array(
							"value" => 433935,
							"tooltip" => array(
								"content" => "<span style=\"font-weight:bold;\">Tehran</span><br />Population: 433935"
							)
						)
					)
				)
			);	
	?>

<script>
	
	// Fake data for countries and cities from 2003 to 2013
	var data = <?php echo json_encode($arr);	 ?>;

	// Default plots params
	var plots = {
            "paris": {
                "latitude": 48.86,
                "longitude": 2.3444,
				"text" : {
					"position": "left",
					"content": "Paris"
				},
				"href":"http://en.wikipedia.org/w/index.php?search=Paris"
            },
            "newyork": {
                "latitude": 40.667,
                "longitude": -73.833,
				"text": {
					"content" : "New york"
				},
				"href":"http://en.wikipedia.org/w/index.php?search=New York"
            },
            "sydney": {
                "latitude": -33.917,
                "longitude": 151.167,
				"text": {
					"content" : "Sydney"
				},
				"href":"http://en.wikipedia.org/w/index.php?search=Sidney"
            },
            "brasilia": {
                "latitude": -15.781682,
                "longitude": -47.924195,
				"text": {
					"content" : "Brasilia"
				},
				"href":"http://en.wikipedia.org/w/index.php?search=Brasilia"
            },
            "tokyo": {
                "latitude": 35.687418,
                "longitude": 139.692306,
				"text": {
					"content" : "Tokyo"
				},
				"href":"http://en.wikipedia.org/w/index.php?search=Tokyo"
            },
            "tehran": {
                "latitude": 35.6961,
                "longitude": 51.4231,
				"text": {
					"content" : "Tehran"
				},
				"href":"http://en.wikipedia.org/w/index.php?search=Tokyo"
            }
        };

</script>

