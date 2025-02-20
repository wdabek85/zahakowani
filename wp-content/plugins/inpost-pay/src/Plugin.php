<?php


declare ( strict_types=1 );

namespace Ilabs\Inpost_Pay;

use Exception;
use Isolated\Inpost_Pay\Ilabs\Ilabs_Plugin\Abstract_Ilabs_Plugin;
use Isolated\Inpost_Pay\Ilabs\Ilabs_Plugin\Woocommerce_Logger;
use Isolated\Inpost_Pay\Isolated_Guzzlehttp\GuzzleHttp\Client;
use Ilabs\Inpost_Pay\Lib\omnibus\Plugin as Omnibus_Plugin;

class Plugin extends Abstract_Ilabs_Plugin {

	const SHORT_SLUG = 'inpost_pay';

	private ?Omnibus_Plugin $omnibus = null;

	/**
	 * @throws Exception
	 */
	protected function before_init() {
		if ( $this->omnibus_enabled() ) {
			$this->get_omnibus()->before_init();
		}

		add_action( 'plugins_loaded',
			[ '\Ilabs\Inpost_Pay\InpostPay', 'getInstance' ] );
		register_activation_hook( __FILE__,
			[ '\Ilabs\Inpost_Pay\InpostPay', 'activate' ] );
		register_deactivation_hook(
			__FILE__,
			[ '\Ilabs\Inpost_Pay\InpostPay', 'deactivate' ]
		);

		$migration = new Migration();
		$migration->run();
	}

	protected function plugins_loaded_hooks() {
		if ( $this->omnibus_enabled() ) {
			$this->get_omnibus()->plugins_loaded_hooks();
		}
	}

	/**
	 * @throws Exception
	 */
	public function enqueue_frontend_scripts() {
		if ( $this->omnibus_enabled() ) {
			$this->get_omnibus()->enqueue_frontend_scripts();
		}
	}

	public function enqueue_dashboard_scripts() {
		if ( $this->omnibus_enabled() ) {
			$this->get_omnibus()->enqueue_frontend_scripts();
		}

		wp_enqueue_style( 'inpostpay',
			plugins_url( '../assets/css/admin-style.css', __FILE__ ),
			[],
			$this->get_plugin_version()
		);
		wp_enqueue_style( 'inpostpay-select2',
			plugins_url( '../assets/css/select2.min.css', __FILE__ ),
			[],
			$this->get_plugin_version()
		);
		wp_register_script( 'inpostpay-admin-script',
			plugins_url( '../assets/js/admin-script.js', __FILE__ ),
			[],
			$this->get_plugin_version()
		);
		wp_enqueue_script( 'inpostpay-admin-script' );
	}

	public function init() {
		if ( $this->omnibus_enabled() ) {
			$this->omnibus->init();
		}

		add_action( 'wp', function () {
			if ( isset( $_GET['izitest'] ) ) {
				$result = ( new WooDeliveryPrice() )->mapDelivery();
				echo '<pre>';
				var_dump( $result );
				die;
				/*$result = $this->shipping_cost_settings()->findGroup(
					'courier',
					['pww']
				);
				echo '<pre>';
				var_dump($result->getApiDeliveryOptionsMap());die;*/
			}
		} );


	}

	public function get_plugin_version(): string {
		$plugin_data = get_plugin_data( plugin_dir_path( __FILE__ ) . '../inpost-pay.php' );

		return $plugin_data['Version'];
	}

	public function get_woocommerce_logger( ?string $log_id = null
	): Woocommerce_Logger {

		if ( $this instanceof Omnibus_Plugin ) {
			$log_id = $log_id
				? $this->prefix_by_short_slug( 'Omnibus_' . $log_id )
				: $this->get_from_config( 'slug' );
		} else {
			$log_id = $log_id
				? $this->prefix_by_short_slug( $log_id )
				: $this->get_from_config( 'slug' );
		}


		$logger = new Woocommerce_Logger( $log_id );

		if ( ! get_option( 'izi_debug' ) ) {
			$logger->set_null_logger( true );
		}

		return $logger;
	}

	public function prefix_by_short_slug( string $string ): string {
		return self::SHORT_SLUG . '_' . $string;
	}

	public function get_guzzle_client_instance(): Client {
		return new Client();
	}

	public function get_omnibus(): Omnibus_Plugin {
		if ( ! $this->omnibus ) {
			$this->omnibus = new Omnibus_Plugin();
		}

		return $this->omnibus;
	}

	public function omnibus_enabled(): bool {
		return false;
	}
}
