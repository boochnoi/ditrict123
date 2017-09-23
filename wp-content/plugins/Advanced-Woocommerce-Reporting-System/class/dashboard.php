<?php
	global $pw_rpt_main_class;
?>

	<!-- DASHBOARD HEADER -->
	<div class="row">
        <h3 class="page-title">
        Dashboard <small>reports & statistics</small>
        </h3>
        <div class="page-bar">
            <ul class="page-breadcrumb">
                <li>
                    <i class="fa fa-home"></i>
                    <a href="index.html">Home</a>
                    <i class="fa fa-angle-right"></i>
                </li>
                <li>
                    <a href="#">Dashboard</a>
                </li>
            </ul>
            <div class="page-toolbar">
                <div id="dashboard-report-range" class="pull-right tooltips btn btn-fit-height grey-salt" data-placement="top" data-original-title="Change dashboard date range">
                    
                    <div id="reportrange" class="pull-right" style="background: #fff; cursor: pointer; padding: 5px 10px; border: 1px solid #ccc; width: 100%">
                        <i class="glyphicon glyphicon-calendar fa fa-calendar"></i>&nbsp;
                        <span></span> <b class="caret"></b>
                    </div>
                    
                    
                </div>
            </div>
        </div>
	</div>

	<!-- DASHBOARD BOXES -->
	<div class="postbox hide">
        <div class="handlediv" title="Click to toggle"><br></div>
        <h3 class="hndle ui-sortable-handle"><span><?php _e('Totals',__PW_REPORT_WCREPORT_TEXTDOMAIN__);?></span></h3>
        <div class="inside">
        	<div class="row">
                <div class="col-lg-3 col-md-3 col-sm-6 col-xs-12">
                    <div class="dashboard-stat blue-madison">
                        <div class="visual">
                            <i class="fa fa-comments"></i>
                        </div>
                        <div class="details">
                            <div class="number">
                                 1349
                            </div>
                            <div class="desc">
                                 New Feedbacks
                            </div>
                        </div>
                        <a class="more" href="javascript:;">
                        View more <i class="m-icon-swapright m-icon-white"></i>
                        </a>
                    </div>
                </div>
                <div class="col-lg-3 col-md-3 col-sm-6 col-xs-12">
                    <div class="dashboard-stat red-intense">
                        <div class="visual">
                            <i class="fa fa-bar-chart-o"></i>
                        </div>
                        <div class="details">
                            <div class="number">
                                 12,5M$
                            </div>
                            <div class="desc">
                                 Total Profit
                            </div>
                        </div>
                        <a class="more" href="javascript:;">
                        View more <i class="m-icon-swapright m-icon-white"></i>
                        </a>
                    </div>
                </div>
                <div class="col-lg-3 col-md-3 col-sm-6 col-xs-12">
                    <div class="dashboard-stat green-haze">
                        <div class="visual">
                            <i class="fa fa-shopping-cart"></i>
                        </div>
                        <div class="details">
                            <div class="number">
                                 549
                            </div>
                            <div class="desc">
                                 New Orders
                            </div>
                        </div>
                        <a class="more" href="javascript:;">
                        View more <i class="m-icon-swapright m-icon-white"></i>
                        </a>
                    </div>
                </div>
                <div class="col-lg-3 col-md-3 col-sm-6 col-xs-12">
                    <div class="dashboard-stat purple-plum">
                        <div class="visual">
                            <i class="fa fa-globe"></i>
                        </div>
                        <div class="details">
                            <div class="number">
                                 +89%
                            </div>
                            <div class="desc">
                                 Brand Popularity
                            </div>
                        </div>
                        <a class="more" href="javascript:;">
                        View more <i class="m-icon-swapright m-icon-white"></i>
                        </a>
                    </div>
                </div>
            </div>
        </div>	
    </div>

	<!-- OTHER BOXES -->	
	<div class="postbox hide">
        <div class="handlediv" title="Click to toggle"><br></div>
        <h3 class="hndle ui-sortable-handle"><span><?php _e('SUMMERY',__PW_REPORT_WCREPORT_TEXTDOMAIN__);?></span></h3>
        <div class="inside">
        	<div class="row">
                <div class="col-lg-3 col-md-3 col-sm-6 col-xs-12">
                    <div class="dashboard-stat blue-madison">
                        <div class="visual">
                            <i class="fa fa-comments"></i>
                        </div>
                        <div class="details">
                            <div class="number">
                                 1349
                            </div>
                            <div class="desc">
                                 New Feedbacks
                            </div>
                        </div>
                        <a class="more" href="javascript:;">
                        View more <i class="m-icon-swapright m-icon-white"></i>
                        </a>
                    </div>
                </div>
                <div class="col-lg-3 col-md-3 col-sm-6 col-xs-12">
                    <div class="dashboard-stat red-intense">
                        <div class="visual">
                            <i class="fa fa-bar-chart-o"></i>
                        </div>
                        <div class="details">
                            <div class="number">
                                 12,5M$
                            </div>
                            <div class="desc">
                                 Total Profit
                            </div>
                        </div>
                        <a class="more" href="javascript:;">
                        View more <i class="m-icon-swapright m-icon-white"></i>
                        </a>
                    </div>
                </div>
                <div class="col-lg-3 col-md-3 col-sm-6 col-xs-12">
                    <div class="dashboard-stat green-haze">
                        <div class="visual">
                            <i class="fa fa-shopping-cart"></i>
                        </div>
                        <div class="details">
                            <div class="number">
                                 549
                            </div>
                            <div class="desc">
                                 New Orders
                            </div>
                        </div>
                        <a class="more" href="javascript:;">
                        View more <i class="m-icon-swapright m-icon-white"></i>
                        </a>
                    </div>
                </div>
                <div class="col-lg-3 col-md-3 col-sm-6 col-xs-12">
                    <div class="dashboard-stat purple-plum">
                        <div class="visual">
                            <i class="fa fa-globe"></i>
                        </div>
                        <div class="details">
                            <div class="number">
                                 +89%
                            </div>
                            <div class="desc">
                                 Brand Popularity
                            </div>
                        </div>
                        <a class="more" href="javascript:;">
                        View more <i class="m-icon-swapright m-icon-white"></i>
                        </a>
                    </div>
                </div>
            </div>
        </div>	
    </div>
    
    
    <!-- CENTER CHARTS -->
	<div class="postbox hide">
        <div class="handlediv" title="Click to toggle"><br></div>
        <h3 class="hndle ui-sortable-handle"><span><?php _e('SUMMERY CHARTS',__PW_REPORT_WCREPORT_TEXTDOMAIN__);?></span></h3>
        <div class="inside">
        	<div class="row">
                <div class="col-lg-12 col-md-12 col-sm-6 col-xs-12">
                
                
                	<div class="tabs tabsA tabs-style-topline"> 
                        <nav>
                            <ul class="tab-uls">
                                <li><a href="#section-bar-1" class=""> <div><i class="fa fa-cogs"></i><?php echo __('Sale By Months',__PW_REPORT_WCREPORT_TEXTDOMAIN__) ?></div><div>142,12 Visits</div><div>200,12 Sales</div></a></li>
                                <li><a href="#section-bar-1" class=""> <div><i class="fa fa-text-width"></i> <?php echo __('Sale By Days',__PW_REPORT_WCREPORT_TEXTDOMAIN__) ?></div><div>1000 Visits</div><div>22392 Sales</div></a></li>
                                <li><a href="#section-bar-2" class=""> <div><i class="fa fa-list-alt"></i><?php echo __('Sale By Week',__PW_REPORT_WCREPORT_TEXTDOMAIN__) ?></div><div>50022 Visits</div><div>234231 Sales</div></a></li>
                                <li><a href="#section-bar-1" class=""> <div><i class="fa fa-columns"></i><?php echo __('Top Products',__PW_REPORT_WCREPORT_TEXTDOMAIN__) ?></div><div>343223 Visits</div><div>8943 Sales</div></a></li>
                            </ul>
                        </nav>
                        <div class="content-wrap">
                        	
                            <section id="section-bar-1">
                            	<div id="chartdiv" style="width: 100%; height: 450px;"></div>
                            </section>
                            
                            <section id="section-bar-2">
                            	<div id="chartdiv1" style="width: 100%; height: 450px;"></div>
                            </section>
                            
                            <section id="section-bar-3">
                            	SEC3
                            </section>
                            
                            <section id="section-bar-4">
                            	SEC4
                            </section>
                        	
                        </div>
					</div>                        	    
                        
                        
                        
                        
                        
                        
                        
                </div>
        	</div>
    	</div>
	</div>                
    
    
    <!-- DATA TABLES -->
	<?php
		$table_name='products';
		$pw_rpt_main_class->table_html($table_name);
	?>	      
    
    
    <!-- MAPS -->
    <div class="postbox">
        <div class="handlediv" title="Click to toggle"><br></div>
        <h3 class="hndle ui-sortable-handle"><span><?php _e('Map',__PW_REPORT_WCREPORT_TEXTDOMAIN__);?></span></h3>
        <div class="inside">
        
        
        	<div class="container5">
                <div class="col-md-12">
                    <div class="map">
                        <span>Alternative content for the map</span>
                    </div>
                </div>
                <div class="col-md-12">
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
    

<script type="text/javascript">

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

	jQuery( document ).ready(function( $ ) {
		
		var chart = AmCharts.makeChart("chartdiv", {
  "type": "serial",
  "addClassNames": true,
  "theme": "light",
  "autoMargins": false,
  "marginLeft": 30,
  "marginRight": 8,
  "marginTop": 10,
  "marginBottom": 26,
  "balloon": {
    "adjustBorderColor": false,
    "horizontalPadding": 10,
    "verticalPadding": 8,
    "color": "#ffffff"
  },

  "dataProvider": [{
    "year": 2009,
    "income": 23.5,
    "expenses": 21.1
  }, {
    "year": 2010,
    "income": 26.2,
    "expenses": 30.5
  }, {
    "year": 2011,
    "income": 30.1,
    "expenses": 34.9
  }, {
    "year": 2012,
    "income": 29.5,
    "expenses": 31.1
  }, {
    "year": 2013,
    "income": 30.6,
    "expenses": 28.2,
  }, {
    "year": 2014,
    "income": 34.1,
    "expenses": 32.9,
    "dashLengthColumn": 5,
    "alpha": 0.2,
    "additional": "(projection)"
  }],
  "valueAxes": [{
    "axisAlpha": 0,
    "position": "left"
  }],
  "startDuration": 1,
  "graphs": [{
    "alphaField": "alpha",
    "balloonText": "<span style='font-size:12px;'>[[title]] in [[category]]:<br><span style='font-size:20px;'>[[value]]</span> [[additional]]</span>",
    "fillAlphas": 1,
    "title": "Income",
    "type": "column",
    "valueField": "income",
    "dashLengthField": "dashLengthColumn"
  }, {
    "id": "graph2",
    "balloonText": "<span style='font-size:12px;'>[[title]] in [[category]]:<br><span style='font-size:20px;'>[[value]]</span> [[additional]]</span>",
    "bullet": "round",
    "lineThickness": 3,
    "bulletSize": 7,
    "bulletBorderAlpha": 1,
    "bulletColor": "#FFFFFF",
    "useLineColorForBulletBorder": true,
    "bulletBorderThickness": 3,
    "fillAlphas": 0,
    "lineAlpha": 1,
    "title": "Expenses",
    "valueField": "expenses"
  }],
  "categoryField": "year",
  "categoryAxis": {
    "gridPosition": "start",
    "axisAlpha": 0,
    "tickLength": 0
  },
  "export": {
    "enabled": true
  }
});
	
	var chart = AmCharts.makeChart("chartdiv1", {
  "type": "serial",
  "addClassNames": true,
  "theme": "light",
  "autoMargins": false,
  "marginLeft": 30,
  "marginRight": 8,
  "marginTop": 10,
  "marginBottom": 26,
  "balloon": {
    "adjustBorderColor": false,
    "horizontalPadding": 10,
    "verticalPadding": 8,
    "color": "#ffffff"
  },

  "dataProvider": [{
    "year": 2009,
    "income": 23.5,
    "expenses": 21.1
  }, {
    "year": 2010,
    "income": 26.2,
    "expenses": 30.5
  }, {
    "year": 2011,
    "income": 30.1,
    "expenses": 34.9
  }, {
    "year": 2012,
    "income": 29.5,
    "expenses": 31.1
  }, {
    "year": 2013,
    "income": 30.6,
    "expenses": 28.2,
  }, {
    "year": 2014,
    "income": 34.1,
    "expenses": 32.9,
    "dashLengthColumn": 5,
    "alpha": 0.2,
    "additional": "(projection)"
  }],
  "valueAxes": [{
    "axisAlpha": 0,
    "position": "left"
  }],
  "startDuration": 1,
  "graphs": [{
    "alphaField": "alpha",
    "balloonText": "<span style='font-size:12px;'>[[title]] in [[category]]:<br><span style='font-size:20px;'>[[value]]</span> [[additional]]</span>",
    "fillAlphas": 1,
    "title": "Income",
    "type": "column",
    "valueField": "income",
    "dashLengthField": "dashLengthColumn"
  }, {
    "id": "graph2",
    "balloonText": "<span style='font-size:12px;'>[[title]] in [[category]]:<br><span style='font-size:20px;'>[[value]]</span> [[additional]]</span>",
    "bullet": "round",
    "lineThickness": 3,
    "bulletSize": 7,
    "bulletBorderAlpha": 1,
    "bulletColor": "#FFFFFF",
    "useLineColorForBulletBorder": true,
    "bulletBorderThickness": 3,
    "fillAlphas": 0,
    "lineAlpha": 1,
    "title": "Expenses",
    "valueField": "expenses"
  }],
  "categoryField": "year",
  "categoryAxis": {
    "gridPosition": "start",
    "axisAlpha": 0,
    "tickLength": 0
  },
  "export": {
    "enabled": true
  }
});
	
		[].slice.call( document.querySelectorAll( ".tabsA" ) ).forEach( function( el ) {
			new CBPFWTabs( el );
		});
	
		function cb(start, end) {
			$('#reportrange span').html(start.format('MMMM D, YYYY') + ' - ' + end.format('MMMM D, YYYY'));
		}
		cb(moment().subtract(29, 'days'), moment());
	
		$('#reportrange').daterangepicker({
			ranges: {
			   'Today': [moment(), moment()],
			   'Yesterday': [moment().subtract(1, 'days'), moment().subtract(1, 'days')],
			   'Last 7 Days': [moment().subtract(6, 'days'), moment()],
			   'Last 30 Days': [moment().subtract(29, 'days'), moment()],
			   'This Month': [moment().startOf('month'), moment().endOf('month')],
			   'Last Month': [moment().subtract(1, 'month').startOf('month'), moment().subtract(1, 'month').endOf('month')]
			}
		}, cb);
	
	});
</script>