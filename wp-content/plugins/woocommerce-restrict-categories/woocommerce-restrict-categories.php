<?php
/*
Plugin Name: WooCommerce Restrict Categories Plus
Plugin URI:  http://ignitewoo.com
Description: Allows you to restrict the categories that users ( or specific roles ) can view in your store.
Version: 3.5.10
Author: Ignitewoo.com
Author URI: http://ignitewoo.com
Email: support@ignitewoo.com
*/

/*

Copyright (c) 2013 - IgniteWoo.com

Portions Copyright (c) 2012 - Matthew Muro

*/

/**
* Required functions
*/
if ( ! function_exists( 'ignitewoo_queue_update' ) )
	require_once( dirname( __FILE__ ) . '/ignitewoo_updater/ignitewoo_update_api.php' );

$this_plugin_base = plugin_basename( __FILE__ );

add_action( "after_plugin_row_" . $this_plugin_base, 'ignite_plugin_update_row', 1, 2 );


/**
* Plugin updates
*/
ignitewoo_queue_update( plugin_basename( __FILE__ ), '384c776ed8a97c1a135c15a307966265', '2061' );


/* Restrict Categories class */
class IgniteWoo_Restrict_Product_Categories{
	
	private $cat_list = NULL;
	
	public function __construct() {

		if ( is_admin() ) {

			/* Build options and settings pages. */
			add_action( 'admin_init', array( &$this, 'init' ) );
			add_action( 'admin_menu', array( &$this, 'add_admin' ) );
			
			/* Adds a Settings link to the Plugins page */
			add_filter( 'plugin_action_links', array( &$this, 'rc_plugin_action_links' ), 10, 2 );
			add_filter( 'screen_settings', array( &$this, 'add_screen_options' ) );
			
		}

		add_action( 'wp', array( &$this, 'product_check' ), 999 );

		add_action( 'init', array( &$this, 'load_plugin_textdomain' ) );

		add_filter( 'woocommerce_product_categories_widget_args', array( &$this, 'widget_args' ) );

		add_filter( 'loop_shop_post_in', array( $this, 'product_filter' ), 99 );
		
		add_filter( 'the_posts', array( $this, 'the_posts' ), 12, 2 );
		
		add_filter( 'woocommerce_related_products_args', array( $this, 'related_products_filter' ), 12, 2 );
		
		add_action( 'init', array( $this, 'layered_nav_init' ), 90 );
		
		add_filter( 'woocommerce_product_subcategories_args', array( &$this, 'product_subcategories_args' ), 999, 1 );

	}

	
	function load_plugin_textdomain() {

		$locale = apply_filters( 'plugin_locale', get_locale(), 'ignitewoo-restrict-categories' );

		load_textdomain( 'ignitewoo-restrict-categories', WP_LANG_DIR.'/woocommerce/ignitewoo-restrict-categories-'.$locale.'.mo' );

		$plugin_rel_path = apply_filters( 'ignitewoo_translation_file_rel_path', dirname( plugin_basename( __FILE__ ) ) . '/languages' );

		load_plugin_textdomain( 'ignitewoo-restrict-categories', false, $plugin_rel_path );

	}

	
	function product_check() { 
		global $post;
		
		if ( !is_product() ) 
			return;
			
		$this->get_the_cats();
		
		if ( empty( $this->cat_list ) )
			return;

		if ( !is_array( $this->cat_list ) )
			$this->cat_list = explode( ',' , $this->cat_list );

		$cats = wp_get_object_terms( $post->ID, 'product_cat' );
		
		if ( empty( $cats ) )
			return;

		$has_access = false; 
		
		foreach( $cats as $c ) { 
		
			if ( in_array( $c->term_id, $this->cat_list ) )
				$has_access = true;
		
		}

		if ( !$has_access ) { 
		
			$url = get_permalink( woocommerce_get_page_id( 'shop' ) );

			wp_redirect( $url );
			
			die;
			
		}
		
	}

	
	function widget_args( $args ) {

		if ( !isset( $this->cat_list ) || empty( $this->cat_list ) )
			return;

		$args['include'] = $this->cat_list;

		return $args;
		
	}


	function get_terms_args( $args = array() , $taxonomies = array() ) {
		global $product; 

		// For variable products that use attribute taxonomies
		if ( !empty( $product ) && !empty( $product->children ) )
			return $args;
		
		if ( is_admin() || current_user_can('administrator') )
			return $args;

		if ( empty( $taxonomies ) || count( $taxonomies ) <= 0 )
			return;

		if ( in_array( 'product_tag', $taxonomies ) )
			return $args;

		$args['include'] = $this->cat_list;

		return $args;
	}

	
	/**
	 * Add Settings link to Plugins page
	 * 
	 * @since 1.8 
	 * @return $links array Links to add to plugin name
	 */
	public function rc_plugin_action_links($links, $file) {
		if ( $file == plugin_basename(__FILE__) )
			$links[] = '<a href="admin.php?page=restrict-categories">'.__('Settings', 'ignitewoo-restrict-categories').'</a>';
	
		return $links;
	}

	
	/**
	 * Get all categories that will be used as options.
	 * 
	 * @since 1.0
	 * @uses get_categories() Returns an array of category objects matching the query parameters.  
	 * @return $cat array All category slugs.
	 */
	public function get_cats() {

		$categories = get_terms( 'product_cat','hide_empty=0' );

		foreach ( $categories as $category ) {
			$cat[] = array(
				'slug' => $category->slug
				);
		}
	
		return $cat;
	}

	
	/**
	 * Set up the options array which will output all roles with categories.
	 * 
	 * @since 1.0
	 * @uses get_roles() Returns an array of all user roles.
	 * @uses get_cats() Returns an array of all categories.
	 * @return $rc_options array Multidimensional array with options.
	 */
	public function populate_opts() {
		$roles = $this->get_roles();
		$cats = $this->get_cats();
		
		
		$rc_options[] = 
					array(
					'name' => __('Not Logged In', 'ignitewoo-restrict-categories' ),
					'id' => 'not_logged_in_cats',
					'options' => $cats
					);
					
					
		foreach ( $roles as $name => $id ) {
				$rc_options[] = 
					array(
					'name' => $name,
					'id' => $id . '_cats',
					'options' => $cats
					);
		}
		
		return $rc_options;	
	}

	
	/**
	 * Set up the user options array which will output all users with categories.
	 * 
	 * @since 1.6
	 * @uses get_logins() Returns an array of all user logins.
	 * @uses get_cats() Returns an array of all categories.
	 * @return $rc_user_options array Multidimensional array with options.
	 */
	public function populate_user_opts() {
		$logins = $this->get_logins();
		$cats = $this->get_cats();
		
		foreach ( $logins as $name => $id ) {
				$rc_user_options[] = 
					array(
					'name' => $name,
					'id' => $id . '_user_cats',
					'options' => $cats
					);
		}
	
		return $rc_user_options;	
	}

	
	/**
	 * Set up the roles array which uses similar code to wp_dropdown_roles().
	 * 
	 * @since 1.0
	 * @uses get_editable_roles() Fetch a filtered list of user roles that the current user is allowed to edit.
	 * @return $roles array Returns array of user roles with the "pretty" name and the slug.
	 */
	public function get_roles() {
		$editable_roles = get_editable_roles();
	
		foreach ( $editable_roles as $role => $name ) {
			$roles[ $name['name'] ] = $role;
		}
	
		return $roles;
	}

	
	/**
	 * Set up the user logins array.
	 * 
	 * @since 1.6
	 * @uses get_users Returns an array filled with information about the blog's users. WP 3.1
	 * @uses get_users_of_blog() Returns an array filled with information about the blog's users. WP 3.0
	 * @return $users array Returns array of user logins.
	 */
	public function get_logins() {
		if ( function_exists( 'get_users' ) ) {
			$blogusers = get_users();
			
			foreach ( $blogusers as $login ) {
				$users[ $login->user_login ] = $login->user_nicename;
			}
		}
		elseif ( function_exists( 'get_users_of_blog' ) ) {
			$blogusers = get_users_of_blog();
			
			foreach ( $blogusers as $login ) {
				$users[ $login->user_login ] = $login->user_id;
			}
		}
	
		return $users;
	}

	
	/**
	 * Register database options and set defaults, which are blank
	 * 
	 * @since 1.0
	 * @uses register_setting() Register a setting in the database
	 */
	public function init() {
	
		if ( isset( $_REQUEST['option_page'] ) && $_REQUEST['option_page'] == 'restrict_cats_options_group' ) { 
		 
			$opts = array( 
				'restrict_cat_roles' =>  $_REQUEST['restrict_cat_roles'],
				'restrict_cat_users' => $_REQUEST['restrict_cat_users'],
			);
			
			update_option( 'restrict_cat_settingss', $opts );

		}

		register_setting( 'IgniteWoo_RestrictCats_options_group', 'IgniteWoo_RestrictCats_options', array( &$this, 'options_sanitize' ) );
		
		register_setting( 'IgniteWoo_RestrictCats_user_options_group', 'IgniteWoo_RestrictCats_user_options', array( &$this, 'options_sanitize' ) );
		
		register_setting( 'restrict_cats_options_group', 'restrict_cat_settings', array( &$this, 'options_sanitize' ) );

				
		/* Set the options to a variable */
		add_option( 'IgniteWoo_RestrictCats_options' );
		
		add_option( 'IgniteWoo_RestrictCats_user_options' );
		
		add_option( 'restrict_cat_settings' );
		
		$screen_options = get_option( 'RestrictCats-screen-options' );
		
		/* Default is 20 per page */
		$defaults = array(
			'roles_per_page' => 20,
			'users_per_page' => 20,
		);
		
		/* If the option doesn't exist, add it with defaults */
		if ( !$screen_options )
			update_option( 'RestrictCats-screen-options', $defaults );
		
		/* If the user has saved the Screen Options, update */
		if ( isset( $_REQUEST['restrict-categories-screen-options-apply'] ) && in_array( $_REQUEST['restrict-categories-screen-options-apply'], array( 'Apply', 'apply' ) ) ) {
		
			$roles_per_page = absint( $_REQUEST['RestrictCats-screen-options']['roles_per_page'] );
			
			$users_per_page = absint( $_REQUEST['RestrictCats-screen-options']['users_per_page'] );
			
			$updated_options = array(
				'roles_per_page' => $roles_per_page,
				'users_per_page' => $users_per_page,
			);
			
			update_option( 'RestrictCats-screen-options', $updated_options );
		}

	}

	
	/**
	 * Adds the Screen Options tab
	 * 
	 * @since 2.4
	 */
	public function add_screen_options($current) {
		global $current_screen;

		$options = get_option( 'RestrictCats-screen-options' );
		
		if ( 'woocommerce_page_restrict-categories' !== $current_screen->id )
			return;

		
		$current = '<h5>' . __(' Show on screen','ignitewoo-restrict-categories' ) .'</h5>
				<input type="text" value="' . $options['roles_per_page'] . '" maxlength="3" id="restrict-categories-roles-per-page" name="RestrictCats-screen-options[roles_per_page]" class="screen-per-page"> <label for="restrict-categories-roles-per-page">'. __('Roles', 'ignitewoo-restrict-categories' ) .'</label>
				<input type="text" value="' . $options['users_per_page'] . '" maxlength="3" id="restrict-categories-users-per-page" name="RestrictCats-screen-options[users_per_page]" class="screen-per-page"> <label for="restrict-categories-users-per-page">' .__('Users','ignitewoo-restrict-categories' ).'</label>
				<input type="submit" value="'.__('Apply','ignitewoo-restrict-categories').'" class="button" id="restrict-categories-screen-options-apply" name="restrict-categories-screen-options-apply">';

		
		return $current;
	}

	
	/**
	 * Sanitize input
	 * 
	 * @since 1.3
	 * @return $input array Returns array of input if available
	 */
	public function options_sanitize( $input ) {

		if ( !isset( $_REQUEST['option_page'] ) )
			return;
		
		$options =  ( 'IgniteWoo_RestrictCats_user_options_group' == $_REQUEST['option_page'] ) ? get_option( 'IgniteWoo_RestrictCats_user_options' ) : get_option( 'IgniteWoo_RestrictCats_options' );

		if ( is_array( $input ) ) {
		
			foreach( $input as $k => $v ) {
				
				if ( isset( $_REQUEST['IgniteWoo_RestrictCats_user_options'][$k] ) )
					$options[ $k ] = $_REQUEST['IgniteWoo_RestrictCats_user_options'][$k];
					//$options['IgniteWoo_RestrictCats_user_options'][$k] = $v;
				else 
					$options[ $k ] = $v;
			}
		}

		return $options;
	}

	
	/**
	 * Add options page and handle data reset
	 * 
	 * 
	 * @since 1.0
	 * @uses add_options_page() Creates a menu item under the Settings menu.
	 */
	public function add_admin() {

		/* Add menu to Settings */
		add_submenu_page( 'woocommerce', __('Restrict Categories', 'ignitewoo-restrict-categories'), __('Restrict Categories', 'ignitewoo-restrict-categories'), 'manage_woocommerce', 'restrict-categories', array( &$this, 'admin' ) );
		
		if ( !isset( $_GET['page'] ) || !isset( $_REQUEST['action'] ) )
			return;
			
		/* Resets the options */
		if ( $_GET['page'] == 'restrict-categories' && 'reset' == $_REQUEST['action'] ) {
		
			$nonce = $_REQUEST['_wpnonce'];
			
			/* Security check to verify the nonce */
			if ( ! wp_verify_nonce($nonce, 'rc-reset-nonce') )
				die(__('Security check'));
			
			/* Reset Roles and Users options */
			update_option( 'IgniteWoo_RestrictCats_options', array() );
			update_option( 'IgniteWoo_RestrictCats_user_options', array() );
			
			/* Set submitted action to display success message */
			$_POST['reset'] = true;
		}


	}

	
	/**
	 * Builds the options settings page
	 * 
	 * @since 1.0
	 * @global $rc_options array The global options array populated by populate_opts().
	 * @global $rc_user_options array The global options array populated by populate_user_opts().
	 * @uses get_option() A safe way to get options from the options database table.
	 * @uses wp_list_categories() Displays a list of categories
	 */
	public function admin() {

		/* Display message for resetting form */
		if ( isset( $_POST['reset'] ) )
			_e('<div id="setting-error-settings_updated" class="updated settings-error"><p><strong>Settings reset.</strong></p></div>', 'ignitewoo-restrict-categories');
		
		/* Default main tab is Settings */
		$tab = 'settings';
		
		/* Set variables if the Users tab is selected */
		if ( isset( $_GET['type'] ) && $_GET['type'] == 'users' ) {
			$rc_user_options = $this->populate_user_opts();
			$tab = 'users';
		} else if ( isset( $_GET['type'] ) && $_GET['type'] == 'roles' ) {
			$tab = 'roles';
			$rc_options = $this->populate_opts();
		}

		/* Setup links for Roles/Users tabs */
		$base_tab = esc_url( admin_url( 'admin.php?page=restrict-categories' ) );
		
		$users_tab = add_query_arg( 'type', 'users', $base_tab );
		$roles_tab = add_query_arg( 'type', 'roles', $base_tab );
		
		
		$settingss = get_option( 'restrict_cat_settingss', false );

		$role_opt = isset( $settingss['restrict_cat_roles'] ) ? $settingss['restrict_cat_roles'] : null;
		$user_opt = isset( $settingss['restrict_cat_users'] ) ? $settingss['restrict_cat_users'] : null;

		?>
		
		<script>
			jQuery( document ).ready( function() {

				jQuery( '.categorychecklist_click' ).click( function() {
					var restrict_tree = jQuery( this ).attr( "rel" );
					restrict_action = jQuery( this ).attr( "act" );
					jQuery ( ".categorychecklist_" + restrict_tree ).find( "li input" ).each( function() {
						if ( "select" == restrict_action ) 
							jQuery( this ).prop('checked', true);
						else
							jQuery( this ).prop('checked', false);
					})
					return false;
				});

			});

		</script>
		<div class="wrap">
			<?php screen_icon( 'options-general' ); ?>
			
			<h2><?php _e('Restrict Categories - W/P/L/O/C/K/E/R/./C/O/M', 'ignitewoo-restrict-categories'); ?></h2>
			
			<h2 class="nav-tab-wrapper">
				<a href="<?php echo $base_tab; ?>" class="nav-tab <?php echo ( $tab == 'settings' ) ? 'nav-tab-active' : ''; ?>">Settings</a>
				<a href="<?php echo $roles_tab; ?>" class="nav-tab <?php echo ( $tab == 'roles' ) ? 'nav-tab-active' : ''; ?>">Roles</a>
				<a href="<?php echo $users_tab; ?>" class="nav-tab <?php echo ( $tab == 'users' ) ? 'nav-tab-active' : ''; ?>">Users</a>
			</h2>
			
			<form method="post" action="options.php">
				<?php
				/* Create a new instance of our user/roles boxes class */
				$boxes = new IgniteWoo_RestrictCats_User_Role_Boxes();
				
				if ( $tab == 'settings' ) { ?>

				<fieldset>
				
					<?php settings_fields( 'restrict_cats_options_group' ); ?>
					
					<p><?php _e( 'Adjust the settings to suit your needs. WARNING: If you have large number of users your server may cause a script timeout before settings for all users load. Contact your hosting company and ask them to increase the PHP timeout limit.', 'ignitewoo-restrict-categories' )?></em></p>
					<p><input type="checkbox" name="restrict_cat_users" value="yes" <?php checked( $user_opt, 'yes', true ) ?> > Enable Restricting Users</p>
					
					<p><input type="checkbox" name="restrict_cat_roles" value="yes" <?php checked( $role_opt, 'yes', true ) ?>> Enable Restricting Roles</p>
					
					<p class="submit">
							<input type="submit" name="submit" class="button-primary" value="<?php _e('Save Changes', 'ignitewoo-restrict-categories') ?>" />
						</p>
					
				</fieldset>
				<?php } else if ( $tab == 'roles' ) { ?>
					<?php
					if ( empty( $role_opt ) || 'yes' !== $role_opt ) { 
						?>
						<p><?php _e( 'Role restrictions settings are not enabled in the settings tab', 'ignitewoo-restrict-categories' )?></em></p>
						<?php
					} else { 
					?>.
					<fieldset>
						<p><?php _e( 'Select the product categories the a role can access. <em>If no categories are defined for a given role then all categories can be accessed by users with that role.', 'ignitewoo-restrict-categories' )?></em></p>
						<?php
							settings_fields( 'IgniteWoo_RestrictCats_options_group' );

							/* Create boxes for Roles */
							$boxes->start_box( get_option( 'IgniteWoo_RestrictCats_options' ), $rc_options, 'IgniteWoo_RestrictCats_options' );
						?>
						<p class="submit">
							<input type="submit" name="submit" class="button-primary" value="<?php _e('Save Changes', 'ignitewoo-restrict-categories') ?>" />
						</p>
					</fieldset>
					<?php } ?>
					
				<?php } elseif ( $tab == 'users' ) {
				
					if ( empty( $user_opt ) || 'yes' !== $user_opt ) { 
						?>
						<p><?php _e( 'User restrictions settings are not enabled in the settings tab', 'ignitewoo-restrict-categories' )?></em></p>
						<?php
					} else { 
					
						settings_fields( 'IgniteWoo_RestrictCats_user_options_group' );
				?>
					<fieldset>
						<p><?php _e( "Select the product categories the a user can access. <em>If no categories are defined for a given user then all categories can be accessed that user, unless you've limited category access for their user role.</em></p>
						<p> <strong>NOTE:</strong> Selecting categories for a user <em>take precedence over</em> any categories you have chosen for that user's role.", 'ignitewoo-restrict-categories' ) ?></p>
						<?php
							/* Create boxes for Users */
							$boxes->start_box( get_option( 'IgniteWoo_RestrictCats_user_options' ), $rc_user_options, 'IgniteWoo_RestrictCats_user_options' );
						?>
						</fieldset>
						
						<p class="submit">
							<input type="submit" name="submit" class="button-primary" value="<?php _e('Save Changes', 'ignitewoo-restrict-categories') ?>" />
						</p>
					<?php } ?>
				<?php } ?>

			</form>

			<?php if ( 'settings' !== $tab ) { ?>
			<h3><?php _e('Reset to Default Settings', 'ignitewoo-restrict-categories'); ?></h3>
				
			<p><?php _e('This option will reset all changes you have made to the default restrictions configuration.  <strong>You cannot undo this process</strong>.', 'ignitewoo-restrict-categories'); ?></p>
					
			<form method="post">
				<input class="button-secondary" name="reset" type="submit" value="<?php _e('Reset', 'ignitewoo-restrict-categories'); ?>" />
				<input type="hidden" name="action" value="reset" />
				<?php wp_nonce_field( 'rc-reset-nonce' ); ?>
			</form>
			<?php } ?>
		</div>
	<?php
	
	}

	
	function related_products_filter( $args = array() ) { 
		global $post;
		
		if ( empty( $args['post__in'] ) )
			return;
			
		$post__in = array_unique( apply_filters( 'loop_shop_post_in', array() ), 99 );
	
		if ( empty( $post__in ) )
			return $args;
		
		foreach( $args['post__in'] as $k => $v ) { 
			if ( !in_array( $v, $post__in ) )
				unset( $args['post__in'][$k] );
		}

		// No remaining related products? Set 2 from the $post__in list
		// This may not result in "related" but it prevents restricted items from appearing
		
		// First unset the current post from the array
		foreach( $post__in as $k => $v ) 
			if ( $v == $post->ID )
				unset( $post__in[ $k ] );
				
		if ( empty( $args['post__in'] ) )
			$args['post__in'] = array_slice( $post__in, 0, 2 );
		
		return $args;
	
	}
	
	
	// Filter search results
	public function the_posts( $posts, $query = false ) {

		// Abort if there's no query
		if ( ! $query )
			return $posts;

		$array = apply_filters( 'loop_shop_post_in', array() );
		
		if ( empty( $array ) )
			$array = array();
		
		$post__in = array_unique( $array );

		// Abort if we're not filtering posts
		if ( empty( $post__in ) )
			return $posts;

		// Abort if this query has already been done
		if ( ! empty( $query->wc_query ) )
			return $posts;

		// Abort if this isn't a search query
		if ( empty( $query->query_vars["s"] ) )
			return $posts;

		// Abort if we're not on a post type archive/product taxonomy
		//if 	( ! $query->is_post_type_archive( 'product' ) && ! $query->is_tax( get_object_taxonomies( 'product' //) ) )
		//	return $posts;

		$filtered_posts = array();
		
		$queried_post_ids = array();

		foreach ( $posts as $post ) {
			if ( in_array( $post->ID, $post__in ) ) {
				$filtered_posts[] = $post;
				$queried_post_ids[] = $post->ID;
			}
		}

		$query->posts = $filtered_posts;
		
		$query->post_count = count( $filtered_posts );

		// Ensure filters are set
		$this->unfiltered_product_ids = $queried_post_ids;
		
		$this->filtered_product_ids = $queried_post_ids;
		
		$this->layered_nav_product_ids = null;

		if ( sizeof( $this->layered_nav_post__in ) > 0 ) {
			$this->layered_nav_product_ids = array_intersect( $this->unfiltered_product_ids, $this->layered_nav_post__in );
		} else {
			$this->layered_nav_product_ids = $this->unfiltered_product_ids;
		}
		
		
		return $filtered_posts;
	}

	// Filter products in the shop
	// Runs via a WooCommerce hook "loop_shop_post_in"
	function product_filter( $matched_products = null ) {
		global $wpdb, $current_user;

		if ( empty( $current_user ) )
			$current_user = get_currentuserinfo();
		
		if ( empty( $this->cat_list ) )
			$this->get_the_cats();

		if ( empty( $this->cat_list ) )
			return array();

		if ( !is_array( $this->cat_list ) )
			$cats = explode( ',' , $this->cat_list );
		else 
			$cats = $this->cat_list;
			
		$cats = array_unique( $cats );
			
		$cats = implode( "','", $cats );

		$sql = "SELECT DISTINCT ID, post_parent, post_type FROM $wpdb->posts AS posts
				LEFT JOIN {$wpdb->term_relationships} AS rel ON posts.ID=rel.object_ID
				LEFT JOIN {$wpdb->term_taxonomy} AS tax USING( term_taxonomy_id )
				LEFT JOIN {$wpdb->terms} AS term USING( term_id )
				WHERE post_type IN ( 'product', 'product_variation' ) 
				AND post_status = 'publish' 
				AND tax.taxonomy = 'product_cat'
				AND term.term_id IN ('" . $cats. "')
			";

		$matched_products_query = $wpdb->get_results( $sql, OBJECT_K );

		$matched_products = array();

		 if ( $matched_products_query ) {
		 
			foreach ( $matched_products_query as $product ) {
			
				if ( $product->post_type == 'product' )
					$matched_products[] = $product->ID;
					
				if ( $product->post_parent > 0 && ! in_array( $product->post_parent, $matched_products ) )
					$matched_products[] = $product->post_parent;
			}
	        }
//var_dump( $matched_products ); die;
	        return array_unique( $matched_products );
	}

	
	public function layered_nav_init( ) {

		if ( !is_active_widget( false, false, 'woocommerce_layered_nav', true ) || is_admin() )
			return;

		add_filter( 'loop_shop_post_in', array( $this, 'layered_nav_query' ) );
		
	}
	
	
	public function layered_nav_query( $filtered_posts ) {
		global $_chosen_attributes, $wp_query, $woocommerce;

		if ( empty( $this->cat_list ) )
			$this->get_the_cats();

		if ( empty( $this->cat_list ) )
			return;

		if ( !is_array( $this->cat_list ) )
			$cats = explode( ',' , $this->cat_list );
			
		if ( is_array( $cats ) ) 
			$cats = array_unique( $cats );
		else 
			$cats = array();
		
		if ( sizeof( $_chosen_attributes ) <= 0 )
			return;

		// Unhook the WooCommerce filter and let this one take control
		remove_filter( 'loop_shop_post_in', array( $woocommerce, 'layered_nav_query' ) );
		
		$matched_products   = array(
			'and' => array(),
			'or'  => array()
		);
		
		$filtered_attribute = array(
			'and' => false,
			'or'  => false
		);

		foreach ( $_chosen_attributes as $attribute => $data ) {
		
			$matched_products_from_attribute = array();
			
			$filtered = false;

			if ( sizeof( $data['terms'] ) > 0 ) {
			
				foreach ( $data['terms'] as $value ) {

					$posts = get_posts(
						array(
							'post_type' 	=> 'product',
							'numberposts' 	=> -1,
							'post_status' 	=> 'publish',
							'fields' 		=> 'ids',
							'no_found_rows' => true,
							'tax_query' => array(
								'relation' => 'AND',
								array(
									'taxonomy' 	=> $attribute,
									'terms' 	=> $value,
									'field' 	=> 'id'
								),
								array(
									'taxonomy' 	=> 'product_cat',
									'terms' 	=> $cats,
									'field' 	=> 'id'
								)
							)
						)
					);

					if ( !is_wp_error( $posts ) ) {

						if ( sizeof( $matched_products_from_attribute ) > 0 || $filtered )
							$matched_products_from_attribute = $data['query_type'] == 'or' ? array_merge( $posts, $matched_products_from_attribute ) : array_intersect( $posts, $matched_products_from_attribute );
						else
							$matched_products_from_attribute = $posts;

						$filtered = true;
					}
				}
			}

			if ( sizeof( $matched_products[ $data['query_type'] ] ) > 0 || $filtered_attribute[ $data['query_type'] ] === true ) {
				$matched_products[ $data['query_type'] ] = ( $data['query_type'] == 'or' ) ? array_merge( $matched_products_from_attribute, $matched_products[ $data['query_type'] ] ) : array_intersect( $matched_products_from_attribute, $matched_products[ $data['query_type'] ] );
			} else {
				$matched_products[ $data['query_type'] ] = $matched_products_from_attribute;
			}

			$filtered_attribute[ $data['query_type'] ] = true;

			$this->filtered_product_ids_for_taxonomy[ $attribute ] = $matched_products_from_attribute;
		}

		// Combine our AND and OR result sets
		if ( $filtered_attribute['and'] && $filtered_attribute['or'] )
			$results = array_intersect( $matched_products[ 'and' ], $matched_products[ 'or' ] );
		else
			$results = array_merge( $matched_products[ 'and' ], $matched_products[ 'or' ] );

		if ( $filtered ) {

			WC()->query->layered_nav_post__in   = $results;
			WC()->query->layered_nav_post__in[] = 0;

			if ( sizeof( $filtered_posts ) == 0 ) {
				$filtered_posts   = $results;
				$filtered_posts[] = 0;
			} else {
				$filtered_posts   = array_intersect( $filtered_posts, $results );
				$filtered_posts[] = 0;
			}

		}
	
		return (array) $filtered_posts;
	}
	
	
	function product_subcategories_args( $args = array() ) { 

		if ( empty( $args ) )
			return array();
			
		if ( empty( $this->cat_list ) ) {
		
			$args['include'] = '9999999'; // dummy load
			
			return $args;
		
		}
		
		// Main query only
		if ( !is_main_query() ) 
			return;

		// Don't show when filtering, searching or when on page > 1 and ensure we're on a product archive
		if ( is_search() || is_filtered() || is_paged() || ( ! is_product_category() && ! is_shop() ) ) 
			return;

		// Check categories are enabled
		if ( is_shop() && get_option( 'woocommerce_shop_page_display' ) == '' ) 
			return;

		$args['include'] = $this->cat_list;

		return $args;
	
	}
	
	
	/**
	 * Rewrites the query to only display the selected categories from the settings page
	 * 
	 * @since 1.0
	 * @global $wp_query object The global WP_Query object.
	 * @global $current_user object The global user object.
	 * @uses WP_User() Retrieve user object.
	 * @uses get_option() A safe way to get options from the options database table.
	 */
	public function get_the_cats() {
		global $wp_query, $current_user, $user_ID;

		//if ( !$user_ID )
		//	return;
		
		$this->cat_list = '';
		
		/* Get selected categories for Roles */
		$settings = get_option( 'IgniteWoo_RestrictCats_options' );

		if ( !$user_ID  && !empty( $settings[ 'not_logged_in_cats' ] ) ) {

			$settings[ 'not_logged_in_cats' ] = array_values( array_diff( $settings[ 'not_logged_in_cats' ], array( 'RestrictCategoriesDefault' ) ) );

			//Build the category list 
			foreach ( $settings[ 'not_logged_in_cats' ] as $category ) {
			
				$term_id = get_term_by( 'slug', $category, 'product_cat' )->term_id;

				// If WPML is installed, return the translated ID
				if ( function_exists( 'icl_object_id' ) )
					$term_id = icl_object_id( $term_id, 'product_cat', true );
				
				if ( !empty( $this->cat_list ) )
					$this->cat_list = explode( ',', $this->cat_list );
				
				$this->cat_list[] = $term_id;
				
				$this->cat_list = implode( ',', $this->cat_list );
				
				//$this->cat_list .= $term_id . ',';
				
				$this->cat_list_names[] = $category;
			}

			$this->cat_filters( $this->cat_list );

			return;
		}

		/* Get the current user in the admin */
		$user = new WP_User( $user_ID );
				
		/* Get the user role */
		$user_cap = $user->roles;
		
		/* Get the user login name/ID */
		if ( function_exists( 'get_users' ) )
			$user_login = $user->user_nicename;
			
		elseif ( function_exists( 'get_users_of_blog' ) )
			$user_login = $user->ID;

		
		/* Get selected categories for Users */
		$settings_user = get_option( 'IgniteWoo_RestrictCats_user_options' );

		/* Selected categories for User overwrites Roles selection */
		if ( is_array( $settings_user ) && !empty( $settings_user[ $user_login . '_user_cats' ] ) ) {

			/* Strip out the placeholder category, which is only used to make sure the checkboxes work */
			$settings_user[ $user_login . '_user_cats' ] = array_values( array_diff( $settings_user[ $user_login . '_user_cats' ], array( 'RestrictCategoriesDefault' ) ) );
			
			/* Build the category list */
			foreach ( $settings_user[ $user_login . '_user_cats' ] as $category ) {

				$term_id = get_term_by( 'slug', $category, 'product_cat' )->term_id;

				/* If WPML is installed, return the translated ID */
				if ( function_exists( 'icl_object_id' ) )
					$term_id = icl_object_id( $term_id, 'product_cat', true );
				
				$this->cat_list .= $term_id . ',';
				
				$this->cat_list_names[] = $category;
			}

			$this->cat_filters( $this->cat_list );
		}
		
		else {

			foreach ( $user_cap as $key ) {

				if ( is_array( $settings ) && !empty( $settings[ $key . '_cats' ] ) ) {
				
					// Strip out the placeholder category, which is only used to make sure the checkboxes work 
					$settings[ $key . '_cats' ] = array_values( array_diff( $settings[ $key . '_cats' ], array( 'RestrictCategoriesDefault' ) ) );
					
					//Build the category list 
					foreach ( $settings[ $key . '_cats' ] as $category ) {
					
						$term_id = get_term_by( 'slug', $category, 'product_cat' )->term_id;

						// If WPML is installed, return the translated ID
						if ( function_exists( 'icl_object_id' ) )
							$term_id = icl_object_id( $term_id, 'product_cat', true );
						
						$this->cat_list .= $term_id . ',';
						
						$this->cat_list_names[] = $category;
					}
				}

				if ( isset( $this->cat_list ) ) 
					$this->cat_filters( $this->cat_list );

			}
		}

	}

	
	/**
	 * Adds filters for category restriction
	 * 
	 * @since 1.6
	 * @global $cat_list string The global comma-separated list of restricted categories.
	 */
	public function cat_filters( $categories ) {

		if ( !isset( $categories ) ) 
			return;

		$categories = explode( ',', $categories );
		
		$categories = implode( ',', $categories );

		$this->cat_list = rtrim( $categories, ',' );

	}
	
}


$ignitewoo_rc = new IgniteWoo_Restrict_Product_Categories();



/**
 * Creates each box for users and roles.
 * 
 * @since 1.8
 */
class IgniteWoo_RestrictCats_User_Role_Boxes {
	
	/**
	 * Various information needed for displaying the pagination
	 *
	 * @since 2.4
	 * @var array
	 */
	var $_pagination_args = array();
	
	public function start_box($settings, $options, $options_name) {

		/* Create a new instance of our custom walker class */
		$walker = new RestrictCats_Walker_Category_Checklist();


		/* Get screen options from the wp_options table */
		$screen_options = get_option( 'RestrictCats-screen-options' );

		/* How many to show per page */
		$per_page = ( 'IgniteWoo_RestrictCats_options' == $options_name  ) ? $screen_options['roles_per_page'] : $screen_options['users_per_page'];

		/* What page are we looking at? */
		$current_page = $this->get_pagenum();

		/* How many do we have? */
		$total_items = count( $options );

		/* Calculate pagination */
		$options = array_slice( $options, ( ( $current_page - 1 ) * $per_page ), $per_page );

		/* Register our pagination */
		$this->set_pagination_args( array(
			'total_items' => $total_items,
			'per_page'    => $per_page,
			'total_pages' => ceil( $total_items / $per_page )
		) );

		/* Display pagination */
		echo '<div class="tablenav">';
			$this->pagination( 'top' );
		echo '<br class="clear" /></div>';

		$z = 0;
		
		/* Loop through each role and build the checkboxes */
		foreach ( $options as $key => $value ) :

			$z++;
			
			$id = $value['id'];

			/* Get selected categories from database, if available */
			if ( isset( $settings ) && is_array( $settings) && is_array( $settings[ $id ] ) )
				$selected = $settings[ $id ];
			else
				$selected = array();

			/* Setup links for Roles/Users tabs in this class */
			$roles_tab = esc_url( admin_url( 'admin.php?page=restrict-categories' ) );
			$users_tab = add_query_arg( $id . '-tab', 'popular', $roles_tab );

			/* If the Users tab is selected, setup query_arg for checkbox tabs */
			if ( isset( $_REQUEST['type'] ) && $_REQUEST['type'] == 'users' ) {
				$roles_tab = add_query_arg( array( 'type' => 'users', $id . '-tab' => 'all' ), $roles_tab );
				$users_tab = add_query_arg( array( 'type' => 'users', $id . '-tab' => 'popular' ), $roles_tab );
			}

			/* Make sure View All and Most Used tabs work when paging */
			if ( isset( $_REQUEST['paged'] ) ) {
				$roles_tab = add_query_arg( array( 'paged' => absint( $_REQUEST['paged'] ) ), $roles_tab );
				$users_tab = add_query_arg( array( 'paged' => absint( $_REQUEST['paged'] ) ), $users_tab );
			}

			/* View All tab is default */
			$current_tab = 'all';

			/* Check which checkbox tab is selected */
			if ( isset( $_REQUEST[ $id . '-tab' ] ) && in_array( $_REQUEST[ $id . '-tab' ], array( 'all', 'popular' ) ) )
				$current_tab = $_REQUEST[ $id . '-tab' ];
		?>
			<div id="side-sortables" class="metabox-holder" style="float:left; padding:5px;">
				<div class="postbox">
					<h3 class="hndle"><span><?php echo $value['name']; ?></span></h3>

		<div class="inside" style="padding:0 10px;">
						<div class="taxonomydiv">
			<ul id="taxonomy-category-tabs" class="taxonomy-tabs add-menu-item-tabs">
				<li<?php echo ( 'all' == $current_tab ? ' class="tabs"' : '' ); ?>><a href="<?php echo add_query_arg( $id . '-tab', 'all', $roles_tab ); ?>" class="nav-tab-link"><?php _e('View All', 'ignitewoo-restrict-categories' )?></a></li>
				<li<?php echo ( 'popular' == $current_tab ? ' class="tabs"' : '' ); ?>><a href="<?php echo $users_tab; ?>" class="nav-tab-link"><?php _e('Most Used', 'ignitewoo-restrict-categories' )?></a></li>
			</ul>
							<div id="<?php echo $id; ?>-all" class="tabs-panel <?php echo ( 'all' == $current_tab ? 'tabs-panel-active' : 'tabs-panel-inactive' ); ?>">
								<ul class="categorychecklist categorychecklist_<?php echo $z ?> form-no-clear" rel="<?php echo $z ?>">
								<?php
									wp_list_categories(
										array(
										'admin' => $id,
										'selected_cats' => $selected,
										'options_name' => $options_name,
										'hide_empty' => 0,
										'title_li' => '',
										'disabled' => ( 'all' == $current_tab ? false : true ),
										'walker' => $walker,
										'taxonomy' => 'product_cat'
										)
									);

									$disable_checkbox = ( 'all' == $current_tab ) ? '' : 'disabled="disabled"';
								?>
				<input style="display:none;" <?php echo $disable_checkbox; ?> type="checkbox" value="RestrictCategoriesDefault" checked="checked" name="<?php echo $options_name; ?>[<?php echo $id; ?>][]">
								</ul>
							</div>
			<div id="<?php echo $id; ?>-popular" class="tabs-panel <?php echo ( 'popular' == $current_tab ? 'tabs-panel-active' : 'tabs-panel-inactive' ); ?>">
				<ul class="categorychecklist form-no-clear">
								<?php
									wp_list_categories(
										array(
										'admin' => $id,
										'selected_cats' => $selected,
										'options_name' => $options_name,
										'hide_empty' => 0,
										'title_li' => '',
										'orderby' => 'count',
										'order' => 'DESC',
										'disabled' => ( 'popular' == $current_tab ? false : true ),
										'walker' => $walker
										)
									);

									$disable_checkbox = ( 'popular' == $current_tab ) ? '' : 'disabled="disabled"';
								?>
				<input style="display:none;" <?php echo $disable_checkbox; ?> type="checkbox" value="RestrictCategoriesDefault" checked="checked" name="<?php echo $options_name; ?>[<?php echo $id; ?>][]">
								</ul>
							</div>
						</div>

			<?php
							$shift_default = array_diff( $selected, array( 'RestrictCategoriesDefault' ) );
							$selected = array_values( $shift_default );
						?>
						<p style="padding-left:10px;"><strong><?php echo count( $selected ); ?></strong> <?php echo ( count( $selected ) > 1 || count( $selected ) == 0 ) ? 'categories' : 'category'; ?> selected</p>

						<p>
							<a class="categorychecklist_click" href="#" rel="<?php echo $z ?>" act="select" ><?php _e('Select All','ignitewoo-restrict-categories')?></a> | <a class="categorychecklist_click" href="#" rel="<?php echo $z ?>" act="unselect"><?php _e('Unselect All','ignitewoo-restrict-categories')?></a>
						</p>

					</div>
				</div>
			</div>
		<?php
		endforeach;	
	}
	
	/**
	 * Get the current page number
	 *
	 * @since 2.4
	 * @access protected
	 *
	 * @return int
	 */
	protected function get_pagenum() {
		$pagenum = isset( $_REQUEST['paged'] ) ? absint( $_REQUEST['paged'] ) : 0;

		if( isset( $this->_pagination_args['total_pages'] ) && $pagenum > $this->_pagination_args['total_pages'] )
			$pagenum = $this->_pagination_args['total_pages'];

		return max( 1, $pagenum );
	}

	
	/**
	 * Get number of items to display on a single page
	 *
	 * @since 2.4
	 * @access protected
	 *
	 * @return int
	 */
	protected function get_items_per_page( $option, $default = 20 ) {
		$per_page = (int) get_user_option( $option );
		if ( empty( $per_page ) || $per_page < 1 )
			$per_page = $default;

		return (int) apply_filters( $option, $per_page );
	}

	
	/**
	 * Display the pagination.
	 *
	 * @since 2.4
	 * @access protected
	 */
	protected function pagination( $which ) {
		if ( empty( $this->_pagination_args ) )
			return;

		extract( $this->_pagination_args );

		$output = '<span class="displaying-num">' . sprintf( _n( '1 item', '%s items', $total_items ), number_format_i18n( $total_items ) ) . '</span>';

		$current = $this->get_pagenum();

		$current_url = ( is_ssl() ? 'https://' : 'http://' ) . $_SERVER['HTTP_HOST'] . $_SERVER['REQUEST_URI'];

		$current_url = remove_query_arg( array( 'hotkeys_highlight_last', 'hotkeys_highlight_first' ), $current_url );

		$page_links = array();

		$disable_first = $disable_last = '';
		if ( $current == 1 )
			$disable_first = ' disabled';
		if ( $current == $total_pages )
			$disable_last = ' disabled';

		$page_links[] = sprintf( "<a class='%s' title='%s' href='%s'>%s</a>",
			'first-page' . $disable_first,
			esc_attr__( 'Go to the first page' ),
			esc_url( remove_query_arg( 'paged', $current_url ) ),
			'&laquo;'
		);

		$page_links[] = sprintf( "<a class='%s' title='%s' href='%s'>%s</a>",
			'prev-page' . $disable_first,
			esc_attr__( 'Go to the previous page' ),
			esc_url( add_query_arg( 'paged', max( 1, $current-1 ), $current_url ) ),
			'&lsaquo;'
		);

		if ( 'bottom' == $which )
			$html_current_page = $current;
		else
			$html_current_page = sprintf( "<input class='current-page' title='%s' type='text' name='%s' value='%s' size='%d' />",
				esc_attr__( 'Current page' ),
				esc_attr( 'paged' ),
				$current,
				strlen( $total_pages )
			);

		$html_total_pages = sprintf( "<span class='total-pages'>%s</span>", number_format_i18n( $total_pages ) );
		$page_links[] = '<span class="paging-input">' . sprintf( _x( '%1$s of %2$s', 'paging' ), $html_current_page, $html_total_pages ) . '</span>';

		$page_links[] = sprintf( "<a class='%s' title='%s' href='%s'>%s</a>",
			'next-page' . $disable_last,
			esc_attr__( 'Go to the next page' ),
			esc_url( add_query_arg( 'paged', min( $total_pages, $current+1 ), $current_url ) ),
			'&rsaquo;'
		);

		$page_links[] = sprintf( "<a class='%s' title='%s' href='%s'>%s</a>",
			'last-page' . $disable_last,
			esc_attr__( 'Go to the last page' ),
			esc_url( add_query_arg( 'paged', $total_pages, $current_url ) ),
			'&raquo;'
		);

		$output .= "\n<span class='pagination-links'>" . join( "\n", $page_links ) . '</span>';

		if ( $total_pages )
			$page_class = $total_pages < 2 ? ' one-page' : '';
		else
			$page_class = ' no-pages';

		$this->_pagination = "<div class='tablenav-pages{$page_class}'>$output</div>";

		echo $this->_pagination;
	}

	
	/**
	 * An internal method that sets all the necessary pagination arguments
	 *
	 * @since 2.4
	 * @param array $args An associative array with information about the pagination
	 * @access protected
	 */
	protected function set_pagination_args( $args ) {
	
		$args = wp_parse_args( $args, array(
			'total_items' => 0,
			'total_pages' => 0,
			'per_page' => 0,
		) );

		if ( !$args['total_pages'] && $args['per_page'] > 0 )
			$args['total_pages'] = ceil( $args['total_items'] / $args['per_page'] );

		// redirect if page number is invalid and headers are not already sent
		if ( ! headers_sent() && ( ! defined( 'DOING_AJAX' ) || ! DOING_AJAX ) && $args['total_pages'] > 0 && $this->get_pagenum() > $args['total_pages'] ) {
		
			wp_redirect( add_query_arg( 'paged', $args['total_pages'] ) );
			
			exit;
		}

		$this->_pagination_args = $args;
	}
}

/**
 * Custom walker class to create a category checklist
 * 
 * @since 1.5
 */
class RestrictCats_Walker_Category_Checklist extends Walker {

	var $tree_type = 'category';
	
	var $db_fields = array ('parent' => 'parent', 'id' => 'term_id'); //TODO: decouple this

	function start_lvl( &$output, $depth = 0 , $args = array() ) {
	
		$indent = str_repeat("\t", $depth);
		
		$output .= "$indent<ul class='children'>\n";
	}

	
	function end_lvl(  &$output, $depth = 0 , $args = array() ) {
	
		$indent = str_repeat("\t", $depth);
		
		$output .= "$indent</ul>\n";
	}

	
	function start_el ( &$output, $category, $depth = 0 , $args = array(), $current_object_id = 0 ) {

		extract($args);
		
		if ( empty($taxonomy) )
			$taxonomy = 'product_cat';

		if ( $taxonomy == 'product_cat' )
			$name = 'post_category';
		else
			$name = 'tax_input['.$taxonomy.']';
		
		$output .= "\n<li id='{$taxonomy}-{$category->term_id}'>" . 
		'<label class="selectit"><input value="' . $category->slug . '" type="checkbox" name="' . $options_name . '['. $admin .'][]" ' . checked( in_array( $category->slug, $selected_cats ), true, false ) . ( $disabled === true ? 'disabled="disabled"' : '' ) . ' /> ' . esc_html( apply_filters('the_category', $category->name ) ) . '</label>';
	}

	
	function end_el( &$output, $object, $depth = 0 , $args = array() ) {
	
		$output .= "</li>\n";
		
	}
}

/**
 * Delete options from the database
 * 
 * @since 1.8
 */
 /*
if ( isset ( $rc ) )
	register_uninstall_hook( __FILE__, 'RestrictCats_uninstall' );

	
function RestrictCats_uninstall() {

	delete_option( 'IgniteWoo_RestrictCats_options' );
	
	delete_option( 'IgniteWoo_RestrictCats_user_options' );
}
*/
?>