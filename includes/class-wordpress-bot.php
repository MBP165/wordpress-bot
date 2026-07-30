<?php
/**
 * The core plugin class.
 */
class Wordpress_Bot {

	/**
	 * The loader that's responsible for maintaining and registering all hooks.
	 *
	 * @var Wordpress_Bot_Loader
	 */
	protected $loader;

	/**
	 * The unique identifier of this plugin.
	 *
	 * @var string
	 */
	protected $plugin_name;

	/**
	 * The current version of the plugin.
	 *
	 * @var string
	 */
	protected $version;

	public function __construct() {
		if ( defined( 'WORDPRESS_BOT_VERSION' ) ) {
			$this->version = WORDPRESS_BOT_VERSION;
		} else {
			$this->version = '1.0.0';
		}
		$this->plugin_name = 'wordpress-bot';

		$this->load_dependencies();
		$this->set_locale();
		$this->define_admin_hooks();
		$this->define_public_hooks();
	}

	/**
	 * Load the required dependencies for this plugin.
	 */
	private function load_dependencies() {
		require_once plugin_dir_path( dirname( __FILE__ ) ) . 'includes/class-wordpress-bot-loader.php';
		require_once plugin_dir_path( dirname( __FILE__ ) ) . 'includes/class-wordpress-bot-i18n.php';
		require_once plugin_dir_path( dirname( __FILE__ ) ) . 'admin/class-wordpress-bot-admin.php';
		require_once plugin_dir_path( dirname( __FILE__ ) ) . 'public/class-wordpress-bot-public.php';

		$this->loader = new Wordpress_Bot_Loader();
	}

	/**
	 * Define the locale for this plugin for internationalization.
	 */
	private function set_locale() {
		$plugin_i18n = new Wordpress_Bot_i18n();
		$this->loader->add_action( 'plugins_loaded', $plugin_i18n, 'load_plugin_textdomain' );
	}

	/**
	 * Register all of the hooks related to the admin area functionality.
	 */
	private function define_admin_hooks() {
		$plugin_admin = new Wordpress_Bot_Admin( $this->get_plugin_name(), $this->get_version() );

		$this->loader->add_action( 'admin_enqueue_scripts', $plugin_admin, 'enqueue_styles' );
		$this->loader->add_action( 'admin_enqueue_scripts', $plugin_admin, 'enqueue_scripts' );
	}

	/**
	 * Register all of the hooks related to the public-facing functionality.
	 */
	private function define_public_hooks() {
		$plugin_public = new Wordpress_Bot_Public( $this->get_plugin_name(), $this->get_version() );

		$this->loader->add_action( 'wp_enqueue_scripts', $plugin_public, 'enqueue_styles' );
		$this->loader->add_action( 'wp_enqueue_scripts', $plugin_public, 'enqueue_scripts' );
	}

	/**
	 * Run the loader to execute all of the hooks with WordPress.
	 */
	public function run() {
		$this->loader->run();
	}

	public function get_plugin_name() {
		return $this->plugin_name;
	}

	public function get_loader() {
		return $this->loader;
	}

	public function get_version() {
		return $this->version;
	}
}
