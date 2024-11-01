<?php
if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if access directly
}
global $wpdb;
$smartshare_schedule_table = $wpdb->prefix . 'smartshare_schedule';
$smartshare_facebook_post  = $wpdb->prefix . 'smartshare_facebook_post';
$smartshare_twitter_post   = $wpdb->prefix . 'smartshare_twitter_post';
$smartshare_linkedin_post  = $wpdb->prefix . 'smartshare_linkedin_post';

$status_check = "pending";

$queue_post_list = $wpdb->get_results( $wpdb->prepare( "
                                    SELECT * FROM(
									SELECT sss.id,sss.total_posts,sss.post_in_queue, sss.post_in_shared,sss.post_in_skipped, sss.post_in_failed, sss.schedule_settings, sfp.post_id, sfp.fb_account as facebook_account_value, NULL as twitter_account_value, NULL as linkin_account_value, sfp.id AS fb_row_id, NULL AS twitter_row_id, NULL AS linkedin_row_id, sfp.fb_type_id AS fb_type_id, NULL AS tw_id, NULL AS linkedin_id, sfp.fb_type AS fb_type, sfp.share_status AS share_status, sfp.share_date AS share_date FROM %1s sss
									RIGHT JOIN %1s sfp ON sss.id = sfp.scheduler_id
									WHERE sss.status = %s and sfp.share_status = %s
									UNION ALL SELECT sss.id,sss.total_posts,sss.post_in_queue, sss.post_in_shared,sss.post_in_skipped, sss.post_in_failed, sss.schedule_settings, stp.post_id, NULL as facebook_account_value, stp.tw_account as twitter_account_value, NULL as linkin_account_value, NULL AS fb_row_id, stp.id AS twitter_row_id, NULL AS linkedin_row_id, NULL AS fb_type_id, stp.tw_id AS tw_id, NULL AS linkedin_id, NULL AS fb_type, stp.share_status AS share_status, stp.share_date AS share_date FROM %1s sss
									RIGHT JOIN %1s stp ON sss.id = stp.scheduler_id
									WHERE sss.status = %s and stp.share_status = %s
									UNION ALL SELECT sss.id,sss.total_posts,sss.post_in_queue, sss.post_in_shared,sss.post_in_skipped, sss.post_in_failed, sss.schedule_settings, slp.post_id,  NULL as facebook_account_value, NULL as twitter_account_value, slp.linkedin_account as linkin_account_value, NULL AS fb_row_id, NULL AS twitter_row_id, slp.id AS linkedin_row_id, NULL AS fb_type_id, NULL AS tw_id, slp.linkedin_id AS linkedin_id, NULL AS fb_type, slp.share_status AS share_status, slp.share_date AS share_date FROM %1s sss
									RIGHT JOIN %1s slp ON sss.id = slp.scheduler_id
									WHERE sss.status = %s and slp.share_status = %s) t ORDER BY post_id", array(
	$smartshare_schedule_table,
	$smartshare_facebook_post,
	"in_process",
	$status_check,
	$smartshare_schedule_table,
	$smartshare_twitter_post,
	"in_process",
	$status_check,
	$smartshare_schedule_table,
	$smartshare_linkedin_post,
	"in_process",
	$status_check,
) ) );

if ( isset( $queue_post_list ) && ! empty( $queue_post_list ) ) {

	$smartshare_post_time = "{$wpdb->prefix}smartshare_post_time";
	$social_time_list     = $wpdb->get_results( $wpdb->prepare( "SELECT * FROM %1s", $smartshare_post_time ), ARRAY_A );

	$social_time_array = array();

	if ( isset( $social_time_list ) && ! empty( $social_time_list ) ) {
		foreach ( $social_time_list as $social_time ) {
			$time_sheet_list                                     = isset( $social_time['social_time'] ) && ! empty( $social_time['social_time'] ) ? $social_time['social_time'] : '';
			$social_time_array[ $social_time['social_account'] ] = maybe_unserialize( $time_sheet_list );
		}
	}

	$smartshare_schedule_table = "{$wpdb->prefix}smartshare_schedule";
	$get_scheduler             = $wpdb->get_row( $wpdb->prepare( "SELECT * FROM %1s WHERE `status` = %s ORDER BY id DESC", array(
		$smartshare_schedule_table,
		"in_process",
	) ) );

	$ss_schedule_id       = ( isset( $get_scheduler->id ) ) ? $get_scheduler->id : '';
	$ss_schedule_settings = ( isset( $get_scheduler->schedule_settings ) ) ? maybe_unserialize( $get_scheduler->schedule_settings ) : array();
	$final_post_per_day   = ( isset( $ss_schedule_settings['final_post_per_day'] ) ) ? $ss_schedule_settings['final_post_per_day'] : '';
	$ss_post_per_day      = ( isset( $ss_schedule_settings['ss_post_per_day'] ) ) ? $ss_schedule_settings['ss_post_per_day'] : '';
	$ss_per_slot_limit    = $final_post_per_day / $ss_post_per_day;
	$ss_cron_share_count  = 1;

	$utc = ( isset( $ss_schedule_settings['ss_timezone'] ) ) ? str_replace( 'UTC', '', $ss_schedule_settings['ss_timezone'] ) : '+0';

	$today_formatted = gmdate( "H:i", time() + 3600 * ( $utc + date( "I" ) ) );
	$today_date      = gmdate( "d-m-Y", time() + 3600 * ( $utc + date( "I" ) ) );
	$yesterday_date  = gmdate( "d-m-Y", time() - 60 * 60 * 24 + 3600 * ( $utc + date( "I" ) ) );

	$ss_shared_post_today_count = get_option( 'ss_shared_post_today_count_' . $today_date );
	$ss_shared_post_today_count = isset( $ss_shared_post_today_count ) ? $ss_shared_post_today_count : 0;
	if ( isset( $ss_shared_post_today_count ) && $ss_shared_post_today_count > 0 ) {
		$ss_shared_post_today_count = isset( $ss_shared_post_today_count ) ? $ss_shared_post_today_count : 0;
	} else {
		delete_option( 'ss_shared_post_today_count_' . $yesterday_date );
		update_option( 'ss_shared_post_today_count_' . $today_date, 0 );
		$ss_shared_post_today_count = 0;
	}

	foreach ( $queue_post_list as $queue_post_data ) {

		if ( isset( $queue_post_data->facebook_account_value ) && ! empty( $queue_post_data->facebook_account_value ) ) {
			$update_table         = $wpdb->prefix . 'smartshare_facebook_post';
			$post_share_time_list = $social_time_array['facebook'];
			$social_media         = 'facebook';
			$ss_row_id            = isset( $queue_post_data->fb_row_id ) & ! empty( $queue_post_data->fb_row_id ) ? $queue_post_data->fb_row_id : 0;
		} else if ( isset( $queue_post_data->twitter_account_value ) && ! empty( $queue_post_data->twitter_account_value ) ) {
			$update_table         = $wpdb->prefix . 'smartshare_twitter_post';
			$post_share_time_list = $social_time_array['twitter'];
			$social_media         = 'twitter';
			$ss_row_id            = isset( $queue_post_data->twitter_row_id ) & ! empty( $queue_post_data->twitter_row_id ) ? $queue_post_data->twitter_row_id : 0;
		} else if ( isset( $queue_post_data->linkin_account_value ) && ! empty( $queue_post_data->linkin_account_value ) ) {
			$update_table         = $wpdb->prefix . 'smartshare_linkedin_post';
			$post_share_time_list = $social_time_array['linkedin'];
			$social_media         = 'linkedin';
			$ss_row_id            = isset( $queue_post_data->linkedin_row_id ) & ! empty( $queue_post_data->linkedin_row_id ) ? $queue_post_data->linkedin_row_id : 0;
		}

		$time_between_slots_boolean = in_array( $today_formatted, $post_share_time_list ) ? true : false;
		

		if ( 'pending' === $queue_post_data->share_status && $final_post_per_day > $ss_shared_post_today_count && $ss_per_slot_limit >= $ss_cron_share_count && $time_between_slots_boolean ) { //&& $time_between_slots_boolean

			$ss_shared_post_today_count = absint( $ss_shared_post_today_count ) + 1;
			update_option( 'ss_shared_post_today_count_' . $today_date, $ss_shared_post_today_count );
			$this->smart_share_ss_instant_share_post( $ss_row_id, $social_media );
			delete_transient( "ss_success_message" );
			delete_transient( "ss_error_message" );

		}
		$ss_cron_share_count ++;
	}
}
echo esc_html_e( 'Cron ', 'social-media-smart-share' );
exit;
