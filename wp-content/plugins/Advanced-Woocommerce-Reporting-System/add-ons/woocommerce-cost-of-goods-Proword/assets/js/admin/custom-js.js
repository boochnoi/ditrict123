(function() {  "use strict";
  jQuery(function($) {
    $('select#field_to_edit').on('_PW_COST_GOOD_FIELD_VAR_ajax_data', function() {
      return {
        value: window.prompt(woocommerce_admin_meta_boxes_variations.i18n_enter_a_value)
      };
    });
    return $('#the-list').on('click', '.editinline', function(e) {
      var cost, inline_data, post_id;
      post_id = $(this).closest('tr').attr('id');
      post_id = post_id.replace('post-', '');
      inline_data = $('#wc_cog_inline_' + post_id);
      cost = inline_data.find('.cost').text();
      return $('input[name="_PW_COST_GOOD_FIELD"]').val(cost);
    });
  });

}).call(this);


