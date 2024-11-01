<?php
// Exit if accessed directly
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}
$image_url = SMART_SHARE_PLUGIN_URL . 'admin/images/right_click.png';
?>
<div class="dotstore_plugin_sidebar">
	<div class="dotstore-important-link">
		<h2><span class="dotstore-important-link-title"><?php esc_html_e( 'Important link', 'social-media-smart-share' ); ?></span></h2>
		<div class="video-detail important-link">
			<ul>
				<li>
					<img src="<?php echo esc_url( $image_url ); ?>">
					<a target="_blank"
					   href="<?php echo esc_url( 'http://www.thedotstore.com/docs/plugins/smart-share-wordpress-plugins/' ); ?>"><?php esc_html_e( 'Plugin documentation', 'social-media-smart-share' ); ?></a>
				</li>
				<li>
					<img src="<?php echo esc_url( $image_url ); ?>">
					<a target="_blank"
					   href="<?php echo esc_url( 'https://www.thedotstore.com/support/' ); ?>"><?php esc_html_e( 'Support platform', 'social-media-smart-share' ); ?></a>
				</li>
				<li>
					<img src="<?php echo esc_url( $image_url ); ?>">
					<a target="_blank"
					   href="<?php echo esc_url( 'http://www.thedotstore.com/suggest-a-feature' ); ?>"><?php esc_html_e( 'Suggest A Feature', 'social-media-smart-share' ); ?></a>
				</li>
				<li>
					<img src="<?php echo esc_url( $image_url ); ?>">
					<a target="_blank" href="<?php echo esc_url( '#' ); ?>"><?php esc_html_e( 'Changelog', 'social-media-smart-share' ); ?></a>
				</li>
			</ul>
		</div>
	</div>

	<div class="dotstore-important-link">
		<h2><span class="dotstore-important-link-title"><?php esc_html_e( 'OUR POPULAR PLUGINS', 'social-media-smart-share' ); ?></span></h2>
		<div class="video-detail important-link">
			<ul>
				<li>
					<img class="sidebar_plugin_icone" src="<?php echo esc_url( SMART_SHARE_PLUGIN_URL . 'admin/images/advance-flat-rate2.png' ); ?>">
					<a target="_blank"
					   href="<?php echo esc_url( 'http://www.thedotstore.com/advanced-flat-rate-shipping-method-for-woocommerce' ); ?>"><?php esc_html_e( 'Advanced Flat Rate Shipping Method', 'social-media-smart-share' ); ?></a>
				</li>
				<li>
					<img class="sidebar_plugin_icone" src="<?php echo esc_url( SMART_SHARE_PLUGIN_URL . 'admin/images/wc-conditional-product-fees.png' ); ?>">
					<a target="_blank"
					   href="<?php echo esc_url( 'http://www.thedotstore.com/woocommerce-conditional-product-fees-checkout' ); ?>"><?php esc_html_e( 'WooCommerce Conditional Product Fees', 'social-media-smart-share' ); ?></a>
				</li>
				<li>
					<img class="sidebar_plugin_icone" src="<?php echo esc_url( SMART_SHARE_PLUGIN_URL . 'admin/images/advance-menu-manager.png' ); ?>">
					<a target="_blank"
					   href="<?php echo esc_url( 'http://www.thedotstore.com/advance-menu-manager-wordpress' ); ?>"><?php esc_html_e( 'Advance Menu Manager', 'social-media-smart-share' ); ?></a>
				</li>
				<li>
					<img class="sidebar_plugin_icone" src="<?php echo esc_url( SMART_SHARE_PLUGIN_URL . 'admin/images/wc-enhanced-ecommerce-analytics-integration.png' ); ?>">
					<a target="_blank"
					   href="<?php echo esc_url( 'http://www.thedotstore.com/woocommerce-enhanced-ecommerce-analytics-integration-with-conversion-tracking' ); ?>"><?php esc_html_e( 'Woo Enhanced Ecommerce Analytics Integration', 'social-media-smart-share' ); ?></a>
				</li>
				<li>
					<img class="sidebar_plugin_icone" src="<?php echo esc_url( SMART_SHARE_PLUGIN_URL . 'admin/images/advanced-product-size-charts.png' ); ?>">
					<a target="_blank"
					   href="<?php echo esc_url( 'http://www.thedotstore.com/woocommerce-advanced-product-size-charts' ); ?>"><?php esc_html_e( 'Advanced Product Size Charts', 'social-media-smart-share' ); ?></a>
				</li>
				<li>
					<img class="sidebar_plugin_icone" src="<?php echo esc_url( SMART_SHARE_PLUGIN_URL . 'admin/images/wc-conditional-product-fees.png' ); ?>">
					<a target="_blank"
					   href="<?php echo esc_url( 'https://www.thedotstore.com/product/woocommerce-blocker-lite-prevent-fake-orders-blacklist-fraud-customers/' ); ?>"><?php esc_html_e( 'WooCommerce Blocker – Prevent Fake Orders', 'social-media-smart-share' ); ?></a>
				</li>
			</ul>
		</div>
		<div class="view-button">
			<a class="view_button_dotstore" target="_blank"
			   href="<?php echo esc_url( 'http://www.thedotstore.com/plugins' ); ?>"><?php esc_html_e( 'VIEW ALL', 'social-media-smart-share' ); ?></a>
		</div>
	</div>

</div>
