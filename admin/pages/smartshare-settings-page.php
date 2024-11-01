<?php
if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if access directly
}
?>
<div class="wrap">
	<?php require_once plugin_dir_path( __FILE__ ) . 'header/plugin-header.php'; ?>
	<div class="ss-container">
		<?php
		$active_tab = filter_input( INPUT_GET, 'tab', FILTER_SANITIZE_STRING ); // Twitter consumer key
		$active_tab = isset( $active_tab ) && ! empty( $active_tab ) ? $active_tab : 'general';
		?>

		<h2 class="nav-tab-wrapper">
			<a href="?page=smartshare-settings&tab=general"
			   class="nav-tab <?php echo 'general' === $active_tab ? esc_attr('nav-tab-active') : ''; ?>"><?php echo esc_html__('General','social-media-smart-share'); ?></a>
			<a href="?page=smartshare-settings&tab=sharing_time"
			   class="nav-tab <?php echo 'sharing_time' === $active_tab ? esc_attr('nav-tab-active') : ''; ?>"><?php echo esc_html__('Sharing Time','social-media-smart-share'); ?></a>
		</h2>

		<div class="ss-section-left">

			<div class="setting_container">

				<?php
				if ( 'general' === $active_tab || empty ( $active_tab ) ) {
					?>
					<form method="post" action="options.php">
						<?php
						settings_fields( 'smartshare-settings-group' );
						do_settings_sections( 'smartshare-settings-group' );

						$ss_bitly_api_key     = get_option( 'ss_bitly_api_key' );
						$ss_bitly_api_key_val = ! empty( $ss_bitly_api_key ) ? $ss_bitly_api_key : '';
						?>
						<table class="form-table">
							<tr valign="top">
								<th scope="row"><?php esc_html_e( 'Bitly Access Token', 'social-media-smart-share' ); ?></th>
								<td><input type="text" name="ss_bitly_api_key"
								           value="<?php echo esc_attr( $ss_bitly_api_key_val ); ?>"/></td>
							</tr>
							<tr>
								<td></td>
								<td>
									<a target="_blank" href=<?php echo esc_url("http://www.thedotstore.com/docs/plugins/smart-share-wordpress-plugins/free-plugin-settings/connect-bit-ly-smart-share-plugin/"); ?>><?php esc_html_e( 'Click here', 'social-media-smart-share' ); ?></a><?php esc_html_e( ' for guide to generate the bitly access token', 'social-media-smart-share' ); ?>
								</td>
							</tr>
						</table>

						<?php submit_button(); ?>
					</form>
				<?php } else {
					global $wpdb;
					$smartshare_post_time_table = "{$wpdb->prefix}smartshare_post_time";

					$smartshare_post_time_data = $wpdb->get_results( $wpdb->prepare( "SELECT * FROM %1s", $smartshare_post_time_table ) );
					if ( isset( $smartshare_post_time_data ) && ! empty( $smartshare_post_time_data ) ) {
						foreach ( $smartshare_post_time_data as $smartshare_post_time_data_value ) {
							$social_time = maybe_unserialize( $smartshare_post_time_data_value->social_time );
							$table_class = 'facebooktbl';
							if ( 'facebook' === $smartshare_post_time_data_value->social_account ) {
								$table_class = 'facebooktbl';
							} else if ( 'twitter' === $smartshare_post_time_data_value->social_account ) {
								$table_class = 'twittertbl';
							} else if ( 'linkedin' === $smartshare_post_time_data_value->social_account ) {
								$table_class = 'linkedintbl';
							}
							?>
							<table style="border: 1px solid black; margin-top:50px;" class="<?php echo esc_attr( $table_class ) ?>">
								<tr>
									<th colspan="5"
									    align="center"> <?php esc_html_e( ucfirst( $smartshare_post_time_data_value->social_account ) ); ?> </th>
								</tr>
								<tr>
									<th style="border: 1px solid black"><?php esc_html_e( 'Slot 1' ); ?></th>
									<th style="border: 1px solid black"><?php esc_html_e( 'Slot 2' ); ?></th>
									<th style="border: 1px solid black"><?php esc_html_e( 'Slot 3' ); ?></th>
									<th style="border: 1px solid black"><?php esc_html_e( 'Slot 4' ); ?></th>
									<th style="border: 1px solid black"><?php esc_html_e( 'Slot 5' ); ?></th>
								</tr>
								<tr>
									<?php foreach ( $social_time as $social_time_value ) {
										$social_time_value = date( "h:i a", strtotime( $social_time_value ) );
										?>
										<td style="border: 1px solid black"><?php esc_html_e( $social_time_value ); ?></td>
									<?php } ?>
								</tr>
							</table>
						<?php }
					}
				}
				?>
			</div>
		</div>
	</div>
	<?php require_once plugin_dir_path( __FILE__ ) . 'header/plugin-sidebar.php'; ?>
</div>

