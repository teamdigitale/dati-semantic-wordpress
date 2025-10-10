<?php

// Exit if accessed directly
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

class OxyExtrasLicense {

	static $prefix    = '';
	static $title     = '';
	static $store_url = '';
	static $item_id   = null;

	static function init( $prefix, $title, $store_url, $item_id ) {

		self::$prefix    = $prefix;
		self::$title     = $title;
		self::$store_url = $store_url;
		self::$item_id   = $item_id;

		add_action( 'admin_init', array( __CLASS__, 'register_option' ) );
		add_action( 'admin_action_update', array( __CLASS__, 'activate_license' ) );
		add_action( 'admin_action_update', array( __CLASS__, 'deactivate_license' ) );
		add_action( 'admin_notices', array( __CLASS__, 'admin_notices' ) );
	}

	static function is_activated_license() {
		// return true;
		$status = get_option( self::$prefix . 'license_status' );

		if ( $status && $status === 'valid' ) {
			return true;
		}

		return false;
	}

	static function license_page() {
		// $license = get_option( self::$prefix.'license_key' );
		$status = get_option( self::$prefix . 'license_status' );
		?>

		<h2><?php echo self::$title . ' ' . __( 'License' ); ?></h2>

		<div style="background-color: #fff; padding: 15px 20px; border: 1px solid #ccd0d4">
			<p>Follow the steps below to license the plugin:</p>

			<ol>
				<li>Paste your key in the License Key field</li>
				<li>Click "Activate License" button</li>
			</ol>

			<form method="post" action="options.php">

				<?php settings_fields( self::$prefix . 'license' ); ?>

				<table class="form-table">
					<tbody>
						<tr valign="top">
							<th scope="row" valign="top">
								<?php _e( 'License Key' ); ?>
							</th>
							<td>
								<input id="<?php echo self::$prefix; ?>license_key" name="<?php echo self::$prefix; ?>license_key" type="text" class="regular-text" placeholder="<?php echo ( $status !== false && $status === 'valid' ) ? '********************************' : ''; ?>" value="" />
								<label class="description" for="<?php echo self::$prefix; ?>license_key"><?php _e( 'Enter your license key' ); ?></label>
							</td>
						</tr>
						
						
					</tbody>
				</table>
				<?php if ( $status !== false && $status == 'valid' ) { ?>
					
					<?php wp_nonce_field( self::$prefix . 'nonce', self::$prefix . 'nonce' ); ?>
					<input type="submit" class="button-secondary" name="<?php echo self::$prefix; ?>license_deactivate" value="<?php _e( 'Deactivate License' ); ?>"/>
					<span style="color:green;display: inline-flex;align-items: center;justify-content: center;padding: 5px 10px;"><?php _e( 'Active' ); ?></span>
					<?php
				} else {
					wp_nonce_field( self::$prefix . 'nonce', self::$prefix . 'nonce' );
					?>
					<input type="submit" class="button button-primary" name="<?php echo self::$prefix; ?>license_activate" value="<?php _e( 'Activate License' ); ?>"/>
				<?php } ?>
				

			</form>
			<?php

			echo '<hr style="margin: 30px 0 20px 0;" /><p>';
				_e( 'The new additional components can be found in the <strong>+Add</strong> panel inside Oxygen editor under <strong>Extras</strong>.' );
			echo '</p>';

			printf( '<img src="%s" alt="%s" width="300" height="948" />', plugins_url( 'img/oxyextras-in-add-panel.png', __FILE__ ), 'OxyExtras in Add panel' );
			echo '</div>';

	}

	static function register_option() {
		// creates our settings in the options table
		register_setting( self::$prefix . 'license', self::$prefix . 'license_key', array( __CLASS__, 'edd_sanitize_license' ) );
	}

	static function edd_sanitize_license( $new ) {
		$old = get_option( self::$prefix . 'license_key' );
		if ( $old && $old != $new ) {
			delete_option( self::$prefix . 'license_status' ); // new license has been entered, so must reactivate
		}
		return $new;
	}

	static function activate_license() {

		// listen for our activate button to be clicked
		if ( isset( $_POST[ self::$prefix . 'license_activate' ] ) ) {
			ob_start();
			// run a quick security check
			if ( ! check_admin_referer( self::$prefix . 'nonce', self::$prefix . 'nonce' ) ) {
				return; // get out if we didn't click the Activate button
			}

			// retrieve the license from the database
			// $license = trim( get_option( self::$prefix.'license_key' ) );
			$license = $_POST[ self::$prefix . 'license_key' ] ? sanitize_text_field( $_POST[ self::$prefix . 'license_key' ] ) : false;

			update_option( self::$prefix . 'license_key', $license );
			// data to send in our API request
			$api_params = array(
				'edd_action' => 'activate_license',
				'license'    => $license,
				'item_name'  => urlencode( self::$title ), // the name of our product in EDD
				'url'        => home_url(),
			);

			// Call the custom API.
			$response = wp_remote_post(
				self::$store_url,
				array(
					'timeout'   => 15,
					'sslverify' => false,
					'body'      => $api_params,
				)
			);

			// make sure the response came back okay
			if ( is_wp_error( $response ) || 200 !== wp_remote_retrieve_response_code( $response ) ) {

				if ( is_wp_error( $response ) ) {
					$message = $response->get_error_message();
				} else {
					$message = __( 'An error occurred, please try again.' );
				}
			} else {

				$license_data = json_decode( wp_remote_retrieve_body( $response ) );

				if ( false === $license_data->success ) {

					switch ( $license_data->error ) {

						case 'expired':
							$message = sprintf(
								__( 'Your license key expired on %s.' ),
								date_i18n( get_option( 'date_format' ), strtotime( $license_data->expires, current_time( 'timestamp' ) ) )
							);
							break;

						case 'disabled':
						case 'revoked':
							$message = __( 'Your license key has been disabled.' );
							break;

						case 'missing':
							$message = __( 'Invalid license.' );
							break;

						case 'invalid':
						case 'site_inactive':
							$message = __( 'Your license is not active for this URL.' );
							break;

						case 'item_name_mismatch':
							$message = sprintf( __( 'This appears to be an invalid license key for %s.' ), self::$title );
							break;

						case 'no_activations_left':
							$message = __( 'Your license key has reached its activation limit.' );
							break;

						default:
							$message = __( 'An error occurred, please try again.' );
							break;
					}
				}
			}

			// Check if anything passed on a message constituting a failure
			if ( ! empty( $message ) ) {
				$base_url = add_query_arg( 'tab', 'license', menu_page_url( self::$prefix . 'menu' ) );
				$redirect = add_query_arg(
					array(
						'sl_activation' => 'false',
						'message'       => urlencode( $message ),
						'nonce'		=> wp_create_nonce( self::$prefix . 'license_nonce' )
					),
					$base_url
				);

				wp_redirect( $redirect );
				exit();
			}

			// $license_data->license will be either "valid" or "invalid"

			update_option( self::$prefix . 'license_status', $license_data->license );
			wp_redirect( add_query_arg( 'tab', 'license', menu_page_url( self::$prefix . 'menu' ) ) );
			exit();
		}
	}

	static function deactivate_license() {

		// listen for our activate button to be clicked
		if ( isset( $_POST[ self::$prefix . 'license_deactivate' ] ) ) {
			ob_start();
			// run a quick security check
			if ( ! check_admin_referer( self::$prefix . 'nonce', self::$prefix . 'nonce' ) ) {
				return; // get out if we didn't click the Activate button
			}

			// retrieve the license from the database
			$license = trim( get_option( self::$prefix . 'license_key' ) );
			$license = $_POST[ self::$prefix . 'license_key' ] && strlen( $_POST[ self::$prefix . 'license_key' ] ) > 8 ? sanitize_text_field( $_POST[ self::$prefix . 'license_key' ] ) : $license;

			// data to send in our API requestf
			$api_params = array(
				'edd_action' => 'deactivate_license',
				'license'    => $license,
				'item_id'    => self::$item_id,
				'item_name'  => urlencode( self::$title ), // the name of our product in EDD
				'url'        => home_url(),
			);

			// Call the custom API.
			$response = wp_remote_post(
				self::$store_url,
				array(
					'timeout'   => 15,
					'sslverify' => false,
					'body'      => $api_params,
				)
			);

			// make sure the response came back okay
			if ( is_wp_error( $response ) || 200 !== wp_remote_retrieve_response_code( $response ) ) {

				if ( is_wp_error( $response ) ) {
					$message = $response->get_error_message();
				} else {
					$message = __( 'An error occurred, please try again.' );
				}

				$base_url = add_query_arg( 'tab', 'license', menu_page_url( self::$prefix . 'menu' ) );
				$redirect = add_query_arg(
					array(
						'sl_activation' => 'false',
						'message'       => urlencode( $message ),
						'nonce'		=> wp_create_nonce( self::$prefix . 'license_nonce' )
					),
					$base_url
				);

				wp_redirect( $redirect );
				exit();
			}

			// decode the license data
			$license_data = json_decode( wp_remote_retrieve_body( $response ) );

			// $license_data->license will be either "deactivated" or "failed"
			// if($license_data->license == 'deactivated' ) {
			// delete_option( self::$prefix.'license_status' );
			// }

			delete_option( self::$prefix . 'license_status' );
			delete_option( self::$prefix . 'license_key' );

			wp_redirect( add_query_arg( 'tab', 'license', menu_page_url( self::$prefix . 'menu' ) ) );
			exit();

		}
	}

	static function admin_notices() {
		if ( isset( $_GET['sl_activation'] ) && ! empty( $_GET['message'] ) && isset( $_GET['nonce'] ) && wp_verify_nonce( $_GET['nonce'], self::$prefix . 'license_nonce' ) ) {

			switch ( $_GET['sl_activation'] ) {

				case 'false':
					$message = urldecode( $_GET['message'] );
					?>
					<div class="error">
						<p><?php echo esc_html( $message ); ?></p>
					</div>
					<?php
					break;

				case 'true':
				default:
					// Developers can put a custom success message here for when activation is successful if they way.
					break;

			}
		}
	}

	static function check_license() {

		global $wp_version;

		$license = trim( get_option( self::$prefix . 'license_key' ) );

		$api_params = array(
			'edd_action' => 'check_license',
			'license'    => $license,
			'item_name'  => urlencode( self::$title ),
			'url'        => home_url(),
		);

		// Call the custom API.
		$response = wp_remote_post(
			self::$store_url,
			array(
				'timeout'   => 15,
				'sslverify' => false,
				'body'      => $api_params,
			)
		);

		if ( is_wp_error( $response ) ) {
			return false;
		}

		$license_data = json_decode( wp_remote_retrieve_body( $response ) );

		if ( $license_data->license == 'valid' ) {
			echo 'valid';
			exit;
			// this license is still valid
		} else {
			echo 'invalid';
			exit;
			// this license is no longer valid
		}
	}


}

?>