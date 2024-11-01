<?php
if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if access directly
}
global $wpdb;

$ss_current_page      = filter_input( INPUT_GET, 'page', FILTER_SANITIZE_STRING );
$ss_queue_post_page   = admin_url( 'admin.php?page=smartshare-history' );
$ss_skipped_post_page = admin_url( 'admin.php?page=smartshare-skipped' );
$ss_shared_post_page  = admin_url( 'admin.php?page=smartshare-shared' );
$ss_failed_post_page  = admin_url( 'admin.php?page=smartshare-failed' );

$smartshare_schedule_table = "{$wpdb->prefix}smartshare_schedule";
$smartshare_facebook_post  = $wpdb->prefix . 'smartshare_facebook_post';
$smartshare_twitter_post   = $wpdb->prefix . 'smartshare_twitter_post';
$smartshare_linkedin_post  = $wpdb->prefix . 'smartshare_linkedin_post';


if ( 'smartshare-history' === $ss_current_page ) {
	$status_check = "pending";
	$ss_order_by  = 'post_id';
} else if ( 'smartshare-skipped' === $ss_current_page ) {
	$status_check = "skipped";
	$ss_order_by  = 'post_id';
} else if ( 'smartshare-shared' === $ss_current_page ) {
	$status_check = "shared";
	$ss_order_by  = 'unix_timestamp(share_date) DESC';
} else if ( 'smartshare-failed' === $ss_current_page ) {
	$status_check = "failed";
	$ss_order_by  = 'post_id';
}

$post_list_count = $wpdb->get_results( $wpdb->prepare( "
                                       SELECT post_in_queue, post_in_shared, post_in_skipped, post_in_failed , schedule_settings, total_posts FROM %1s
                                       WHERE ( status = %s OR status = %s)", array(
	$smartshare_schedule_table,
	"in_process",
	"pause",
) ) );

$ss_schedule_settings     = ( isset( $post_list_count[0]->schedule_settings ) ) ? maybe_unserialize( $post_list_count[0]->schedule_settings ) : array();
$ss_total_scheduled_posts = ( isset( $post_list_count[0]->total_posts ) ) ? $post_list_count[0]->total_posts : 0;


$ss_total_scheduled_posts_sql = $wpdb->get_row( $wpdb->prepare( "
                                    SELECT COUNT(*) as ss_total_scheduled_posts FROM(
									SELECT sss.id,sss.total_posts,sss.post_in_queue, sss.post_in_shared,sss.post_in_skipped, sss.post_in_failed, sss.schedule_settings, sfp.post_id, sfp.fb_account as facebook_account_value, NULL as twitter_account_value, NULL as linkin_account_value, sfp.id AS fb_row_id, NULL AS twitter_row_id, NULL AS linkedin_row_id, sfp.fb_type_id AS fb_type_id, NULL AS tw_id, NULL AS linkedin_id, sfp.fb_type AS fb_type, sfp.share_status AS share_status, sfp.share_date AS share_date FROM %1s sss
									RIGHT JOIN %1s sfp ON sss.id = sfp.scheduler_id
									WHERE ( sss.status = %s or sss.status = %s ) and sfp.share_status = %s
									UNION ALL SELECT sss.id,sss.total_posts,sss.post_in_queue, sss.post_in_shared,sss.post_in_skipped, sss.post_in_failed, sss.schedule_settings, stp.post_id, NULL as facebook_account_value, stp.tw_account as twitter_account_value, NULL as linkin_account_value, NULL AS fb_row_id, stp.id AS twitter_row_id, NULL AS linkedin_row_id, NULL AS fb_type_id, stp.tw_id AS tw_id, NULL AS linkedin_id, NULL AS fb_type, stp.share_status AS share_status, stp.share_date AS share_date FROM %1s sss
									RIGHT JOIN %1s stp ON sss.id = stp.scheduler_id
									WHERE ( sss.status = %s or sss.status = %s ) and stp.share_status = %s
									UNION ALL SELECT sss.id,sss.total_posts,sss.post_in_queue, sss.post_in_shared,sss.post_in_skipped, sss.post_in_failed, sss.schedule_settings, slp.post_id,  NULL as facebook_account_value, NULL as twitter_account_value, slp.linkedin_account as linkin_account_value, NULL AS fb_row_id, NULL AS twitter_row_id, slp.id AS linkedin_row_id, NULL AS fb_type_id, NULL AS tw_id, slp.linkedin_id AS linkedin_id, NULL AS fb_type, slp.share_status AS share_status, slp.share_date AS share_date FROM %1s sss
									RIGHT JOIN %1s slp ON sss.id = slp.scheduler_id
									WHERE ( sss.status = %s or sss.status = %s ) and slp.share_status = %s) t ORDER BY post_id", array(
	$smartshare_schedule_table,
	$smartshare_facebook_post,
	"in_process",
	"pause",
	$status_check,
	$smartshare_schedule_table,
	$smartshare_twitter_post,
	"in_process",
	"pause",
	$status_check,
	$smartshare_schedule_table,
	$smartshare_linkedin_post,
	"in_process",
	"pause",
	$status_check,
) ) );

$ss_paged = filter_input( INPUT_GET, 'paged', FILTER_SANITIZE_NUMBER_INT );
$pageno   = isset( $ss_paged ) && ! empty( $ss_paged ) ? $ss_paged : 1;

$ss_total_scheduled_posts = $ss_total_scheduled_posts_sql->ss_total_scheduled_posts;

$no_of_records_per_page = 10;
$offset                 = ( $pageno - 1 ) * $no_of_records_per_page;
$ss_total_pages         = ceil( $ss_total_scheduled_posts / $no_of_records_per_page );

$queue_post_list = $wpdb->get_results( $wpdb->prepare( "
                                    SELECT * FROM(
									SELECT sss.id,sss.total_posts,sss.post_in_queue, sss.post_in_shared,sss.post_in_skipped, sss.post_in_failed, sss.schedule_settings, sfp.post_id, sfp.fb_account as facebook_account_value, NULL as twitter_account_value, NULL as linkin_account_value, sfp.id AS fb_row_id, NULL AS twitter_row_id, NULL AS linkedin_row_id, sfp.fb_type_id AS fb_type_id, NULL AS tw_id, NULL AS linkedin_id, sfp.fb_type AS fb_type, sfp.share_status AS share_status, sfp.share_date AS share_date FROM %1s sss
									RIGHT JOIN %1s sfp ON sss.id = sfp.scheduler_id
									WHERE ( sss.status = %s or sss.status = %s ) and sfp.share_status = %s
									UNION ALL SELECT sss.id,sss.total_posts,sss.post_in_queue, sss.post_in_shared,sss.post_in_skipped, sss.post_in_failed, sss.schedule_settings, stp.post_id, NULL as facebook_account_value, stp.tw_account as twitter_account_value, NULL as linkin_account_value, NULL AS fb_row_id, stp.id AS twitter_row_id, NULL AS linkedin_row_id, NULL AS fb_type_id, stp.tw_id AS tw_id, NULL AS linkedin_id, NULL AS fb_type, stp.share_status AS share_status, stp.share_date AS share_date FROM %1s sss
									RIGHT JOIN %1s stp ON sss.id = stp.scheduler_id
									WHERE ( sss.status = %s or sss.status = %s ) and stp.share_status = %s
									UNION ALL SELECT sss.id,sss.total_posts,sss.post_in_queue, sss.post_in_shared,sss.post_in_skipped, sss.post_in_failed, sss.schedule_settings, slp.post_id,  NULL as facebook_account_value, NULL as twitter_account_value, slp.linkedin_account as linkin_account_value, NULL AS fb_row_id, NULL AS twitter_row_id, slp.id AS linkedin_row_id, NULL AS fb_type_id, NULL AS tw_id, slp.linkedin_id AS linkedin_id, NULL AS fb_type, slp.share_status AS share_status, slp.share_date AS share_date FROM %1s sss
									RIGHT JOIN %1s slp ON sss.id = slp.scheduler_id
									WHERE ( sss.status = %s or sss.status = %s ) and slp.share_status = %s) t ORDER BY %1s LIMIT %1s, %1s", array(
	$smartshare_schedule_table,
	$smartshare_facebook_post,
	"in_process",
	"pause",
	$status_check,
	$smartshare_schedule_table,
	$smartshare_twitter_post,
	"in_process",
	"pause",
	$status_check,
	$smartshare_schedule_table,
	$smartshare_linkedin_post,
	"in_process",
	"pause",
	$status_check,
	$ss_order_by,
	$offset,
	$no_of_records_per_page,
) ) );

if ( empty( $post_list_count ) ) {
	$queue_post_list_count   = 0;
	$skipped_post_list_count = 0;
	$shared_post_list_count  = 0;
	$failed_post_list_count  = 0;
}
foreach ( $post_list_count as $queue_post_data ) {
	$queue_post_list_count   = isset( $queue_post_data->post_in_queue ) & ! empty( $queue_post_data->post_in_queue ) ? $queue_post_data->post_in_queue : 0;
	$skipped_post_list_count = isset( $queue_post_data->post_in_skipped ) & ! empty( $queue_post_data->post_in_skipped ) ? $queue_post_data->post_in_skipped : 0;
	$shared_post_list_count  = isset( $queue_post_data->post_in_shared ) & ! empty( $queue_post_data->post_in_shared ) ? $queue_post_data->post_in_shared : 0;
	$failed_post_list_count  = isset( $queue_post_data->post_in_failed ) & ! empty( $queue_post_data->post_in_failed ) ? $queue_post_data->post_in_failed : 0;
}

$ss_user_account_data = get_option( 'ss_user_account_data' );
$ss_user_account_data = isset( $ss_user_account_data ) & ! empty( $ss_user_account_data ) ? $ss_user_account_data : array();
?>
<ul>
	<li class="<?php echo ( 'smartshare-history' === $ss_current_page ) ? 'active' : ''; ?>">
		<a href="<?php echo esc_url( $ss_queue_post_page ); ?>"
		   data-releted="tab-1"><?php printf( esc_html__( 'Queue Post (%s)' ), esc_html( $queue_post_list_count ) ); ?></a>
	</li>
	<li class="<?php echo ( 'smartshare-skipped' === $ss_current_page ) ? 'active' : ''; ?>">
		<a href="<?php echo esc_url( $ss_skipped_post_page ); ?>"
		   data-releted="tab-2"><?php printf( esc_html__( 'skipped (%s)' ), esc_html( $skipped_post_list_count ) ); ?></a>
	</li>
	<li class="<?php echo ( 'smartshare-shared' === $ss_current_page ) ? 'active' : ''; ?>">
		<a href="<?php echo esc_url( $ss_shared_post_page ); ?>"
		   data-releted="tab-3"><?php printf( esc_html__( 'Shared Post (%s)' ), esc_html( $shared_post_list_count ) ); ?></a>
	</li>
	<li class="<?php echo ( 'smartshare-failed' === $ss_current_page ) ? 'active' : ''; ?>">
		<a href="<?php echo esc_url( $ss_failed_post_page ); ?>"
		   data-releted="tab-3"><?php printf( esc_html__( 'failed (%s)' ), esc_html( $failed_post_list_count ) ); ?></a>
	</li>
</ul>
