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
	 * Construct.
	 */
	public function __construct() {
		$this->register_hooks();
	}

	/**
	 * Register hooks.
	 *
	 * @return void
	 */
	public function register_hooks(): void {

		$hook = sprintf( '%s/print_scripts', $this->admin_page_slug );
		add_action( $hook, [ $this, 'action__print_scripts' ] );

		$this->maybe_register_ajax_action();

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

		if ( $this->supports_ajax() ) {
			$default_props['ajax'] = $this->ajax_props();
		}

		$admin_slug = json_encode( str_replace( '-', '_', ( string ) $this->admin_page_slug ) );
		$card_slug  = json_encode( str_replace( '-', '_', ( string ) $this->card_slug ) );
		$props      = json_encode( wp_parse_args( $props, $default_props ) );

		printf( '<script>window[%s]["cards"][%s] = %s</script>%s', $admin_slug, $card_slug, $props, PHP_EOL );
	}



}
