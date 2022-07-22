<?php declare( strict_types=1 );

trait WME_Sparkplug_Uses_Ajax {

	/**
	 * @var string
	 */
	protected $ajax_action;

	protected function supports_ajax(): bool {
		return ! empty( $this->admin_page_slug ) && ! empty( $this->ajax_action );
	}

	/**
	 * Register callback for AJAX sub-action.
	 *
	 * @param string $registered_sub_action Slug of sub-action.
	 * @param callable $callback Callback for the sub-action.
	 *
	 * @return void
	 */
	public function add_ajax_action( string $registered_sub_action, callable $callback ): void {
		if ( ! $this->supports_ajax() ) {
			trigger_error( 'AJAX action cannot be added: <code>ajax_action</code> property is undefined.', E_USER_WARNING );
			return;
		}

		$hook = $this->admin_page_slug . '/' . $this->ajax_action;

		add_action( $hook, static function ( $requested_sub_action ) use ( $registered_sub_action, $callback ): void {
			if ( $registered_sub_action !== $requested_sub_action ) {
				return;
			}

			call_user_func( $callback );
		} );
	}

	/**
	 * Action: wp_ajax_{$this->ajax_action}
	 *
	 * Handle AJAX requests.
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

		if ( ! wp_verify_nonce( $_REQUEST['_wpnonce'], $this->ajax_action ) ) {
			wp_send_json_error( 'Nonce is invalid.', 403 );
		}

		do_action( $this->admin_page_slug . '/' . $this->ajax_action, $sub_action );
	}

}
