<?php
/**
 * The admin-specific functionality of the plugin.
 *
 * @link       www.casheer.com
 * @since      1.0.0
 *
 * @package    casheer
 * @subpackage casheer/admin
 */

/**
 * The admin-specific functionality of the plugin.
 *
 * Defines the plugin name, version, and two examples hooks for how to
 * enqueue the admin-specific stylesheet and JavaScript.
 *
 * @package    casheer
 * @subpackage casheer/admin
 * @author     Casheer <info@casheer.com>
 */
class casheer_Admin {

	/**
	 * The ID of this plugin.
	 *
	 * @since    1.0.0
	 * @access   private
	 * @var      string    $plugin_name    The ID of this plugin.
	 */
	private $plugin_name;

	/**
	 * The version of this plugin.
	 *
	 * @since    1.0.0
	 * @access   private
	 * @var      string    $version    The current version of this plugin.
	 */
	private $version;

	/**
	 * Initialize the class and set its properties.
	 *
	 * @since    1.0.0
	 * @param      string $plugin_name       The name of this plugin.
	 * @param      string $version    The version of this plugin.
	 */
	public function __construct( $plugin_name, $version ) {

		$this->plugin_name = $plugin_name;
		$this->version = $version;

	}

	/**
	 * Register the stylesheets for the admin area.
	 *
	 * @since    1.0.0
	 */
	public function enqueue_styles() {

		/**
		 * This function is provided for demonstration purposes only.
		 *
		 * An instance of this class should be passed to the run() function
		 * defined in casheer_Loader as all of the hooks are defined
		 * in that particular class.
		 *
		 * The casheer_Loader will then create the relationship
		 * between the defined hooks and the functions defined in this
		 * class.
		 */

		wp_enqueue_style( $this->plugin_name, plugin_dir_url( __FILE__ ) . 'css/casheer-admin.css', array(), $this->version, 'all' );

	}

	/**
	 * Register the JavaScript for the admin area.
	 *
	 * @since    1.0.0
	 */
	public function enqueue_scripts() {

		/**
		 * This function is provided for demonstration purposes only.
		 *
		 * An instance of this class should be passed to the run() function
		 * defined in casheer_Loader as all of the hooks are defined
		 * in that particular class.
		 *
		 * The casheer_Loader will then create the relationship
		 * between the defined hooks and the functions defined in this
		 * class.
		 */

		wp_enqueue_script( $this->plugin_name, plugin_dir_url( __FILE__ ) . 'js/casheer-admin.js', array( 'jquery' ), $this->version, false );

	}

	/**
	 * Register the Gateway Class.
	 *
	 * @since    1.0.0
	 */
	public function add_gatewayclass() {
		$gateways[] = 'WC_casheer_Gateway'; // your class name is here
		return $gateways;
	}

	
	/**
	 * Register the Gateway Menu.
	 *
	 * @since    1.0.0
	 */
	public function admin_menu() {
		add_menu_page(
			__( 'Casheer Payment Methods', 'Casheer Payment Methods' ),
			'Casheer Checkout',
			'manage_options',
			'casheer_payment_gateway',
			array( $this, 'settings' ),
			'dashicons-admin-generic',
			13
		);
	}

	/**
	 * Register the Gateway Settings Panel.
	 *
	 * @since    1.0.0
	 */
	public function settings() {
		require_once plugin_dir_path( dirname( __FILE__ ) ) . 'admin/partials/casheer-admin-display.php';
	}

	/**
	 * Save Submission of Payment Data.
	 *
	 * @since    1.0.0
	 */
	public function save_casheer_form() {

		// Nonce verification.
		$casheer_setting_nonce = ! empty( $_POST['casheer_nonce'] ) ? sanitize_text_field( wp_unslash( $_POST['casheer_nonce'] ) ) : '';

		if ( empty( $casheer_setting_nonce ) || ! wp_verify_nonce( $casheer_setting_nonce, 'casheer_setting_nonce' ) ) {

			esc_html_e( 'Sorry, due to some security issue, your settings could not be saved. Please Reload the screen.', 'casheer' );
			wp_die();
		}

		$customize_payment = ! empty( $_POST['customize_payment'] ) ?  $this->sanitize_recursive( wp_unslash( $_POST['customize_payment'] ) ) : array();

		$data = json_encode( $customize_payment, true );

		if( ! empty( $data ) ) {

			$all_methods = get_option( 'customize_payment_data' );
			if ( ! empty( $all_methods ) ) {
				update_option( 'customize_payment_data', $data );
			} else {
				add_option( 'customize_payment_data', $data );
			}
		}

		$url = get_site_url() . '/wp-admin/admin.php?page=casheer_payment_gateway';
		wp_redirect( $url );
		exit;
	}

	
	public function casheer_update_daily( $schedules ) {
		$schedules['daily'] = array(
				'interval'  => 86400,
				'display'   => __( 'Once Daily', 'casheer' )
		);
		return $schedules;
	}	
	
    public function casheer_cron_set()
	{
			// Schedule an action if it's not already scheduled
			if ( ! wp_next_scheduled( 'casheer_update_daily' ) ) {
				wp_schedule_event(time(), 'daily', 'casheer_update_daily' );
			}
	}
	public function casheer_update_daily_event_func() {
			$query = new WC_Order_Query( array(
			'limit' => -1,
			'orderby' => 'date',
			'order' => 'DESC',
			));
			$orders = $query->get_orders();
		   foreach ($orders as $order) {
				// Process each order
				$order_id = $order->get_id();
				$order_data = $order->get_data();
				// For example, get the order total
				$order_total = $order->get_total();

				$url = 'https://kwpayapi.casheer.com/V1/api/GenToken/GetStatus';
				$response = wp_remote_post($url, array(
					'method'    => 'POST',
					'body'      => json_encode(array('merchantCode' => 'KW275753','authKey'=>'E0pFSYXoTgKrvnvo','referenceID'=>$order_id)),
					'headers'   => array(
						'Content-Type' => 'application/json',
					),
				));
				
				if (is_wp_error($response)) {
					$error_message = $response->get_error_message();
					error_log("Cron job failed: $error_message");
				} else {
					$response_body = wp_remote_retrieve_body($response);
					error_log("Cron job succeeded: $response_body");
					$response_body = json_decode($response_body,true);
						if ( ! empty($response_body['result'] ) ) {
							switch($response_body['result']['result']) {
								case 'CAPTURED':
									$order->update_status( 'processing' ); // Processing.				
									break;
								case 'NOT CAPTURED':
									$order->update_status( 'cancelled' ); // Missing.
									break;
								case 'DECLINED':
									$order->update_status( 'cancelled' ); // Denied.
									break;
								case 'REJECTED':
									$order->update_status( 'cancelled' ); // Denied.
									break;
								case 'BLOCKED':
									$order->update_status( 'cancelled' ); // Denied.
									break;
							}
						
						} else {
							$order->update_status( 'pending' );
						}
						
						$note = "Track Id: ".$order_id."\r\n Payment Status: ".$response_body['result']['result'];
						$order->add_order_note($note);	
				}
			}
		}

	/**
	 * Sanitization for associative array.
	 *
	 * @since    1.0.0
	 * @param    array $s    array to sanitize
	 */
	function sanitize_recursive($s) {
		if ( is_array( $s ) ) {
		  return(array_map( array( $this, 'sanitize_recursive' ), $s));
		} else {
		  return htmlspecialchars($s);
		}
	  }
}
