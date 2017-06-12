<style type="text/css">
	.ig-gallery-wrap .theme-browser .theme .theme-installed{
		/*position: relative;*/
		width: 2em;
  		padding: 0;
  		height: 2em;
	}
	.theme-browser .theme .theme-installed:before{
		font-size: 30px;
		top:-2px;
		left:-4px;
	}
	.ig-gallery-wrap .theme-browser .theme{
		margin: 0 1% 1% 0;
		width: 24%;
	}
	.ig-gallery-wrap .theme-browser .theme .theme-screenshot img{
		height: 13em;
	}
	.expanded .wp-full-overlay-footer.ig-get-pro-footer{
		position: fixed;
	    bottom: 60px;
	    left: 0;
	    height: 24px;
	    background: #ece788;
	    text-align: center;
	    padding-top: 0.2em;
	    border-top: 1px dashed #ddd;
	    border-bottom: 1px dashed #ddd;
	}
	.expanded .wp-full-overlay-footer.ig-get-pro-footer span{
		color: #900101;
	}
</style>
<div class="wrap ig-gallery-wrap">
	<h2><?php esc_html_e( 'Gallery items' ); ?><span class="title-count theme-count"></span></h2>
	<div class="ig-gal-description"><?php _e('Here\'s a collection of some ','icegram') ?><strong><?php _e('beautiful, powerful ready-to-use Icegram Campaigns.','icegram') ?></strong></div>
    <div><?php _e('No coding or special skills required. Simply click to ' ,'icegram')?><strong><?php _e(' Use This ','icegram')?></strong><?php _e('and the campaign will automatically appear in your Icegram dashboard.','icegram')?></div>
	<br/>
	<div class="theme-browser">
		<div class="themes"></div>
	</div>
	<div class="theme-install-overlay wp-full-overlay expanded"></div>
<!-- <div class="theme-overlay"></div> -->
</div><!-- .wrap -->
<script id="tmpl-theme" type="text/template">
	<# if ( data.image ) { #>
		<div class="theme-screenshot">
			<img src="{{ data.image.guid }}" alt="" />
		</div>
	<# } else { #>
		<div class="theme-screenshot blank"></div>
	<# } #>
	<span class="more-details" id="{{ data.id }}-action"><?php _e( 'Preview' ); ?></span>
	<div class="theme-author"><?php printf( __( 'By %s' ), '{{{ data.id }}}' ); ?></div>
</script>
<!-- TODO:: Remove it if not required -->

<script id="tmpl-theme-preview" type="text/template">
	<div class="wp-full-overlay-sidebar">
		<div class="wp-full-overlay-header">
			<a href="#" class="close-full-overlay"><span class="screen-reader-text"><?php _e( 'Close' ); ?></span></a>
			<a href="#" class="previous-theme"><span class="screen-reader-text"><?php _ex( 'Previous', 'Button label for a theme' ); ?></span></a>
			<a href="#" class="next-theme"><span class="screen-reader-text"><?php _ex( 'Next', 'Button label for a theme' ); ?></span></a>
			<a href="?action=fetch_messages&campaign_id={{data.campaign_id}}&gallery_item={{data.slug}}" class="button button-primary theme-install" style="display:none"><?php _e( 'Use This', 'icegram' ); ?></a>
			<a href="http://www.icegram.com/pricing/" target="_blank" class="button button-primary ig-get-pro " style="display:none">
				<# if(data.plan === '3') { #>
					<span><?php _e("Get The Max Plan", 'icegram') ?></span>
				<# } else if(data.plan === '2') { #>
					<span><?php _e("Get The Pro Plan", 'icegram') ?></span>
				<# } #>	
			</a>
		</div>
		<div class="wp-full-overlay-sidebar-content">
			<div class="install-theme-info">
				<h3 class="theme-name">{{ data.title.rendered }}</h3>
				<span class="theme-by"><?php printf( __( 'By %s' ), 'Icegram' ); ?></span>

				<img class="theme-screenshot" src="{{ data.image.guid }}" alt="" />

				<div class="theme-details">
					<!--
					<# if ( data.rating && data.rating > 0 ) { #>
						<div class="theme-rating">
							{{{ data.stars }}}
							<span class="num-ratings">Rating : {{ data.rating }}</span>
						</div>
					<# } else { #>
						<span class="no-rating"><?php _e( 'This theme has not been rated yet.' ); ?></span>
					<# } #>
					<div class="theme-version"><?php printf( __( 'Version: %s' ), '{{ data.version }}' ); ?></div> -->
					<div class="theme-description">{{{ data.description }}}</div>
				</div>
			</div>
		</div>
		<div class="wp-full-overlay-footer ig-get-pro ig-get-pro-footer " style="display:none">
		    <# if(data.plan === '3') { #>
				<span><?php _e("This template is available in the 'Max Plan'", 'icegram') ?></span>
			<# } else if(data.plan === '2') { #>
				<span><?php _e("This template is available in the 'Pro Plan'", 'icegram') ?></span>
			<# } #>
		</div>
		<div class="wp-full-overlay-footer">
			<button type="button" class="collapse-sidebar button-secondary" aria-expanded="true" aria-label="<?php esc_attr_e( 'Collapse Sidebar' ); ?>">
				<span class="collapse-sidebar-arrow"></span>
				<span class="collapse-sidebar-label"><?php _e( 'Collapse' ); ?></span>
			</button>
		</div>
	</div>
	<div class="wp-full-overlay-main">
		<iframe src="{{ data.link }}?utm_source=ig_inapp&utm_campaign=ig_gallery&utm_medium={{data.campaign_id}}" title="<?php esc_attr_e( 'Preview' ); ?>" />
	</div>
</script>
