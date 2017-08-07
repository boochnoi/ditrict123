<?php

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly
}

class WF_Calc_Per_Order extends WF_Calc_Strategy {
	public function wf_find_shipping_classes( $package ) {
		$found_shipping_classes = array();
		$group_key = 'wf_per_order';
		$found_shipping_classes[$group_key] = array();
		if ( sizeof( $package['contents'] ) > 0 ) {
			foreach ( $package['contents'] as $item_id => $values ) {
				$values['data'] = $this->wf_load_product( $values['data']  );
				if ( $values['data']->needs_shipping() ) {
					$found_class = $values['data']->get_shipping_class();
					if (!empty($found_class) && !in_array($found_class,$found_shipping_classes[$group_key])) 
						$found_shipping_classes[$group_key][] = $found_class;										
				}
			}
		}
		return $found_shipping_classes;
	}

	public function wf_find_product_category( $package ) {
		$found_product_category = array();
		$group_key = 'wf_per_order';
		$found_product_category[$group_key] = array();
		if ( sizeof( $package['contents'] ) > 0 ) {
			foreach ( $package['contents'] as $item_id => $values ) {
				$values['data'] = $this->wf_load_product( $values['data']  );
				if ( $values['data']->needs_shipping() ){ 					
					if( (WC()->version > '2.7.0') ){
						$par_id = $values['data']->get_parent_id($values['data']->id);
						$product_id = !empty( $par_id ) ? $par_id : $values['data']->id;
					}else{
						$product_id = $values['data']->id;
					}
					$product_cat = wp_get_post_terms( $product_id, 'product_cat', array( "fields" => "ids" ) );
					
					if(!empty($product_cat)) 
						$found_product_category[$group_key] = array_merge($found_product_category[$group_key],$product_cat);
				}
			}
		}
		return $found_product_category;
	}
	
	public function wf_get_grouped_package($package){
		$group_key = 'wf_per_order';
		$rule = array();
		$rule[$group_key] = array(); 
		foreach ( $package['contents'] as $item_id => $values ) {
			$values['data'] = $this->wf_load_product( $values['data']  );
			if ( $values['data']->needs_shipping() ) {
				$rule[$group_key][] = $values;																								
			}
		}
		return $rule;		
	}
		
	private function wf_load_product( $product ){
		if( !$product ){
			return false;
		}
		return ( WC()->version < '2.7.0' ) ? $product : new wf_product( $product );
	}

	public function wf_calc_tax(){
		return 'per_order';
	}		
}
?>