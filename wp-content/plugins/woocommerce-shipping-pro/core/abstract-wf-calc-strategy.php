<?php
abstract class WF_Calc_Strategy {

	public $calculation_mode = "";
	public $rate_matrix = "";
	
	function __construct(){
		$a = func_get_args();
	   $this->calculation_mode = $a[0];
	   $this->rate_matrix = $a[1];
	}
	
	public function wf_row_selection_choice(){
		switch($this->calculation_mode){
			case 'per_item_max_cost':  
			case 'per_line_item_max_cost':  
			case 'per_order_max_cost': 
			case 'per_category_max_cost':  
			case 'per_shipping_class_max_cost': 
				return 'max_cost'; break; 
			case 'per_item_min_cost':  
			case 'per_line_item_min_cost':  
			case 'per_order_min_cost':
			case 'per_category_min_cost':  
			case 'per_shipping_class_min_cost': 
				return 'min_cost'; break;		
		}
	}
	
	public static function get_calc_mode( $calculation_mode = 'per_order_max',$rate_matrix = null ) {
		$calc_strategy = null;
		switch($calculation_mode){
			case 'per_order_max_cost':
			case 'per_order_min_cost':
				if ( ! class_exists( 'WF_Calc_Per_Order' ) )
					include_once 'class-wf-calc-per-order.php' ;
				$calc_strategy = new WF_Calc_Per_Order($calculation_mode,$rate_matrix);
				break;
			case 'per_item_max_cost':
			case 'per_item_min_cost':
				if ( ! class_exists( 'WF_Calc_Per_Item' ) )
					include_once 'class-wf-calc-per-item.php' ;
				$calc_strategy = new WF_Calc_Per_Item($calculation_mode,$rate_matrix);
				break;
			case 'per_line_item_max_cost':
			case 'per_line_item_min_cost':
				if ( ! class_exists( 'WF_Calc_Per_Line_Item' ) )
					include_once 'class-wf-calc-per-line-item.php' ;
				$calc_strategy = new WF_Calc_Per_Line_Item($calculation_mode,$rate_matrix);
				break;
			case 'per_category_max_cost':
			case 'per_category_min_cost':
				if ( ! class_exists( 'WF_Calc_Per_Category' ) )
					include_once 'class-wf-calc-per-category.php' ;
				$calc_strategy = new WF_Calc_Per_Category($calculation_mode,$rate_matrix);
				break;
			case 'per_shipping_class_max_cost':
			case 'per_shipping_class_min_cost':
				if ( ! class_exists( 'WF_Calc_Per_Class' ) )
					include_once 'class-wf-calc-per-class.php' ;
				$calc_strategy = new WF_Calc_Per_Class($calculation_mode,$rate_matrix);
				break;								
		}
		return 	$calc_strategy;
	}
}	
?>