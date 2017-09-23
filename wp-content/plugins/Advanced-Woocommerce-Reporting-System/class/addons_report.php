<?php
	//GET FROM XML
	$api_url='http://proword.net/xmls/Woo_Reporting/add-ons.php';
	
	$response = wp_remote_get(  $api_url );
				
	/* Check for errors, if there are some errors return false */
	if ( is_wp_error( $response ) or ( wp_remote_retrieve_response_code( $response ) != 200 ) ) {
		return false;
	}
	
	/* Transform the JSON string into a PHP array */
	$result = json_decode( wp_remote_retrieve_body( $response ), true );
	$add_ons_status='';
	foreach($result as $add_ons){
		$add_ons_status[]=
			array(
				"id" => $add_ons['id'],
				"label" => $add_ons['label'],
				"desc" =>$add_ons['desc'],
				"link" => $add_ons['link'],
				"icon" => $add_ons['icon'],
				"folder_name" => $add_ons['folder_name'],
				"define_variable" => $add_ons['define_variable'],
			);	
	}
	
	
	$add_ons_statuss=array(
		array(
			"id" => "crosstab",
			"label" => 'CrossTab Add-on',
			"desc" => 
				"			
				<p>The variation reports are the <b>most important report</b> for each shop (shop manager), you can generate powerful reports for <b>variation</b> with <b>Woocommerce Reporting</b> plugin.</p>".
				 "<ul>"
				."<li>'Product/Month'</li>"
				."<li>Variation/Month</li>"
				."<li>Product/Country</li>"
				."<li>Product/State</li>"
				."<li>Country/Month</li>"
				."<li>Payment Gateway/Month</li>"
				."<li>Order Status /Month</li>"
				."</ul>
				",
			"icon" => "<div class='awr-descicon'><i class='fa fa-random'></i></div>",
			"link" => "http://www.proword.net/crosstab-addones/",
			"folder_name" => "PW-Advanced-Woocommerce-Reporting-System-Crosstab-addon",
			"define_variable" => "__PW_CROSSTABB_ADD_ON__",
		),
		array(
			"id" => "variation",
			"label" => 'Variation Add-on',
			"desc" => 
			"
				<p>The variation reports are the most important report for each shop (shop manager), you can generate powerful reports for variation with Woocommerce Reporting plugin.</p>
			",
			"icon" => "<div class='awr-descicon'><i class='fa fa-line-chart '></i></div>",
			"link" => "http://www.proword.net/addones-variation-addones/",
			"folder_name" => "PW-Advanced-Woocommerce-Reporting-System-Variaion-addon",
			"define_variable" => "__PW_VARIATION_ADD_ON__",
		),
		array(
			"id" => "vendors",
			"label" => 'Vendors Add-on',
			"desc" => "
				<p>The variation reports are the most important report for each shop (shop manager), you can generate powerful reports for variation with Woocommerce Reporting plugin.</p>
			",
			"icon" => "<div class='awr-descicon'><i class='fa fa-cart-arrow-down '></i></div>",
			"link" => "http://www.proword.net/addones-Vendors-addones/",
			"folder_name" => "PW-Advanced-Woocommerce-Reporting-System-Vendors-addon",
			"define_variable" => "__PW_VENDORS_ADD_ON__",
		),
	);
	
	
	echo '
	<div class="wrap">
		<div class="row">
			<div class="col-xs-12">
				<div class="awr-addons-cnt awr-addones-active" style="background:#fff">
					<div class="awr-descicon"><i class="fa fa-pencil-square-o"></i></div>
					<div class="awr-desc-content">	
						<h3 class="awr-addones-title" style="color:#333;border-bottom:1px solid #ccc;padding-bottom:5px">Your Request</h3>
						<div class="awr-addnoes-desc">If you need any custom report please email your request to <strong>info@proword.net</strong> or filling the request form by clicking on <strong>"Send Your Request"</strong>  button.</div>
						<a class="awr-addons-btn" href="http://proword.net/request/" target="_blank" style="background: #eee;"><i class="fa fa-paper-plane"></i>Send Your Request</a>
					</div>
					<div class="awr-clearboth"></div>
				</div>
				
			';
	foreach($add_ons_status as $plugin){
		//IS ACTIVE
		$active=defined($plugin['define_variable']);
		
		//IS EXIST
		$my_plugin = WP_PLUGIN_DIR . '/' .$plugin['folder_name'];
		$exist=is_dir( $my_plugin );
		
		$label=$plugin['label'];
		$desc =$plugin['desc'];
		$icon = $plugin['icon'];
		$active_status='';
		$btn='';
		
		if($exist){
			if($active)
			{
				$active_status="awr-addones-active";
				$btn='<a class="awr-addons-btn" href="#" ><i class="fa fa-check"></i>'.__('Activated',__PW_REPORT_WCREPORT_TEXTDOMAIN__).'</a>';
			}else
			{
				$active_status="awr-addones-deactive";
				$btn='<a class="awr-addons-btn" href="'.admin_url()."plugins.php".'" target="_blank"><i class="fa fa-plug"></i>'.__('Activate Here',__PW_REPORT_WCREPORT_TEXTDOMAIN__).'</a>';
			
			}
		}else
		{
			$active_status="awr-addones-disable";
			$btn='<a class="awr-addons-btn" href="'.$plugin['link'].'" target="_blank"><i class="fa fa-shopping-cart"></i>'.__('Buy Now',__PW_REPORT_WCREPORT_TEXTDOMAIN__).'</a>';
			
			
			
		}
		
		//echo '<div style="background:'.$color.'"><div><h4>'.$label.'</h4></div>'.$text.'</div>';
		echo '
			  <div class="awr-addons-cnt '.$active_status.'">
				'.$icon.'
				<div class="awr-desc-content">	
					<h3 class="awr-addones-title">'.$label.'</h3>
					<div class="awr-addnoes-desc">'.$desc.'</div>
					'.$btn.'
				</div>
				<div class="awr-clearboth"></div>
			  </div>';
	}
	echo '
			</div><!--col-xs-12 -->
		</div><!--row -->
	</div><!--wrap -->
	';
?>