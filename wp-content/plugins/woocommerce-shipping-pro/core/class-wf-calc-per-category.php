<?php

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly
}

class WF_Calc_Per_Category extends WF_Calc_Strategy {
	public function wf_find_shipping_classes( $package ) {
		$found_shipping_classes = array();

		// Find shipping classes for products in the cart
		if ( sizeof( $package['contents'] ) > 0 ) {
			foreach ( $package['contents'] as $item_id => $values ) {
				$values['data'] = $this->wf_load_product( $values['data']  );
				if ( $values['data']->needs_shipping() ) {
					
					if( (WC()->version > '2.7.0') ){
						$par_id = $values['data']->get_parent_id($values['data']->id);
						$product_id = !empty( $par_id ) ? $par_id : $values['data']->id;
					}else{
						$product_id = $values['data']->id;
					}
					$product_categories = wp_get_post_terms( $product_id, 'product_cat', array( "fields" => "ids" ) );	
					$best_category = $this->wf_get_category_priority($product_categories);
				
					$found_class = $values['data']->get_shipping_class();
					if(!isset($found_shipping_classes[$best_category]))
						$found_shipping_classes[$best_category] = array();
					if (!in_array($found_class,$found_shipping_classes[$best_category])) 
						$found_shipping_classes[$best_category][] = $found_class;										
				}
			}
		}
		return $found_shipping_classes;
	}

	public function wf_find_product_category( $package ) {
		$found_product_category = array();

		// Find shipping classes for products in the cart
		if ( sizeof( $package['contents'] ) > 0 ) {
			foreach ( $package['contents'] as $item_id => $values ) {
				$values['data'] = $this->wf_load_product( $values['data']  );
				if ( $values['data']->needs_shipping() ) 
					if( (WC()->version > '2.7.0') ){
						$par_id = $values['data']->get_parent_id($values['data']->id);
						$product_id = !empty( $par_id ) ? $par_id : $values['data']->id;
					}else{
						$product_id = $values['data']->id;
					}
					$product_categories = wp_get_post_terms( $product_id, 'product_cat', array( "fields" => "ids" ) );	
					$best_category = $this->wf_get_category_priority($product_categories);
				
					if(!isset($found_product_category[$best_category]))
						$found_product_category[$best_category] = array();
					if (!in_array($best_category,$found_product_category[$best_category])) 
						$found_product_category[$best_category][] = $best_category;							
			}
		}
		return $found_product_category;
	}

	public function wf_get_grouped_package($package){
		$rule = array();
		foreach ( $package['contents'] as $item_id => $values ) {
			$values['data'] = $this->wf_load_product( $values['data']  );
			if ( $values['data']->needs_shipping() ) {
				if( (WC()->version > '2.7.0') ){
					$par_id = $values['data']->get_parent_id($values['data']->id);
					$product_id = !empty( $par_id ) ? $par_id : $values['data']->id;
				}else{
					$product_id = $values['data']->id;
				}
				$product_categories = wp_get_post_terms( $product_id, 'product_cat', array( "fields" => "ids" ) );	
				$best_category = $this->wf_get_category_priority($product_categories);
				if(!isset($rule[$best_category])) 
					$rule[$best_category] = array(); 
				$rule[$best_category][] = $values;
			}
		}
		return $rule;		
	}
	
	public function wf_calc_tax(){
		return 'per_order';
	}
	
	public $category_priority_map = null;
	public function wf_get_category_priority_map(){
		if($this->category_priority_map) return $this->category_priority_map;
		$row_selection_choice = $this->wf_row_selection_choice();
		$category_priority = array();
		if(sizeof($this->rate_matrix) > 0) {
			foreach($this->rate_matrix as $rule ) {
				$shipping_cost = 0;
				$price = $rule['fee'] + 1 * $rule['cost'];
				if ( $price !== false) $shipping_cost =  $price;
				
				$rule_categories = isset($rule['product_category']) ? $rule['product_category'] : array();
				foreach($rule_categories as $cat_val){
					if(isset($category_priority[$cat_val])){
						if($category_priority[$cat_val] > $shipping_cost && $row_selection_choice == 'min_cost'
						|| $category_priority[$cat_val] < $shipping_cost && $row_selection_choice == 'max_cost')
							 $category_priority[$cat_val] = $shipping_cost;
					}
					else								
						$category_priority[$cat_val] = $shipping_cost;																								
				}
			}
			if($row_selection_choice == 'min_cost')
				asort($category_priority);
			else
				arsort($category_priority);			
		}
		$this->category_priority_map = $category_priority;
		return $this->category_priority_map;
	}
	
	public function wf_get_category_priority($product_categories){
		$priority_map = $this->wf_get_category_priority_map();
		foreach($priority_map as $cat_key=>$cat_value){
			if(in_array($cat_key,$product_categories))
				return $cat_key;			
		}
		return isset($product_categories[0]) ? $product_categories[0] : '';
	}
	
	private function wf_load_product( $product ){
		if( !$product ){
			return false;
		}
		return ( WC()->version < '2.7.0' ) ? $product : new wf_product( $product );
	}	
}
?>