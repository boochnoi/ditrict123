<?php
if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly
}

if ( class_exists( 'WP_Importer' ) ) {
	class WF_Rate_Matrix_Importer extends WP_Importer {

		var $id;
		var $file_url;
		var $import_page;
		var $delimiter;
		var $posts = array();
		var $imported;
		var $skipped;

		/**
		 * __construct function.
		 */
		public function __construct() {
			$this->import_page = 'shippingpro_rate_matrix_csv';
		}

		/**
		 * Registered callback function for the WordPress Importer
		 *
		 * Manages the three separate stages of the CSV import process
		 */
		public function dispatch() {

			$this->header();

			if ( ! empty( $_POST['delimiter'] ) )
				$this->delimiter = stripslashes( trim( $_POST['delimiter'] ) );

			if ( ! $this->delimiter )
				$this->delimiter = ',';

			$step = empty( $_GET['step'] ) ? 0 : (int) $_GET['step'];

			switch ( $step ) {

				case 0:
					$this->greet();
					break;

				case 1:
					check_admin_referer( 'import-upload' );
					if ( $this->handle_upload() ) {

						if ( $this->id )
							$file = get_attached_file( $this->id );
						else
							$file = ABSPATH . $this->file_url;

						add_filter( 'http_request_timeout', array( $this, 'bump_request_timeout' ) );

						if ( function_exists( 'gc_enable' ) )
							gc_enable();

						@set_time_limit(0);
						@ob_flush();
						@flush();

						$this->import( $file );
					}
					break;
			}

			$this->footer();
		}

		/**
		 * format_data_from_csv function.
		 *
		 * @param mixed $data
		 * @param string $enc
		 * @return string
		 */
		public function format_data_from_csv( $data, $enc ) {
			return ( $enc == 'UTF-8' ) ? $data : utf8_encode( $data );
		}

		/**
		 * import function.
		 *
		 * @param mixed $file
		 */
		function import( $file ) {
			global $wpdb;

			$this->imported = $this->skipped = 0;

			if ( ! is_file($file) ) {
				echo '<p><strong>' . __( 'Sorry, there has been an error.', 'wf_woocommerce_shipping_pro' ) . '</strong><br />';
				echo __( 'The file does not exist, please try again.', 'wf_woocommerce_shipping_pro' ) . '</p>';
				$this->footer();
				die();
			}

			$new_rates = array();

			ini_set( 'auto_detect_line_endings', '1' );

			if ( ( $handle = fopen( $file, "r" ) ) !== FALSE ) {

				$header = fgetcsv( $handle, 0, $this->delimiter );
				if ( sizeof( $header ) == 18 || sizeof( $header ) == 17 ) { //Old version compatibility: with or without zone.
					
					$rate_matrix = array();					
					
					while ( ( $row = fgetcsv( $handle, 0, $this->delimiter ) ) !== FALSE ) {
						if(sizeof( $header ) == 18){
							list($shipping_name, $method_group, $zone_list, $country_list, $state_list, $postal_code, $shipping_class, $product_category, $min_weight, $max_weight, $min_item, $max_item, $min_price, $max_price, $cost_based_on, $fee, $cost, $weigh_rounding) = $row;
						}else{
							list($shipping_name, $method_group, $country_list, $state_list, $postal_code, $shipping_class, $product_category, $min_weight, $max_weight, $min_item, $max_item, $min_price, $max_price, $cost_based_on, $fee, $cost, $weigh_rounding) = $row;
						}
						$matrix_row = array();
						$matrix_row['shipping_name'] = $shipping_name;
						$matrix_row['method_group'] = $method_group;
						$matrix_row['zone_list'] = 	!empty($zone_list) ? explode(";", $zone_list) : array();
						$matrix_row['country_list'] = !empty($country_list) ? explode(";", $country_list) : array();
						$matrix_row['state_list'] = !empty($state_list) ? explode(";", $state_list) : array();
						$matrix_row['postal_code'] = $postal_code;
						$matrix_row['shipping_class'] = !empty($shipping_class) ? explode(";", $shipping_class) : array();
						$matrix_row['product_category'] = !empty($product_category) ? explode(";", $product_category) : array();
						$matrix_row['min_weight'] = $min_weight;
						$matrix_row['max_weight'] = $max_weight;
						$matrix_row['min_item'] = $min_item;  
						$matrix_row['max_item'] = $max_item;
						$matrix_row['min_price'] = $min_price;
						$matrix_row['max_price'] = $max_price;
						$matrix_row['cost_based_on'] = $cost_based_on;
						$matrix_row['fee'] = $fee;
						$matrix_row['cost'] = $cost;
						$matrix_row['weigh_rounding'] = $weigh_rounding;	

						$rate_matrix[] = $matrix_row;						
						$this->imported++;
					}
					
					$shipping_pro_settings = get_option( 'woocommerce_wf_woocommerce_shipping_pro_settings' );
					if(!empty($rate_matrix) && is_array($rate_matrix) && !empty($shipping_pro_settings) && is_array($shipping_pro_settings)){
						$shipping_pro_settings['rate_matrix'] = $rate_matrix;
						update_option('woocommerce_wf_woocommerce_shipping_pro_settings', $shipping_pro_settings);						
					}
					
				} else {

					echo '<p><strong>' . __( 'Sorry, there has been an error.', 'wf_woocommerce_shipping_pro' ) . '</strong><br />';
					echo __( 'The CSV is invalid.', 'wf_woocommerce_shipping_pro' ) . '</p>';
					$this->footer();
					die();

				}

				fclose( $handle );
			}

			// Show Result
			echo '<div class="updated settings-error below-h2"><p>
				'.sprintf( __( 'Import complete - imported <strong>%s</strong> Rate Matrix Rules and skipped <strong>%s</strong>.', 'wf_woocommerce_shipping_pro' ), $this->imported, $this->skipped ).'
			</p></div>';

			$this->import_end();
		}

		/**
		 * Performs post-import cleanup of files and the cache
		 */
		public function import_end() {
			echo '<p>' . __( 'All done!', 'wf_woocommerce_shipping_pro' ) . ' <a href="' . admin_url('admin.php?page=wc-settings&tab=shipping&section=wf_woocommerce_shipping_pro_method') . '">' . __( 'View Rate Matrix', 'wf_woocommerce_shipping_pro' ) . '</a>' . '</p>';

			do_action( 'import_end' );
		}

		/**
		 * Handles the CSV upload and initial parsing of the file to prepare for
		 * displaying author import options
		 *
		 * @return bool False if error uploading or invalid file, true otherwise
		 */
		public function handle_upload() {

			if ( empty( $_POST['file_url'] ) ) {

				$file = wp_import_handle_upload();

				if ( isset( $file['error'] ) ) {
					echo '<p><strong>' . __( 'Sorry, there has been an error.', 'wf_woocommerce_shipping_pro' ) . '</strong><br />';
					echo esc_html( $file['error'] ) . '</p>';
					return false;
				}

				$this->id = (int) $file['id'];

			} else {

				if ( file_exists( ABSPATH . $_POST['file_url'] ) ) {

					$this->file_url = esc_attr( $_POST['file_url'] );

				} else {

					echo '<p><strong>' . __( 'Sorry, there has been an error.', 'wf_woocommerce_shipping_pro' ) . '</strong></p>';
					return false;

				}

			}

			return true;
		}

		/**
		 * header function.
		 */
		public function header() {
			echo '<div class="wrap"><div class="icon32 icon32-woocommerce-importer" id="icon-woocommerce"><br></div>';
			echo '<h2>' . __( 'Import Shipping Pro Rate Matrix', 'wf_woocommerce_shipping_pro' ) . '</h2>';
		}

		/**
		 * footer function.
		 */
		public function footer() {
			echo '</div>';
		}

		/**
		 * greet function.
		 */
		public function greet() {

			echo '<div class="narrow">';
			echo '<p>' . __( 'Hi there! Upload a CSV file containing Shipping Pro Rate Matrix to import the contents into your shop. Choose a .csv file to upload, then click "Upload file and import".', 'wf_woocommerce_shipping_pro' ).'</p>';

			echo '<p>' . sprintf( __( 'Shipping Pro Rate Matrix need to be defined with columns in a specific order (18 columns). <a href="%s">Click here to download a sample</a>.', 'wf_woocommerce_shipping_pro' ), wf_plugin_url() . '/sample-data/sample_shipping_pro_rate_matrix.csv' ) . '</p>';

			$action = 'admin.php?import=shippingpro_rate_matrix_csv&step=1';

			$bytes = apply_filters( 'import_upload_size_limit', wp_max_upload_size() );
			$size = size_format( $bytes );
			$upload_dir = wp_upload_dir();
			if ( ! empty( $upload_dir['error'] ) ) :
				?><div class="error"><p><?php _e( 'Before you can upload your import file, you will need to fix the following error:', 'wf_woocommerce_shipping_pro' ); ?></p>
				<p><strong><?php echo $upload_dir['error']; ?></strong></p></div><?php
			else :
				?>
				<form enctype="multipart/form-data" id="import-upload-form" method="post" action="<?php echo esc_attr(wp_nonce_url($action, 'import-upload')); ?>">
					<table class="form-table">
						<tbody>
							<tr>
								<th>
									<label for="upload"><?php _e( 'Choose a file from your computer:', 'wf_woocommerce_shipping_pro' ); ?></label>
								</th>
								<td>
									<input type="file" id="upload" name="import" size="25" />
									<input type="hidden" name="action" value="save" />
									<input type="hidden" name="max_file_size" value="<?php echo $bytes; ?>" />
									<small><?php printf( __('Maximum size: %s', 'wf_woocommerce_shipping_pro' ), $size ); ?></small>
								</td>
							</tr>
							<tr>
								<th>
									<label for="file_url"><?php _e( 'OR enter path to file:', 'wf_woocommerce_shipping_pro' ); ?></label>
								</th>
								<td>
									<?php echo ' ' . ABSPATH . ' '; ?><input type="text" id="file_url" name="file_url" size="25" />
								</td>
							</tr>
							<tr>
								<th><label><?php _e( 'Delimiter', 'wf_woocommerce_shipping_pro' ); ?></label><br/></th>
								<td><input type="text" name="delimiter" placeholder="," size="2" /></td>
							</tr>
						</tbody>
					</table>
					<p class="submit">
						<input type="submit" class="button" value="<?php esc_attr_e( 'Upload file and import', 'wf_woocommerce_shipping_pro' ); ?>" />
					</p>
				</form>
				<?php
			endif;

			echo '</div>';
		}

		/**
		 * Added to http_request_timeout filter to force timeout at 60 seconds during import
		 *
		 * @param  int $val
		 * @return int 60
		 */
		public function bump_request_timeout( $val ) {
			return 60;
		}
	}
}
