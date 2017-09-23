(function($) {
	"use strict";
	
	jQuery(document).ready(function($) {
		$('select.variation_actions').bind('variable_availability_instock_notification_ajax_data variable_availability_lowstock_notification_ajax_data variable_availability_outofstock_notification_ajax_data variable_availability_backorder_notification_ajax_data', function(event, data) {
			var value = window.prompt(woocommerce_admin_meta_boxes_variations.i18n_enter_a_value);
			
			if (value != null) {
				data.value = value;
			}
			
			return data;
		});
		
		$(document).on('click', '#bulk-edit input[name*="change_availability_"]', function(e) {
			var input = $(this);
			var input_name = input.attr('name');
			
			var parent = input.closest('label');
			
			var textarea_name = input_name.replace('change_', '_');
			var textarea = parent.find('textarea[name="' + textarea_name + '"]');
			
			if (input.is(':checked')) {
				textarea.show();
			} else {
				textarea.hide();
			}
		});
		
		$('#the-list').on('click', '.editinline', function() {
			
			inlineEditPost.revert();
			
			var $post_id = $(this).closest('tr').attr('id');
				$post_id = $post_id.replace('post-', '');
				
			var $wcan_inline_data = $('#wcan_inline_' + $post_id);
			
			$('textarea[name="_availability_instock_notification"]', '.inline-quick-edit').val($wcan_inline_data.find('._availability_instock_notification').html());
			$('textarea[name="_availability_lowstock_notification"]', '.inline-quick-edit').val($wcan_inline_data.find('._availability_lowstock_notification').html());
			$('textarea[name="_availability_outofstock_notification"]', '.inline-quick-edit').val($wcan_inline_data.find('._availability_outofstock_notification').html());
			$('textarea[name="_availability_backorder_notification"]', '.inline-quick-edit').val($wcan_inline_data.find('._availability_backorder_notification').html());
		});
	});
	
})(jQuery);
