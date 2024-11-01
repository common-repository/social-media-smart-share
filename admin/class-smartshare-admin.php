<?php
/**
 * The admin-specific functionality of the plugin.
 *
 * @link       https://www.multidots.com/
 * @since      1.0.0
 *
 * @package    Smartshare
 * @subpackage Smartshare/admin
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if access directly
}

use Abraham\TwitterOAuth\TwitterOAuth;

// import client class
use LinkedIn\Client;
use LinkedIn\Scope;

/**
 * The admin-specific functionality of the plugin.
 *
 * Defines the plugin name, version, and two examples hooks for how to
 * enqueue the admin-specific stylesheet and JavaScript.
 *
 * Class Smart_Share_Admin
 *
 * @package    Smartshare
 * @subpackage Smartshare/admin
 * @author     Multidots <inquiry@multidots.com>
 */
class Smart_Share_Admin {

	/**
	 * The ID of this plugin.
	 *
	 * @since    1.0.0
	 * @access   private
	 * @var      string $plugin_name The ID of this plugin.
	 */
	private $plugin_name;

	/**
	 * The version of this plugin.
	 *
	 * @since    1.0.0
	 * @access   private
	 * @var      string $version The current version of this plugin.
	 */
	private $version;

	/**
	 * Initialize the class and set its properties.
	 *
	 * @param string $plugin_name The name of this plugin.
	 * @param string $version     The version of this plugin.
	 *
	 * @since    1.0.0
	 */
	public function __construct( $plugin_name, $version ) {

		$this->plugin_name = $plugin_name;
		$this->version     = $version;

		add_action( 'init', array( $this, 'smart_share_autocron_callback' ) );
	}

	/**
	 * Custom pagination
	 *
	 * @param     $page
	 * @param int $current
	 */
	public
	static function smart_share_pagination(
		$page, $current = 1
	) {
		$big = 999999999;
		echo wp_kses( paginate_links( array(
			'base'      => str_replace( $big, '%#%', esc_url( get_pagenum_link( $big ) ) ),
			'format'    => '?paged=%#%',
			'current'   => $current,
			'total'     => $page,
			'prev_text' => __( '«' ),
			'next_text' => __( '»' ),
		) ), array(
			'span' => array(
				'aria-current' => array(),
				'class'        => array(),
			),
			'a'    => array(
				'class' => array(),
				'href'  => array(),
			),
		) );
	}

	/**
	 * @param $file
	 *
	 * @return mixed
	 */
	public function ss_parent_menu_activated( $file ) {
		global $plugin_page;
		if ( 'smartshare-settings' === $plugin_page || 'smartshare-accounts' === $plugin_page || 'smartshare-schedule' === $plugin_page || 'smartshare-history' === $plugin_page || 'smartshare-settings' === $plugin_page || 'martshare-started' === $plugin_page || 'smartshare-started' === $plugin_page || 'smartshare-quick-info' === $plugin_page ) {
			$plugin_page = 'smartshare_dashbord'; //phpcs:ignore
		}

		return $file;
	}

	/**
	 * Register the stylesheets for the admin area.
	 *
	 * @param $hook
	 *
	 * @since    1.0.0
	 */
	public function smart_share_enqueue_styles( $hook ) {

		/**
		 * This function is provided for demonstration purposes only.
		 *
		 * An instance of this class should be passed to the run() function
		 * defined in Smart_Share_Loader as all of the hooks are defined
		 * in that particular class.
		 *
		 * The Smart_Share_Loader will then create the relationship
		 * between the defined hooks and the functions defined in this
		 * class.
		 */
		if (
			false !== strpos( $hook, 'smartshare-settings' ) ||
			false !== strpos( $hook, 'smartshare-accounts' ) ||
			false !== strpos( $hook, 'smartshare-schedule' ) ||
			false !== strpos( $hook, 'smartshare-history' ) ||
			false !== strpos( $hook, 'smartshare-started' ) ||
			false !== strpos( $hook, 'smartshare-accounts' ) ||
			false !== strpos( $hook, 'smartshare-quick-info' ) ||
			false !== strpos( $hook, 'smartshare-skipped' ) ||
			false !== strpos( $hook, 'smartshare-shared' ) ||
			false !== strpos( $hook, 'smartshare-failed' ) ||
			false !== strpos( $hook, 'smartshare_dashbord' )
		) {
			wp_enqueue_style( $this->plugin_name, plugin_dir_url( __FILE__ ) . 'css/smartshare-admin.css', array(), $this->version, 'all' );
			wp_enqueue_style( $this->plugin_name . 'media', plugin_dir_url( __FILE__ ) . 'css/smartshare-media.css', array(), $this->version, 'all' );
			wp_enqueue_style( $this->plugin_name . 'fancybox', plugin_dir_url( __FILE__ ) . 'css/jquery.fancybox.css', array(), $this->version, 'all' );
			wp_enqueue_style( $this->plugin_name . 'select2css', plugin_dir_url( __FILE__ ) . 'css/select2.css', array(), $this->version, 'all' );
			wp_enqueue_style( $this->plugin_name . 'jquery-ui', plugin_dir_url( __FILE__ ) . 'css/jquery-ui.css', array(), $this->version, 'all' );
		}
	}

	/**
	 * Register the JavaScript for the admin area.
	 *
	 * @param $hook
	 *
	 * @since    1.0.0
	 */
	public function smart_share_enqueue_scripts( $hook ) {

		/**
		 * This function is provided for demonstration purposes only.
		 *
		 * An instance of this class should be passed to the run() function
		 * defined in Smart_Share_Loader as all of the hooks are defined
		 * in that particular class.
		 *
		 * The Smart_Share_Loader will then create the relationship
		 * between the defined hooks and the functions defined in this
		 * class.
		 */
		if (
			false !== strpos( $hook, 'smartshare-settings' ) ||
			false !== strpos( $hook, 'smartshare-accounts' ) ||
			false !== strpos( $hook, 'smartshare-schedule' ) ||
			false !== strpos( $hook, 'smartshare-history' ) ||
			false !== strpos( $hook, 'smartshare-started' ) ||
			false !== strpos( $hook, 'smartshare-accounts' ) ||
			false !== strpos( $hook, 'smartshare-quick-info' ) ||
			false !== strpos( $hook, 'smartshare_dashbord' ) ||
			false !== strpos( $hook, 'smartshare-skipped' ) ||
			false !== strpos( $hook, 'smartshare-shared' ) ||
			false !== strpos( $hook, 'smartshare-failed' ) ||
			false !== strpos( $hook, 'smartshare_dashbord' )
		) {
			wp_enqueue_script( $this->plugin_name . 'fancybox-js', plugin_dir_url( __FILE__ ) . 'js/jquery.fancybox.pack.js', array( 'jquery' ), $this->version, true );
			wp_enqueue_script( $this->plugin_name . 'select2-js', plugin_dir_url( __FILE__ ) . 'js/select2.js', array( 'jquery' ), $this->version, true );
			wp_enqueue_script( 'jquery-ui-sortable' );
			wp_enqueue_script( $this->plugin_name, plugin_dir_url( __FILE__ ) . 'js/smartshare-admin.js', array( 'jquery' ), $this->version, true );
			wp_localize_script( $this->plugin_name, 'SS_object', array(
				'ajax_url'               => admin_url( 'admin-ajax.php' ),
				'ajax_nonce'             => wp_create_nonce( 'social_account_link' ),
				'ss_remove_profile_url'  => admin_url( 'admin.php?page=smartshare-accounts' ),
				'ss_reset_schedule_data' => __( 'Are you sure you want to reset the data?', 'social-media-smart-share' ),
			) );
		}
	}

	/**
	 * Register plugin settings group.
	 */
	public
	function smart_share_settings_register() {
		//register our settings
		register_setting( 'smartshare-settings-group', 'ss_bitly_api_key' );
	}

	/**
	 * Register a custom menu page.
	 */
	public
	function smart_share_custom_menu_page() {
		global $GLOBALS;
		if ( empty( $GLOBALS['admin_page_hooks']['smartshare'] ) ) {
			add_menu_page( __( 'DotStore', 'social-media-smart-share' ), __( 'DotStore', 'social-media-smart-share' ), 'manage_options', 'smartshare', array(
				$this,
				'smart_share_menu_page',
			), SMART_SHARE_PLUGIN_URL . 'admin/images/menu-icon.png', 6 );
		}

		add_submenu_page( 'smartshare', __( 'Smart Share', 'social-media-smart-share' ), __( 'Smart Share', 'social-media-smart-share' ), 'manage_options', 'smartshare_dashbord', array(
			$this,
			'smart_share_menu_page',
		) );
		add_submenu_page( 'smartshare_dashbord', __( 'Auto Share', 'social-media-smart-share' ), __( 'Auto Share', 'social-media-smart-share' ), 'manage_options', 'smartshare-accounts', array(
			$this,
			'smart_share_autoshare_page',
		) );
		add_submenu_page( 'smartshare_dashbord', __( 'Schedule', 'social-media-smart-share' ), __( 'Schedule', 'social-media-smart-share' ), 'manage_options', 'smartshare-schedule', array(
			$this,
			'smart_share_schedule_page',
		) );
		add_submenu_page( 'smartshare_dashbord', __( 'Share', 'social-media-smart-share' ), __( 'Share', 'social-media-smart-share' ), 'manage_options', 'smartshare-history', array(
			$this,
			'smart_share_history_page',
		) );
		add_submenu_page( 'smartshare_dashbord', __( 'Settings', 'social-media-smart-share' ), __( 'Settings', 'social-media-smart-share' ), 'manage_options', 'smartshare-settings', array(
			$this,
			'smart_share_settings_page',
		) );

		/*SHARE TAB SUB PAGES*/
		add_submenu_page( 'smartshare_dashbord', __( 'Skipped', 'social-media-smart-share' ), __( 'Skipped', 'social-media-smart-share' ), 'manage_options', 'smartshare-skipped', array(
			$this,
			'smart_share_skipped_post_page',
		) );
		add_submenu_page( 'smartshare_dashbord', __( 'Shared Post', 'social-media-smart-share' ), __( 'Shared Post', 'social-media-smart-share' ), 'manage_options', 'smartshare-shared', array(
			$this,
			'smart_share_shared_post_page',
		) );
		add_submenu_page( 'smartshare_dashbord', __( 'Failed', 'social-media-smart-share' ), __( 'Failed', 'social-media-smart-share' ), 'manage_options', 'smartshare-failed', array(
			$this,
			'smart_share_failed_post_page',
		) );

		add_submenu_page( 'smartshare_dashbord', __( 'Getting Started', 'social-media-smart-share' ), __( 'Getting Started', 'social-media-smart-share' ), 'manage_options', 'smartshare-started', array(
			$this,
			'smart_share_getting_started',
		) );

		add_submenu_page( 'smartshare_dashbord', __( 'Quick Info', 'social-media-smart-share' ), __( 'Quick Info', 'social-media-smart-share' ), 'manage_options', 'smartshare-quick-info', array(
			$this,
			'smart_share_quick_info',
		) );
	}

	/**
	 * Display a smartshare dashboard page
	 */
	public
	function smart_share_menu_page() {
		require plugin_dir_path( __FILE__ ) . 'pages/smartshare-dashboard-page.php';
	}

	/**
	 * Display a smartshare auto share page
	 */
	public
	function smart_share_autoshare_page() {
		require plugin_dir_path( __FILE__ ) . 'pages/smartshare-auto-share-page.php';
	}

	/**
	 * Display a smartshare schedule page
	 */
	public
	function smart_share_schedule_page() {
		require plugin_dir_path( __FILE__ ) . 'pages/smartshare-schedule-page.php';
	}

	/**
	 * Display a smartshare sharing page
	 */
	public
	function smart_share_history_page() {
		require plugin_dir_path( __FILE__ ) . 'pages/smartshare-history-page.php';
	}

	/**
	 * Display a smartshare skipped post page
	 */
	public
	function smart_share_skipped_post_page() {
		require plugin_dir_path( __FILE__ ) . 'pages/smartshare-skipped-post-page.php';
	}

	/**
	 * Display a smartshare shared post page
	 */
	public
	function smart_share_shared_post_page() {
		require plugin_dir_path( __FILE__ ) . 'pages/smartshare-shared-post-page.php';
	}

	/**
	 * Display a smartshare failed post page
	 */
	public
	function smart_share_failed_post_page() {
		require plugin_dir_path( __FILE__ ) . 'pages/smartshare-failed-post-page.php';
	}

	/**
	 * Display a smartshare history page
	 */
	public
	function smart_share_settings_page() {
		require plugin_dir_path( __FILE__ ) . 'pages/smartshare-settings-page.php';
	}

	/**
	 * Display a getting started page
	 */
	public
	function smart_share_getting_started() {
		require plugin_dir_path( __FILE__ ) . 'pages/smartshare-getting-started-page.php';
	}

	/**
	 * Display a smartshare quick info page
	 */
	public
	function smart_share_quick_info() {
		require plugin_dir_path( __FILE__ ) . 'pages/smartshare-quick-info-page.php';
	}

	/**
	 * Execute external code
	 */
	public
	function smart_share_autocron_callback() {

		if ( get_option( 'ss_do_activation_redirect', false ) ) {
			delete_option( 'ss_do_activation_redirect' );
			wp_safe_redirect( admin_url( '/admin.php?page=smartshare-started' ) );
			exit;
		}

		$ss_auto_cron = FILTER_INPUT( INPUT_GET, 'ss_auto_cron', FILTER_SANITIZE_STRING );
		if ( isset( $ss_auto_cron ) && 'auto' === $ss_auto_cron ) {
			require_once plugin_dir_path( __FILE__ ) . 'pages/smartshare-post-cron-event-handler.php';
		}
	}

	/**
	 * Ser plugin settings link on plugin screen
	 *
	 * @param $links
	 *
	 * @return array
	 */
	public
	function ss_plugin_settings_links(
		$ss_links
	) {
		$ss_links[] = sprintf( '<a href="%s">%s</a>', esc_url( get_admin_url( null, 'admin.php?page=smartshare-settings' ) ), __( 'Settings', 'social-media-smart-share' ) );

		return $ss_links;
	}

	/**
	 * Remove main parent menu
	 */
	public
	function smart_share_remove_admin_submenus() {
		remove_submenu_page( 'smartshare', 'smartshare' );
	}

	/**
	 * Save Schedule Settings
	 */
	public
	function smart_share_schedule_settings_callback() {
		global $wpdb;

		if ( ! wp_verify_nonce( FILTER_INPUT( INPUT_POST, 'submit_schedule_settings_nonce', FILTER_SANITIZE_STRING ), 'schedule_settings' ) ) {
			return;
		}

		$referer_url = FILTER_INPUT( INPUT_POST, '_wp_http_referer', FILTER_SANITIZE_STRING );

		$post_array           = filter_input_array( INPUT_POST );
		$ss_schedule_settings = array();
		$ss_args              = array(
			'posts_per_page' => - 1,
			'fields'         => 'ids',
			'post_type'      => 'post',
			'post_status'    => 'publish',
		);
		$ss_reset_schedule    = 0;

		if ( isset( $post_array['ss_schedule_submit'] ) && ! empty( $post_array['ss_schedule_submit'] ) ) {

			// Get user accounts data.
			$ss_user_account_data = get_option( 'ss_user_account_data' );
			$ss_user_account_data = isset( $ss_user_account_data ) & ! empty( $ss_user_account_data ) ? $ss_user_account_data : array();
			$total_post           = 0;
			$main_count           = 1;

			if ( empty( $ss_user_account_data ) ) {
				set_transient( 'ss_error_message', esc_html__( 'Please add social account from "Setup" tab before creating schedule.', 'social-media-smart-share' ), 45 );
				wp_safe_redirect( admin_url( '/admin.php?page=smartshare-schedule' ) );
				exit();
			}

			if ( isset( $post_array['ss_schedule_cats'] ) && ! empty( $post_array['ss_schedule_cats'] ) ) {
				$ss_schedule_settings['ss_schedule_cats'] = $post_array['ss_schedule_cats'];
				$ss_args['category__in']                  = $post_array['ss_schedule_cats'];
			}
			if ( isset( $post_array['ss_post_age'] ) && ! empty( $post_array['ss_post_age'] ) ) {
				$ss_schedule_settings['ss_post_age'] = $post_array['ss_post_age'];

				switch ( $post_array['ss_post_age'] ) {
					case "week" :
						$ss_args['date_query'] = array(
							array( 'after' => '1 week ago' ),
						);
						break;
					case "month" :
						$ss_args['date_query'] = array(
							array( 'after' => '1 month ago' ),
						);
						break;
					case "quarter" :
						$ss_args['date_query'] = array(
							array( 'after' => '3 month ago' ),
						);
						break;
					case "year" :
						$ss_args['date_query'] = array(
							array( 'after' => '1 year ago' ),
						);
						break;
					default:
						$ss_args['date_query'] = array();
				}
			}
			if ( isset( $post_array['ss_post_per_day'] ) && ! empty( $post_array['ss_post_per_day'] ) ) {
				$ss_schedule_settings['ss_post_per_day'] = $post_array['ss_post_per_day'];
			}
			if ( isset( $post_array['post_content'] ) && ! empty( $post_array['post_content'] ) ) {
				$ss_schedule_settings['post_content'] = $post_array['post_content'];
			}
			if ( isset( $post_array['post_hashtag'] ) && ! empty( $post_array['post_hashtag'] ) ) {
				$ss_schedule_settings['post_hashtag'] = $post_array['post_hashtag'];
			}
			if ( ! empty( $post_array['ss_timezone'] ) ) {


				if ( strpos( $post_array['ss_timezone'], 'UTC' ) !== false ) {
					$ss_timezoneOffset = $post_array['ss_timezone'];
					if ( 'UTC' === $ss_timezoneOffset ) {
						$ss_timezoneOffset = 'UTC+0';
					}
				} else {
					$ss_time   = new \DateTime( 'now', new DateTimeZone( $post_array['ss_timezone'] ) );
					$ss_offset = date_offset_get( $ss_time ) / 3600;
					if ( 0 <= $ss_offset ) {
						$ss_timezoneOffset = 'UTC+' . date_offset_get( $ss_time ) / 3600;
					} else {
						$ss_timezoneOffset = 'UTC' . date_offset_get( $ss_time ) / 3600;
					}
				}

				$ss_schedule_settings['ss_timezone']      = $ss_timezoneOffset;
				$ss_schedule_settings['ss_timezone_name'] = $post_array['ss_timezone'];
			}
			if ( ! empty( $post_array['ss_img_priority'] ) ) {
				$ss_schedule_settings['ss_img_priority'] = trim( $post_array['ss_img_priority'], '"' );
			}

			//Get posts from the above filters
			$ss_query = new WP_Query( $ss_args );

			$ss_post_ids = isset( $ss_query->posts ) ? $ss_query->posts : array();

		} else if ( isset( $post_array['ss_schedule_reset'] ) && ! empty( $post_array['ss_schedule_reset'] ) ) {
			$ss_reset_schedule = 1;
		}

		if ( isset( $ss_query ) && $ss_query->found_posts > 0 ) {

			// Save this schedule to database
			$smartshare_schedule = $wpdb->prefix . 'smartshare_schedule';
			$add_schedule        = $wpdb->insert( $smartshare_schedule, array(
				'schedule_settings' => maybe_serialize( $ss_schedule_settings ),
			), array(
				'%s',
			) );

			if ( false !== $add_schedule ) {
				$scheduler_id             = $wpdb->insert_id;
				$smartshare_facebook_post = $wpdb->prefix . 'smartshare_facebook_post';
				$smartshare_twitter_post  = $wpdb->prefix . 'smartshare_twitter_post';
				$smartshare_linkedin_post = $wpdb->prefix . 'smartshare_linkedin_post';

				if ( isset( $ss_user_account_data ) && ! empty( $ss_user_account_data ) ) {

					/*SCHEDULE POST ID ARRAY*/
					foreach ( $ss_post_ids as $ss_post_id ) {

						/*LOOP FOR SINGLE EXISTING ACCOUNT*/
						foreach ( $ss_user_account_data as $account_key => $account_info ) {

							if ( 'fb_account' === $account_key ) {

								if ( isset( $account_info ) && ! empty( $account_info ) ) {

									/*FB ACCOUNT LOOP FOR CREATE SCHEDULE ENTRY*/
									foreach ( $account_info as $fb_account ) {

										if ( isset( $fb_account['account_pages'] ) && ! empty( $fb_account['account_pages'] ) ) {

											$fb_account_name = isset( $fb_account['account_name'] ) & ! empty( $fb_account['account_name'] ) ? $fb_account['account_name'] : '';

											/*FACEBOOK PAGE LOOP FOR CREATE SCHEDULE ENTRY*/

											foreach ( $fb_account['account_pages'] as $fb_page ) {

												$fb_page_name = isset( $fb_page['name'] ) & ! empty( $fb_page['name'] ) ? $fb_page['name'] : '';
												$fb_page_id   = isset( $fb_page['id'] ) & ! empty( $fb_page['id'] ) ? $fb_page['id'] : 0;

												$wpdb->insert( $smartshare_facebook_post, array(
													'scheduler_id' => absint( $scheduler_id ),
													'post_id'      => absint( $ss_post_id ),
													'fb_account'   => $fb_account_name,
													'fb_type'      => $fb_page_name,
													'fb_type_id'   => absint( $fb_page_id ),
												), array(
													'%d',
													'%d',
													'%s',
													'%s',
													'%d',
												) );

												$total_post ++;
											}
										}

										if ( isset( $fb_account['account_groups'] ) && ! empty( $fb_account['account_groups'] ) ) {

											/*FACEBOOK GROUP LOOP FOR CREATE SCHEDULE ENTRY*/
											foreach ( $fb_account['account_groups'] as $fb_group ) {

												$fb_group_name = isset( $fb_group['name'] ) & ! empty( $fb_group['name'] ) ? $fb_group['name'] : '';
												$fb_group_id   = isset( $fb_group['id'] ) & ! empty( $fb_group['id'] ) ? $fb_group['id'] : 0;

												$wpdb->insert( $smartshare_facebook_post, array(
													'scheduler_id' => absint( $scheduler_id ),
													'post_id'      => absint( $ss_post_id ),
													'fb_account'   => $fb_account_name,
													'fb_type'      => $fb_group_name,
													'fb_type_id'   => absint( $fb_group_id ),
												), array(
													'%d',
													'%d',
													'%s',
													'%s',
													'%d',
												) );

												$total_post ++;
											}
										}
									}
								}
							}
							if ( 'tw_account' === $account_key ) {

								if ( isset( $account_info ) && ! empty( $account_info ) ) {

									/*TWITTER ACCOUNT LOOP FOR CREATE SCHEDULE ENTRY*/
									foreach ( $account_info as $tw_account ) {

										$tw_account_name = isset( $tw_account['account_name'] ) & ! empty( $tw_account['account_name'] ) ? $tw_account['account_name'] : '';
										$tw_account_id   = isset( $tw_account['account_id'] ) & ! empty( $tw_account['account_id'] ) ? $tw_account['account_id'] : '';

										$wpdb->insert( $smartshare_twitter_post, array(
											'scheduler_id' => absint( $scheduler_id ),
											'post_id'      => absint( $ss_post_id ),
											'tw_account'   => $tw_account_name,
											'tw_id'        => $tw_account_id,
										), array(
											'%d',
											'%d',
											'%s',
											'%s',
										) );

										$total_post ++;
									}
								}

							}
							if ( 'linkedin_account' === $account_key ) {

								if ( isset( $account_info ) && ! empty( $account_info ) ) {

									/*LINKEDIN ACCOUNT LOOP FOR CREATE SCHEDULE ENTRY*/
									foreach ( $account_info as $linkedin_account ) {

										$account_first_name  = isset( $linkedin_account['account_first_name'] ) & ! empty( $linkedin_account['account_first_name'] ) ? $linkedin_account['account_first_name'] : '';
										$account_last_name   = isset( $linkedin_account['account_last_name'] ) & ! empty( $linkedin_account['account_last_name'] ) ? $linkedin_account['account_last_name'] : '';
										$linkedin_account_id = isset( $linkedin_account['account_id'] ) & ! empty( $linkedin_account['account_id'] ) ? $linkedin_account['account_id'] : '';

										$wpdb->insert( $smartshare_linkedin_post, array(
											'scheduler_id'     => absint( $scheduler_id ),
											'post_id'          => absint( $ss_post_id ),
											'linkedin_account' => $account_first_name . ' ' . $account_last_name,
											'linkedin_id'      => $linkedin_account_id,
										), array(
											'%d',
											'%d',
											'%s',
											'%s',
										) );

										$total_post ++;
									}
								}
							}
						}
						if ( 1 === $main_count ) {
							$ss_schedule_settings['final_post_per_day'] = $total_post * $ss_schedule_settings['ss_post_per_day'];
						}
						$main_count ++;
					}

					$total_days        = ceil( $total_post / $ss_schedule_settings['final_post_per_day'] );
					$end_date          = strtotime( "+" . $total_days . " day" );
					$schedule_end_date = date( 'Y-m-d H:i:s', $end_date );

					$update_schedule_data = array(
						'total_posts'       => absint( $total_post ),
						'post_in_queue'     => absint( $total_post ),
						'schedule_settings' => maybe_serialize( $ss_schedule_settings ),
						'end_date'          => $schedule_end_date,
					);
					$wpdb->update( $smartshare_schedule, $update_schedule_data, array( 'id' => $scheduler_id ) );
				}

				set_transient( 'ss_success_message', esc_html__( 'Schedule added successfully!', 'social-media-smart-share' ), 45 );
			} else {
				set_transient( 'ss_error_message', esc_html__( 'There was an error saving your schedule. Please try again!', 'social-media-smart-share' ), 45 );
			}
		} else {
			if ( 0 === $ss_reset_schedule ) {
				set_transient( 'ss_error_message', esc_html__( 'No posts found matching your filter criteria!', 'social-media-smart-share' ), 45 );
			}
		}
		wp_safe_redirect( admin_url( '/admin.php?page=smartshare-schedule' ) );
		exit();
	}

	/**
	 * Facebook profile sync
	 */
	public function smart_share_facebook_account_sync() {
		$fb_nonce = check_ajax_referer( 'social_account_link', 'security' );
		if ( ! empty( $fb_nonce ) ) {
			session_start();//phpcs:ignore

			$post_action = filter_input( INPUT_POST, 'action', FILTER_SANITIZE_STRING );
			if ( isset( $post_action ) && 'ss_user_fb_account_sync' === $post_action ) {

				$ss_fb_app_id = filter_input( INPUT_POST, 'ss_fb_app_id', FILTER_SANITIZE_STRING );
				$ss_fb_app_id = isset( $ss_fb_app_id ) && ! empty( $ss_fb_app_id ) ? $ss_fb_app_id : '';

				$ss_fb_app_secret = filter_input( INPUT_POST, 'ss_fb_app_secret', FILTER_SANITIZE_STRING );
				$ss_fb_app_secret = isset( $ss_fb_app_secret ) && ! empty( $ss_fb_app_secret ) ? $ss_fb_app_secret : '';

				$ss_fb_submit = filter_input( INPUT_POST, 'ss_fb_submit', FILTER_SANITIZE_STRING );
				$ss_fb_submit = isset( $ss_fb_submit ) && ! empty( $ss_fb_submit ) ? $ss_fb_submit : '';

				$fb_redirect_uri = esc_url( admin_url( 'admin.php?page=smartshare-accounts' ) );
				$fb_login_url    = esc_url( 'https://www.facebook.com/v2.10/dialog/oauth?client_id=' . $ss_fb_app_id . '&state=7626ec0aba96c1208894838598b66c2d&response_type=code&sdk=php-sdk-5.6.3&redirect_uri=' . rawurlencode( $fb_redirect_uri ) . '&scope=public_profile,email,publish_pages,publish_to_groups' );

				$_SESSION['ss_submit_value'] = $ss_fb_submit; //phpcs:ignore

				setcookie( 'ss_fb_app_id', $ss_fb_app_id );
				setcookie( 'ss_fb_app_secret', $ss_fb_app_secret );

				$result = array(
					'response'     => 'ss-user-fb-profile-synced',
					'fb_login_url' => $fb_login_url,
				);
				wp_send_json_success( $result );
			}
		}
		wp_die();
	}

	/**
	 * Linkedin profile sync
	 */
	public function smart_share_linkedin_account_sync() {

		if ( wp_verify_nonce( FILTER_INPUT( INPUT_POST, 'smartshare_linkedin_form_data_nonce', FILTER_SANITIZE_STRING ), 'smartshare_linkedin_form_data' ) ) {
			$ss_li_client_id = filter_input( INPUT_POST, 'ss_linkedin_client_id', FILTER_SANITIZE_STRING ); //LinkedIn App ID
			$ss_li_client_id = isset( $ss_li_client_id ) && ! empty( $ss_li_client_id ) ? $ss_li_client_id : '';

			$ss_li_client_secret = filter_input( INPUT_POST, 'ss_linkedin_client_secret', FILTER_SANITIZE_STRING ); //LinkedIn App Secret
			$ss_li_client_secret = isset( $ss_li_client_secret ) && ! empty( $ss_li_client_secret ) ? $ss_li_client_secret : '';

			$ss_li_code = filter_input( INPUT_GET, 'code', FILTER_SANITIZE_STRING );

			$redirectURL = admin_url( 'admin.php?page=smartshare-accounts' ); //Callback URL

			$_SESSION['ss_submit_value'] = 'ss_li_submit';//phpcs:ignore

			setcookie( 'ss_linkedin_client_id', $ss_li_client_id );
			setcookie( 'ss_linkedin_client_secret', $ss_li_client_secret );

			$provider = new League\OAuth2\Client\Provider\LinkedIn( [
				'clientId'     => $ss_li_client_id,
				'clientSecret' => $ss_li_client_secret,
				'redirectUri'  => $redirectURL,
			] );

			if ( ! isset( $ss_li_code ) ) {

				$options                 = [
					'scope' => [ 'r_liteprofile', 'r_emailaddress', 'w_member_social', 'rw_company_admin' ],
				];
				$linkedin_auth_url       = $provider->getAuthorizationUrl( $options );
				$_SESSION['oauth2state'] = $provider->getState(); //phpcs:ignore
				header( 'Location: ' . $linkedin_auth_url );
				exit;
			}
		}
	}

	/**
	 * Remove user profile.
	 */
	public
	function smart_share_remove_user_profile() {
		$post_action = filter_input( INPUT_POST, 'action', FILTER_SANITIZE_STRING );
		if ( isset( $post_action ) && 'ss_remove_user_profile' === $post_action ) {
			global $wpdb;

			$ss_profile_data_id = filter_input( INPUT_POST, 'ss_profile_data_id', FILTER_SANITIZE_STRING );
			$ss_profile_data_id = isset( $ss_profile_data_id ) && ! empty( $ss_profile_data_id ) ? urldecode( $ss_profile_data_id ) : '';

			$ss_profile_name = filter_input( INPUT_POST, 'ss_profile_name', FILTER_SANITIZE_STRING );
			$ss_profile_name = isset( $ss_profile_name ) && ! empty( $ss_profile_name ) ? urldecode( $ss_profile_name ) : '';

			$ss_profile_account = filter_input( INPUT_POST, 'ss_profile_account', FILTER_SANITIZE_STRING );
			$ss_profile_account = isset( $ss_profile_account ) && ! empty( $ss_profile_account ) ? urldecode( $ss_profile_account ) : '';

			$ss_profile_index     = filter_input( INPUT_POST, 'ss_profile_index', FILTER_SANITIZE_NUMBER_INT );
			$ss_user_account_data = get_option( 'ss_user_account_data' );

			$smartshare_schedule_table = "{$wpdb->prefix}smartshare_schedule";

			$get_scheduler = $wpdb->get_results( $wpdb->prepare( "
                                       SELECT * FROM %1s
                                       WHERE ( status = %s OR status = %s)", array(
				$smartshare_schedule_table,
				"in_process",
				"pause",
			) ), ARRAY_A );

			$ss_current_scheduler_id = ( isset( $get_scheduler[0]['id'] ) ) ? $get_scheduler[0]['id'] : null;

			$updated_schedule_data = array();
			$ss_row_deleted        = 0;

			if ( isset( $ss_current_scheduler_id ) && ! empty( $ss_current_scheduler_id ) ) {
				if ( 'fb_account' === $ss_profile_account ) {
					$delete_table    = $wpdb->prefix . 'smartshare_facebook_post';
					$fb_page_count   = count( $ss_user_account_data[ $ss_profile_account ][ $ss_profile_index ]['account_pages'] );
					$fb_group_count  = count( $ss_user_account_data[ $ss_profile_account ][ $ss_profile_index ]['account_groups'] );
					$deleted_account = absint( $fb_page_count ) + absint( $fb_group_count );
				} elseif ( 'tw_account' === $ss_profile_account ) {
					$delete_table    = $wpdb->prefix . 'smartshare_twitter_post';
					$deleted_account = 1;
				} else {
					$delete_table    = $wpdb->prefix . 'smartshare_linkedin_post';
					$deleted_account = 1;
				}
				$ss_deleted_rows = $wpdb->delete( $delete_table, array(
					'scheduler_id'      => $ss_current_scheduler_id,
					$ss_profile_account => $ss_profile_name,
					'share_status'      => 'pending',
				) );
				$ss_deleted_rows = ( isset( $ss_deleted_rows ) ) ? $ss_deleted_rows : 0;

				$ss_skip_deleted_rows = $wpdb->delete( $delete_table, array(
					'scheduler_id'      => $ss_current_scheduler_id,
					$ss_profile_account => $ss_profile_name,
					'share_status'      => 'skipped',
				) );
				$ss_skip_deleted_rows = ( isset( $ss_skip_deleted_rows ) ) ? $ss_skip_deleted_rows : 0;

				$ss_failed_deleted_rows = $wpdb->delete( $delete_table, array(
					'scheduler_id'      => $ss_current_scheduler_id,
					$ss_profile_account => $ss_profile_name,
					'share_status'      => 'failed',
				) );
				$ss_failed_deleted_rows = ( isset( $ss_failed_deleted_rows ) ) ? $ss_failed_deleted_rows : 0;

				$ss_schedule_settings = ( isset( $get_scheduler[0]['schedule_settings'] ) ) ? maybe_unserialize( $get_scheduler[0]['schedule_settings'] ) : array();
				$ss_post_in_queue     = ( isset( $get_scheduler[0]['post_in_queue'] ) ) ? $get_scheduler[0]['post_in_queue'] : 0;

				$ss_updated_total_post_in_queue  = absint( $ss_post_in_queue ) - absint( $ss_deleted_rows );
				$ss_updated_total_post_in_skip   = absint( $get_scheduler[0]['post_in_skipped'] ) - absint( $ss_skip_deleted_rows );
				$ss_updated_total_post_in_failed = absint( $get_scheduler[0]['post_in_failed'] ) - absint( $ss_failed_deleted_rows );

				$ss_schedule_settings['final_post_per_day'] = $ss_schedule_settings['final_post_per_day'] - ( absint( $deleted_account ) * absint( $ss_schedule_settings['ss_post_per_day'] ) );
				$ss_schedule_settings['final_post_per_day'] = ( 0 === absint( $ss_schedule_settings['final_post_per_day'] ) ) ? 0 : $ss_schedule_settings['final_post_per_day'];
				$ss_updated_total_post_in_queue             = ( 0 === absint( $ss_updated_total_post_in_queue ) ) ? 0 : $ss_updated_total_post_in_queue;

				if ( 0 === absint( $ss_updated_total_post_in_queue ) ) {
					$schedule_end_date = date( 'Y-m-d H:i:s' );
				} else {
					$total_days        = ceil( $ss_updated_total_post_in_queue / absint( $ss_schedule_settings['final_post_per_day'] ) );
					$end_date          = strtotime( "+" . $total_days . " day" );
					$schedule_end_date = date( 'Y-m-d H:i:s', $end_date );
				}

				$ss_row_deleted = $ss_skip_deleted_rows + $ss_deleted_rows + $ss_updated_total_post_in_failed;

				$updated_schedule_data = array(
					'post_in_queue'     => $ss_updated_total_post_in_queue,
					'post_in_skipped'   => $ss_updated_total_post_in_skip,
					'post_in_failed'    => $ss_updated_total_post_in_failed,
					'end_date'          => $schedule_end_date,
					'schedule_settings' => maybe_serialize( $ss_schedule_settings ),
				);

			}

			unset( $ss_user_account_data["$ss_profile_account"][ $ss_profile_index ] );

			setcookie( 'ss_fb_app_id', '', time() - ( 15 * 60 ) );
			setcookie( 'ss_fb_app_secret', '', time() - ( 15 * 60 ) );
			delete_option( 'ss_user_fb_account' );

			$synced_account_count = count( $ss_user_account_data["$ss_profile_account"] );
			if ( 0 === $synced_account_count ) {
				unset( $ss_user_account_data["$ss_profile_account"] );
			}

			$synced_account_count = count( $ss_user_account_data );

			if ( 0 === $synced_account_count && isset( $ss_current_scheduler_id ) && ! empty( $ss_current_scheduler_id ) && $ss_row_deleted > 0 ) {
				$updated_schedule_data['status'] = 'completed';
				$wpdb->update( $smartshare_schedule_table, $updated_schedule_data, array( 'id' => $ss_current_scheduler_id ) );
			} else {
				if ( isset( $ss_current_scheduler_id ) && ! empty( $ss_current_scheduler_id ) && $ss_row_deleted > 0 ) {
					$wpdb->update( $smartshare_schedule_table, $updated_schedule_data, array( 'id' => $ss_current_scheduler_id ) );
				}
			}

			update_option( 'ss_user_account_data', $ss_user_account_data, false );

			set_transient( 'ss_success_message', esc_html__( 'Account deleted successfully!', 'social-media-smart-share' ), 45 );

			$result = array(
				'message'  => esc_html__( 'Profile Removed Successfully', 'social-media-smart-share' ),
				'response' => 'ss-user-profile-deleted',
			);
			wp_send_json_success( $result );
		}
		wp_die();
	}

	/**
	 * Delete Current Queue
	 */
	public
	function smart_share_delete_current_queue() {
		$post_action = filter_input( INPUT_POST, 'action', FILTER_SANITIZE_STRING );
		if ( isset( $post_action ) && 'ss_delete_current_queue' === $post_action ) {
			global $wpdb;
			$ss_current_queue_id = filter_input( INPUT_POST, 'ss_current_queue_id', FILTER_SANITIZE_NUMBER_INT );
			$ss_current_queue_id = isset( $ss_current_queue_id ) && ! empty( $ss_current_queue_id ) ? $ss_current_queue_id : '';

			$smartshare_schedule = $wpdb->prefix . 'smartshare_schedule';
			$delete_schedule     = $wpdb->delete( $smartshare_schedule, array( 'id' => $ss_current_queue_id ) );

			if ( false !== $delete_schedule ) {
				$wpdb->query( "DELETE FROM {$wpdb->options} WHERE option_name LIKE 'ss_shared_post_today_count_%'" );
				set_transient( 'ss_success_message', esc_html__( 'Schedule deleted successfully!', 'social-media-smart-share' ), 45 );
			} else {
				set_transient( 'ss_error_message', esc_html__( 'There was an error delete your schedule. Please try again!', 'social-media-smart-share' ), 45 );
			}
		}
	}

	/**
	 * Resume and post Current Queue
	 */
	public
	function smart_share_resume_pause_current_queue() {

		$post_action = filter_input( INPUT_POST, 'action', FILTER_SANITIZE_STRING );
		if ( isset( $post_action ) && 'ss_pause_resume_current_queue' === $post_action ) {
			global $wpdb;

			$ss_current_queue_id = filter_input( INPUT_POST, 'ss_current_queue_id', FILTER_SANITIZE_NUMBER_INT );
			$ss_current_queue_id = isset( $ss_current_queue_id ) && ! empty( $ss_current_queue_id ) ? $ss_current_queue_id : '';

			$ss_current_queue_status = filter_input( INPUT_POST, 'ss_current_queue_status', FILTER_SANITIZE_STRING );
			$ss_current_queue_status = isset( $ss_current_queue_status ) && ! empty( $ss_current_queue_status ) ? $ss_current_queue_status : 'in_process';

			if ( 'in_process' === $ss_current_queue_status ) {
				$update_status = 'pause';
			} else {
				$update_status = 'in_process';
			}

			$smartshare_schedule = $wpdb->prefix . 'smartshare_schedule';
			$update_schedule     = $wpdb->update( $smartshare_schedule, array( 'status' => $update_status ), array( 'id' => $ss_current_queue_id ) );

			if ( false !== $update_schedule ) {
				//set_transient( 'ss_success_message', 'Schedule updated successfully!', 45 );
			} else {
				set_transient( 'ss_error_message', esc_html__( 'There was an error update your schedule. Please try again!', 'social-media-smart-share' ), 45 );
			}
		}
	}

	/**
	 * Resume and post Current Queue
	 */
	public
	function smart_share_skip_queue_post() {

		$post_action = filter_input( INPUT_POST, 'action', FILTER_SANITIZE_STRING );
		if ( isset( $post_action ) && 'ss_skip_queue_post' === $post_action ) {
			global $wpdb;

			$ss_current_post_id = filter_input( INPUT_POST, 'ss_current_post_id', FILTER_SANITIZE_NUMBER_INT );
			$ss_current_post_id = isset( $ss_current_post_id ) && ! empty( $ss_current_post_id ) ? $ss_current_post_id : '';

			$ss_social_media = filter_input( INPUT_POST, 'ss_social_media', FILTER_SANITIZE_STRING );
			$ss_social_media = isset( $ss_social_media ) && ! empty( $ss_social_media ) ? $ss_social_media : '';

			if ( 'facebook' === $ss_social_media ) {
				$update_table = $wpdb->prefix . 'smartshare_facebook_post';
			} elseif ( 'twitter' === $ss_social_media ) {
				$update_table = $wpdb->prefix . 'smartshare_twitter_post';
			} elseif ( 'linkedin' === $ss_social_media ) {
				$update_table = $wpdb->prefix . 'smartshare_linkedin_post';
			} else {
				set_transient( 'ss_error_message', esc_html__( 'There was an error skipped your post. Please try again!', 'social-media-smart-share' ), 45 );

				return;
			}
			$get_post_exists = $wpdb->get_row( $wpdb->prepare( "SELECT * FROM $update_table WHERE share_status = %s and id = %d", array(
				"pending",
				$ss_current_post_id,
			) ) );  // WPCS: unprepared SQL OK.

			if ( isset( $get_post_exists ) && ! empty( $get_post_exists ) ) {
				$skipped_schedule = $wpdb->update( $update_table, array( 'share_status' => 'skipped' ), array( 'id' => $ss_current_post_id ) );
				if ( false !== $skipped_schedule ) {
					$this->ss_update_current_schedule( 'skipped', $get_post_exists->share_status );
					set_transient( 'ss_success_message', esc_html__( 'Post successfully skipped!', 'social-media-smart-share' ), 45 );
				} else {
					set_transient( 'ss_error_message', esc_html__( 'There was an error skipped your post. Please try again!', 'social-media-smart-share' ), 45 );
				}
			} else {
				set_transient( 'ss_error_message', esc_html__( 'There was an error skipped your post. Please try again!', 'social-media-smart-share' ), 45 );

				return;
			}

		}
	}

	/**
	 * Update current schedule.
	 *
	 * @param $share_status
	 * @param $past_post_status
	 *
	 * @return bool
	 */
	public function ss_update_current_schedule( $share_status, $past_post_status ) {

		if ( ! isset( $share_status ) || empty( $share_status ) ) {
			return;
		}

		global $wpdb;
		$updated_schedule_data     = array();
		$smartshare_schedule_table = "{$wpdb->prefix}smartshare_schedule";
		$get_scheduler             = $wpdb->get_row( $wpdb->prepare( "SELECT * FROM %1s WHERE `status` = %s or `status` = %s ORDER BY id DESC", array(
			$smartshare_schedule_table,
			"in_process",
			"pause",
		) ), ARRAY_A );

		$ss_schedule_settings = ( isset( $get_scheduler['schedule_settings'] ) ) ? maybe_unserialize( $get_scheduler['schedule_settings'] ) : array();
		$ss_post_per_day      = isset( $ss_schedule_settings['final_post_per_day'] ) ? $ss_schedule_settings['final_post_per_day'] : '';

		$current_schedule_id     = $get_scheduler['id'];
		$current_post_in_queue   = $get_scheduler['post_in_queue'];
		$current_post_in_shared  = $get_scheduler['post_in_shared'];
		$current_post_in_skipped = $get_scheduler['post_in_skipped'];
		$current_post_in_failed  = $get_scheduler['post_in_failed'];
		$current_total_posts     = $get_scheduler['total_posts'];

		if ( 'pending' === $past_post_status ) {
			$target_status = 'post_in_queue';
		} else {
			$target_status = 'post_in_' . $past_post_status;
		}


		switch ( $share_status ) {
			case 'pending':
				$updated_schedule_data['post_in_queue']  = absint( $current_post_in_queue ) + 1;
				$updated_schedule_data['post_in_failed'] = absint( $current_post_in_failed ) - 1;
				break;
			case 'skipped':
				$updated_schedule_data['post_in_skipped'] = absint( $current_post_in_skipped ) + 1;
				$updated_schedule_data['post_in_queue']   = absint( $current_post_in_queue ) - 1;
				break;
			case 'shared':
				$updated_schedule_data['post_in_shared'] = absint( $current_post_in_shared ) + 1;
				$updated_schedule_data[ $target_status ] = absint( $get_scheduler[ $target_status ] ) - 1;
				break;
			case 'failed';
				$updated_schedule_data['post_in_failed'] = absint( $current_post_in_failed ) + 1;
				$updated_schedule_data[ $target_status ] = absint( $get_scheduler[ $target_status ] ) - 1;
				break;
			case 'deleted';
				$updated_schedule_data['post_in_skipped'] = absint( $current_post_in_skipped ) - 1;
				$updated_schedule_data['total_posts']     = absint( $current_total_posts ) - 1;
				$updated_schedule_data['post_in_queue']   = $current_post_in_queue;
				break;
		}

		if ( 'failed' !== $past_post_status && 'skipped' !== $past_post_status || 'pending' === $share_status ) {
			$total_days                        = ceil( $updated_schedule_data['post_in_queue'] / $ss_post_per_day );
			$end_date                          = strtotime( "+" . $total_days . " day" );
			$schedule_end_date                 = date( 'Y-m-d H:i:s', $end_date );
			$updated_schedule_data['end_date'] = $schedule_end_date;
		}

		if ( 0 === $updated_schedule_data['post_in_queue'] ) {
			$updated_schedule_data['status'] = 'completed';
		}

		$update_schedule = $wpdb->update( $smartshare_schedule_table, $updated_schedule_data, array( 'id' => $current_schedule_id ) );
		if ( false !== $update_schedule ) {
			return true;
		} else {
			return false;
		}
	}

	/**
	 * Delete current skipped post.
	 */
	public function smart_share_delete_skipped_post() {
		$post_action = filter_input( INPUT_POST, 'action', FILTER_SANITIZE_STRING );
		if ( isset( $post_action ) && 'ss_delete_skipped_post' === $post_action ) {
			global $wpdb;

			$ss_current_post_id = filter_input( INPUT_POST, 'ss_current_post_id', FILTER_SANITIZE_NUMBER_INT );
			$ss_current_post_id = isset( $ss_current_post_id ) && ! empty( $ss_current_post_id ) ? $ss_current_post_id : '';

			$ss_social_media = filter_input( INPUT_POST, 'ss_social_media', FILTER_SANITIZE_STRING );
			$ss_social_media = isset( $ss_social_media ) && ! empty( $ss_social_media ) ? $ss_social_media : '';

			if ( 'facebook' === $ss_social_media ) {
				$delete_table = $wpdb->prefix . 'smartshare_facebook_post';
			} elseif ( 'twitter' === $ss_social_media ) {
				$delete_table = $wpdb->prefix . 'smartshare_twitter_post';
			} elseif ( 'linkedin' === $ss_social_media ) {
				$delete_table = $wpdb->prefix . 'smartshare_linkedin_post';
			}

			$delete_skipped = $wpdb->delete( $delete_table, array( 'id' => $ss_current_post_id ) );

			$get_post_exists = $wpdb->get_row( $wpdb->prepare( "SELECT * FROM $delete_table WHERE  id = %d", array( $ss_current_post_id ) ) );  // WPCS: unprepared SQL OK.

			if ( false !== $delete_skipped ) {
				$this->ss_update_current_schedule( 'deleted', $get_post_exists->share_status );
				set_transient( 'ss_success_message', esc_html__( 'Post successfully deleted!', 'social-media-smart-share' ), 45 );
			} else {
				set_transient( 'ss_error_message', esc_html__( 'There was an error deleting your post. Please try again!', 'social-media-smart-share' ), 45 );
			}
		}
	}

	/**
	 * Resume and post Current Queue
	 *
	 * @param string $ss_postid
	 * @param string $ss_socialmedia
	 *
	 * @throws \LinkedIn\Exception
	 */
	public function smart_share_ss_instant_share_post( $ss_postid = '', $ss_socialmedia = '' ) {

		global $wpdb;

		if ( empty( $ss_postid ) ) {
			$ss_current_post_id = filter_input( INPUT_POST, 'ss_current_post_id', FILTER_SANITIZE_NUMBER_INT );
			$ss_current_post_id = isset( $ss_current_post_id ) && ! empty( $ss_current_post_id ) ? $ss_current_post_id : '';
		} else {
			$ss_current_post_id = $ss_postid;
		}

		if ( empty( $ss_socialmedia ) ) {
			$ss_social_media = filter_input( INPUT_POST, 'ss_social_media', FILTER_SANITIZE_STRING );
			$ss_social_media = isset( $ss_social_media ) && ! empty( $ss_social_media ) ? $ss_social_media : '';
		} else {
			$ss_social_media = $ss_socialmedia;
		}

		if ( 'facebook' === $ss_social_media ) {
			$update_table = $wpdb->prefix . 'smartshare_facebook_post';
		} elseif ( 'twitter' === $ss_social_media ) {
			$update_table = $wpdb->prefix . 'smartshare_twitter_post';
		} elseif ( 'linkedin' === $ss_social_media ) {
			$update_table = $wpdb->prefix . 'smartshare_linkedin_post';
		} else {
			set_transient( 'ss_error_message', esc_html__( 'There was an error share your post. Please try again!', 'social-media-smart-share' ), 45 );

			return;
		}

		$get_post_exists = $wpdb->get_row( $wpdb->prepare( "SELECT * FROM $update_table WHERE id = %d", array( $ss_current_post_id ) ) );  // WPCS: unprepared SQL OK.

		if ( isset( $get_post_exists ) && ! empty( $get_post_exists ) ) {

			if ( 'shared' === $get_post_exists->share_status ) {

				set_transient( 'ss_error_message', esc_html__( 'Seems like your post already shared on your account.', 'social-media-smart-share' ), 45 );

				return;

			} else {

				if ( 'facebook' === $ss_social_media ) {
					$this->ss_facebook_instant_auto_share( $get_post_exists );
				} elseif ( 'twitter' === $ss_social_media ) {
					$this->ss_twitter_instant_auto_share( $get_post_exists );
				} elseif ( 'linkedin' === $ss_social_media ) {
					$this->ss_linkedin_instant_auto_share( $get_post_exists );
				}
			}

		} else {

			set_transient( 'ss_error_message', esc_html__( 'There was an error share your post. Please try again!', 'social-media-smart-share' ), 45 );

			return;
		}

		//		}
	}

	/**
	 * Post auto share in facebook.
	 *
	 * @param $current_post_data
	 */
	public function ss_facebook_instant_auto_share( $current_post_data ) {

		global $wpdb;

		if ( ! isset( $current_post_data ) || empty( $current_post_data ) ) {
			set_transient( 'ss_error_message', esc_html__( 'There was an error share your post. Please try again!', 'social-media-smart-share' ), 45 );

			return;
		}

		$ss_user_account_data = get_option( 'ss_user_account_data' );

		$ss_user_account_data = isset( $ss_user_account_data ) & ! empty( $ss_user_account_data ) ? $ss_user_account_data : array();

		$ss_current_post_id  = $current_post_data->id;
		$post_id             = $current_post_data->post_id;
		$fb_page_id          = $current_post_data->fb_type_id;
		$current_post_status = $current_post_data->share_status;

		if ( is_array( $ss_user_account_data ) ) {

			foreach ( $ss_user_account_data['fb_account'] as $fb_account ) {

				if ( ! empty( $fb_account['account_pages'] ) ) {

					foreach ( $fb_account['account_pages'] as $fb_page ) {

						if ( $fb_page['id'] === $fb_page_id ) {
							$page_access_token = $fb_page['access_token'];
						}
					}
				}

				if ( ! empty( $fb_account['account_groups'] ) ) {

					foreach ( $fb_account['account_groups'] as $fb_group ) {

						if ( $fb_group['id'] === $fb_page_id ) {
							$page_access_token = $fb_account['account_token'];
						}
					}
				}
			}

			$post_data = $this->get_post_data_from_post_id( $post_id );

			$post_image_url = isset( $post_data['post_image_url'] ) && ! empty( $post_data['post_image_url'] ) ? $post_data['post_image_url'] : '';

			if ( isset( $post_data['post_excerpt'] ) && ! empty( $post_data['post_excerpt'] ) ) {
				$post_description = $post_data['post_excerpt'] . ' ' . $post_data['short_url'] . PHP_EOL . wp_strip_all_tags( $post_data['hash_tag_list'] );
			} else {
				$post_description = $post_data['post_title'] . ' ' . $post_data['short_url'] . PHP_EOL . wp_strip_all_tags( $post_data['hash_tag_list'] );
			}

			$post_sharing_datail = array(
				'body' => array(
					'url'          => $post_image_url,
					'message'      => $post_description,
					'access_token' => $page_access_token,
				),
			);

			if ( isset( $post_image_url ) && ! empty( $post_image_url ) ) {

				$image_data = wp_remote_retrieve_header( wp_safe_remote_get( $post_image_url ), 'content-type' );
				if ( strpos( $image_data, 'image/' ) !== false ) {
					$page_post_response        = wp_remote_post( 'https://graph.facebook.com/' . $fb_page_id . '/photos', $post_sharing_datail );
					$page_post_response_decode = json_decode( wp_remote_retrieve_body( $page_post_response ), true );
				} else {
					$page_post_response        = wp_remote_post( 'https://graph.facebook.com/' . $fb_page_id . '/feed', $post_sharing_datail );
					$page_post_response_decode = json_decode( wp_remote_retrieve_body( $page_post_response ), true );
				}
			} else {
				$page_post_response        = wp_remote_post( 'https://graph.facebook.com/' . $fb_page_id . '/feed', $post_sharing_datail );
				$page_post_response_decode = json_decode( wp_remote_retrieve_body( $page_post_response ), true );
			}

			$update_table = $wpdb->prefix . 'smartshare_facebook_post';

			if ( 200 === $page_post_response['response']['code'] ) {
				$shared_date = date( 'Y-m-d H:i:s' );

				$wpdb->update( $update_table, array(
					'share_status' => 'shared',
					'share_date'   => $shared_date,
				), array( 'id' => $ss_current_post_id ) );
				$this->ss_update_current_schedule( 'shared', $current_post_status );

				update_post_meta( $post_id, 'ss_facebook_bitly_short_url', $post_data['short_url'] );

				set_transient( 'ss_success_message', esc_html__( 'Post successfully shared on your social account', 'social-media-smart-share' ), 45 );
			} else {
				if ( 'failed' !== $current_post_status ) {
					$failed_data = array(
						'share_status' => 'failed',
						'failed_log'   => maybe_serialize( $page_post_response_decode['error'] ),
					);
					$wpdb->update( $update_table, $failed_data, array( 'id' => $ss_current_post_id ) );
					$this->ss_update_current_schedule( 'failed', $current_post_status );
				}

				set_transient( 'ss_error_message', esc_html( $page_post_response_decode['error']['message'] ), 45 );
			}

		} else {
			set_transient( 'ss_error_message', esc_html__( 'Your account currently deactivated.', 'social-media-smart-share' ), 45 );

			return;
		}
	}

	/**
	 * Retrieve the post date from the post ID
	 *
	 * @param      $post_id
	 * @param bool $is_short_url
	 *
	 * @return array
	 */
	public function get_post_data_from_post_id( $post_id, $is_short_url = true ) {

		global $wpdb;
		$post_data                 = array();
		$smartshare_schedule_table = "{$wpdb->prefix}smartshare_schedule";
		$get_scheduler             = $wpdb->get_row( $wpdb->prepare( "SELECT * FROM `%1s` WHERE `status` = %s or `status` = %s ORDER BY id DESC", array(
			$smartshare_schedule_table,
			"in_process",
			"pause",
		) ) );
		$ss_schedule_settings      = ( isset( $get_scheduler->schedule_settings ) ) ? maybe_unserialize( $get_scheduler->schedule_settings ) : array();
		$ss_post_content           = isset( $ss_schedule_settings['post_content'] ) ? $ss_schedule_settings['post_content'] : 'post_content';
		$ss_post_hashtag           = isset( $ss_schedule_settings['post_hashtag'] ) ? $ss_schedule_settings['post_hashtag'] : 'post_hashtag';
		$ss_image_source           = isset( $ss_schedule_settings['ss_img_priority'] ) ? $ss_schedule_settings['ss_img_priority'] : 'feat_image,image_from_content,custom_field';

		$share_post_data = get_post( $post_id );

		if ( empty( $share_post_data ) ) {
			return $post_data;
		}

		$post_data['post_title'] = $share_post_data->post_title;

		if ( has_excerpt( $post_id ) ) {
			$post_data['post_excerpt'] = $share_post_data->post_excerpt;
		} else {
			$post_data['post_excerpt'] = '';
		}

		if ( 'post_content_excerpt' === $ss_post_content ) {
			if ( has_excerpt( $post_id ) ) {
				$post_data['post_excerpt'] = wp_trim_words( $share_post_data->post_excerpt, 30 );
			} else {
				$post_data['post_excerpt'] = '';
			}
		} else {
			$post_data['post_excerpt'] = $share_post_data->post_title;
		}

		if ( true === $is_short_url ) {
			$long_url               = get_the_permalink( $post_id );
			$post_data['short_url'] = $this->smart_share_remote_request( $long_url );
		}

		if ( 'post_hash_tags' === $ss_post_hashtag ) {
			$post_data['hash_tag_list'] = $this->ss_get_the_hashtag_from_wp_tag( $post_id );
		} else {
			$post_data['hash_tag_list'] = $this->ss_the_hashtag_from_wp_category( $post_id );
		}

		$ss_image_source = explode( ',', $ss_image_source );

		foreach ( $ss_image_source as $image_source ) {
			if ( ! isset( $post_data['post_image_url'] ) && empty( $post_data['post_image_url'] ) && 'feat_image' === $image_source ) {
				$local_image = wp_get_attachment_image_src( get_post_thumbnail_id( $post_id ), 'full' );
				if ( isset( $local_image ) && ! empty( $local_image ) ) {
					$image_src                   = $local_image[0];
					$post_data['post_image_url'] = isset( $image_src ) && ! empty( $image_src ) ? $image_src : null;
				}
			}

			if ( ! isset( $post_data['post_image_url'] ) && empty( $post_data['post_image_url'] ) && 'image_from_content' === $image_source ) {
				$post_content = $share_post_data->post_content;
				$ss_img_regex = '/src="([^"]*)"/';
				preg_match_all( $ss_img_regex, $post_content, $matches );
				$matches                     = array_reverse( $matches );
				$post_data['post_image_url'] = isset( $matches[0][0] ) && ! empty( $matches[0][0] ) ? $matches[0][0] : null;
			}

			if ( ! isset( $post_data['post_image_url'] ) && empty( $post_data['post_image_url'] ) && 'custom_field' === $image_source ) {
				$ss_share_post_meta = get_post_meta( $post_id );

				if ( isset( $ss_share_post_meta ) && ! empty( $ss_share_post_meta ) ) {

					foreach ( $ss_share_post_meta as $post_value ) {

						if ( filter_var( $post_value[0], FILTER_VALIDATE_URL ) ) {

							$headers = wp_remote_retrieve_header( wp_safe_remote_get( $post_value[0] ), 'content-type' );

							if ( strpos( $headers, 'image/' ) !== false ) {
								//if ( in_array( 'image/', $headers['Content-Type'] ) ) {
								$post_data['post_image_url'] = isset( $post_value[0] ) && ! empty( $post_value[0] ) ? $post_value[0] : null;
							}
						}
					}
				}
			}
		}

		return $post_data;
	}

	/**
	 * Return a Short URL.
	 *
	 * @param $long_url
	 *
	 * @return string
	 */
	public function smart_share_remote_request( $long_url ) {

		$bitly_host  = "api-ssl.bitly.com"; // $host
		$bitly_token = get_option( 'ss_bitly_api_key' );
		$bitly_token = isset( $bitly_token ) & ! empty( $bitly_token ) ? $bitly_token : null;

		if ( ! isset( $bitly_token ) && empty( $bitly_token ) ) {
			return $long_url;
		}

		$url    = "https://{$bitly_host}/v4/shorten";
		$header = array(
			"Content-type"  => "application/json",
			"Authorization" => "Bearer {$bitly_token}",
			"Host"          => $bitly_host,

		);

		$args = array(
			'method'  => 'POST',
			'headers' => $header,
			'body'    => wp_json_encode( array(
				'long_url' => $long_url,
			) ),
		);

		return $this->smart_share_remote_post( $url, $args );
	}

	/**
	 * Retrieve the remote information by url
	 *
	 * @param $url
	 * @param $args
	 *
	 * @return string
	 */
	public function smart_share_remote_post( $url, $args ) {
		$response = wp_remote_post( $url, $args );

		if ( is_wp_error( $response ) ) {
			$error_message = $response->get_error_message();

			return "Something went wrong: $error_message";
		} else {
			$body_json_result = wp_remote_retrieve_body( $response );
			$body_json_decode = json_decode( $body_json_result );

			return $body_json_decode->link;
		}
	}

	/**
	 * Retrieve the has tag from the wp tag
	 *
	 * @param $post_id
	 *
	 * @return string
	 */
	public
	function ss_get_the_hashtag_from_wp_tag(
		$post_id
	) {
		$tags          = get_the_tags( $post_id );
		$hash_tag_list = '';

		if ( isset( $tags ) && ! empty( $tags ) ) {
			foreach ( $tags as $tag ) {
				$hash_tag_list .= sprintf( '<span class="hash_tag">#%s </span>', $tag->name );
			}
		}

		return $hash_tag_list;
	}

	/**
	 * Retrieve the has tag from the category
	 *
	 * @param $post_id
	 *
	 * @return string
	 */
	public function ss_the_hashtag_from_wp_category( $post_id ) {
		$tags          = get_the_category( $post_id );
		$hash_tag_list = '';
		if ( isset( $tags ) && ! empty( $tags ) ) {
			foreach ( $tags as $tag ) {
				$hash_tag_list .= sprintf( '<span class="hash_tag">#%s </span>', $tag->name );
			}
		}

		return $hash_tag_list;
	}

	/**
	 * Insert the auto share data
	 *
	 * @param $current_post_data
	 */
	public function ss_twitter_instant_auto_share( $current_post_data ) {

		global $wpdb;

		$ss_user_account_data = get_option( 'ss_user_account_data' );
		$ss_user_account_data = isset( $ss_user_account_data ) & ! empty( $ss_user_account_data ) ? $ss_user_account_data : array();

		$ss_current_post_id  = $current_post_data->id;
		$post_id             = $current_post_data->post_id;
		$tw_account_name     = $current_post_data->tw_account;
		$current_post_status = $current_post_data->share_status;

		$post_data = $this->get_post_data_from_post_id( $post_id );

		$post_image_url = isset( $post_data['post_image_url'] ) && ! empty( $post_data['post_image_url'] ) ? $post_data['post_image_url'] : '';

		if ( isset( $post_data['post_excerpt'] ) && ! empty( $post_data['post_excerpt'] ) ) {
			$post_description = $post_data['post_excerpt'] . ' ' . $post_data['short_url'] . PHP_EOL . wp_strip_all_tags( $post_data['hash_tag_list'] );
		} else {
			$post_description = $post_data['post_title'] . ' ' . $post_data['short_url'] . PHP_EOL . wp_strip_all_tags( $post_data['hash_tag_list'] );
		}

		$tw_account_consumer_key          = '';
		$tw_account_consumer_secret       = '';
		$tw_account_consumer_token        = '';
		$tw_account_consumer_token_secret = '';

		if ( isset( $ss_user_account_data['tw_account'] ) && ! empty( $ss_user_account_data['tw_account'] ) ) {

			foreach ( $ss_user_account_data['tw_account'] as $tw_account ) {

				if ( $tw_account['account_name'] === $tw_account_name ) {
					$tw_account_consumer_key          = $tw_account['consumer_key'];
					$tw_account_consumer_secret       = $tw_account['consumer_secret'];
					$tw_account_consumer_token        = $tw_account['consumer_token'];
					$tw_account_consumer_token_secret = $tw_account['consumer_token_secret'];
				}
			}

			$connection = new TwitterOAuth( $tw_account_consumer_key, $tw_account_consumer_secret, $tw_account_consumer_token, $tw_account_consumer_token_secret );

			$connection->setTimeouts( 150, 150 );

			if ( isset( $post_image_url ) && ! empty( $post_image_url ) ) {

				$image_data = wp_remote_retrieve_header( wp_safe_remote_get( $post_image_url ), 'content-type' );

				if ( strpos( $image_data, 'image/' ) !== false ) {
					$image_file  = wp_remote_retrieve_body( wp_safe_remote_get( $post_image_url ) );
					$media       = $connection->upload( 'media/upload', [ 'media' => $image_file ], false );
					$tw_media_id = isset( $media->media_id_string ) && ! empty( $media->media_id_string ) ? $media->media_id_string : '';
				}
			}

			if ( isset( $tw_media_id ) && ! empty( $tw_media_id ) ) {
				$statues = $connection->post( "statuses/update", [
					"status"    => $post_description,
					"media_ids" => $tw_media_id,
				] );
			} else {
				$statues = $connection->post( "statuses/update", [ "status" => $post_description ] );
			}

			$tw_error    = isset( $statues->errors[0]->message ) && ! empty( $statues->errors[0]->message ) ? $statues->errors[0]->message : '';
			$tw_share_id = isset( $statues->id ) && ! empty( $statues->id ) ? $statues->id : '';

			$update_table = $wpdb->prefix . 'smartshare_twitter_post';

			if ( isset( $tw_share_id ) && ! empty( $tw_share_id ) ) {
				$shared_date = date( 'Y-m-d H:i:s' );

				$wpdb->update( $update_table, array(
					'share_status' => 'shared',
					'share_date'   => $shared_date,
				), array( 'id' => $ss_current_post_id ) );
				$this->ss_update_current_schedule( 'shared', $current_post_status );

				update_post_meta( $post_id, 'ss_twitter_bitly_short_url', $post_data['short_url'] );

				set_transient( 'ss_success_message', esc_html__( 'Post successfully shared on your social account.', 'social-media-smart-share' ), 45 );
			} else {
				if ( 'failed' !== $current_post_status ) {
					$failed_data = array(
						'share_status' => 'failed',
						'failed_log'   => esc_html( $tw_error ),
					);
					$wpdb->update( $update_table, $failed_data, array( 'id' => $ss_current_post_id ) );
					$this->ss_update_current_schedule( 'failed', $current_post_status );
				}

				set_transient( 'ss_error_message', esc_html( $tw_error ), 45 );
			}
		} else {
			set_transient( 'ss_error_message', esc_html__( 'Your account currently deactivated.', 'social-media-smart-share' ), 45 );

			return;
		}

		//wp_die();
	}

	/**
	 * Insert Linkedin auto share information
	 *
	 * @param $current_post_data
	 *
	 * @throws \LinkedIn\Exception
	 */
	public function ss_linkedin_instant_auto_share( $current_post_data ) {

		global $wpdb;

		$ss_user_account_data = get_option( 'ss_user_account_data' );
		$ss_user_account_data = isset( $ss_user_account_data ) & ! empty( $ss_user_account_data ) ? $ss_user_account_data : array();

		$ss_current_post_id = $current_post_data->id;

		$post_id             = $current_post_data->post_id;
		$linkedin_account_id = $current_post_data->linkedin_id;
		$current_post_status = $current_post_data->share_status;

		$ss_li_client_id = filter_input( INPUT_COOKIE, 'ss_linkedin_client_id', FILTER_SANITIZE_STRING ); //LinkedIn App ID
		$ss_li_client_id = isset( $ss_li_client_id ) && ! empty( $ss_li_client_id ) ? $ss_li_client_id : '';

		$ss_li_client_secret = filter_input( INPUT_COOKIE, 'ss_linkedin_client_secret', FILTER_SANITIZE_STRING ); //LinkedIn App Secret
		$ss_li_client_secret = isset( $ss_li_client_secret ) && ! empty( $ss_li_client_secret ) ? $ss_li_client_secret : '';


		$post_data = $this->get_post_data_from_post_id( $post_id );

		$post_image_url = isset( $post_data['post_image_url'] ) && ! empty( $post_data['post_image_url'] ) ? $post_data['post_image_url'] : '';

		$post_shared = 0;

		if ( isset( $post_data['post_excerpt'] ) && ! empty( $post_data['post_excerpt'] ) ) {
			$post_description = $post_data['post_excerpt'] . ' ' . $post_data['short_url'] . PHP_EOL . wp_strip_all_tags( $post_data['hash_tag_list'] );
		} else {
			$post_description = $post_data['post_title'] . ' ' . $post_data['short_url'] . PHP_EOL . wp_strip_all_tags( $post_data['hash_tag_list'] );
		}

		if ( is_array( $ss_user_account_data ) ) {

			$access_token = $ss_user_account_data['linkedin_account'][0]['account_token'];


			$client = new Client( $ss_li_client_id, $ss_li_client_secret );

			$client->setAccessToken( $access_token );

			$userUrn = 'urn:li:person:' . $linkedin_account_id;

			$image_data = wp_remote_retrieve_header( wp_safe_remote_get( $post_image_url ), 'content-type' );

			if ( strpos( $image_data, 'image/' ) !== false ) {
			}

			if ( isset( $image_data ) && ! empty ( $image_data ) ) {

				// Post Share with Image
				$registerUpload = array(
					"registerUploadRequest" => array(
						"recipes"              => array( "urn:li:digitalmediaRecipe:feedshare-image" ),
						"owner"                => $userUrn,
						"serviceRelationships" => array(
							array(
								"relationshipType" => "OWNER",
								"identifier"       => "urn:li:userGeneratedContent",
							),
						),
					),
				);

				$registerUploadResponse = $client->post( 'assets?action=registerUpload', $registerUpload );
				$uploadUrl              = $registerUploadResponse['res']['value']['uploadMechanism']['com.linkedin.digitalmedia.uploading.MediaUploadHttpRequest']['uploadUrl'];
				$imgAsset               = $registerUploadResponse['res']['value']['asset'];
				$imgPath                = $post_image_url;

				$args     = array(
					'method'    => 'PUT',
					'headers'   => array(
						'Authorization'             => 'Bearer ' . $access_token,
						'Content-Type'              => 'Content-Type: image/jpg',
						'Cache-Control'             => 'no-cache',
						'x-li-format'               => 'json',
						'X-RestLi-Protocol-Version' => '2.0.0',
					),
					'sslverify' => false,
					'body'      => wp_remote_retrieve_body( wp_safe_remote_get( $imgPath ) ),
				);
				$response = wp_remote_request( $uploadUrl, $args );

				$postContent   = array(
					"author"          => $userUrn,
					"lifecycleState"  => "PUBLISHED",
					"specificContent" => array(
						"com.linkedin.ugc.ShareContent" => array(
							"shareCommentary"    => array(
								"text" => $post_description,
							),
							"shareMediaCategory" => "IMAGE",
							"media"              => array(
								array(
									"status"      => "READY",
									"description" => array( "text" => $post_data['post_title'] ),
									"media"       => $imgAsset,
									"title"       => array(
										"text" => $post_data['post_title'],
									),
								),
							),
						),
					),
					"visibility"      => array(
						"com.linkedin.ugc.MemberNetworkVisibility" => "PUBLIC",
					),
				);
				$postResponse  = $client->post( 'ugcPosts', $postContent );
				$response_code = wp_remote_retrieve_response_code( $response );
				if ( isset( $response_code ) && ! empty( $response_code ) && isset( $postResponse ) && ! empty( $postResponse ) ) {
					if ( ( 201 === $postResponse['code'] || 200 === $postResponse['code'] ) && ( 201 === $response_code || 200 === $response_code ) ) {
						$post_shared = 1;
					} else {
						$post_shared = 0;
					}
				}
			} else {
				// Post Share without Image
				$postContent  = array(
					"author"          => $userUrn,
					"lifecycleState"  => "PUBLISHED",
					"specificContent" => array(
						"com.linkedin.ugc.ShareContent" => array(
							"shareCommentary"    => array(
								"text" => $post_description,
							),
							"shareMediaCategory" => "ARTICLE",
							"media"              => array(
								array(
									"status"      => "READY",
									"description" => array( "text" => $post_description ),
									"originalUrl" => $post_data['short_url'],
									"title"       => array(
										"text" => $post_data['post_title'],
									),
								),
							),
						),
					),
					"visibility"      => array(
						"com.linkedin.ugc.MemberNetworkVisibility" => "PUBLIC",
					),
				);
				$postResponse = $client->post( 'ugcPosts', $postContent );
				if ( isset( $postResponse ) && ! empty( $postResponse ) ) {
					if ( 201 === $postResponse['code'] || 200 === $postResponse['code'] ) {
						$post_shared = 1;
					} else {
						$post_shared = 0;
					}
				}
			}

			$update_table = $wpdb->prefix . 'smartshare_linkedin_post';

			if ( isset( $post_shared ) && ! empty( $post_shared ) && 1 === $post_shared ) {
				$shared_date = date( 'Y-m-d H:i:s' );

				$wpdb->update( $update_table, array(
					'share_status' => 'shared',
					'share_date'   => $shared_date,
				), array( 'id' => $ss_current_post_id ) );
				$this->ss_update_current_schedule( 'shared', $current_post_status );

				update_post_meta( $post_id, 'ss_linkedin_bitly_short_url', $post_data['short_url'] );

				set_transient( 'ss_success_message', esc_html__( 'Post successfully shared on your social account', 'social-media-smart-share' ), 45 );
			} else {

				if ( 'failed' !== $current_post_status ) {
					$failed_data = array(
						'share_status' => 'failed',
						'failed_log'   => maybe_serialize( $postResponse ),
					);
					$wpdb->update( $update_table, $failed_data, array( 'id' => $ss_current_post_id ) );
					$this->ss_update_current_schedule( 'failed', $current_post_status );
				}

				set_transient( 'ss_error_message', esc_html( "Something went wrong..!! Please try again later", 'social-media-smart-share' ), 45 );
			}
		}

		//wp_die();
	}

	/**
	 * Add to queue failed post..
	 */
	public function smart_share_queue_failed_post() {

		$post_action = filter_input( INPUT_POST, 'action', FILTER_SANITIZE_STRING );
		if ( isset( $post_action ) && 'ss_queue_failed_post' === $post_action ) {
			global $wpdb;

			$ss_current_post_id = filter_input( INPUT_POST, 'ss_current_post_id', FILTER_SANITIZE_NUMBER_INT );
			$ss_current_post_id = isset( $ss_current_post_id ) && ! empty( $ss_current_post_id ) ? $ss_current_post_id : '';

			$ss_social_media = filter_input( INPUT_POST, 'ss_social_media', FILTER_SANITIZE_STRING );
			$ss_social_media = isset( $ss_social_media ) && ! empty( $ss_social_media ) ? $ss_social_media : '';

			if ( 'facebook' === $ss_social_media ) {
				$update_table = $wpdb->prefix . 'smartshare_facebook_post';
			} elseif ( 'twitter' === $ss_social_media ) {
				$update_table = $wpdb->prefix . 'smartshare_twitter_post';
			} elseif ( 'linkedin' === $ss_social_media ) {
				$update_table = $wpdb->prefix . 'smartshare_linkedin_post';
			} else {
				set_transient( 'ss_error_message', esc_html__( 'There was an error in adding your post to queue. Please try again!' ), 45 );

				return;
			}
			$get_post_exists = $wpdb->get_row( $wpdb->prepare( "SELECT * FROM $update_table WHERE share_status = %s and id = %d", array(
				"failed",
				$ss_current_post_id,
			) ) ); // WPCS: unprepared SQL OK.

			if ( isset( $get_post_exists ) && ! empty( $get_post_exists ) ) {
				$failed_schedule = $wpdb->update( $update_table, array( 'share_status' => 'pending' ), array( 'id' => $ss_current_post_id ) );
				if ( false !== $failed_schedule ) {
					$this->ss_update_current_schedule( 'pending', $get_post_exists->share_status );
					set_transient( 'ss_success_message', esc_html__( 'Post successfully added to queue!', 'social-media-smart-share' ), 45 );
				} else {
					set_transient( 'ss_error_message', esc_html__( 'There was an error in adding your post to queue. Please try again!', 'social-media-smart-share' ), 45 );
				}
			} else {
				set_transient( 'ss_error_message', esc_html__( 'There was an error in adding your post to queue. Please try again!', 'social-media-smart-share' ), 45 );

				return;
			}
		}
	}
}
