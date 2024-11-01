<?php
if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if access directly
}

$ss_current_page  = filter_input( INPUT_GET, 'page', FILTER_SANITIZE_STRING );
$ss_accounts_page = admin_url( 'admin.php?page=smartshare-accounts' );
$ss_schedule_page = admin_url( 'admin.php?page=smartshare-schedule' );
$ss_history_page  = admin_url( 'admin.php?page=smartshare-history' );

?>


<div class="ss-tabs-ul-container">

	<ul class="ss-tab-ul">

		<li class="<?php echo ( 'smartshare-accounts' === $ss_current_page ) ? 'ss-active' : ''; ?>">
			<a href="<?php echo esc_url( $ss_accounts_page ); ?>"><?php esc_html_e( 'Setup', 'social-media-smart-share' ); ?></a>
		</li>

		<li class="<?php echo ( 'smartshare-schedule' === $ss_current_page ) ? 'ss-active' : ''; ?>">
			<a href="<?php echo esc_url( $ss_schedule_page ); ?>"><?php esc_html_e( 'Schedule', 'social-media-smart-share' ); ?></a>
		</li>

		<li class="<?php echo ( 'smartshare-history' === $ss_current_page ) || ( 'smartshare-failed' === $ss_current_page ) || ( 'smartshare-skipped' === $ss_current_page ) || ( 'smartshare-shared' === $ss_current_page ) ? 'ss-active' : ''; ?>">
			<a href="<?php echo esc_url( $ss_history_page ); ?>"><?php esc_html_e( 'Share', 'social-media-smart-share' ); ?></a>
		</li>

	</ul>

</div>