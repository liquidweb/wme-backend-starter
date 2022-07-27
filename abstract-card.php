<?php declare( strict_types=1 );

abstract class WME_Sparkplug_Card {

	use WME_Sparkplug_Uses_Ajax;

	/**
	 * @var string
	 */
	protected $admin_page_slug;

	/**
	 * @var string
	 */
	protected $card_slug;

	/**
	 * Properties for card.
	 *
	 * @return array
	 */
	abstract public function props(): array;

	/**
	 * Register hooks.
	 *
	 * @return void
	 */
	public function register_hooks(): void {

		$hook = sprintf( '%s/print_scripts', $this->admin_page_slug );
		add_action( $hook, [ $this, 'action__print_scripts' ] );

		if ( ! method_exists( $this, 'supports_ajax' ) || ! $this->supports_ajax() ) {
			return;
		}

		$hook = sprintf( 'wp_ajax_%s', $this->ajax_action );
		add_action( $hook, [ $this, 'action__wp_ajax' ] );

	}

	/**
	 * Action: {$this->admin_page_slug}/print_scripts
	 *
	 * Print card properties to admin page.
	 *
	 * @uses $this->props()
	 *
	 * @return void
	 */
	public function action__print_scripts(): void {
		$props         = ( array ) $this->props();
		$default_props = [];

		if ( empty( $props ) ) {
			return;
		}

		if ( ! empty( $this->card_ajax_action ) ) {
			$default_props['ajax'] = [
				'url'   => add_query_arg( 'action', $this->card_ajax_action, admin_url( 'admin-ajax.php' ) ),
				'nonce' => wp_create_nonce( $this->card_ajax_action ),
			];
		}

		$card_slug = json_encode( ( string ) $this->card_slug );
		$props     = json_encode( wp_parse_args( $props, $default_props ) );

		printf( '<script>window["wizards"][%s] = %s</script>%s', $card_slug, $props, PHP_EOL );
	}



}
