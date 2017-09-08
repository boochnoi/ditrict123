jQuery.extend( jQuery.fn, 
{
	hasParent: function(p) {
		return this.filter(function(){
			return jQuery(p).find(this).length;
		});
	}
});
var sb = 
{
	basename: function (path, suffix) 
	{
			  //  discuss at: http://phpjs.org/functions/basename/
			  // original by: Kevin van Zonneveld (http://kevin.vanzonneveld.net)
			  // improved by: Ash Searle (http://hexmen.com/blog/)
			  // improved by: Lincoln Ramsay
			  // improved by: djmix
			  // improved by: Dmitry Gorelenkov
			  //   example 1: basename('/www/site/home.htm', '.htm');
			  //   returns 1: 'home'
			  //   example 2: basename('ecra.php?p=1');
			  //   returns 2: 'ecra.php?p=1'
			  //   example 3: basename('/some/path/');
			  //   returns 3: 'path'
			  //   example 4: basename('/some/path_ext.ext/','.ext');
			  //   returns 4: 'path_ext'

			  var b = path;
			  var lastChar = b.charAt(b.length - 1);

			  if (lastChar === '/' || lastChar === '\\') {
			    b = b.slice(0, -1);
			  }

			  b = b.replace(/^.*[\/\\]/g, '');

			  if (typeof suffix === 'string' && b.substr(b.length - suffix.length) == suffix) {
			    b = b.substr(0, b.length - suffix.length);
			  }

			  return b;
	}
};
jQuery(function()
{
	jQuery(document).on('click', 'a.action-icon-selector', function()
	{
	    //var icon_uni = jQuery(this).data('src');
		var ico = jQuery(this).attr('rel');
		//var icon_file = jQuery(this).find('img:first').attr('src');
	    //jQuery('#woocommerce_woo_advance_order_status_action_icon').val( sb.basename(icon_file) );
		jQuery('#woocommerce_woo_advance_order_status_action_icon').val( ico );
	    //jQuery('.default-action-icon-selector').attr('class', 'button default-action-icon-selector tips woocommerce_status_action_font_icon woocommerce_status_actions_' + icon_uni);
	    jQuery('#action-icon-selector').attr('class', 'button tips icon-' + ico);
	    jQuery('#TB_closeWindowButton').click();
	});
});