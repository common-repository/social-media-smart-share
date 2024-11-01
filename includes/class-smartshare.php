<?php
if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if access directly
}
/**
 * The file that defines the core plugin class
 *
 * A class definition that includes attributes and functions used across both the
 * public-facing side of the site and the admin area.
 *
 * @link       https://www.multidots.com/
 * @since      1.0.0
 *
 * @package    Smart_Share
 * @subpackage Smartshare/includes
 */

/**
 * The core plugin class.
 *
 * This is used to define internationalization, admin-specific hooks, and
 * public-facing site hooks.
 *
 * Also maintains the unique identifier of this plugin as well as the current
 * version of the plugin.
 *
 * @since      1.0.0
 * @package    Smart_Share
 * @subpackage Smartshare/includes
 * @author     Multidots <inquiry@multidots.com>
 */
class Smart_Share {

	/**
	 * The loader that's responsible for maintaining and registering all hooks that power
	 * the plugin.
	 *
	 * @since    1.0.0
	 * @access   protected
	 * @var      Smart_Share_Loader $loader Maintains and registers all hooks for the plugin.
	 */
	protected $loader;

	/**
	 * The unique identifier of this plugin.
	 *
	 * @since    1.0.0
	 * @access   protected
	 * @var      string $plugin_name The string used to uniquely identify this plugin.
	 */
	protected $plugin_name;

	/**
	 * The current version of the plugin.
	 *
	 * @since    1.0.0
	 * @access   protected
	 * @var      string $version The current version of the plugin.
	 */
	protected $version;

	/**
	 * Define the core functionality of the plugin.
	 *
	 * Set the plugin name and the plugin version that can be used throughout the plugin.
	 * Load the dependencies, define the locale, and set the hooks for the admin area and
	 * the public-facing side of the site.
	 *
	 * @since    1.0.0
	 */
	public function __construct() {
		if ( defined( 'SMART_SHARE_VERSION' ) ) {
			$this->version = SMART_SHARE_VERSION;
		} else {
			$this->version = '1.0.0';
		}
		$this->plugin_name = 'social-media-smart-share';

		$this->load_dependencies();
		$this->set_locale();
		$this->define_admin_hooks();
	}

	/**
	 * Load the required dependencies for this plugin.
	 *
	 * Include the following files that make up the plugin:
	 *
	 * - Smart_Share_Loader. Orchestrates the hooks of the plugin.
	 * - Smart_Share_i18n. Defines internationalization functionality.
	 * - Smart_Share_Admin. Defines all hooks for the admin area.
	 * - Smart_Share_Public. Defines all hooks for the public side of the site.
	 *
	 * Create an instance of the loader which will be used to register the hooks
	 * with WordPress.
	 *
	 * @since    1.0.0
	 * @access   private
	 */
	private function load_dependencies() {

		/**
		 * The class responsible for orchestrating the actions and filters of the
		 * core plugin.
		 */
		require_once plugin_dir_path( dirname( __FILE__ ) ) . 'includes/class-smartshare-loader.php';

		/**
		 * The class responsible for defining internationalization functionality
		 * of the plugin.
		 */
		require_once plugin_dir_path( dirname( __FILE__ ) ) . 'includes/class-smartshare-i18n.php';

		/**
		 * The class responsible for defining all actions that occur in the admin area.
		 */
		require_once plugin_dir_path( dirname( __FILE__ ) ) . 'admin/class-smartshare-admin.php';

		/**
		 * The class responsible for defining all actions that occur in the public-facing
		 * side of the site.
		 */
		require plugin_dir_path( dirname( __FILE__ ) ) . '/twitteroauth/autoload.php';

		require plugin_dir_path( dirname( __FILE__ ) ) . '/linkedin/vendor/autoload.php';

		$this->loader = new Smart_Share_Loader();

	}

	/**
	 * Define the locale for this plugin for internationalization.
	 *
	 * Uses the Smart_Share_i18n class in order to set the domain and to register the hook
	 * with WordPress.
	 *
	 * @since    1.0.0
	 * @access   private
	 */
	private function set_locale() {

		$plugin_i18n = new Smart_Share_i18n();

		$this->loader->add_action( 'plugins_loaded', $plugin_i18n, 'load_plugin_textdomain' );

	}

	/**
	 * Register all of the hooks related to the admin area functionality
	 * of the plugin.
	 *
	 * @since    1.0.0
	 * @access   private
	 */
	private function define_admin_hooks() {

		$plugin_admin = new Smart_Share_Admin( $this->get_plugin_name(), $this->get_version() );

		$this->loader->add_action( 'admin_enqueue_scripts', $plugin_admin, 'smart_share_enqueue_styles' );
		$this->loader->add_action( 'admin_enqueue_scripts', $plugin_admin, 'smart_share_enqueue_scripts' );

		//create new top-level menu
		$this->loader->add_action( 'admin_menu', $plugin_admin, 'smart_share_custom_menu_page' );

		//call register settings function
		$this->loader->add_action( 'admin_init', $plugin_admin, 'smart_share_settings_register' );

		// Ajax call to sync user facebook account
		$this->loader->add_action( 'wp_ajax_ss_user_fb_account_sync', $plugin_admin, 'smart_share_facebook_account_sync' );

		$this->loader->add_action( 'admin_post_admin_add_user_li_account', $plugin_admin, 'smart_share_linkedin_account_sync' );

		// Ajax call to remove user profile
		$this->loader->add_action( 'wp_ajax_ss_remove_user_profile', $plugin_admin, 'smart_share_remove_user_profile' );

		// Ajax call to delete current queue
		$this->loader->add_action( 'wp_ajax_ss_delete_current_queue', $plugin_admin, 'smart_share_delete_current_queue' );

		// Ajax call to resume/pause current queue
		$this->loader->add_action( 'wp_ajax_ss_pause_resume_current_queue', $plugin_admin, 'smart_share_resume_pause_current_queue' );

		// Ajax call to skip queue post
		$this->loader->add_action( 'wp_ajax_ss_skip_queue_post', $plugin_admin, 'smart_share_skip_queue_post' );

		// Ajax call to delete skipped post
		$this->loader->add_action( 'wp_ajax_ss_delete_skipped_post', $plugin_admin, 'smart_share_delete_skipped_post' );

		// Ajax call to share post on social media instantly
		$this->loader->add_action( 'wp_ajax_ss_instant_share_post', $plugin_admin, 'smart_share_ss_instant_share_post' );

		// Ajax call to delete skipped post
		$this->loader->add_action( 'wp_ajax_ss_queue_failed_post', $plugin_admin, 'smart_share_queue_failed_post' );

		// Store schedule settings
		$this->loader->add_action( 'admin_post_smart_share_schedule_settings', $plugin_admin, 'smart_share_schedule_settings_callback' );

		// Add setting links
		$this->loader->add_action( 'plugin_action_links_'. SMART_SHARE_BASENAME, $plugin_admin, 'ss_plugin_settings_links' );

		// remove parent menu from the admin menu
		$this->loader->add_action('admin_head', $plugin_admin, 'smart_share_remove_admin_submenus');

		// activated parent menu always
		$this->loader->add_action('parent_file', $plugin_admin, 'ss_parent_menu_activated');
	}

	/**
	 * Run the loader to execute all of the hooks with WordPress.
	 *
	 * @since    1.0.0
	 */
	public function run() {
		$this->loader->run();
	}

	/**
	 * The name of the plugin used to uniquely identify it within the context of
	 * WordPress and to define internationalization functionality.
	 *
	 * @return    string    The name of the plugin.
	 * @since     1.0.0
	 */
	public function get_plugin_name() {
		return $this->plugin_name;
	}

	/**
	 * The reference to the class that orchestrates the hooks with the plugin.
	 *
	 * @return    Smart_Share_Loader    Orchestrates the hooks of the plugin.
	 * @since     1.0.0
	 */
	public function get_loader() {
		return $this->loader;
	}

	/**
	 * Retrieve the version number of the plugin.
	 *
	 * @return    string    The version number of the plugin.
	 * @since     1.0.0
	 */
	public function get_version() {
		return $this->version;
	}

}
