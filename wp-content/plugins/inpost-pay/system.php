<?php

class __Inpost_Pay_System {

	const TEST_PLUGIN_ACTIVE = 1;

	const TEST_PLUGIN_INSTALLED = 2;

	const TEST_PLUGIN_NOT_INSTALLED = 0;



	/**
	 * @var boolean
	 */
	private $result = true;

	/**
	 * @var array
	 */
	private $plugin_config;

	/**
	 * @var string
	 */
	private $basename;

	/**
	 * @param array $plugin_config
	 */
	public function __construct( array $plugin_config ) {
		$this->plugin_config = $plugin_config;
	}

	public function evaluate_system() {
		$basename       = basename( __DIR__ );
		$this->basename = $basename;

		$this->test_php();
		$this->test_required_plugins();
		$this->test_php_extensions_installed();

		return $this->result;
	}

	private function test_php() {
		if ( PHP_VERSION_ID < $this->get_plugin_min_php_int() ) {
			$this->system_test_failed();
			$this->print_error( sprintf( __( "PHP version is older than %s so this plugin will not work. Please contact your host and ask them to upgrade.",
				'inpost-pay' ),
				$this->get_plugin_min_php() ) );
		}
	}

	private function test_required_plugins() {
		if ($this->get_required_plugins()){
			include_once(ABSPATH.'wp-admin/includes/plugin.php');

			foreach ( $this->get_required_plugins() as $slug ) {
				switch ( $this->test_plugin( $slug ) ) {
					case self::TEST_PLUGIN_INSTALLED:
						$this->system_test_failed();
						$this->print_error( sprintf( __( "The required plugin: %s is installed but not activated.",
							'inpost-pay' ),
							$slug ) );
						break;

					case self::TEST_PLUGIN_NOT_INSTALLED:
						$this->system_test_failed();
						$this->print_error( sprintf( __( "The required plugin: %s is not installed.",
							'inpost-pay' ),
							$slug ) );
				}
			}
		}

	}

	private function test_php_extensions_installed() {
		if ($this->get_required_php_extensions()){
			$not_installed_extensions = [];
			foreach ( $this->get_required_php_extensions() as $extension ) {
				if ( ! extension_loaded( $extension ) ) {
					$not_installed_extensions[] = $extension;
				}
			}
			if ( ! empty( $not_installed_extensions ) ) {
				$this->system_test_failed();
				$this->print_error( sprintf( __( "The required PHP extensions: ( %s) are not installed. Please contact your host and ask them to install.",
					'inpost-pay' ),
					implode( ', ', $not_installed_extensions ) ) );
			}
		}
	}

	public function print_error( $message ) {
		add_action( 'admin_notices', function () use ( $message ) {
			printf( "<div class='notice notice-error error'><p><strong style='color: red;'>%s: ",
				$this->get_plugin_name() );
			echo $message;
			echo "</strong></p></div>";
		} );
	}


	private function configure_translations() {
		$basename = $this->basename;
		add_action( 'plugins_loaded', function () use ( $basename ) {
			load_plugin_textdomain( $this->get_plugin_text_domain(), false, $basename . "/lang" );
		} );
	}

	private function get_plugin__FILE__() {
		return $this->plugin_config['__FILE__'];
	}

	private function get_plugin_slug() {
		return $this->plugin_config['slug'];
	}

	private function get_plugin_lang_dir() {
		return $this->plugin_config['lang_dir'];
	}

	private function get_plugin_text_domain() {
		return $this->plugin_config['text_domain'];
	}

	private function get_plugin_min_php_int() {
		return $this->plugin_config['min_php_int'];
	}

	private function get_plugin_min_php() {
		return $this->plugin_config['min_php'];
	}

	private function get_plugin_name() {
		return $this->plugin_config['name'];
	}

	/**
	 * @return null|array
	 */
	private function get_required_plugins() {
		if ( ! isset( $this->plugin_config['required_plugins'] ) ) {
			return null;
		}

		return $this->plugin_config['required_plugins'];
	}

	/**
	 * @return null|array
	 */
	private function get_required_php_extensions() {
		if ( ! isset( $this->plugin_config['required_php_extensions'] ) ) {
			return null;
		}

		return $this->plugin_config['required_php_extensions'];
	}

	private function test_plugin( $slug ) {
		if ( is_plugin_active( $slug . '/' . $slug . '.php' ) ) {
			return self::TEST_PLUGIN_ACTIVE;
		} elseif ( is_plugin_inactive( $slug . '/' . $slug . '.php' ) ) {
			return self::TEST_PLUGIN_INSTALLED;
		} else {
			return self::TEST_PLUGIN_NOT_INSTALLED;
		}
	}


	public function system_test_failed() {

		if ( $this->result ) {
			$this->configure_translations();
		}

		$this->result = false;
	}


}
