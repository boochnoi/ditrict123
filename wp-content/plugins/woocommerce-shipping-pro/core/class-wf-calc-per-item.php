<?php

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly
}

class WF_Calc_Per_Item extends WF_Calc_Strategy {
	public function wf_find_shipping_classes( $package ) {
		$found_shipping_classes = array();

		// Find shipping classes for products in the cart
		if ( sizeof( $package['contents'] ) > 0 ) {
			foreach ( $package['contents'] as $item_id => $values ) {
				$values['data'] = $this->wf_load_product( $values['data']  );
				if ( $values['data']->needs_shipping() ) {
					$max_quatinty = $values['quantity'];
					$found_class = $values['data']->get_shipping_class();
					for( $i=1; $i<=$max_quatinty; $i++ ){
						if(!isset($found_shipping_classes[$item_id.'-'.$i]))
							$found_shipping_classes[$item_id.'-'.$i] = array();

						if (!empty($found_class) && !in_array($found_class,$found_shipping_classes[$item_id.'-'.$i])) 
							$found_shipping_classes[$item_id.'-'.$i][] = $found_class;
					}
					
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
				if ( $values['data']->needs_shipping() ) {
					$max_quatinty = $values['quantity'];

					if( (WC()->version > '2.7.0') ){
						$par_id = $values['data']->get_parent_id($values['data']->id);
						$product_id = !empty( $par_id ) ? $par_id : $values['data']->id;
					}else{
						$product_id = $values['data']->id;
					}
					$cat_val = wp_get_post_terms( $product_id, 'product_cat', array( "fields" => "ids" ) );
					for( $i=1; $i<=$max_quatinty; $i++ ){
						$found_product_category[$item_id.'-'.$i] = $cat_val;
					}
				}
			}
		}
		return $found_product_category;
	}

	public function wf_get_grouped_package($package){
		$rule = array();
		foreach ( $package['contents'] as $item_id => $values ) {
			$values['data'] = $this->wf_load_product( $values['data']  );
			if ( $values['data']->needs_shipping() ) {
				$max_quatinty = $values['quantity'];
				for( $i=1; $i<=$max_quatinty; $i++ ){
					$values['quantity'] = 1;
					$rule[$item_id.'-'.$i][] = $values;
				}
			}
		}
		return $rule;
	}
	
	public function wf_calc_tax(){
		return 'per_item';
	}

	private function wf_load_product( $product ){
		if( !$product ){
			return false;
		}
		return ( WC()->version < '2.7.0' ) ? $product : new wf_product( $product );
	}
		
}
?>