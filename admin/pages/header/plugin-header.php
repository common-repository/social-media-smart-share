<?php
if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if access directly
}
$plugin_name    = SMART_SHARE_NAME;
$plugin_version = SMART_SHARE_VERSION;
?>
<h2></h2>
<div id="dotsstoremain">
	<div class="all-pad">
		<header class="dots-header">
			<div class="dots-logo-main">
				<img src="<?php echo esc_url( SMART_SHARE_PLUGIN_URL . 'admin/images/smart-share-75.png' ); ?>">
			</div>
			<div class="dots-header-right">
				<div class="logo-detail">
					<strong><?php esc_html_e( $plugin_name, 'social-media-smart-share' ); ?></strong>
					<span><?php esc_html_e( 'Free Version ', 'social-media-smart-share' ); ?><?php esc_html_e( $plugin_version ); ?></span>
				</div>

				<div class="button-dots">
					<span class="support_dotstore_image">
                        <a target="_blank" href="<?php echo esc_url( 'https://www.thedotstore.com/support/' ); ?>">
                            <img src="<?php echo esc_url( SMART_SHARE_PLUGIN_URL . 'admin/images/support_new.png' ); ?>">
                        </a>
                    </span>
				</div>
			</div>

			<?php
			$ss_paged            = filter_input( INPUT_GET, 'page', FILTER_SANITIZE_STRING );
			$smartshare_dashbord = isset( $ss_paged ) && $ss_paged == 'smartshare_dashbord' ? 'active' : '';
			$smartshare_accounts = isset( $ss_paged ) && $ss_paged == 'smartshare-accounts' ? 'active' : '';
			$smartshare_schedule = isset( $ss_paged ) && $ss_paged == 'smartshare-schedule' ? 'active' : '';
			$smartshare_history  = isset( $ss_paged ) && $ss_paged == 'smartshare-history' ? 'active' : '';
			$smartshare_settings = isset( $ss_paged ) && $ss_paged == 'smartshare-settings' ? 'active' : '';
			$smartshare_started  = isset( $ss_paged ) && $ss_paged == 'smartshare-started' ? 'active' : '';
			$smartshare_quick    = isset( $ss_paged ) && $ss_paged == 'smartshare-quick-info' ? 'active' : '';

			if ( isset( $ss_paged ) && $ss_paged == 'smartshare-skipped' || $ss_paged == 'smartshare-shared' || $ss_paged == 'smartshare-failed' ) {
				$smartshare_history = 'active';
			}

			$about_plugin = '';

			if ( isset( $ss_paged ) && $ss_paged == 'smartshare-quick-info' || $ss_paged == 'smartshare-started' ) {
				$about_plugin = 'active';
			}

			?>
			<div class="dots-menu-main">
				<nav>
					<ul>
						<li>
							<a class="dotstore_plugin <?php echo esc_attr( $smartshare_dashbord ); ?>"
							   href="<?php echo esc_url( admin_url( '/admin.php?page=smartshare_dashbord' ) );
							   ?>"><?php esc_html_e(
									'Dashboard', 'social-media-smart-share' ); ?></a>
						</li>
						<li>
							<a class="dotstore_plugin <?php echo esc_attr( $smartshare_accounts ); ?>"
							   href="<?php echo esc_url( admin_url( '/admin.php?page=smartshare-accounts' ) );
							   ?>"><?php esc_html_e( 'Setup', 'social-media-smart-share' ); ?></a>
						</li>
						<li>
							<a class="dotstore_plugin <?php echo esc_attr( $smartshare_schedule ); ?>"
							   href="<?php echo esc_url( admin_url( '/admin.php?page=smartshare-schedule' ) );
							   ?>"><?php esc_html_e( 'Schedule', 'social-media-smart-share' ); ?></a>
						</li>
						<li>
							<a class="dotstore_plugin <?php echo esc_attr( $smartshare_history ); ?>" href="<?php echo esc_url( admin_url( '/admin.php?page=smartshare-history' ) );
							?>"><?php
								esc_html_e( 'Share', 'social-media-smart-share' ); ?></a>
						</li>
						<li>
							<a class="dotstore_plugin <?php echo esc_attr( $smartshare_settings ); ?>"
							   href="<?php echo esc_url( admin_url( '/admin.php?page=smartshare-settings' ) );
							   ?>"><?php esc_html_e( 'Settings', 'social-media-smart-share' ); ?></a>
						</li>
						<li>
							<a class="dotstore_plugin <?php echo esc_attr( $about_plugin ); ?>"><?php esc_html_e( 'About Plugin', 'social-media-smart-share' ); ?></a>
							<ul class="sub-menu">
								<li>
									<a class="dotstore_plugin <?php echo esc_attr( $smartshare_started ); ?>"
									   href="<?php echo esc_url( admin_url( '/admin.php?page=smartshare-started' ) );
									   ?>"><?php esc_html_e( 'Getting Started', 'social-media-smart-share' ); ?></a>
								</li>
								<li>
									<a class="dotstore_plugin <?php echo esc_attr( $smartshare_quick ); ?>"
									   href="<?php echo esc_url( admin_url( '/admin.php?page=smartshare-quick-info' ) );
									   ?>"><?php esc_html_e( 'Quick Info', 'social-media-smart-share' ); ?></a>
								</li>
							</ul>
						</li>
						<li>
							<a class="dotstore_plugin"><?php esc_html_e( 'Dotstore', 'social-media-smart-share' ); ?></a>
							<ul class="sub-menu">
								<li><a target="_blank"
								       href="<?php echo esc_url( 'http://www.thedotstore.com/woocommerce-plugins' ); ?>"><?php esc_html_e( 'WooCommerce Plugins', 'social-media-smart-share' ); ?></a>
								</li>
								<li><a target="_blank"
								       href="<?php echo esc_url( 'http://www.thedotstore.com/wordpress-plugins' ); ?>"><?php esc_html_e( 'Wordpress Plugins', 'social-media-smart-share' ); ?></a>
								</li>
								<li><a target="_blank"
								       href="<?php echo esc_url( 'https://www.thedotstore.com/support/' ); ?>"><?php esc_html_e( 'Contact Support', 'social-media-smart-share' ); ?></a>
								</li>
							</ul>
						</li>
					</ul>
				</nav>
			</div>

		</header>
		<?php
		if ( get_transient( "ss_success_message" ) ) { ?>
			<div id="message" class="ss_update ss_notice_msg">
				<p><?php esc_html_e( get_transient( "ss_success_message" ) ); ?></p>
			</div>
			<?php
			delete_transient( "ss_success_message" );
			delete_transient( "ss_error_message" );
		}
		if ( get_transient( "ss_error_message" ) ) {
			?>
			<div id="message" class="ss_error ss_notice_msg">
				<p><?php esc_html_e( get_transient( "ss_error_message" ) ); ?></p>
			</div>
			<?php
			delete_transient( "ss_error_message" );
		}