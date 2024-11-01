<?php
if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if access directly
}
global $wpdb;

$ss_current_page  = filter_input( INPUT_GET, 'page', FILTER_SANITIZE_STRING );
$ss_accounts_page = admin_url( 'admin.php?page=smartshare-accounts' );
$ss_schedule_page = admin_url( 'admin.php?page=smartshare-schedule' );
$ss_history_page  = admin_url( 'admin.php?page=smartshare-history' );

//Get All the categories for post
$args            = array(
	"hide_empty" => 0,
	"type"       => "post",
);
$post_categories = get_categories( $args );

// Get scheduled data
$smartshare_schedule_table = "{$wpdb->prefix}smartshare_schedule";
$get_scheduler             = $wpdb->get_row( $wpdb->prepare( "SELECT * FROM %1s WHERE `status` = %s or `status` = %s ORDER BY id DESC", array(
	$smartshare_schedule_table,
	"in_process",
	"pause",
) ) );
$ss_schedule_settings      = ( isset( $get_scheduler->schedule_settings ) ) ? maybe_unserialize( $get_scheduler->schedule_settings ) : array();

$ss_default_timezone = get_option( 'timezone_string' );

if ( ! isset( $ss_default_timezone ) || empty( $ss_default_timezone ) ) {
	$ss_default_timezone = get_option( 'gmt_offset' );
	if ( 'UTC' === get_option( 'gmt_offset' ) ) {
		$ss_default_timezone = 'UTC';
	} else {
		if ( 0 <= $ss_default_timezone ) {
			$ss_default_timezone = 'UTC+' . $ss_default_timezone;
		} else {
			$ss_default_timezone = 'UTC' . $ss_default_timezone;
		}
	}
}
$ss_schedule_cats       = isset( $ss_schedule_settings['ss_schedule_cats'] ) ? $ss_schedule_settings['ss_schedule_cats'] : array();
$ss_post_age            = isset( $ss_schedule_settings['ss_post_age'] ) ? $ss_schedule_settings['ss_post_age'] : '';
$ss_post_per_day        = isset( $ss_schedule_settings['ss_post_per_day'] ) ? $ss_schedule_settings['ss_post_per_day'] : '';
$post_content           = isset( $ss_schedule_settings['post_content'] ) ? $ss_schedule_settings['post_content'] : '';
$post_hashtag           = isset( $ss_schedule_settings['post_hashtag'] ) ? $ss_schedule_settings['post_hashtag'] : '';
$ss_timezone            = isset( $ss_schedule_settings['ss_timezone_name'] ) ? $ss_schedule_settings['ss_timezone_name'] : $ss_default_timezone;
$ss_img_priority        = isset( $ss_schedule_settings['ss_img_priority'] ) ? $ss_schedule_settings['ss_img_priority'] : '';
$ss_img_priority_sorted = ! empty( $ss_img_priority ) ? trim( $ss_img_priority, '"' ) : 'feat_image,image_from_content,custom_field';

$admin_post_url = get_admin_url() . 'admin-post.php';

$allowed_tags = array(
	'p' => array(),
	'a' => array(
		'href'   => array(),
		'target' => array(),
	),
	'b' => array(),
	'optgroup' => array(
		'label' => array(),
	),
	'option' => array(
		'name' => array(),
		'value' => array(),
		'selected' => array(),
	),
);
?>
<div class="wrap">
	<?php require_once plugin_dir_path( __FILE__ ) . 'header/plugin-header.php'; ?>
	<div class="ss-container schedule_settings_container">

		<div class="ss-tabs-container">
			<!----------- Tab HTML ----------->
			<?php
			if ( ! empty( $get_scheduler ) ) {
				$que_id               = ( isset( $get_scheduler->id ) ) ? 'SS' . str_pad( $get_scheduler->id, 2, '0', STR_PAD_LEFT ) : '';
				$ss_total_shared_post = isset( $get_scheduler->post_in_shared ) ? $get_scheduler->post_in_shared : 0;
				$ss_total_failed_post = isset( $get_scheduler->post_in_failed ) ? $get_scheduler->post_in_failed : 0;
				?>
				<div class="ss-data-main">
					<div class="ss-data-inner">
						<div class="ss-data-row">
							<div class="ss-data-col">
								<span class="ss-title"><?php esc_html_e( "que id", "social-media-smart-share" ); ?></span>
								<span class="ss-data"><?php echo esc_html( $que_id ); ?></span>
							</div>
							<?php if ( isset( $get_scheduler->total_posts ) ) { ?>
								<div class="ss-data-col">
									<span class="ss-title"><?php esc_html_e( "total sharable post", "social-media-smart-share" ) ?></span>
									<span class="ss-data"><?php echo esc_html( $get_scheduler->total_posts ); ?></span>
								</div>
							<?php } ?>
							<?php if ( isset( $get_scheduler->post_in_queue ) ) { ?>
								<div class="ss-data-col">
									<span class="ss-title"><?php esc_html_e( "post in queue", "social-media-smart-share" ); ?></span>
									<span class="ss-data"><?php echo esc_html( $get_scheduler->post_in_queue ); ?></span>
								</div>
							<?php } ?>
							<div class="ss-data-col">
								<span class="ss-title"><?php esc_html_e( "shared", "social-media-smart-share" ); ?></span>
								<span class="ss-data"><?php echo esc_html( $ss_total_shared_post ); ?></span>
							</div>
							<div class="ss-data-col">
								<span class="ss-title"><?php esc_html_e( "Failed", "social-media-smart-share" ); ?></span>
								<span class="ss-data"><?php echo esc_html( $ss_total_failed_post ); ?></span>
							</div>
							<?php if ( isset( $get_scheduler->start_date ) ) {
								$start_date = date( 'd-M', strtotime( $get_scheduler->start_date ) );
								?>
								<div class="ss-data-col">
									<span class="ss-title"><?php esc_html_e( "start date", "social-media-smart-share" ); ?></span>
									<span class="ss-data"><?php echo esc_html( $start_date ); ?></span>
								</div>
							<?php } ?>
							<?php if ( isset( $get_scheduler->end_date ) ) {
								$end_date = date( 'd-M', strtotime( $get_scheduler->end_date ) );
								?>
								<div class="ss-data-col">
									<span class="ss-title"><?php esc_html_e( "end date", "social-media-smart-share" ); ?></span>
									<span class="ss-data"><?php echo esc_html( $end_date ); ?></span>
								</div>
							<?php } ?>
							<?php if ( isset( $get_scheduler->status ) ) {
								$ss_status = str_replace( '_', ' ', ucwords( $get_scheduler->status ) );
								?>
								<div class="ss-data-col">
									<span class="ss-title"><?php esc_html_e( "status", "social-media-smart-share" ); ?></span>
									<span class="ss-data ss-orange"><?php echo esc_html( $ss_status ); ?></span>
								</div>
							<?php }

							if ( isset( $get_scheduler->status ) && "pause" === $get_scheduler->status ) {
								$pause_resume_btn_text  = __( "Resume Sharing", "social-media-smart-share" );
								$pause_resume_btn_class = 'resume_btn';
							} else {
								$pause_resume_btn_text  = __( "Pause Sharing", "social-media-smart-share" );
								$pause_resume_btn_class = 'pause_btn';
							}
							?>
							<div class="ss-data-col ss-control-btn">
								<a href="#ss-pause-resume-queue"
								   class="button button-primary resume_sharing ss_pause_resume_schedule_btn <?php echo esc_attr( $pause_resume_btn_class ); ?>"><?php esc_html_e( $pause_resume_btn_text ); ?></a>
								<a href="#ss-delete-queue" class="button button-secondary delete_queue ss_delete_schedule_btn"><?php esc_html_e( "delete queue", "social-media-smart-share" );
									?></a>
							</div>
						</div>
					</div>
				</div>
			<?php } ?>
			<div class="ss-tab-content schedule-wrap">
				<form action="<?php echo esc_url( $admin_post_url ); ?>" method="post" id="ss-schedule-form">
					<?php wp_nonce_field( 'schedule_settings', 'submit_schedule_settings_nonce' ); ?>
					<div class="ss-form-row">
						<div class="ss-schedule-post-label">
							<label><?php esc_html_e( 'Select Content Type', 'social-media-smart-share' ); ?></label>
						</div>
						<div class="ss-schedule-post-field">
							<div class="ss-schedule-post-block-box">
								<input type="checkbox" disabled readonly checked id="ss_schedule_content_type" name="ss_schedule_content_type" value="yes">
								<label for="ss_schedule_content_type"><?php esc_html_e( 'Post','social-media-smart-share' ); ?></label>
							</div>
							<div class="ss-post-categories ss-tooltip-container">
								<label class="ss-post-label"><?php esc_html_e( 'Select Post Category', 'social-media-smart-share' ); ?></label>
								<select name="ss_schedule_cats[]" class="ss-categories" multiple="multiple">
									<?php
									if ( ! empty( $post_categories ) ) {
										foreach ( $post_categories as $cat_key => $cat_value ) {
											?>
											<option
												value="<?php echo esc_attr( $cat_value->term_id ); ?>" <?php echo( ! empty( $ss_schedule_cats ) && in_array( $cat_value->term_id,
												$ss_schedule_cats ) ? ' selected="selected"' : '' ) ?>><?php echo esc_html( $cat_value->name ); ?></option>
											<?php
										}
									}
									?>
								</select>
								<span class="ss-schedule-post-tooltip-icon"></span>
							</div>
							<div class="ss-schedule-post-tooltip-block-box">
								<p><?php esc_html_e( 'Here you can select multiple categories of a website to share content on social media profile.', 'social-media-smart-share' ); ?></p>
							</div>
						</div>
					</div>

					<?php
					$post_age_array = array(
						'week'    => 'Last Week',
						'month'   => 'Last Month',
						'quarter' => 'Last Quarter',
						'year'    => 'Last Year',
					);
					?>
					<div class="ss-form-row">
						<div class="ss-schedule-post-label">
							<label><?php esc_html_e( 'Select Post Age', 'social-media-smart-share' ); ?> <span>*</span></label>
						</div>
						<div class="ss-schedule-post-field">
							<div class="ss-tooltip-container">
								<select name="ss_post_age">
									<option value=""><?php echo esc_html( 'All Time', 'social-media-smart-share' ); ?></option>
									<?php
									if ( ! empty( $post_age_array ) ) {
										foreach ( $post_age_array as $post_age_key => $post_age_val ) {
											?>
											<option
												value="<?php echo esc_attr( $post_age_key ); ?>" <?php selected( $ss_post_age, $post_age_key ); ?>><?php echo esc_html( $post_age_val );
												?></option>
											<?php
										}
									}
									?>
								</select>
								<span class="ss-schedule-post-tooltip-icon"></span>
							</div>
							<div class="ss-schedule-post-tooltip-block-box">
								<p><?php esc_html_e( 'Select the minimum age of posts available for sharing. Like All Time, Last week, Last month, Last Quarter, Last year.', 'social-media-smart-share' ); ?></p>
							</div>

						</div>
					</div>

					<?php
					$share_post_per_day_array = array(
						'1' => '1',
						'2' => '2',
						'3' => '3',
						'4' => '4',
						'5' => '5',
					);
					?>

					<div class="ss-form-row">
						<div class="ss-schedule-post-label">
							<label><?php esc_html_e( 'Share No Of Post Per Day?', 'social-media-smart-share' ); ?> <span>*</span></label>
						</div>
						<div class="ss-schedule-post-field ">
							<div class="ss-tooltip-container">
								<select name="ss_post_per_day">
									<?php
									if ( ! empty( $share_post_per_day_array ) ) {
										foreach ( $share_post_per_day_array as $post_per_day_key => $post_per_day_val ) {
											?>
											<option
												value="<?php echo esc_attr( $post_per_day_key ); ?>" <?php selected( $ss_post_per_day, $post_per_day_key ); ?>><?php echo esc_html(
													$post_per_day_val ); ?></option>
											<?php
										}
									}
									?>
								</select>
								<span class="ss-schedule-post-tooltip-icon"></span>
							</div>
							<div class="ss-schedule-post-tooltip-block-box">
								<p><?php esc_html_e( 'A number of posts to share per social account. According to that trigger of the scheduled job.', 'social-media-smart-share' ); ?></p>
							</div>
						</div>
					</div>

					<div class="ss-form-row">
						<div class="ss-schedule-post-label">
							<label><?php esc_html_e( 'Select Post Content From', 'social-media-smart-share' ); ?> <span>*</span></label>
						</div>
						<div class="ss-schedule-post-field">
							<div class="ss-tooltip-container">
								<div class="ss-schedule-radio">
									<input type="radio" name="post_content" value="post_content_title"
									       checked <?php checked( $post_content, 'post_content_title' ); ?>><?php esc_html_e( 'Post Title', 'social-media-smart-share' ); ?>
								</div>
								<div class="ss-schedule-radio">
									<input type="radio" name="post_content"
									       value="post_content_excerpt" <?php checked( $post_content, 'post_content_excerpt' ); ?>><?php esc_html_e( 'Post Excerpt', 'social-media-smart-share' ); ?>
								</div>
								<span class="ss-schedule-post-tooltip-icon"></span>
							</div>
							<div class="ss-schedule-post-tooltip-block-box">
								<ul>
									<li><?php echo wp_kses( __( 'Select the <b>“Post Title”</b> then the smart share plugin will fetch the blog post title of the selected blog post and share on the social media account as a social media post content.', 'social-media-smart-share' ), $allowed_tags ); ?></li>
									<li><?php echo wp_kses( __( 'Select the <b>“Post Excerpt”</b> then the smart share plugin will fetch the post excerpt from the selected blog post and share on the social media account as a social media post content.', 'social-media-smart-share' ), $allowed_tags ); ?></li>
								</ul>
							</div>
						</div>
					</div>

					<div class="ss-form-row">
						<div class="ss-schedule-post-label">
							<label><?php esc_html_e( 'Select HashTag', 'social-media-smart-share' ); ?> <span>*</span></label>
						</div>
						<div class="ss-schedule-post-field">
							<div class="ss-tooltip-container">
								<div class="ss-schedule-radio">
									<input type="radio" name="post_hashtag" value="post_hash_tags"
									       checked <?php checked( $post_hashtag, 'post_hash_tags' ); ?>><?php esc_html_e( 'Post Tags', 'social-media-smart-share' ); ?>
								</div>
								<div class="ss-schedule-radio">
									<input type="radio" name="post_hashtag"
									       value="post_has_category" <?php checked( $post_hashtag, 'post_has_category' ); ?>><?php esc_html_e( 'Post Category', 'social-media-smart-share' ); ?>
								</div>
								<span class="ss-schedule-post-tooltip-icon"></span>
							</div>
							<div class="ss-schedule-post-tooltip-block-box">
								<ul>
									<li><?php echo wp_kses( __( 'Select <b>“Post Tags”</b>  then the plugin will fetch the post tags from the selected WordPress post and share the post tags on the social media account as a social media post content.', 'social-media-smart-share' ), $allowed_tags ); ?></li>
									<li><?php echo wp_kses( __( 'Select the <b>“Post Category”</b>  then the plugin will create the post category as a tag of the selected WordPress post and share the tag on the social media account as a social media post content.', 'social-media-smart-share' ), $allowed_tags ); ?></li>
								</ul>
							</div>
						</div>
					</div>
					<div class="ss-form-row">
						<div class="ss-schedule-post-label">
							<label><?php esc_html_e( 'Select Time Zone', 'social-media-smart-share' ); ?> <span>*</span></label>
						</div>
						<div class="ss-schedule-post-field">
							<div class="ss-tooltip-container">
								<select name="ss_timezone">
									<?php echo wp_kses(wp_timezone_choice( $ss_timezone ),$allowed_tags);?>
								</select>
								<span class="ss-schedule-post-tooltip-icon"></span>
							</div>
							<div class="ss-schedule-post-tooltip-block-box">
								<p><?php esc_html_e( 'Select the time zone to share social media post. Smart Share Plugin will share the posts on the social media account as per the selected time zone.', 'social-media-smart-share' ); ?></p>
							</div>
						</div>
					</div>

					<?php
					$ss_img_priority_sorted_array = explode( ",", $ss_img_priority_sorted );

					$image_priority_array = array(
						'feat_image'         => 'Feature Image',
						'image_from_content' => 'Image From Content',
						'custom_field'       => 'Custom Field',
					);
					?>
					<div class="ss-form-row">
						<div class="ss-schedule-post-label">
							<label><?php esc_html_e( 'Select Image Priority', 'social-media-smart-share' ); ?> <span>*</span></label>
						</div>
						<div class="ss-schedule-post-field">
							<div class="ss-image-priority">
								<div class="ss-tooltip-container">
									<ul id="ss_schedule_img_sortable" name="ss_img_priority">
										<?php foreach ( $ss_img_priority_sorted_array as $image_priority_val ) { ?>
											<li id="<?php echo esc_attr( $image_priority_val ); ?>" class="ui-state-default">
												<?php echo esc_html( $image_priority_array[ $image_priority_val ] ); ?>
											</li>
										<?php } ?>
									</ul>
									<span class="ss-schedule-post-tooltip-icon"></span>
								</div>
								<div class="ss-schedule-post-tooltip-block-box">
									<p><?php esc_html_e( 'With this option, you can set image priority. smart share plugin will fetch the image of the WordPress Post/Page and shares on the social media account with the social media post content.', 'social-media-smart-share' ); ?></p>
								</div>
								<input type="hidden" name="ss_img_priority" id="ss_img_priority" value="<?php echo esc_attr( $ss_img_priority ); ?>">

							</div>
						</div>
					</div>

					<div class="ss-form-row">
						<?php
						$schedule_button_text = ( isset( $get_scheduler->id ) ) ? esc_html__("Reschedule") : esc_html__("Schedule");
						$reschedule_btn_class = ( isset( $get_scheduler->id ) ) ? esc_html__("ss-reschedule-btn") : "";
						?>
						<input type="hidden" name="action" value="smart_share_schedule_settings">
						<input type="submit" name="ss_schedule_submit" id="ss-schedule-create"
						       class="button button-primary ss-schedule ss-schedule-form <?php echo esc_html( $reschedule_btn_class ); ?>"
						       value="<?php echo esc_attr( $schedule_button_text ); ?>">
						<input type="submit" name="ss_schedule_reset" id="ss-schedule-reset" class="button button-secondary ss-schedule-form ss-reset"
						       value="<?php esc_html_e( "Reset", "smartshare" ); ?>">
					</div>
				</form>
			</div>
		</div>
	</div>
	<?php require_once plugin_dir_path( __FILE__ ) . 'header/plugin-sidebar.php'; ?>
</div>
<input type="hidden" id="curr_scheduler" value="<?php echo ( isset( $get_scheduler->id ) ) ? esc_html( $get_scheduler->id ) : ''; ?>">
<input type="hidden" id="curr_scheduler_status" value="<?php echo ( isset( $get_scheduler->status ) ) ? esc_html( $get_scheduler->status ) : ''; ?>">
<div id="ss-pause-resume-queue" class="ss-confirm-popup" style="display:none;">
	<p><?php
		if ( isset( $get_scheduler->status ) && "in_process" === $get_scheduler->status ) {
			$pause_resume_dec_text = esc_html__( "Are you sure you want to pause this Queue?", "smartshare" );
		} else {
			$pause_resume_dec_text = esc_html__( "Are you sure you want to resume this Queue?", "smartshare" );
		}
		esc_html_e( $pause_resume_dec_text );
		?></p>
	<div class="ss_pause_resumes_conf_btns">
		<input type="button" id="ss_pause_resume_queue_yes" class="button button-primary" value="<?php echo esc_html__( 'Yes','social-media-smart-share' ); ?>">
		<input type="button" id="ss_pause_resume_queue_no" class="button button-secondary" value="<?php echo esc_attr( 'No','social-media-smart-share' ); ?>">
	</div>
</div>
<div id="ss-delete-queue" class="ss-confirm-popup" style="display:none;">
	<p><?php esc_html_e( "Are you sure you want to delete this Queue?" ); ?></p>
	<div class="ss_del_conf_btns">
		<input type="button" id="ss_del_queue_yes" class="button button-primary" value="<?php echo esc_attr( 'Yes','social-media-smart-share' ); ?>">
		<input type="button" id="ss_del_queue_no" class="button button-secondary" value="<?php echo esc_attr( 'No','social-media-smart-share' ); ?>">
	</div>
</div>
<div id="ss_reschedule_modal" style="display: none;">
	<p><?php esc_html_e( 'Your Post sharing queue is on going. If you want to reschedule a new queue then please delete currently running queue or wait until the current queue will finish.','social-media-smart-share' ); ?></p>
</div>
<div id="ss_reset_modal" style="display: none;">
	<p><?php esc_html_e( 'Your Post sharing queue is on going. If you want to reset a queue then please delete currently running queue or wait until the current queue will finish.', 'social-media-smart-share' ); ?></p>
</div>
