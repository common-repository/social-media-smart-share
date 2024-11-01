<?php

// Exit if accessed directly
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}
?>
<div class="wrap">
	<?php require_once plugin_dir_path( __FILE__ ) . 'header/plugin-header.php'; ?>
	<div class="ss-container">
		<div class="ss-section-left">
			<div class="ss-main-table res-cl">
				<h2><?php esc_html_e( 'Thanks For Installing Smart Share', 'social-media-smart-share' ); ?></h2>
				<table class="table-outer ss-gettingstarted-table-main">
					<tbody>
					<tr>
						<td class="fr-2">
							<p class="block gettingstarted"><strong><?php esc_html_e( 'Getting Started', 'social-media-smart-share' ); ?></strong></p>
							<p class="block textgetting"><?php esc_html_e( 'Setup your social media accounts with plugin and plugin will posting your post in your social accounts behalf of you.', 'social-media-smart-share' ); ?></p>
							<p class="block textgetting">
								<strong><?php esc_html_e( 'Step 1:', 'social-media-smart-share' ); ?></strong> <?php esc_html_e( 'Setup your social account (eg. Twitter, Facebook, Linkedin) with required detail.', 'social-media-smart-share' ); ?>
								<span class="gettingstarted">
                                <img src="<?php echo esc_url(SMART_SHARE_PLUGIN_URL . 'admin/images/setup.jpg'); ?>">
                            </span>
							</p>
							<p class="block gettingstarted textgetting">
								<strong><?php esc_html_e( 'Step 2:', 'social-media-smart-share' ); ?></strong> <?php esc_html_e( 'Create schedule and based on that scheduled post will be shared according to the schedule set.', 'social-media-smart-share' ); ?>
								<span class="gettingstarted">
                                <img src="<?php echo esc_url(SMART_SHARE_PLUGIN_URL . 'admin/images/create_schedule.jpg'); ?>">
                            </span>
							</p>
							<p class="block gettingstarted textgetting">
								<strong><?php esc_html_e( 'Step 3:', 'social-media-smart-share' ); ?></strong> <?php esc_html_e( 'All the posts in queue that you have added while scheduling will be display here and that will be shared with your social accounts on given timeslot.', 'social-media-smart-share' ); ?>
								<span class="gettingstarted">
                                <img src="<?php echo esc_url(SMART_SHARE_PLUGIN_URL . 'admin/images/queue_list.jpg'); ?>">
                            </span>
							</p>
						</td>
					</tr>
					</tbody>
				</table>
			</div>
		</div>
	</div>
	<?php require_once plugin_dir_path( __FILE__ ) . 'header/plugin-sidebar.php'; ?>
</div>
