<?php

defined( 'ABSPATH' ) or exit;

if ( ! class_exists( 'PW_COST_GOOD_ADMIN_DEPRECATOR' ) ) :


class PW_COST_GOOD_ADMIN_DEPRECATOR {


	/** @var string plugin name */
	protected $plugin_name;

	/** @var array deprecated/removed hooks */
	protected $hooks;


	/**
	 * Setup class
	 *
	 * @param string $plugin_name
	 * @param array $hooks
	 */
	public function __construct( $plugin_name, $hooks ) {

		$this->plugin_name = $plugin_name;
		$this->hooks       = $hooks;

		add_action( 'shutdown', array( $this, 'handle_deprecated_hooks' ), 999 );
	}


	/**
	 * Trigger a notice when other actors have attached callbacks to hooks that
	 * are either deprecated or removed. This only runs when WP_DEBUG is on.
	 *
	 * @since 4.3.0
	 */
	public function handle_deprecated_hooks() {
		global $wp_filter;

		// follow WP core behavior for showing deprecated notices and only do so when WP_DEBUG is on
		if ( defined( 'WP_DEBUG' ) && WP_DEBUG && apply_filters( 'sv_wc_plugin_framework_show_deprecated_hook_notices', true ) ) {

			// sanity check
			if ( ! is_array( $wp_filter ) || empty( $wp_filter ) ) {
				return;
			}

			foreach ( $this->hooks as $old_hook_tag => $hook ) {

				// if other actors have attached a callback to the deprecated/removed hook...
				if ( isset( $wp_filter[ $old_hook_tag ] ) ) {

					$this->trigger_error( $old_hook_tag, $hook );
				}
			}
		}
	}


	/**
	 * Trigger the deprecated/removed notice
	 *
	 * @since 4.3.0
	 * @param string $old_hook_name deprecated/removed hook name
	 * @param array $hook {
	 *   @type string $version version the hook was deprecated/removed in
	 *   @type bool $removed if present and true, the message will indicate the hook was removed instead of deprecated
	 *   @type string|bool $replacement if present and a string, the message will indicate the replacement hook to use,
	 *     otherwise (if bool and false) the message will indicate there is no replacement available.
	 * }
	 */
	protected function trigger_error( $old_hook_name, $hook ) {

		// e.g. WooCommerce Memberships: "wc_memberships_some_hook" was deprecated in version 1.2.3.
		$message = sprintf( '%1$s: action/filter "%2$s" was %3$s in version %4$s. ',
			$this->plugin_name,
			$old_hook_name,
			isset( $hook['removed'] ) && $hook['removed'] ? 'removed' : 'deprecated',
			$hook['version']
		);

		// e.g. Use "wc_memberships_some_new_hook" instead.
		$message .= ( isset( $hook['replacement'] ) && false !== $hook['replacement'] ) ? sprintf( 'Use %1$s instead.', $hook['replacement'] ) : 'There is no replacement available.';

		// triggers as E_USER_NOTICE
		trigger_error( $message );
	}


}


endif; // class exists check
