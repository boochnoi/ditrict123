<?php
	
	$pw_active_plugin = array(
		/*array(  
			'label'	=> __('Activation Type',__PW_REPORT_WCREPORT_TEXTDOMAIN__),
			'desc'	=> __('Enter Your Purchase Code',__PW_REPORT_WCREPORT_TEXTDOMAIN__),		
			'name'  => __PW_REPORT_WCREPORT_FIELDS_PERFIX__.'activate_type',
			'id'	=> __PW_REPORT_WCREPORT_FIELDS_PERFIX__.'activate_type',
			'type'  => 'select',
			'options' => array (  
				'one' => array (
					'label' => __('Direct Activate',__PW_REPORT_WCREPORT_TEXTDOMAIN__),  
					'value' => 'direct'  
				),
				'two' => array (
					'label' => __('Via Proword Site',__PW_REPORT_WCREPORT_TEXTDOMAIN__),  
					'value' => 'proword'  
				)
			)
		),*/
		array(  
			'label'	=> __('Domain Name',__PW_REPORT_WCREPORT_TEXTDOMAIN__),
			'desc'	=> __('Enter Your Domain Name',__PW_REPORT_WCREPORT_TEXTDOMAIN__),		
			'name'  => __PW_REPORT_WCREPORT_FIELDS_PERFIX__.'activate_domain_name',
			'id'	=> __PW_REPORT_WCREPORT_FIELDS_PERFIX__.'activate_domain_name',
			'type'	=> 'text',
			/*'dependency' => array(
				'parent_id' => array(__PW_REPORT_WCREPORT_FIELDS_PERFIX__ . 'activate_type'),
				__PW_REPORT_WCREPORT_FIELDS_PERFIX__ . 'activate_type'	  => array('select','direct') 	
			)	*/	
		),
		array(  
			'label'	=> __('Purchase Code',__PW_REPORT_WCREPORT_TEXTDOMAIN__),
			'desc'	=> __('Enter Your Purchase Code',__PW_REPORT_WCREPORT_TEXTDOMAIN__),		
			'name'  => __PW_REPORT_WCREPORT_FIELDS_PERFIX__.'activate_purchase_code',
			'id'	=> __PW_REPORT_WCREPORT_FIELDS_PERFIX__.'activate_purchase_code',
			'type'	=> 'text',		
			/*'dependency' => array(
				'parent_id' => array(__PW_REPORT_WCREPORT_FIELDS_PERFIX__ . 'activate_type'),
				__PW_REPORT_WCREPORT_FIELDS_PERFIX__ . 'activate_type'	  => array('select','direct') 	
			)*/
		),
		/*array(  
			'label'	=> __('Active Via Proword.net',__PW_REPORT_WCREPORT_TEXTDOMAIN__),
			'desc'	=> __('Goto <a href="http://proword.net/verify-purchase-code/" target="_blank">Here</a> and after complete the form, copy the form result and paste in below field',__PW_REPORT_WCREPORT_TEXTDOMAIN__),		
			'name'  => __PW_REPORT_WCREPORT_FIELDS_PERFIX__.'activate_proword',
			'id'	=> __PW_REPORT_WCREPORT_FIELDS_PERFIX__.'activate_proword',
			'type'	=> 'notype',		
			'dependency' => array(
				'parent_id' => array(__PW_REPORT_WCREPORT_FIELDS_PERFIX__ . 'activate_type'),
				__PW_REPORT_WCREPORT_FIELDS_PERFIX__ . 'activate_type'	  => array('select','proword') 	
			)
		),
		array(  
			'label'	=> __('File Path',__PW_REPORT_WCREPORT_TEXTDOMAIN__),
			'desc'	=> __('Paste the path',__PW_REPORT_WCREPORT_TEXTDOMAIN__),		
			'name'  => __PW_REPORT_WCREPORT_FIELDS_PERFIX__.'activate_path_file',
			'id'	=> __PW_REPORT_WCREPORT_FIELDS_PERFIX__.'activate_path_file',
			'type'	=> 'text',		
			'dependency' => array(
				'parent_id' => array(__PW_REPORT_WCREPORT_FIELDS_PERFIX__ . 'activate_type'),
				__PW_REPORT_WCREPORT_FIELDS_PERFIX__ . 'activate_type'	  => array('select','proword') 	
			)
		)*/
	);
	
	if (isset($_POST["update_settings"])) {
		// Do the saving
			
		foreach($_POST as $key=>$value){
			if(!isset($_POST[$key])){
				delete_option($key);  
				continue;
			}
			
			$old = get_option($key);  
			$new = $value; 
			if(!is_array($new)) 
			{
				
				/*if($key==__PW_REPORT_WCREPORT_FIELDS_PERFIX__.'activate_type')
				{
					$path_file=__PW_REPORT_WCREPORT_FIELDS_PERFIX__.'activate_path_file';
					if(isset($_POST[$path_file]))
					{
						$url=$_POST[$path_file];
						$response = wp_remote_get(  $url );
				
						//Check for errors, if there are some errors return false 
						if ( is_wp_error( $response ) or ( wp_remote_retrieve_response_code( $response ) != 200 ) ) {
							return false;
						}
  
						
						//Transform the JSON string into a PHP array 
						$result = json_decode( wp_remote_retrieve_body( $response ), true );
						$add_ons_status='';
						foreach($result as $add_ons){
							$add_ons_status[]=
								array(
									"item_name" => $add_ons['item_name'],
									"buyer" => $add_ons['buyer'],
									"created_at" =>$add_ons['created_at'],
									"licence" => $add_ons['licence'],
									"supported_until" => $add_ons['supported_until'],
								);	
						}
					}
					
					
					
					
				}*/
				
				if ($new && $new != $old) {  
					update_option($key, $new);  
				} elseif ('' == $new && $old) {  
					delete_option($key);  
				}
			}else{
				
				//die(print_r($new));
				
				$get_year=array_keys($value);
				$get_year=$get_year[0];
				
				foreach($value[$get_year] as $keys=>$vals){
					
					$old = get_option($key."_".$get_year."_".$keys);  
					$new = $vals; 
					
					if ($new && $new != $old) {  
						update_option($key."_".$get_year."_".$keys, $new);  
					} elseif ('' == $new && $old) {  
						delete_option($key."_".$get_year."_".$keys);  
					}
					
				}
			}
		}
			
		global $pw_rpt_main_class;
		$field=__PW_REPORT_WCREPORT_FIELDS_PERFIX__.'activate_purchase_code';
		$pw_rpt_main_class->pw_plugin_status=get_option($field);
		
		$text='';
		if ($pw_rpt_main_class->dashboard($pw_rpt_main_class->pw_plugin_status)){
			$text=__('Congratulation, The Plugin has been Activated Successfully !',__PW_REPORT_WCREPORT_TEXTDOMAIN__);
			?>
                <div id="setting-error-settings_updated" class="updated settings-error">
                    <p><strong><?php echo $text;?></strong></p>
                </div>
            <?php
		}else{
			$text=__('Unfortunately, The Purchase code is Wrong, Please try Again !',__PW_REPORT_WCREPORT_TEXTDOMAIN__);
			?>
                <div id="setting-error-settings_updated" class="error">
                    <p><strong><?php echo $text;?></strong></p>
                </div>
            <?php
		}
	}	
	
	/*$html= '<table class="form-table">'; 
	
	foreach ($pw_active_plugin as $field) {  
		if(isset($field['dependency']))  
		{
			$html.= pw_report_dependency($field['id'],$field['dependency']);	
		}
		// get value of this field if it exists for this post  
		$meta = get_option($field['id']);  
		// begin a table row with  
		$style='';
		if($field['type']=='notype')
			$style='style="border-bottom:solid 1px #ccc"';
		$html.= '<tr class="'.$field['id'].'_field" '.$style.'> ';
		
		$cols='';
		
		$html.= '
		<td '.$cols.'>';  
		switch($field['type']) {  

			case 'notype':
				$html.= '<span class="description">'.$field['desc'].'</span>';
			break;
			
			case 'text':
				$html.= '<input type="text" name="'.$field['id'].'" id="'.$field['id'].'" class="'.$field['id'].'" value="'.$meta.'" />
				<br /><span class="description">'.$field['desc'].'</span>	';  
			break; 
			
			case 'select':  
				$html.= '<select name="'.$field['id'].'" id="'.$field['id'].'" class="'.$field['id'].'" style="width: 170px;">';  
				foreach ($field['options'] as $option) {  
					$html.= '<option '. selected( $meta , $option['value'],0 ).' value="'.$option['value'].'">'.$option['label'].'</option>';  
				}  
				$html.= '</select><br /><span class="description">'.__($field['desc'],__PW_REPORT_WCREPORT_TEXTDOMAIN__).'</span>';  
			break;
		}
	}
	
	$html.='</table>';*/
	$field_1=$pw_active_plugin[0];
	$field_2=$pw_active_plugin[1];
	
	$meta_1 = get_option($field_1['id']);  
	$meta_2 = get_option($field_2['id']);  
	
	$html= '<div class="wrap">
			<h2>'.__('Plugin Activate',__PW_REPORT_WCREPORT_TEXTDOMAIN__).'</h2>
			<br />
			<form method="POST" action="">
				<input type="hidden" name="update_settings" value="Y" />
				<table class="form-table">
					<tr > 
						<th><label for="'.$field_1['id'].'">'.$field_1['label'].'</label></th> 
						<td>
							<input type="text" name="'.$field_1['id'].'" id="'.$field_1['id'].'" class="'.$field_1['id'].'" value="'.$meta_1.'"/>
						</td>
					</tr>	
					<tr > 
						<th><label for="'.$field_2['id'].'">'.$field_2['label'].'</label></th> 
						<td>
							<input type="text" name="'.$field_2['id'].'" id="'.$field_2['id'].'" class="'.$field_2['id'].'" value="'.$meta_2.'"/>
						</td>
					</tr>	
				</table>
			<div class="awr-setting-submit">
				<input type="submit" value="Save settings" class="button-primary"/>
			</div>
		</form>
	</div>
	
	<script type="text/javascript">
		
	</script>
	';
	
	echo $html;
?>