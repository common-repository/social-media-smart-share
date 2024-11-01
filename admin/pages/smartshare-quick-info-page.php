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
				<h2><?php esc_html_e( 'Quick info', 'social-media-smart-share' ); ?></h2>
				<table class="table-outer">
					<tbody>
					<tr>
						<td class="fr-1"><?php esc_html_e( 'Product Type', 'social-media-smart-share' ); ?></td>
						<td class="fr-2"><?php esc_html_e( 'Wordpress Plugin', 'social-media-smart-share' ); ?></td>
					</tr>
					<tr>
						<td class="fr-1"><?php esc_html_e( 'Product Name', 'social-media-smart-share' ); ?></td>
						<td class="fr-2"><?php esc_html_e( ucwords(strtolower($plugin_name)), 'social-media-smart-share' ); ?></td>
					</tr>
					<tr>
						<td class="fr-1"><?php esc_html_e( 'Installed Version', 'social-media-smart-share' ); ?></td>
						<td class="fr-2"><?php esc_html_e( 'Free Version ', 'social-media-smart-share' ); ?><?php esc_html_e( $plugin_version ); ?></td>
					</tr>
					<tr>
						<td class="fr-1"><?php esc_html_e( 'License & Terms of use', 'social-media-smart-share' ); ?></td>
						<td class="fr-2">
							<a target="_blank" href="<?php echo esc_url( 'https://www.thedotstore.com/terms-and-conditions/' ); ?>"><?php esc_html_e( 'Click here', 'social-media-smart-share' ); ?></a><?php esc_html_e( ' to view license and terms of use.', 'social-media-smart-share' ); ?>
						</td>
					</tr>
					<tr>
						<td class="fr-1"><?php esc_html_e( 'Help & Support', 'social-media-smart-share' ); ?></td>
						<td class="fr-2">
							<ul>
								<li><a href="<?php echo esc_url(admin_url( '/admin.php?page=smartshare-started' )); ?>"><?php esc_html_e( 'Quick Start', 'social-media-smart-share' ); ?></a></li>
								<li>
									<a target="_blank" href="<?php echo esc_url( 'http://www.thedotstore.com/docs/plugins/smart-share-wordpress-plugins/' ); ?>"><?php esc_html_e( 'Guide Documentation', 'social-media-smart-share' ); ?></a>
								</li>
								<li><a target="_blank" href="<?php echo esc_url( 'https://www.thedotstore.com/support/' ); ?>"><?php esc_html_e( 'Support Forum', 'social-media-smart-share' ); ?></a></li>
							</ul>
						</td>
					</tr>
					<tr>
						<td class="fr-1"><?php esc_html_e( 'Localization', 'social-media-smart-share' ); ?></td>
						<td class="fr-2"><?php esc_html_e( 'English', 'social-media-smart-share' ); ?></td>
					</tr>

					</tbody>
				</table>
			</div>
		</div>
	</div>
	<?php require_once plugin_dir_path( __FILE__ ) . 'header/plugin-sidebar.php'; ?>
</div>
