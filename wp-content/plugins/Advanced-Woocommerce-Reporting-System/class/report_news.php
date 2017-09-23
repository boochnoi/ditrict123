<?php
	function pw_show_news($add_ons_status){
		$html='';
		foreach($add_ons_status as $plugin){
			
			$border='';
			if ($plugin === end($add_ons_status)){
				$border="border:0px";
			}
			
			$label=$plugin['label'];
			$desc =$plugin['desc'];
			$active_status='';
			$btn='';
			
			$active_status="awr-news-active";
			$btn='';
			
			//echo '<div style="background:'.$color.'"><div><h4>'.$label.'</h4></div>'.$text.'</div>';
			$html .= '
				  <div class="awr-news-cnt '.$active_status.'" style="'.$border.'">
					<div class="awr-desc-content">	
						<h3 class="awr-news-title"><a class="" href="'.$plugin['link'].'" target="_blank">'.$label.'</a></h3>
						<div class="awr-news-desc">'.$desc.'</div>
						'.$btn.'
					</div>
					<div class="awr-clearboth"></div>
				  </div>';
		}
		return $html;
	}
	
	$read_date=get_option("pw_news_read_date");
				
	//GET FROM XML
	$api_url='http://proword.net/xmls/Woo_Reporting/report-news.php';
	
	$response = wp_remote_get(  $api_url );
				
	/* Check for errors, if there are some errors return false */
	if ( is_wp_error( $response ) or ( wp_remote_retrieve_response_code( $response ) != 200 ) ) {
		return false;
	}
	
	/* Transform the JSON string into a PHP array */
	$result = json_decode( wp_remote_retrieve_body( $response ), true );
	$add_ons_status='';
	
	if($read_date=='')
	{
		$i=0;
		
		foreach($result as $add_ons){
			
			if ($add_ons === reset($result)){
				update_option("pw_news_read_date",$add_ons['date']);
			}
			
			$add_ons_status[]=
				array(
					"id" => $add_ons['id'],
					"date" => $add_ons['date'],
					"label" => $add_ons['label'],
					"desc" =>$add_ons['desc'],
					"link" => $add_ons['link'],
				);	
		}
	}else{
		
		
		foreach($result as $add_ons){
			/*if ($add_ons === reset($result)){
				
			}*/
			
			if($read_date<$add_ons['date']){
				$add_ons_status[]=
				array(
					"id" => $add_ons['id'],
					"date" => $add_ons['date'],
					"label" => $add_ons['label'],
					"desc" =>$add_ons['desc'],
					"link" => $add_ons['link'],
				);	
			}
		}
		update_option("pw_news_read_date",$add_ons['date']);
	}
	
	
	if(is_array($add_ons_status))
	{
	
	echo '
	<div class="wrap awr-news-cnt-wrap">
		<div  class="awr-news-close"><i class="fa fa-times"></i></div>
		<div class="row">
			<div class="col-xs-12">
				<div  class="awr-news-main-title">Latest News</div>';
				if(is_array($add_ons_status))
				{
					
					echo pw_show_news($add_ons_status);
					
				}else{
					
					foreach($result as $add_ons){
						$add_ons_status[]=
							array(
								"id" => $add_ons['id'],
								"date" => $add_ons['date'],
								"label" => $add_ons['label'],
								"desc" =>$add_ons['desc'],
								"link" => $add_ons['link'],
							);	
					}
					
					if(is_array($add_ons_status)){
						echo '<div class="awr-news-cnt">'.__('There is no unread news, ',__PW_REPORT_WCREPORT_TEXTDOMAIN__).'<span class="awr-news-read-oldest">'.__('Show Oldest News !',__PW_REPORT_WCREPORT_TEXTDOMAIN__).'</span></div>';
						echo '<div class="awr-news-oldest">'.pw_show_news($add_ons_status).'</div>';
					}else{
						echo __('There is no news !',__PW_REPORT_WCREPORT_TEXTDOMAIN__);
					}
				}
			echo '
			<div class="awr-addons-cnt awr-addones-active" style="background:#fff">
				<div class="awr-descicon"><i class="fa fa-pencil-square-o"></i></div>
				<div class="awr-desc-content">	
					<h3 class="awr-addones-title" style="color:#333;border-bottom:1px solid #ccc;padding-bottom:5px">Your Request</h3>
					<div class="awr-addnoes-desc">If you need any custom report please email your request to <strong>info@proword.net</strong> or filling the request form by clicking on <strong>"Send Your Request"</strong>  button.</div>
					<a class="awr-addons-btn" href="http://proword.net/request/" target="_blank" style="background: #eee;"><i class="fa fa-paper-plane"></i>Send Your Request</a>
				</div>
				<div class="awr-clearboth"></div>
			</div>
			</div><!--col-xs-12 -->
		</div><!--row -->
	</div><!--wrap -->';
	}
?>