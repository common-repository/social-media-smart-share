<?php
if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if access directly
}

global $wpdb;

$smartshare_schedule_table = $wpdb->prefix . 'smartshare_schedule';
$cache_key                 = "ss_schedular_data";
if ( false === wp_cache_get( $cache_key ) ) {
	$schedular_data = $wpdb->get_results( $wpdb->prepare( "SELECT * FROM %1s ORDER BY id DESC", $smartshare_schedule_table ) );
	wp_cache_set( $cache_key, $schedular_data );
} else {
	$schedular_data = wp_cache_get( $cache_key );
}
?>
<div class="wrap">
	<?php require_once plugin_dir_path( __FILE__ ) . 'header/plugin-header.php'; ?>
	<div class="ss-container">

		<div class="ss-data-main">
			<div class="ss-data-inner">
				<?php
				if ( ! empty( $schedular_data ) && is_array( $schedular_data ) ) {
					foreach ( $schedular_data as $schedular_data_value ) {
						$queue_id = ( isset( $schedular_data_value->id ) ) ? 'SS' . str_pad( $schedular_data_value->id, 2, '0', STR_PAD_LEFT ) : '';
						?>
						<div class="ss-data-row">
							<div class="ss-data-col">
								<span class="ss-title"><?php esc_html_e( "que id", "social-media-smart-share" ); ?></span>
								<span class="ss-data"><?php echo esc_html( $queue_id ); ?></span>
							</div>
							<?php if ( isset( $schedular_data_value->total_posts ) ) { ?>
								<div class="ss-data-col">
									<span class="ss-title"><?php esc_html_e( "total sharable post", "social-media-smart-share" ); ?></span>
									<span class="ss-data"><?php echo esc_html( $schedular_data_value->total_posts ); ?></span>
								</div>
							<?php }
							if ( isset( $schedular_data_value->post_in_queue ) ) { ?>
								<div class="ss-data-col">
									<span class="ss-title"><?php esc_html_e( "post in queue", "social-media-smart-share" ); ?></span>
									<span class="ss-data"><?php echo esc_html( $schedular_data_value->post_in_queue ); ?></span>
								</div>
							<?php }
							if ( isset( $schedular_data_value->post_in_shared ) ) { ?>
								<div class="ss-data-col">
									<span class="ss-title"><?php esc_html_e( "shared", "social-media-smart-share" ); ?></span>
									<span class="ss-data"><?php echo esc_html( $schedular_data_value->post_in_shared ); ?></span>
								</div>
							<?php }
							if ( isset( $schedular_data_value->post_in_failed ) ) { ?>
								<div class="ss-data-col">
									<span class="ss-title"><?php esc_html_e( "Failed", "social-media-smart-share" ); ?></span>
									<span class="ss-data"><?php echo esc_html( $schedular_data_value->post_in_failed ); ?></span>
								</div>
							<?php }
							if ( isset( $schedular_data_value->start_date ) ) {
								$start_date = date( 'd-M', strtotime( $schedular_data_value->start_date ) );
								?>
								<div class="ss-data-col">
									<span class="ss-title"><?php esc_html_e( "start date", "social-media-smart-share" ); ?></span>
									<span class="ss-data"><?php echo esc_html( $start_date ); ?></span>
								</div>
							<?php }
							if ( isset( $schedular_data_value->end_date ) ) {
								$end_date = date( 'd-M', strtotime( $schedular_data_value->end_date ) );
								?>
								<div class="ss-data-col">
									<span class="ss-title"><?php esc_html_e( "end date", "social-media-smart-share" ); ?></span>
									<span class="ss-data"><?php echo esc_html( $end_date ); ?></span>
								</div>
							<?php }
							if ( isset( $schedular_data_value->status ) ) {
								$schedular_status = str_replace( '_', ' ', ucwords( $schedular_data_value->status ) );
								?>
								<div class="ss-data-col">
									<span class="ss-title"><?php esc_html_e( "status", "social-media-smart-share" ); ?></span>
									<span class="ss-data <?php if ( 'In process' === $schedular_status || 'Pause' === $schedular_status ) {
										echo 'ss-orange';
									} else {
										echo 'ss-green';
									} ?>"><?php echo esc_html( $schedular_status ); ?></span>
								</div>
							<?php }

							if ( isset( $schedular_data_value->status ) && "pause" === $schedular_data_value->status ) {
								$pause_resume_btn_text  = __( "Resume Sharing", "social-media-smart-share" );
								$pause_resume_btn_class = 'resume_btn';
							} else {
								$pause_resume_btn_text  = __( "Pause Sharing", "social-media-smart-share" );
								$pause_resume_btn_class = 'pause_btn';
							}
							?>
							<input type="hidden" id="curr_scheduler" value="<?php echo ( isset( $schedular_data_value->id ) ) ? esc_html( $schedular_data_value->id ) : ''; ?>">
							<input type="hidden" id="curr_scheduler_status"
							       value="<?php echo ( isset( $schedular_data_value->status ) ) ? esc_html( $schedular_data_value->status ) : ''; ?>">
							<div class="ss-data-col ss-control-btn">
								<?php if ( isset( $schedular_data_value->status ) && ! empty( $schedular_data_value->status ) && 'completed' !== $schedular_data_value->status ) { ?>
									<a href="#ss-pause-resume-queue"
									   class="button button-primary resume_sharing ss_pause_resume_schedule_btn <?php echo esc_attr( $pause_resume_btn_class ); ?>"><?php echo esc_html_e( $pause_resume_btn_text ); ?></a>
									<a href="#ss-delete-queue"
									   class="button button-secondary delete_queue ss_delete_schedule_btn"><?php esc_html_e( "delete queue", "social-media-smart-share" ); ?></a>
								<?php } ?>
							</div>
						</div>
						<div id="ss-pause-resume-queue" class="ss-confirm-popup" style="display:none;">
							<p><?php
								if ( isset( $schedular_data_value->status ) && "in_process" === $schedular_data_value->status ) {
									$pause_resume_dec_text = esc_html__( "Are you sure you want to pause this Queue?", 'social-media-smart-share' );
								} else {
									$pause_resume_dec_text = esc_html__( "Are you sure you want to resume this Queue?", 'social-media-smart-share' );
								}
								esc_html_e( $pause_resume_dec_text );
								?></p>
							<div class="ss_pause_resumes_conf_btns">
								<input type="button" id="ss_pause_resume_queue_yes" class="button button-primary" value="<?php echo esc_attr( 'Yes' ); ?>">
								<input type="button" id="ss_pause_resume_queue_no" class="button button-secondary" value="<?php echo esc_attr( 'No' ); ?>">
							</div>
						</div>
						<div id="ss-delete-queue" class="ss-confirm-popup" style="display:none;">
							<p><?php esc_html_e( "Are you sure you want to delete this Queue?",'social-media-smart-share' ); ?></p>
							<div class="ss_del_conf_btns">
								<input type="button" id="ss_del_queue_yes" class="button button-primary" value="<?php echo esc_attr( 'Yes' ); ?>">
								<input type="button" id="ss_del_queue_no" class="button button-secondary" value="<?php echo esc_attr( 'No' ); ?>">
							</div>
						</div>
						<?php

					}
				} else {
					?>
					<div class="ss-data-row ss_no_schedule">
						<h4><?php esc_html_e( 'Sorry! No scheduled social media post data found.', 'social-media-smart-share' ); ?></h4>
						<?php

						$allowed_tags        = array(
							'p' => array(),
							'a' => array(
								'href' => array(),
								'target' => array(),
							),
							'b' => array(),
						);
						printf(
							wp_kses( __('<p>Here you can see the list of scheduled social media post queue. For that, you have to follow <a target="_blank" href="%s"><b>step by step process</b></a>.</p>', 'social-media-smart-share'),
								$allowed_tags
							),esc_url(admin_url( '/admin.php?page=smartshare-started' ))
						);
						?>
					</div>
					<?php
				}
				?>

			</div>
		</div><!-- .ss-data -->

	</div>
	<?php require_once plugin_dir_path( __FILE__ ) . 'header/plugin-sidebar.php'; ?>
</div>
