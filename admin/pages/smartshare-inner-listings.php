<?php
if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if access directly
}
$allowed_tags = wp_kses_allowed_html( 'post' );
foreach ( $queue_post_list as $queue_post_data ) {

	$ss_queue_post_id = $queue_post_data->post_id;
	$row_post_id      = '';
	if ( isset( $queue_post_data->facebook_account_value ) && ! empty( $queue_post_data->facebook_account_value ) ) {
		$post_share_platform = "facebook";
		$row_post_id         = $queue_post_data->fb_row_id;
		$social_media_name   = $queue_post_data->facebook_account_value . ' > ' . $queue_post_data->fb_type;
		$social_class        = 'dashicons-facebook';
	} else if ( isset( $queue_post_data->twitter_account_value ) && ! empty( $queue_post_data->twitter_account_value ) ) {
		$post_share_platform = "twitter";
		$row_post_id         = $queue_post_data->twitter_row_id;
		$social_media_name   = $queue_post_data->twitter_account_value;
		$social_class        = 'dashicons-twitter';
	} else if ( isset( $queue_post_data->linkin_account_value ) && ! empty( $queue_post_data->linkin_account_value ) ) {
		$post_share_platform = "linkedin";
		$row_post_id         = $queue_post_data->linkedin_row_id;
		$social_media_name   = $queue_post_data->linkin_account_value;
		$social_class        = 'dashicons-linkedin';
	} else {
		$post_share_platform = "";
	}
	$post_data  = $this->get_post_data_from_post_id( $ss_queue_post_id, false );
	$share_date = $queue_post_data->share_date;
	if ( 'smartshare-history' === $ss_current_page ) {
		$button_class        = "ss_skip_post_btn";
		$dashicon_span_class = "dashicons-controls-skipforward";
		$button_text         = "Skip";
	} else if ( 'smartshare-skipped' === $ss_current_page ) {
		$button_class        = "delete-post-skipped";
		$dashicon_span_class = "dashicons-trash";
		$button_text         = "Delete";
	} else if ( 'smartshare-shared' === $ss_current_page ) {
		$button_class        = "";
		$dashicon_span_class = "";
		$button_text         = "";

		$post_url = get_post_meta( $ss_queue_post_id, 'ss_' . $post_share_platform . '_bitly_short_url', true );

	} else if ( 'smartshare-failed' === $ss_current_page ) {
		$button_class        = "ss_queue_post_btn";
		$dashicon_span_class = "dashicons-controls-skipback";
		$button_text         = "Add to queue";
	}
	?>
	<div class="ss-share-row">
		<div class="ss-share-disc">
			<div class="ss-share-img">
				<img src="<?php echo ( isset( $post_data['post_image_url'] ) ) ? esc_url( $post_data['post_image_url'] ) : ''; ?>" alt="demo">
			</div>
			<div class="ss-share-content">
				<h2><?php echo ( isset( $post_data['post_title'] ) ) ? esc_html( $post_data['post_title'] ) : ''; ?></h2>
				<p>
					<?php echo ( isset( $post_data['post_excerpt'] ) ) ? esc_html( $post_data['post_excerpt'] ) : ''; ?>
					<?php if ( isset( $post_url ) && ! empty( $post_url ) ) { ?>
						<a href="<?php echo esc_url( $post_url ); ?>"><?php echo esc_html( $post_url ); ?></a>
					<?php } ?>
				<div class="hash_tag_list">
					<?php echo ( isset( $post_data['hash_tag_list'] ) ) ? wp_kses( $post_data['hash_tag_list'], $allowed_tags ) : ''; ?>
				</div>
				</p>
			</div>
		</div>
		<div class="ss-share-social">
			<div class="ss-share-profile">
				<?php
				printf( '<span class="ss-share-profile-name">@%s</span>', esc_html( $social_media_name ) );
				printf( '<a href="javacript:void(0);" class="ss-share-profile-icon"><span class="dashicons %s"></span></a>', esc_html( $social_class ) );
				?>
			</div>
			<div class="ss-share-buttons">
				<?php if ( 'smartshare-shared' !== $ss_current_page ) { ?>
					<a href="javascript:void(0);" class="button <?php echo esc_attr( $button_class ); ?> button-secondary" data-post-id="<?php echo esc_attr( $row_post_id ); ?>"
					   data-platform="<?php echo esc_attr( $post_share_platform ); ?>"><span
							class="dashicons <?php echo esc_attr( $dashicon_span_class ); ?>"></span><?php echo esc_html( $button_text ); ?>
					</a>
					<a href="javascript:void(0);" class="button ss_instant_share button-primary" data-post-id="<?php echo esc_attr( $row_post_id ); ?>"
					   data-platform="<?php echo esc_attr( $post_share_platform ); ?>"><span class="dashicons dashicons-share"></span>share now</a>
				<?php } else {
					if ( isset ( $share_date ) ) {
						$utc = ( isset( $ss_schedule_settings['ss_timezone'] ) ) ? str_replace( 'UTC', '', $ss_schedule_settings['ss_timezone'] ) : '+0';
						echo esc_html__( "Shared on : " ) . esc_html( gmdate( "d-M-Y | h:i a", strtotime( $share_date ) + 3600 * ( floatval( $utc ) + date( "I" ) ) ) );
					}
				} ?>
			</div>
		</div>
	</div>
<?php } ?>
<div class="ss-pagination">
	<?php Smart_Share_Admin::smart_share_pagination( $ss_total_pages, $pageno ); ?>
</div>

<div id="ss-skip-queue-post" class="ss-confirm-popup" style="display:none;">
	<p><?php esc_html_e( "Are you sure you want to skip this post?", "social-media-smart-share" ); ?></p>
	<div class="ss_skip_conf_btns">
		<input type="button" id="ss_skip_post_yes" class="button button-primary" value="<?php echo esc_attr( 'Yes' ); ?>">
		<input type="button" id="ss_skip_post_no" class="button button-secondary" value="<?php echo esc_attr( 'No' ); ?>">
	</div>
</div>
<div id="ss-delete-skipped-post" class="ss-confirm-popup" style="display:none;">
	<p><?php esc_html_e( "Are you sure you want to delete this post?", "social-media-smart-share" ); ?></p>
	<div class="ss_delete_conf_btns">
		<input type="button" id="ss_delete_post_yes" class="button button-primary" value="<?php echo esc_attr( 'Yes' ); ?>">
		<input type="button" id="ss_delete_post_no" class="button button-secondary" value="<?php echo esc_attr( 'No' ); ?>">
	</div>
</div>
<div id="ss-instant-share-popup" class="ss-confirm-popup" style="display:none;">
	<p><?php esc_html_e( "Are you sure you want to share this post?", "social-media-smart-share" ); ?></p>
	<div class="ss_skip_conf_btns">
		<input type="button" id="ss_instant_share_post_yes" class="button button-primary" value="<?php echo esc_attr( 'Yes' ); ?>">
		<input type="button" id="ss_instant_share_post_no" class="button button-secondary" value="<?php echo esc_attr( 'No' ); ?>">
	</div>
</div>
<div id="ss-queue-failed-post" class="ss-confirm-popup" style="display:none;">
	<p><?php esc_html_e( "Are you sure you want to add this post to queue?", "social-media-smart-share" ); ?></p>
	<div class="ss_skip_conf_btns">
		<input type="button" id="ss_queue_post_yes" class="button button-primary" value="<?php echo esc_attr( 'Yes' ); ?>">
		<input type="button" id="ss_queue_post_no" class="button button-secondary" value="<?php echo esc_attr( 'No' ); ?>">
	</div>
</div>
