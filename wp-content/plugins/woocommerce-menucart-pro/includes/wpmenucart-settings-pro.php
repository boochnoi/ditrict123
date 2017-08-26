<?php
class WpMenuCartPro_Settings {
	
	public function __construct() {
		add_action( 'admin_init', array( &$this, 'init_settings' ) ); // Registers settings
		add_action( 'admin_menu', array( &$this, 'wpmenucart_add_page' ) );
		add_filter( 'plugin_action_links_'.WpMenuCartPro::$plugin_basename, array( &$this, 'wpmenucart_add_settings_link' ) );

		//Menu admin, not using for now (very complex ajax structure...)
		//add_action( 'admin_init', array( &$this, 'wpmenucart_add_meta_box' ) );
	}
	/**
	 * User settings.
	 */
	public function init_settings() {
		$option = 'wpmenucart';
	
		// Create option in wp_options.
		if ( false == get_option( $option ) ) {
			add_option( $option );
		}
	
		// Section.
		add_settings_section(
			'plugin_settings',
			__( 'Plugin settings', 'wpmenucart' ),
			array( &$this, 'section_options_callback' ),
			$option
		);

		add_settings_field(
			'shop_plugin',
			__( 'Select which e-commerce plugin you would like Menu Cart to work with', 'wpmenucart' ),
			array( &$this, 'select_element_callback' ),
			$option,
			'plugin_settings',
			array(
				'menu'		=> $option,
				'id'		=> 'shop_plugin',
				'options'	=> (array) $this->get_shop_plugins(),
			)
		);			
		
		add_settings_field(
			'menu_slugs',
			__( 'Select the menu(s) in which you want to display the Menu Cart', 'wpmenucart' ),
			array( &$this, 'menus_select_element_callback' ),
			$option,
			'plugin_settings',
			array(
				'menu'		=> $option,
				'id'		=> 'menu_slugs',
				'options'	=> (array) $this->get_menu_array(),
			)
		);

		add_settings_field(
			'always_display',
			__( "Always display cart, even if it's empty", 'wpmenucart' ),
			array( &$this, 'checkbox_element_callback' ),
			$option,
			'plugin_settings',
			array(
				'menu'			=> $option,
				'id'			=> 'always_display',
			)
		);

		add_settings_field(
			'icon_display',
			__( 'Display shopping cart icon.', 'wpmenucart' ),
			array( &$this, 'checkbox_element_callback' ),
			$option,
			'plugin_settings',
			array(
				'menu'			=> $option,
				'id'			=> 'icon_display',
			)
		);

		add_settings_field(
			'flyout_display',
			__( 'Display cart contents in menu fly-out.', 'wpmenucart' ),
			array( &$this, 'checkbox_element_callback' ),
			$option,
			'plugin_settings',
			array(
				'menu'			=> $option,
				'id'			=> 'flyout_display',
			)
		);
		
		add_settings_field(
			'flyout_itemnumber',
			__( 'Set maximum number of products to display in fly-out', 'wpmenucart' ),
			array( &$this, 'select_element_callback' ),
			$option,
			'plugin_settings',
			array(
				'menu'			=> $option,
				'id'			=> 'flyout_itemnumber',
				'options'		=> array(
						'0'			=> '0',
						'1'			=> '1',
						'2'			=> '2',
						'3'			=> '3',
						'4'			=> '4',
						'5'			=> '5',
						'6'			=> '6',
						'7'			=> '7',
						'8'			=> '8',
						'9'			=> '9',
						'10'		=> '10',
				),
			)
		);			

		add_settings_field(
			'cart_icon',
			__( 'Choose a cart icon.', 'wpmenucart' ),
			array( &$this, 'icons_radio_element_callback' ),
			$option,
			'plugin_settings',
			array(
				'menu'			=> $option,
				'id'			=> 'cart_icon',
				'options' 		=> array(
					'0'			=> '0',
					'1'			=> '1',
					'2'			=> '2',
					'3'			=> '3',
					'4'			=> '4',
					'5'			=> '5',
					'6'			=> '6',
					'7'			=> '7',
					'8'			=> '8',
					'9'			=> '9',
					'10'		=> '10',
					'11'		=> '11',
					'12'		=> '12',
					'13'		=> '13',
				),
			)
		);


		add_settings_field(
			'items_display',
			__( 'What would you like to display in the menu?', 'wpmenucart' ),
			array( &$this, 'radio_element_callback' ),
			$option,
			'plugin_settings',
			array(
				'menu'			=> $option,
				'id'			=> 'items_display',
				'options' 		=> array(
					'1'			=> __( 'Items Only.' , 'wpmenucart' ),
					'2'			=> __( 'Price Only.' , 'wpmenucart' ),
					'3'			=> __( 'Both price and items.' , 'wpmenucart' ),
				),
			)
		);
		
		add_settings_field(
			'items_alignment',
			__( 'Select the alignment that looks best with your menu.', 'wpmenucart' ),
			array( &$this, 'radio_element_callback' ),
			$option,
			'plugin_settings',
			array(
				'menu'			=> $option,
				'id'			=> 'items_alignment',
				'options' 		=> array(
					'left'			=> __( 'Align Left.' , 'wpmenucart' ),
					'right'			=> __( 'Align Right.' , 'wpmenucart' ),
					'standard'		=> __( 'Default Menu Alignment.' , 'wpmenucart' ),
				),
			)
		);

		add_settings_field(
			'custom_class',
			__( 'Enter a custom CSS class (optional)', 'wpmenucart' ),
			array( &$this, 'text_element_callback' ),
			$option,
			'plugin_settings',
			array(
				'menu'			=> $option,
				'id'			=> 'custom_class',
			)
		);

		if ( function_exists( 'icl_register_string' ) ) {
			add_settings_field(
				'wpml_string_translation',
				__( "Use WPML String Translation", 'wpmenucart' ),
				array( &$this, 'checkbox_element_callback' ),
				$option,
				'plugin_settings',
				array(
					'menu'			=> $option,
					'id'			=> 'wpml_string_translation',
				)
			);
		}

		if ( class_exists( 'WooCommerce' ) || defined('JIGOSHOP_VERSION') ) {
			add_settings_field(
				'builtin_ajax',
				__( 'Use Built-in AJAX', 'wpmenucart' ),
				array( &$this, 'checkbox_element_callback' ),
				$option,
				'plugin_settings',
				array(
					'menu'			=> $option,
					'id'			=> 'builtin_ajax',
					'description'	=> __( 'Enable this option to use the built-in AJAX / live update functions instead of the default ones from WooCommerce or Jigoshop', 'wpmenucart' ),
				)
			);
		}


		
		// Register settings.
		register_setting( $option, $option, array( &$this, 'wpmenucart_options_validate' ) );

		// Register defaults if settings empty (might not work in case there's only checkboxes and they're all disabled)
		$option_values = get_option($option);
		if ( empty( $option_values ) )
			$this->default_settings();

		// Convert old wpmenucart menu settings to array
		if ( isset($option_values['menu_name_1']) ) {
			$menu_slugs = $this->convert_menu_settings('wpmenucart');
			$option_values['menu_slugs'] = $menu_slugs;

			// ... and turn on WPML String Translation by default while we're at it
			// (only when String Translation is active of course)!
			if ( function_exists( 'icl_t' ) ) {
				$option_values['wpml_string_translation'] = 1;
			}

			update_option( 'wpmenucart', $option_values );
		}
	}

	/**
	 * Add menu page
	 */
	public function wpmenucart_add_page() {
		if (class_exists('WPOvernight_Core')) {
			$wpmenucart_page = add_submenu_page(
				'wpo-core-menu',
				__( 'Menu Cart Pro', 'wpmenucart' ),
				__( 'Menu Cart Pro Setup', 'wpmenucart' ),
				'manage_options',
				'wpmenucart_options_page',
				array( $this, 'wpmenucart_options_do_page' )
			);
		}
		else {
			$wpmenucart_page = add_submenu_page(
				'options-general.php',
				__( 'Menu Cart Pro', 'wpmenucart' ),
				__( 'Menu Cart Pro Setup', 'wpmenucart' ),
				'manage_options',
				'wpmenucart_options_page',
				array( $this, 'wpmenucart_options_do_page' )
			);
		}
		add_action( 'admin_print_styles-' . $wpmenucart_page, array( &$this, 'wpmenucart_admin_styles' ) );
	}

	/**
	 * Add settings link to plugins page
	 */
	public function wpmenucart_add_settings_link( $links ) {
		if (class_exists('WPOvernight_Core')) {
			$settings_link = '<a href="admin.php?page=wpmenucart_options_page">'. __( 'Settings', 'woocommerce' ) . '</a>';
		} else {
			$settings_link = '<a href="options-general.php?page=wpmenucart_options_page">'. __( 'Settings', 'woocommerce' ) . '</a>';	
		}
		array_push( $links, $settings_link );
		return $links;
	}
	
	/**
	 * Styles for settings page
	 */
	public function wpmenucart_admin_styles() {
		wp_register_style( 'wpmenucart-admin', plugins_url( 'css/wpmenucart-icons-pro.css', dirname(__FILE__) ), array(), '', 'all' );
		wp_enqueue_style( 'wpmenucart-admin' );
	}

	public function convert_menu_settings( $option_name ) {
		// get old settings
		$old_options = get_option( $option_name );

		// die (print_r($old_options));

		// convert old menu settings to array
		$menu_slugs =  array (
			'1' => isset($old_options['menu_name_1']) ? $old_options['menu_name_1']:'0',
			'2' => isset($old_options['menu_name_2']) ? $old_options['menu_name_2']:'0',
			'3' => isset($old_options['menu_name_3']) ? $old_options['menu_name_3']:'0',
		);

		return $menu_slugs;
	}
	 
	/**
	 * Default settings.
	 */
	public function default_settings() {
		$wcmenucart_options = get_option('wcmenucart');
		$menu_slugs = $this->convert_menu_settings('wcmenucart');

		$active_shop_plugins = WpMenuCartPro::get_active_shops();
		
		//switch keys & values, then strip plugin path to folder
		foreach ($active_shop_plugins as $key => $value) {
			$filtered_active_shop_plugins[] = dirname($value);
		}

		$first_active_shop_plugin = $filtered_active_shop_plugins[0];	
		$default = array(
			'menu_slugs'		=> $menu_slugs,
			'always_display'	=> isset($wcmenucart_options['always_display']) ? $wcmenucart_options['always_display']:'',
			'icon_display'		=> isset($wcmenucart_options['icon_display']) ? $wcmenucart_options['icon_display']:'1',
			'items_display'		=> isset($wcmenucart_options['items_display']) ? $wcmenucart_options['items_display']:'3',
			'items_alignment'	=> isset($wcmenucart_options['items_alignment']) ? $wcmenucart_options['items_alignment']:'standard',
			'custom_class'		=> '',
			'flyout_display'	=> '',
			'flyout_itemnumber'	=> '5',
			'cart_icon'			=> '0',
			'shop_plugin'		=> $first_active_shop_plugin,
		);

		update_option( 'wpmenucart', $default );
	}

	/**
	 * Build the options page.
	 */
	public function wpmenucart_options_do_page() {		
		?>
	
		<div class="wrap">
			<div class="icon32" id="icon-options-general"><br /></div>
			<h2><?php _e('WP Menu Cart Pro','wpmenucart') ?></h2>
				<?php 
				// print_r(get_option('wpmenucart')); //for debugging
				//print_r($this->get_shop_plugins());
				//print_r(apply_filters( 'active_plugins', get_option( 'active_plugins' )));
				if (!$this->get_menu_array()) {
				?>
				<div class="error" style="width:400px; padding:10px;">
					You need to create a menu before you can use Menu Cart. Go to <strong>Appearence > Menus</strong> and create menu to add the cart to.
				</div>
				<?php } ?>
				<form method="post" action="options.php">
				<?php
									
					settings_fields( 'wpmenucart' );
					do_settings_sections( 'wpmenucart' );

					submit_button();
				?>

			</form>
			<div style="margin-bottom: 30px">
				<h3>ShortCode Implementation</h3>
				<p style="font-size:14px">The [wpmenucart] shortcode is a pro feature that allows you to easily add your menu cart to any page or post. <a href="https://wpovernight.com/faqs/menu-cart-pro-shortcode/">Click here</a> for more details.</p>
			</div>
			<div style="line-height: 20px; background: #F3F3F3;-moz-border-radius: 3px;border-radius: 3px;padding: 10px;-moz-box-shadow: 0 0 5px #ff0000;-webkit-box-shadow: 0 0 5px#ff0000;box-shadow: 0 0 5px #ff0000;padding: 10px;margin:0px auto; font-size: 13.8px;text-align: center;border-color: "> 
			 <?php
			$menucartproad = '<a href="https://wpovernight.com/shop/menu-cart-custom-css/?utm_source=wordpress&utm_medium=menucartpro&utm_campaign=menucartproad">';
			printf (__('For our custom CSS service %sClick here%s. Be sure to use the coupon code <strong>proservice</strong> at checkout. For support, visit %s or email %s','wpmenucart'), $menucartproad,'</a>', '<a href="https://wpovernight.com/support">wpovernight.com</a>','<a href="mailto:support@wpovernight.com">support@wpovernight.com</a>'); ?>			</div>
		</div>

		<script type="text/javascript">
		jQuery('#add_wpmenucart_menu').click(function( event ) {
			event.preventDefault();
			// jQuery('"<p>Test</p>"').insertBefore('#add_wpmenucart_menu');
			var last_select = jQuery(this).closest('td').find('select:last');
			var last_select_id_no = Number(jQuery(last_select).attr('id').replace(/\D/g,''));
			var new_id = 'wpmenucart[menu_slugs]['+String(last_select_id_no+1)+']';
			var clone = jQuery(last_select).clone().attr('id', new_id).attr('name', new_id).val([]).insertBefore('#add_wpmenucart_menu');
			jQuery('<br />').insertBefore('#add_wpmenucart_menu');
		});
		</script>
		<?php
	}

	/**
	 * Get menu array.
	 * 
	 * @return array menu slug => menu name
	 */
	public function get_menu_array() {
		$menus = get_terms( 'nav_menu', array( 'hide_empty' => false ) );
		$menu_list = array();
	
		foreach ( $menus as $menu ) {
			$menu_list[$menu->slug] = $menu->name;
		}
		
		if (!empty($menu_list)) return $menu_list;
	}
	
	/**
	 * Get array of active shop plugins
	 * 
	 * @return array plugin slug => plugin name
	 */
	public function get_shop_plugins() {
		$active_shop_plugins = WpMenuCartPro::get_active_shops();
		
		//switch keys & values, then strip plugin path to folder
		foreach ($active_shop_plugins as $key => $value) {
			$filtered_active_shop_plugins[dirname($value)] = $key;
		}

		$active_shop_plugins = isset($filtered_active_shop_plugins) ? $filtered_active_shop_plugins:'';
				
		return $active_shop_plugins;
	}

	/**
	 * Text field callback.
	 *
	 * @param  array $args Field arguments.
	 *
	 * @return string	  Text field.
	 */
	public function text_element_callback( $args ) {
		$menu = $args['menu'];
		$id = $args['id'];
		$size = isset( $args['size'] ) ? $args['size'] : '25';
	
		$options = get_option( $menu );
	
		if ( isset( $options[$id] ) ) {
			$current = $options[$id];
		} else {
			$current = isset( $args['default'] ) ? $args['default'] : '';
		}

		$disabled = (isset( $args['disabled'] )) ? ' disabled' : '';
		$html = sprintf( '<input type="text" id="%1$s" name="%2$s[%1$s]" value="%3$s" size="%4$s"%5$s/>', $id, $menu, $current, $size, $disabled );
	
		// Displays option description.
		if ( isset( $args['description'] ) ) {
			$html .= sprintf( '<p class="description">%s</p>', $args['description'] );
		}
	
		echo $html;
	}
	
	/**
	 * Displays a selectbox for a settings field
	 *
	 * @param array   $args settings field args
	 */
	public function select_element_callback( $args ) {
		$menu = $args['menu'];
		$id = $args['id'];
		
		$options = get_option( $menu );
		
		if ( isset( $options[$id] ) ) {
			$current = $options[$id];
		} else {
			$current = isset( $args['default'] ) ? $args['default'] : '';
		}

		$disabled = (isset( $args['disabled'] )) ? ' disabled' : '';
		
		$html = sprintf( '<select name="%1$s[%2$s]" id="%1$s[%2$s]"%3$s>', $menu, $id, $disabled );
		$html .= sprintf( '<option value="%s"%s>%s</option>', '0', selected( $current, '0', false ), '' );
		
		foreach ( $args['options'] as $key => $label ) {
			$html .= sprintf( '<option value="%s"%s>%s</option>', $key, selected( $current, $key, false ), $label );
		}
		$html .= sprintf( '</select>' );

		if ( isset( $args['description'] ) ) {
			$html .= sprintf( '<p class="description">%s</p>', $args['description'] );
		}
		
		echo $html;
	}

	/**
	 * Displays a multiple selectbox for a settings field
	 *
	 * @param array   $args settings field args
	 */
	public function menus_select_element_callback( $args ) {
		$menu = $args['menu'];
		$id = $args['id'];

		$html = '';

		$options = get_option( $menu );
		$menu_count = isset($options['menu_slugs'])?count($options['menu_slugs']):0;
		$menu_count = apply_filters( 'wpmenucart_menu_count', $menu_count );

		if ($menu_count < 3) {
			$menu_count = 3;
		}

		for ( $x = 1; $x <= $menu_count; $x++ ) {
			if ( isset( $options[$id][$x] ) ) {
				$current = $options[$id][$x];
			} else {
				$current = isset( $args['default'] ) ? $args['default'] : '';
			}
			
			$disabled = (isset( $args['disabled'] )) ? ' disabled' : '';
			
			$html .= sprintf( '<select name="%1$s[%2$s][%3$s]" id="%1$s[%2$s][%3$s]"%4$s>', $menu, $id, $x, $disabled);
			$html .= sprintf( '<option value="%s"%s>%s</option>', '0', selected( $current, '0', false ), '' );
			
			foreach ( (array) $args['options'] as $key => $label ) {
				$html .= sprintf( '<option value="%s"%s>%s</option>', $key, selected( $current, $key, false ), $label );
			}
			$html .= '</select>';
	
			if ( isset( $args['description'] ) ) {
				$html .= sprintf( '<p class="description">%s</p>', $args['description'] );
			}
			$html .= '<br />';
		}

		$html .= '<button class="button" id="add_wpmenucart_menu" style="margin-top:5px">'.__('Add menu', 'wpmenucart').'</button>';
		
		echo $html;
	}

	/**
	 * Checkbox field callback.
	 *
	 * @param  array $args Field arguments.
	 *
	 * @return string	  Checkbox field.
	 */
	public function checkbox_element_callback( $args ) {
		$menu = $args['menu'];
		$id = $args['id'];
	
		$options = get_option( $menu );
	
		if ( isset( $options[$id] ) ) {
			$current = $options[$id];
		} else {
			$current = isset( $args['default'] ) ? $args['default'] : '';
		}
	
		$disabled = (isset( $args['disabled'] )) ? ' disabled' : '';
		$html = sprintf( '<input type="checkbox" id="%1$s" name="%2$s[%1$s]" value="1"%3$s %4$s/>', $id, $menu, checked( 1, $current, false ), $disabled );
	
		// Displays option description.
		if ( isset( $args['description'] ) ) {
			$html .= sprintf( '<p class="description">%s</p>', $args['description'] );
		}
	
		echo $html;
	}

	/**
	 * Displays a multicheckbox a settings field
	 *
	 * @param array   $args settings field args
	 */
	public function radio_element_callback( $args ) {
		$menu = $args['menu'];
		$id = $args['id'];
	
		$options = get_option( $menu );
	
		if ( isset( $options[$id] ) ) {
			$current = $options[$id];
		} else {
			$current = isset( $args['default'] ) ? $args['default'] : '';
		}

		$html = '';
		foreach ( $args['options'] as $key => $label ) {
			$html .= sprintf( '<input type="radio" class="radio" id="%1$s[%2$s][%3$s]" name="%1$s[%2$s]" value="%3$s"%4$s />', $menu, $id, $key, checked( $current, $key, false ) );
			$html .= sprintf( '<label for="%1$s[%2$s][%3$s]"> %4$s</label><br>', $menu, $id, $key, $label);
		}
		
		// Displays option description.
		if ( isset( $args['description'] ) ) {
			$html .= sprintf( '<p class="description">%s</p>', $args['description'] );
		}

		echo $html;
	}

	/**
	 * Displays a multicheckbox a settings field
	 *
	 * @param array   $args settings field args
	 */
	public function icons_radio_element_callback( $args ) {
		$menu = $args['menu'];
		$id = $args['id'];
	
		$options = get_option( $menu );
	
		if ( isset( $options[$id] ) ) {
			$current = $options[$id];
		} else {
			$current = isset( $args['default'] ) ? $args['default'] : '';
		}

		$icons = '';
		$radios = '';
		
		foreach ( $args['options'] as $key => $iconnumber ) {
			$icons .= sprintf( '<td style="padding-bottom:0;font-size:16pt;" align="center"><label for="%1$s[%2$s][%3$s]"><i class="wpmenucart-icon-shopping-cart-%4$s"></i></label></td>', $menu, $id, $key, $iconnumber);
			$radios .= sprintf( '<td style="padding-top:0" align="center"><input type="radio" class="radio" id="%1$s[%2$s][%3$s]" name="%1$s[%2$s]" value="%3$s"%4$s /></td>', $menu, $id, $key, checked( $current, $key, false ) );
		}

		$html = '<table><tr>'.$icons.'</tr><tr>'.$radios.'</tr></table>';
		$html .= '<p class="description"><i>'. __('<strong>Please note:</strong> you need to open your website in a new tab/browser window after updating the cart icon for the change to be visible!','wpmenucart').'</p>';
		
		echo $html;
	}

	/**
	 * Section null callback.
	 *
	 * @return void.
	 */
	public function section_options_callback() {
	
	}

	/**
	 * Validate/sanitize options input
	 */
	public function wpmenucart_options_validate( $input ) {
		// Create our array for storing the validated options.
		$output = array();

		// Loop through each of the incoming options.
		foreach ( $input as $key => $value ) {

			// Check to see if the current option has a value. If so, process it.
			if ( isset( $input[$key] ) ) {
				// Strip all HTML and PHP tags and properly handle quoted strings.
				if ( is_array( $input[$key] ) && $key == 'menu_slugs' ) {
					$new_subkey = 1; //renumber array
					foreach ( $input[$key] as $sub_key => $sub_value ) {
						if (!empty($sub_value)) {
							$output[$key][$new_subkey] = strip_tags( stripslashes( $input[$key][$sub_key] ) );
							$new_subkey += 1;
						}
					}
				} else {
					$output[$key] = strip_tags( stripslashes( $input[$key] ) );
				}
			}
		}

		// Return the array processing any additional functions filtered by this action.
		return apply_filters( 'wpmenucart_validate_input', $output, $input );
	}

	public function wpmenucart_add_meta_box() {
		add_meta_box(
			'wpmenucart-meta-box',
			__('Menu Cart'),
			array( &$this, 'wpmenucart_menu_item_meta_box' ),
			'nav-menus',
			'side',
			'default'
			);
	}
	
	public function wpmenucart_menu_item_meta_box() {
		global $_nav_menu_placeholder, $nav_menu_selected_id;
		$_nav_menu_placeholder = 0 > $_nav_menu_placeholder ? $_nav_menu_placeholder - 1 : -1;

		?>
		<p>
			<input value="custom" name="menu-item[<?php echo $_nav_menu_placeholder; ?>][menu-item-type]" type="text" />
			<input id="custom-menu-item-url" name="menu-item[<?php echo $_nav_menu_placeholder; ?>][menu-item-url]" type="text" value="" />
			<input id="custom-menu-item-name" name="menu-item[<?php echo $_nav_menu_placeholder; ?>][menu-item-title]" type="text" title="<?php esc_attr_e('Menu Item'); ?>" />
		</p>

		<p class="wpmenucart-meta-box" id="wpmenucart-meta-box">
			<span class="add-to-menu">
				<input type="submit"<?php disabled( $nav_menu_selected_id, 0 ); ?> class="button-secondary submit-add-to-menu right" value="<?php esc_attr_e('Add to Menu'); ?>" name="menucart-menu-item" id="menucart-menu-item" />
				<span class="spinner"></span>
			</span>
		</p>
		<?php
	}


}