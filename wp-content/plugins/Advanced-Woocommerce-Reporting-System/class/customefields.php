<?php

	///////////////////METABOXES//////////////////////////////////
		
	if(!function_exists('add_custom_meta_advanced_reporting')){
		add_action('add_meta_boxes', 'add_custom_meta_advanced_reporting');  
		function add_custom_meta_advanced_reporting() {  
			$types = '';
			
			add_meta_box(  
				'proword_main_page', // $id  
				'<i class="fa fa-th-list"></i> '.__('Cost Of Good',PW_PRO_BOX_LAYOUT_TEXTDOMAIN), // $title   
				'show_customfields', // $callback  
				//'product', // $page  
				'product',
				'normal', // $context  
				'high');
			
		}
	
	
	///////////////////END METABOXES//////////////////////////////////
	global $pw_customfiled_query;
	$pw_customfiled_query= array(
		
		array(  
			'label' => '<strong>'.__('View',PW_PRO_BOX_LAYOUT_TEXTDOMAIN).'</strong>',  
			'desc'  => __('Set count of view',PW_PRO_BOX_LAYOUT_TEXTDOMAIN),
			'id'    => 'pl_view_post',
			'type'  => 'numeric',  
		),
		array(  
			'label' => '<strong>'.__('Like',PW_PRO_BOX_LAYOUT_TEXTDOMAIN).'</strong>',  
			'desc'  => __('Set count of like',PW_PRO_BOX_LAYOUT_TEXTDOMAIN),
			'id'    => 'pl_like_post',
			'type'  => 'numeric',  
		),
		array(  
			'label' => '<strong>'.__('Video Url',PW_PRO_BOX_LAYOUT_TEXTDOMAIN).'</strong>',  
			'desc'  => __('Set embed video url',PW_PRO_BOX_LAYOUT_TEXTDOMAIN),
			'id'    => 'pl_video_url',
			'type'  => 'textbox',  
		),
	);	
	
	function show_customfields() {  
		global $pw_customfiled_query, $post;  
		// Use nonce for verification  
		echo '<input type="hidden" name="show_custom_meta_box_extra_bulkedit_nonce" value="'.wp_create_nonce(basename(__FILE__)).'" />';  
			  
			// Begin the field table and loop  
			echo '<table class="form-table">';  
			foreach ($pw_customfiled_query as $field) {  
			
			
				$meta = get_post_meta($post->ID, $field['id'], true); 
				if($field['type']=='hidden')
				{
					echo '<input type="text" name="'. $field['id'].'" class="pw_step_order" value="'.$meta.'"/>';
					continue;
				}
			
				// get value of this field if it exists for this post  
				 
				// begin a table row with  
				echo '<tr class="'.$field['id'].'_field"> 

						<th><label for="'.$field['id'].'">'.$field['label'].'</label></th> 
						<td>';  
						switch($field['type']) {  
							
							case 'numeric':  
								$default_value=(isset($field['value'])? $field['value']:"");
								$html= '
								<input type="number" name="'.$field['id'].'" id="'.$field['id'].'" value="'.($meta=='' ? $default_value:$meta).'" size="30" class="width_170" min="0" pattern="[-+]?[0-9]*[.,]?[0-9]+" title="Only Digits!" class="input-text qty text" />
	';
								$html.= '
									<br /><span class="description">'.$field['desc'].'</span>';  
								echo $html;	
							break;
							
							case 'textbox':  
								echo '<input type="text" name="'.$field['id'].'" id="'.$field['id'].'" value="'.$meta.'" size="30" class="width_170"/> 
									<br /><span class="description">'.$field['desc'].'</span>';  
							break; 
							
							case 'checkbox':  
								$html= '
								<div class="pwb-slidecheck">
								<input type="checkbox" name="'.$field['id'].'" class="" id="'.$field['id'].'" '.checked( $meta, "on" ,0).'"/>
									<label  for="'.$field['id'].'">'.$field['desc'].'</label>
								</div>';  
								echo $html; 	
							break;  
							
							case 'select':  
								$html= '<select name="'.$field['id'].'" id="'.$field['id'].'" style="width: 170px;">';  
								foreach ($field['options'] as $option) {  
									$html.= '<option '. selected( $meta , $option['value'],0 ).' value="'.$option['value'].'">'.$option['label'].'</option>';  
								}  
								$html.= '</select><br /><span class="description">'.$field['desc'].'</span>';  
								echo $html; 
							break;
							
						} //end switch  
				echo '</td></tr>';  
			} // end foreach  
			echo '</table>'; // end table  
	}
	
	
	function save_custom_meta_customfields ($post_id) {  
		global $pw_customfiled_query;
		
		
		
		// verify nonce
		if(isset($_POST) && !empty($_POST)){
			//die(print_r($_POST));
			if (isset($_POST['show_custom_meta_box_extra_bulkedit_nonce']) && !wp_verify_nonce($_POST['show_custom_meta_box_extra_bulkedit_nonce'], basename(__FILE__)))
				return $post_id;
		// check autosave  
			if (defined('DOING_AUTOSAVE') && DOING_AUTOSAVE)  
				return $post_id;  
			// check permissions  
			if (isset( $_POST['post_type']) && 'page' == $_POST['post_type']) {  
				if (!current_user_can('edit_page', $post_id))  
					return $post_id;  
				} elseif (!current_user_can('edit_post', $post_id)) {  
					return $post_id;  
			}  
			
			$tab_type='product';
			if(isset($_POST) && !empty($_POST)){
				if (isset($_POST['product_tab_type'])){
					$tab_type=esc_attr(($_POST['product_tab_type']));
				}
			}
	
			foreach ($pw_customfiled_query as $field) { 
			
				if(!isset($_POST[$field['id']])){
					delete_post_meta($post_id, $field['id']); 
					continue;
				}
				
				//SAVE TAXOMONY VALUES
				if(($field['type'] == 'tax_select') || ($field['type'] == 'tax_checkbox')){  
					$post = get_post($post_id);  
					$category = $_POST[$field['id']];   //$post['type of tax']
					wp_set_post_terms( $post_id, $category, $field['tax_field'] , false );
					continue;
				}
				
				$post = get_post($post_id);
				$category = $_POST[$field['id']];  
				wp_set_post_terms( $post_id, $category, $field['id'],false );
			
				$old = get_post_meta($post_id, $field['id'], true);  
				$new = $_POST[$field['id']];  
				if ($new && $new != $old) {  
					update_post_meta($post_id, $field['id'], $new);  
				} elseif ('' == $new && $old) {  
					delete_post_meta($post_id, $field['id'], $old);  
				}
	
			} // end foreach
			
		}		
	
	} 
	
	add_action('save_post', 'save_custom_meta_customfields');
	
}//if function_exists('add_custom_meta_box_pro_post_layout')
?>