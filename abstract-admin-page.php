<?php

abstract class WME_Sparkplug_Admin_Page {

	protected $page_title;
	protected $menu_title;
	protected $capability;
	protected $menu_slug;
	protected $callback = '';
	protected $icon_url = '';
	protected $position = null;

	/**
	 * Register hooks.
	 *
	 * @return void
	 */
	public function register_hooks(): void {

		add_action( 'admin_menu', [ $this, 'action__admin_menu' ] );

	}

	/**
	 * Register admin menu page.
	 *
	 * @return false|string
	 */
	protected function register_menu_page() {
		return add_menu_page(
			$this->page_title,
			$this->menu_title,
			$this->capability,
			$this->menu_slug,
			[ $this, 'callback__menu_page' ],
			$this->icon_url,
			$this->position
		);
	}

	/**
	 * Action: admin_menu
	 *
	 * Register menu page.
	 *
	 * @uses $this->register_menu_page()
	 *
	 * @return void
	 */
	public function action__admin_menu(): void {
		if ( 'admin_menu' !== current_action() ) {
			return;
		}

		$slug = $this->register_menu_page();

		add_action( 'admin_print_styles-'  . $slug, [ $this, 'action__admin_print_styles'  ] );
		add_action( 'admin_print_scripts-' . $slug, [ $this, 'action__admin_print_scripts' ] );
	}

	/**
	 * Callback: add_menu_page
	 *
	 * @see $this->action__admin_init()
	 *
	 * @return void
	 */
	public function callback__menu_page(): void {
		printf( '<div id="%1$s-react" data-js="%1$s"></div>', esc_attr( $this->menu_slug ) );
	}

	/**
	 * Action: admin_print_styles_{$page_slug}
	 *
	 * Print or enqueue styles for the admin page.
	 *
	 * @return void
	 */
	public function action__admin_print_styles(): void {
		do_action( sprintf( '%s/print_styles', $this->menu_slug ) );
	}

	/**
	 * Action: admin_print_scripts_{$page_slug}
	 *
	 * Print or enqueue scripts for the admin page.
	 *
	 * @return void
	 */
	public function action__admin_print_scripts(): void {
		do_action( sprintf( '%s/print_config',  $this->menu_slug ) );
		do_action( sprintf( '%s/print_scripts', $this->menu_slug ) );
	}

}
