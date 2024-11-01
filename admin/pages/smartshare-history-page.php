<?php
if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if access directly
}
?>
<div class="wrap">
	<?php require_once plugin_dir_path( __FILE__ ) . 'header/plugin-header.php'; ?>
	<div class="ss-container">
		<div class="ss-tabs-container">

			<div class="ss-tab-content">
				<div class="ss-share">
					<div class="ss-share-tabs">
						<?php include plugin_dir_path( dirname( dirname( __FILE__ ) ) ) . 'admin/pages/smartshare-history-tabs-header.php'; ?>
					</div>
					<!-- .ss-share-tabs -->
					<div class="ss-share-details">
						<div class="ss_tab_info">
							<p><?php esc_html_e( 'User will see the list of “Queue Posts”, which is not shared on the social media and in the queue for sharing on the social media.', 'social-media-smart-share' ); ?></p>
						</div>
						<?php
						if ( $queue_post_list_count > 0 ) {
							?>
							<?php
							include plugin_dir_path( dirname( dirname( __FILE__ ) ) ) . 'admin/pages/smartshare-inner-listings.php';
						} else {
							printf( '<div class="ss-share-row">%s</div>', esc_html__( 'You need to start sharing in order to see any posts in the queue.','social-media-smart-share' ) );
						}
						?>

					</div><!-- .ss-share-details -->
				</div><!-- .ss-share -->
			</div>
		</div><!-- .ss-tabs-container -->
	</div><!-- .ss-container -->
	<?php require_once plugin_dir_path( __FILE__ ) . 'header/plugin-sidebar.php'; ?>
</div>
