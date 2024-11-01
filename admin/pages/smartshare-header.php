<?php
if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if access directly
}
$plugin_version = SMART_SHARE_VERSION;
?>
<div id="dotsstoremain">
	<header class="dots-header">
		<div class="dots-logo-main">
			<img src="<?php echo esc_url( SMART_SHARE_PLUGIN_URL . 'admin/images/multidots.png' ); ?>">
		</div>
		<div class="dots-header-right">
			<div class="logo-detail">
				<strong><?php esc_html_e( 'Social Media Smart Share', 'social-media-smart-share' ); ?></strong>
				<span><?php esc_html_e( 'Free Version ' ,'social-media-smart-share'); ?><?php echo esc_html( $plugin_version ); ?></span>
			</div>

			<div class="button-group">
				<a class="support_dotstore_image" target="_blank" href="<?php echo esc_url( 'http://www.thedotstore.com/support/' ); ?>">
					<img src="<?php echo esc_url( SMART_SHARE_PLUGIN_URL . 'admin/images/support_new.png' ); ?>">
				</a>
			</div>
		</div>
	</header>
</div>
