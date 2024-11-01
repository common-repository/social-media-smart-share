<?php
if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if access directly
}
/**
 * Fired during plugin activation
 *
 * @link       https://www.multidots.com/
 * @since      1.0.0
 *
 * @package    Smartshare
 * @subpackage Smartshare/includes
 */

/**
 * Fired during plugin activation.
 *
 * This class defines all code necessary to run during the plugin's activation.
 *
 * @since      1.0.0
 * @package    Smartshare
 * @subpackage Smartshare/includes
 * @author     Multidots <inquiry@multidots.com>
 */
class Smart_Share_Activator {

	/**
	 * Short Description. (use period)
	 *
	 * Long Description.
	 *
	 * @since    1.0.0
	 */
	public static function ss_activate() {
		self::smart_share_create_tables();
		self::smart_share_create_db();
		/*self::smart_share_start_auto_cron();*/
		add_option('ss_do_activation_redirect', true);
	}

	/**
	 * Creates custom tables
	 *
	 * @global object $wpdb Used to query the database using the WordPress Database API
	 */
	public static function smart_share_create_tables() {
		global $wpdb;

		require_once( ABSPATH . 'wp-admin/includes/upgrade.php' );
		$charset_collate = $wpdb->get_charset_collate();

		$smartshare_schedule = $wpdb->prefix . 'smartshare_schedule';
		if ( ! static::table_exists( $smartshare_schedule ) ) {
			$sql = "CREATE TABLE {$smartshare_schedule} (
			id int(20) NOT NULL AUTO_INCREMENT,
			total_posts bigint(20) NULL DEFAULT 0,
			post_in_queue bigint(20) NULL DEFAULT 0,
			post_in_shared bigint(20) NULL DEFAULT 0,
			post_in_skipped bigint(20) NULL DEFAULT 0,
			post_in_failed bigint(20) NULL DEFAULT 0,			
			schedule_settings longtext NOT NULL,			
			start_date timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
			end_date timestamp NULL DEFAULT '0000-00-00 00:00:00',
			status varchar(50) NOT NULL DEFAULT 'in_process',
			PRIMARY KEY  (id)
			) {$charset_collate};";

			dbDelta( $sql );
		}

		$smartshare_facebook_post = $wpdb->prefix . 'smartshare_facebook_post';
		if ( ! static::table_exists( $smartshare_facebook_post ) ) {
			$sql = "CREATE TABLE {$smartshare_facebook_post}(
			id int(20) NOT NULL AUTO_INCREMENT PRIMARY KEY,
			scheduler_id int(20) NOT NULL,
			post_id bigint(20) NOT NULL,
			fb_account varchar(50) NOT NULL,
			fb_type varchar(50) NOT NULL,
			fb_type_id varchar(50) NOT NULL,
			share_status varchar(50) NOT NULL DEFAULT 'pending',			
			share_date timestamp NULL DEFAULT '0000-00-00 00:00:00',	
			failed_log longtext NULL,									
			FOREIGN KEY (scheduler_id) REFERENCES " . $smartshare_schedule . "(id) ON DELETE CASCADE
			) {$charset_collate};";

			dbDelta( $sql );
		}

		$smartshare_twitter_post = $wpdb->prefix . 'smartshare_twitter_post';
		if ( ! static::table_exists( $smartshare_twitter_post ) ) {
			$sql = "CREATE TABLE {$smartshare_twitter_post}(
			id int(20) NOT NULL AUTO_INCREMENT PRIMARY KEY,
			scheduler_id int(20) NOT NULL,
			post_id bigint(20) NOT NULL,
			tw_account varchar(50) NOT NULL,	
			tw_id varchar(255) NOT NULL,				
			share_status varchar(50) NOT NULL DEFAULT 'pending',			
			share_date timestamp NULL DEFAULT '0000-00-00 00:00:00',	
			failed_log longtext NULL,									
			FOREIGN KEY (scheduler_id) REFERENCES " . $smartshare_schedule . "(id) ON DELETE CASCADE
			) {$charset_collate};";

			dbDelta( $sql );
		}

		$smartshare_linkedin_post = $wpdb->prefix . 'smartshare_linkedin_post';
		if ( ! static::table_exists( $smartshare_linkedin_post ) ) {
			$sql = "CREATE TABLE {$smartshare_linkedin_post}(
			id int(20) NOT NULL AUTO_INCREMENT PRIMARY KEY,
			scheduler_id int(20) NOT NULL,
			post_id bigint(20) NOT NULL,
			linkedin_account varchar(50) NOT NULL,			
			linkedin_id varchar(255) NOT NULL,			
			share_status varchar(50) NOT NULL DEFAULT 'pending',			
			share_date timestamp NULL DEFAULT '0000-00-00 00:00:00',	
			failed_log longtext NULL,									
			FOREIGN KEY (scheduler_id) REFERENCES " . $smartshare_schedule . "(id) ON DELETE CASCADE
			) {$charset_collate};";

			dbDelta( $sql );
		}

		$smartshare_post_time = $wpdb->prefix . 'smartshare_post_time';
		if ( ! static::table_exists( $smartshare_post_time ) ) {
			$sql = "CREATE TABLE {$smartshare_post_time} (
			id int(20) NOT NULL AUTO_INCREMENT,
			social_account varchar(20) NOT NULL,
			social_time longtext NOT NULL,
			PRIMARY KEY  (id)
			) {$charset_collate};";

			dbDelta( $sql );
		}
		set_transient('ss_plugin_redirect_to_started',30);
	}

	/**
	 * Check if the given table exists
	 *
	 * @param string $table The table name.
	 *
	 * @return bool If the table name exists.
	 */
	public static function table_exists( $table ) {
		global $wpdb;

		$table = sanitize_text_field( $table );

		return $wpdb->get_var( $wpdb->prepare( "SHOW TABLES LIKE '%s'", $table ) ) === $table;
	}

	/**
	 *  Creates custom database.
	 */
	public static function smart_share_create_db() {
		global $wpdb;
		$smartshare_post_time          = $wpdb->prefix . 'smartshare_post_time';
		$social_time_array['facebook'] = array( '12:30', '13:00', '13:30', '14:00', '14:30' );
		$social_time_array['twitter']  = array( '12:30', '13:00', '13:30', '14:00', '14:30' );
		$social_time_array['linkedin'] = array( '12:00', '15:00', '15:30', '16:00', '16:30' );

		$ss_post_time_flag = get_option( 'smart_share_post_time' );

		if ( ! empty( $social_time_array ) && '1' !== $ss_post_time_flag ) {
			foreach ( $social_time_array as $key => $value ) {

				$wpdb->insert( $smartshare_post_time, array(
						'social_account' => $key,
						'social_time'    => maybe_serialize( $value ),

					), array( '%s', '%s' ) );

			}
			update_option( 'smart_share_post_time', '1', false );
		}
	}
}
