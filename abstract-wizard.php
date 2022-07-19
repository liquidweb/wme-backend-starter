<?php declare( strict_types=1 );

abstract class WME_Sparkplug_Wizard {

	/**
	 * @var string
	 */
	protected $admin_page_slug;

	/**
	 * @var string
	 */
	protected $wizard_slug;

	/**
	 * @var string
	 */
	protected $wizard_ajax_action;

	/**
	 * Properties for wizard.
	 *
	 * @return array
	 */
	abstract public function props(): array;

	/**
	 * AJAX action for finishing wizard.
	 *
	 * @return void
	 */
	abstract public function finish(): void;

	/**
	 * Register callback for AJAX sub-action.
	 *
	 * @param string $registered_sub_action Slug of sub-action.
	 * @param callable $callback Callback for the sub-action.
	 *
	 * @return void
	 */
	public function add_ajax_action( string $registered_sub_action, callable $callback ): void {
		if ( empty( $this->wizard_ajax_action ) ) {
			trigger_error( 'AJAX action cannot be added: <code>wizard_ajax_action</code> property is undefined.', E_USER_WARNING );
			return;
		}

		$hook = $this->admin_page_slug . '/' . $this->wizard_ajax_action;

		add_action( $hook, static function ( $requested_sub_action ) use ( $registered_sub_action, $callback ): void {
			if ( $registered_sub_action !== $requested_sub_action ) {
				return;
			}

			call_user_func( $callback );
		} );
	}

	/**
	 * Register hooks.
	 *
	 * @return void
	 */
	public function register_hooks(): void {

		if ( empty( $this->wizard_ajax_action ) ) {
			trigger_error( 'AJAX action is required for wizard (<code>wizard_ajax_action</code> property).', E_USER_WARNING );
			return;
		}

		$hook = sprintf( '%s/print_scripts', $this->admin_page_slug );
		add_action( $hook, [ $this, 'action__print_scripts' ] );

		$hook = sprintf( 'wp_ajax_%s', $this->wizard_ajax_action );
		add_action( $hook, [ $this, 'action__wp_ajax' ] );

		$this->add_ajax_action( 'finish', [ $this, 'finish' ] );

	}

	/**
	 * Action: {$admin_page_slug}/print_scripts
	 *
	 * Print wizard properties to admin page.
	 *
	 * @uses $this->props()
	 *
	 * @return void
	 */
	protected function action__print_scripts(): void {
		$props         = ( array ) $this->props();
		$default_props = [];

		if ( empty( $props ) ) {
			return;
		}

		if ( ! empty( $this->wizard_ajax_action ) ) {
			$default_props['ajax'] = [
				'url'   => add_query_arg( 'action', $this->wizard_ajax_action, admin_url( 'admin-ajax.php' ) ),
				'nonce' => wp_create_nonce( $this->wizard_ajax_action ),
			];
		}

		$wizard_slug = json_encode( ( string ) $this->wizard_slug );
		$props       = json_encode( wp_parse_args( $props, $default_props ) );
		?>

		<script>
			window[<?php echo $wizard_slug ?>] = <?php echo $props ?>;
		</script>

		<?php
	}

	/**
	 * Action: wp_ajax_{$wizard_ajax_action}
	 *
	 * Handle AJAX request for wizard.
	 *
	 * @return void
	 */
	public function action__wp_ajax(): void {
		$sub_action = '';

		if ( ! empty( $_GET['sub_action'] ) ) {
			$sub_action = $_GET['sub_action'];
		}

		if ( empty( $sub_action ) || empty( $_REQUEST['_wpnonce'] ) ) {
			wp_send_json_error( 'Missing required parameters.', 400 );
		}

		if ( ! wp_verify_nonce( $_REQUEST['_wpnonce'], $this->wizard_ajax_action ) ) {
			wp_send_json_error( 'Nonce is invalid.', 403 );
		}

		do_action( $this->admin_page_slug . '/' . $this->wizard_ajax_action, $sub_action );
	}

}
