<?php declare( strict_types=1 );

/**
 * Plugin Name: WME Backend Starter
 * Description: Starter for backend of WME Framework.
 * Plugin URI: https://github.com/moderntribe/wme-backend-starter/
 * Author: Modern Tribe
 * Author URI: https://tri.be/
 */

class WME_Backend_Starter {

	/**
	 * @var string Namespace for REST endpoints.
	 */
	public const REST_NAMESPACE = 'wme-backend-starter';

	/**
	 * @var string Required capability for admin menu page.
	 */
	public const MENU_CAPABILITY = 'manage_options';

	/**
	 * @var string Slug for admin menu page.
	 */
	public const MENU_SLUG = 'wme-backend-starter';

	/**
	 * Get singleton instance.
	 * 
	 * @return self
	 */
	public static function instance(): self {
		static $instance = null;

		if ( ! is_null( $instance ) ) {
			return $instance;
		}

		$instance = new self;

		return $instance;
	}

	/**
	 * Construct.
	 */
	protected function __construct() {

		$this->register_hooks();

	}

	/**
	 * Register action and filter hooks.
	 * 
	 * @return void
	 */
	protected function register_hooks(): void {

		add_action( 'admin_menu',    array( $this, 'action__admin_menu' ) );
		add_action( 'rest_api_init', array( $this, 'action__rest_api_init' ) );

	}

	/**
	 * Action: admin_menu
	 * 
	 * Add menu page for wizard.
	 * 
	 * @action admin_menu
	 * 
	 * @return void
	 */
	public function action__admin_menu(): void {
		if ( 'admin_menu' !== current_action() ) {
			return;
		}

		$page_title = __( 'WME Backend Starter' );
		$menu_title = __( 'WME Backend Starter' );
		$callback   = array( $this, 'callback__add_menu_page' );

		add_menu_page( $page_title, $menu_title, self::MENU_CAPABILITY, self::MENU_SLUG, $callback );
	}

	/**
	 * Callback: add_menu_page()
	 * 
	 * @link https://github.com/liquidweb/nexcess-mapps/blob/develop/nexcess-mapps/Integrations/StoreBuilder/Setup.php JSON payloads for React app
	 * @link https://github.com/liquidweb/nexcess-mapps/blob/develop/nexcess-mapps/templates/storebuilderapp-setup.php template with div for React app binding
	 * 
	 * @see $this->action__admin_menu()
	 * 
	 * @return void
	 */
	public function callback__add_menu_page(): void {
		if ( sprintf( 'toplevel_page_%s', self::MENU_SLUG ) !== current_action() ) {
			return;
		}

		wp_enqueue_script(    'wme-backend-starter', 'wme-framework.js' );
		wp_add_inline_script( 'wme-backend-starter', sprintf( 'window.WME.data = %s', wp_json_encode( $this->get_json_payload() ) ), 'before' );
		?>

		<div id="wme-framework-react" data-js="wme-framework-react"></div>
		
		<?php
	}

	/**
	 * Get JSON payload for React app config and data.
	 * 
	 * @return array
	 * 
	 * @todo define; likely retrieve from WP option
	 */
	protected function get_json_payload(): array {
		return array();
	}

	/**
	 * Action: rest_api_init
	 * 
	 * Register REST route for wizard processing.
	 * 
	 * @return void
	 */
	public function action__rest_api_init(): void {
		register_rest_route( self::REST_NAMESPACE, '/final', array(
			'methods'  => WP_REST_Server::CREATABLE,
			'callback' => array( $this, 'callback__rest_final_post' ),
		) );
	}

}

WME_Backend_Starter::instance();