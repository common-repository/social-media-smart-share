<?php
if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if access directly
}

/**
 * Twitter authentication API code
 */
require plugin_dir_path( dirname( dirname( __FILE__ ) ) ) . 'twitteroauth/autoload.php';

use Abraham\TwitterOAuth\TwitterOAuth;

/**
 * Facebook authentication API code
 */
require_once plugin_dir_path( dirname( dirname( __FILE__ ) ) ) . 'Facebook/autoload.php';

use Facebook\Facebook;
use Facebook\Exceptions\FacebookResponseException;
use Facebook\Exceptions\FacebookSDKException;

/**
 * Linkedin authentication API code
 */
require_once plugin_dir_path( dirname( dirname( __FILE__ ) ) ) . 'linkedin/vendor/autoload.php';

// POST DATA FOR TWITTER
$ss_twitter_consumer_key = filter_input( INPUT_POST, 'ss_twitter_consumer_key', FILTER_SANITIZE_STRING ); // Twitter consumer key
$ss_twitter_consumer_key = isset( $ss_twitter_consumer_key ) && ! empty( $ss_twitter_consumer_key ) ? $ss_twitter_consumer_key : '';

$ss_twitter_consumer_secret = filter_input( INPUT_POST, 'ss_twitter_consumer_secret', FILTER_SANITIZE_STRING ); // Twitter consumer secret
$ss_twitter_consumer_secret = isset( $ss_twitter_consumer_secret ) && ! empty( $ss_twitter_consumer_secret ) ? $ss_twitter_consumer_secret : '';

$ss_twitter_access_token = filter_input( INPUT_POST, 'ss_twitter_access_token', FILTER_SANITIZE_STRING ); // Twitter access token
$ss_twitter_access_token = isset( $ss_twitter_access_token ) && ! empty( $ss_twitter_access_token ) ? $ss_twitter_access_token : '';

$ss_twitter_access_token_secret = filter_input( INPUT_POST, 'ss_twitter_access_token_secret', FILTER_SANITIZE_STRING ); // Twitter access token secret
$ss_twitter_access_token_secret = isset( $ss_twitter_access_token_secret ) && ! empty( $ss_twitter_access_token_secret ) ? $ss_twitter_access_token_secret : '';

$ss_twitter_submit = filter_input( INPUT_POST, 'ss_twitter_submit', FILTER_SANITIZE_STRING ); // Twitter submit
$ss_twitter_submit = isset( $ss_twitter_submit ) && ! empty( $ss_twitter_submit ) ? $ss_twitter_submit : '';

$ss_fb_state = filter_input( INPUT_GET, 'state', FILTER_SANITIZE_STRING ); // Get state from URL
$ss_fb_state = isset( $ss_fb_state ) && ! empty( $ss_fb_state ) ? $ss_fb_state : '';

$ss_get_code = filter_input( INPUT_GET, 'code', FILTER_SANITIZE_STRING ); // Get code from URL
$ss_get_code = isset( $ss_get_code ) && ! empty( $ss_get_code ) ? $ss_get_code : '';

// Get user accounts data.
$ss_user_account_data = get_option( 'ss_user_account_data' );
$ss_user_account_data = isset( $ss_user_account_data ) & ! empty( $ss_user_account_data ) ? $ss_user_account_data : array();

$ss_submit_value = isset( $_SESSION['ss_submit_value'] ) ? $_SESSION['ss_submit_value'] : ''; //phpcs:ignore

$allowed_tags = array(
	'p' => array(),
	'a' => array(
		'href'   => array(),
		'target' => array(),
	),
	'b' => array(),
);

if ( 'Submit' === $ss_twitter_submit ) {

	if ( wp_verify_nonce( FILTER_INPUT( INPUT_POST, 'smartshare_twiiter_form_data_nonce', FILTER_SANITIZE_STRING ), 'smartshare_twiiter_form_data' ) ) {

		$ss_account_validation_message = array();

		if ( empty( $ss_twitter_consumer_key ) || empty( $ss_twitter_consumer_secret ) || empty( $ss_twitter_access_token ) || empty( $ss_twitter_access_token_secret ) ) {
			$ss_account_validation_message['error'] = 'All the twitter fields are mandatory for the authentication.';
		} else {
			$connection = new TwitterOAuth( "$ss_twitter_consumer_key", "$ss_twitter_consumer_secret", "$ss_twitter_access_token", "$ss_twitter_access_token_secret" );
			$content    = $connection->get( "account/verify_credentials" );

			if ( empty( $content->errors ) ) {

				$ss_twitter_acc_id   = isset( $content->id ) && ! empty( $content->id ) ? $content->id : '';
				$ss_twitter_acc_name = isset( $content->name ) && ! empty( $content->screen_name ) ? $content->screen_name : '';

				$ss_user_account_data['tw_account'][] = array(
					'consumer_key'          => $ss_twitter_consumer_key,
					'consumer_secret'       => $ss_twitter_consumer_secret,
					'consumer_token'        => $ss_twitter_access_token,
					'consumer_token_secret' => $ss_twitter_access_token_secret,
					'account_id'            => $ss_twitter_acc_id,
					'account_name'          => $ss_twitter_acc_name,
				);

				update_option( 'ss_user_account_data', $ss_user_account_data, false );

			}
		}
		$tw_error = $content->errors[0]->message ? esc_html($content->errors[0]->message) : '';
		if(isset($tw_error) && !empty($tw_error)){
			set_transient( 'ss_error_message', $tw_error , 45 );
		}
	}
} else if ( 'ss_fb_submit' === $ss_submit_value && ! empty( $ss_get_code ) ) {

		$ss_fb_app_id_obj = filter_input( INPUT_COOKIE, 'ss_fb_app_id', FILTER_SANITIZE_NUMBER_INT ); //Facebook App ID
		$ss_fb_app_id     = isset( $ss_fb_app_id_obj ) && ! empty( $ss_fb_app_id_obj ) ? $ss_fb_app_id_obj : '';

		$ss_fb_app_secret_obj = filter_input( INPUT_COOKIE, 'ss_fb_app_secret', FILTER_SANITIZE_STRING ); //Facebook App Secret
		$ss_fb_app_secret     = isset( $ss_fb_app_secret_obj ) && ! empty( $ss_fb_app_secret_obj ) ? $ss_fb_app_secret_obj : '';

		$redirectURL   = admin_url( 'admin.php?page=smartshare-accounts' ); //Callback URL
		$fbPermissions = array( 'public_profile', 'email' );  //Optional permissions

		unset( $_SESSION['ss_submit_value'] ); //phpcs:ignore

		$fb = new Facebook( array(
			'app_id'                => $ss_fb_app_id,
			'app_secret'            => $ss_fb_app_secret,
			'default_graph_version' => 'v2.10',
		) );

		// Get redirect login helper
		$helper = $fb->getRedirectLoginHelper();

		if ( isset( $ss_get_code ) ) {
			$helper->getPersistentDataHandler()->set( 'state', $ss_fb_state );
		}

		try {
			$accessToken = $helper->getAccessToken( $redirectURL );
		} catch ( FacebookResponseException $e ) {
			echo 'Graph returned an error: ' . esc_html( $e->getMessage() );
			exit;
		} catch ( FacebookSDKException $e ) {
			echo 'Facebook SDK returned an error: ' . esc_html( $e->getMessage() );
			exit;
		}

		if ( isset( $accessToken ) ) {
			// OAuth 2.0 client handler helps to manage access tokens
			$oAuth2Client = $fb->getOAuth2Client();
			try {
				/*Get Long live access token 60 days*/
				$token_data_request  = $fb->get( '/oauth/access_token?client_id=' . $ss_fb_app_id . '&client_secret=' . $ss_fb_app_secret . '&grant_type=fb_exchange_token&fb_exchange_token=' . $accessToken, $accessToken );
				$token_data_response = $token_data_request->getGraphNode()->asArray();
				$ss_fb_account_token = isset( $token_data_response['access_token'] ) && ! empty( $token_data_response['access_token'] ) ? $token_data_response['access_token'] : '';

				$profileRequest = $fb->get( '/me?fields=name,first_name,last_name,email,link,gender,locale,cover,picture,accounts{about,page_token,access_token,general_info,name},groups{name,id}', $ss_fb_account_token );
				$fbUserProfile  = $profileRequest->getGraphNode()->asArray();

			} catch ( FacebookResponseException $e ) {
				echo 'Graph returned an error: ' . esc_html( $e->getMessage() );
				exit;
			} catch ( FacebookSDKException $e ) {
				echo 'Facebook SDK returned an error: ' . esc_html( $e->getMessage() );
				exit;
			}
		}

		$ss_fb_account_id     = isset( $fbUserProfile['id'] ) && ! empty( $fbUserProfile['id'] ) ? $fbUserProfile['id'] : '';
		$ss_fb_account_name   = isset( $fbUserProfile['name'] ) && ! empty( $fbUserProfile['name'] ) ? $fbUserProfile['name'] : '';
		$ss_fb_account_email  = isset( $fbUserProfile['email'] ) && ! empty( $fbUserProfile['email'] ) ? $fbUserProfile['email'] : '';
		$ss_fb_account_pages  = isset( $fbUserProfile['accounts'] ) && ! empty( $fbUserProfile['accounts'] ) ? $fbUserProfile['accounts'] : array();
		$ss_fb_account_groups = isset( $fbUserProfile['groups'] ) && ! empty( $fbUserProfile['groups'] ) ? $fbUserProfile['groups'] : array();

		$ss_user_account_data['fb_account'][] = array(
			'account_id'     => $ss_fb_account_id,
			'account_token'  => $ss_fb_account_token,
			'account_name'   => $ss_fb_account_name,
			'account_pages'  => $ss_fb_account_pages,
			'account_groups' => $ss_fb_account_groups,
		);
		update_option( 'ss_user_fb_account', '1', false );
		update_option( 'ss_user_account_data', $ss_user_account_data, false );

} else if ( 'ss_li_submit' === $ss_submit_value && ! empty( $ss_get_code ) ) {

	$ss_li_client_id = filter_input( INPUT_COOKIE, 'ss_linkedin_client_id', FILTER_SANITIZE_STRING ); //LinkedIn App ID
	$ss_li_client_id = isset( $ss_li_client_id ) && ! empty( $ss_li_client_id ) ? $ss_li_client_id : '';

	$ss_li_client_secret = filter_input( INPUT_COOKIE, 'ss_linkedin_client_secret', FILTER_SANITIZE_STRING ); //LinkedIn App Secret
	$ss_li_client_secret = isset( $ss_li_client_secret ) && ! empty( $ss_li_client_secret ) ? $ss_li_client_secret : '';

	$ss_li_state = filter_input( INPUT_GET, 'state', FILTER_SANITIZE_STRING );
	$ss_li_state = isset( $ss_li_state ) && ! empty( $ss_li_state ) ? $ss_li_state : '';

	$redirectURL = admin_url( 'admin.php?page=smartshare-accounts' ); //Callback URL

	unset( $_SESSION['ss_submit_value'] ); //phpcs:ignore

	$provider = new League\OAuth2\Client\Provider\LinkedIn( [
		'clientId'     => $ss_li_client_id,
		'clientSecret' => $ss_li_client_secret,
		'redirectUri'  => $redirectURL,
	] );

	if ( isset( $ss_li_state ) && empty( $ss_get_code ) ) {
		if ( empty( $ss_li_state ) || ( filter_input( INPUT_GET, 'state' ) !== filter_input( INPUT_SESSION, 'oauth2state' ) ) ) {

			unset( $_SESSION['oauth2state'] ); //phpcs:ignore
			exit( 'Invalid state' );

		}
	} else {
		// Try to get an access token (using the authorization code grant)
		$token = $provider->getAccessToken( 'authorization_code', [
			'code' => filter_input( INPUT_GET, 'code' ),
		] );
		// Optional: Now you have a token you can look up a users profile data
		try {

			// We got an access token, let's now get the user's details
			$linkedin_user = $provider->getResourceOwner( $token );

		} catch ( Exception $e ) {

			// Failed to get user details
			exit( 'Oh dear...' );
		}

		$ss_linkedin_account_id         = ( ! empty( $linkedin_user ) && ! empty( $linkedin_user->getid() ) ) ? $linkedin_user->getid() : '';
		$ss_linkedin_account_first_name = ( ! empty( $linkedin_user ) && ! empty( $linkedin_user->getFirstName() ) ) ? $linkedin_user->getFirstName() : '';
		$ss_linkedin_account_last_name  = ( ! empty( $linkedin_user ) && ! empty( $linkedin_user->getlastName() ) ) ? $linkedin_user->getlastName() : '';
		$ss_linkedin_account_token      = ! empty( $token->getToken() ) ? $token->getToken() : '';

		// Use this to interact with an API on the users behalf
		$ss_user_account_data['linkedin_account'][] = array(
			'account_id'         => $ss_linkedin_account_id,
			'account_first_name' => $ss_linkedin_account_first_name,
			'account_last_name'  => $ss_linkedin_account_last_name,
			'account_token'      => $ss_linkedin_account_token,
		);
		update_option( 'ss_user_account_data', $ss_user_account_data, false );
	}
}

$total_fb_profile       = isset( $ss_user_account_data['fb_account'] ) ? count( $ss_user_account_data['fb_account'] ) : 0; // Get user total facebook profile count.
$total_tw_profile       = isset( $ss_user_account_data['tw_account'] ) ? count( $ss_user_account_data['tw_account'] ) : 0; // Get user total twitter profile count.
$total_linkedin_profile = isset( $ss_user_account_data['linkedin_account'] ) ? count( $ss_user_account_data['linkedin_account'] ) : 0; // Get user total linkedin profile count.

?>
<div class="wrap">
	<?php require_once plugin_dir_path( __FILE__ ) . 'header/plugin-header.php'; ?>
	<div class="ss-container">
		<div class="ss-tabs-container ss-animated-fade">
			<div class="ss-tab-content">
				<p class="ss-tab-info">
					<?php
					printf( '%1$s<br>%2$s <strong>%3$s</strong> %4$s.', esc_html( 'To link any social media profile, please click on respective social media button.' ), esc_html( 'To de-synchronize the social account click on' ), esc_html( ' (x)' ), esc_html( 'Delete icon', 'social-media-smart-share' ) ); ?>

				<div class="ss-tab ss-active">

					<?php
					$twitter_icon_img  = SMART_SHARE_PLUGIN_URL . 'admin/images/ss-twitter-icon.png';
					$facebook_icon_img = SMART_SHARE_PLUGIN_URL . 'admin/images/ss-facebook.png';
					$linkedin_icon_img = SMART_SHARE_PLUGIN_URL . 'admin/images/ss-linkedin.png';
					?>

					<ul class="ss-social-profile-list">

						<!-- Twitter Profile -->
						<li>
							<a class="ss-account-fancybox" href="#tw-detailpopup">
								<img src="<?php echo esc_url( $twitter_icon_img ); ?>">
								<span><?php esc_html_e( 'Link Twitter Profile', 'social-media-smart-share' ); ?></span>
							</a>
							<div style="display:none">
								<div id="tw-detailpopup" class="social-account-detail-popup">
									<?php if ( 1 !== $total_tw_profile ) { ?>
										<h3><?php esc_html_e( 'Twitter Credentials', 'social-media-smart-share' ); ?></h3>
										<form action="" method="post" id="ss_twitter_form">
											<div>
												<label for="ss_twitter_consumer_key"><?php esc_html_e( 'Twitter Consumer Key', 'social-media-smart-share' ); ?>
													<span style="color: red;">*</span></label>
												<input type="text" name="ss_twitter_consumer_key"
												       id="ss_twitter_consumer_key" value="" required>
											</div>
											<div>
												<label for="ss_twitter_consumer_secret"><?php esc_html_e( 'Twitter Consumer Secret', 'social-media-smart-share' ); ?>
													<span style="color: red;">*</span></label>

												<input type="text" name="ss_twitter_consumer_secret"
												       id="ss_twitter_consumer_secret" value="" required>
											</div>
											<div>
												<label for="ss_twitter_access_token"><?php esc_html_e( 'Access Token', 'social-media-smart-share' ); ?>
													<span
														style="color: red;">*</span></label>
												<input type="text" name="ss_twitter_access_token"
												       id="ss_twitter_access_token" value="" required>
											</div>
											<div>
												<label for="ss_twitter_access_token_secret"><?php esc_html_e( 'Access Token Secret', 'social-media-smart-share' ); ?>
													<span style="color: red;">*</span></label>
												<input type="text" name="ss_twitter_access_token_secret"
												       id="ss_twitter_access_token_secret" value="" required>
											</div>
											<div>
												<input type="submit" class="ss-button-primary"
												       name="ss_twitter_submit" id="ss_twitter_submit"
												       value="Submit">
												<input type="button" class="ss-button-primary ss_form_cancel"
												       name="ss_twitter_cancel" id="ss_twitter_cancel"
												       value="Reset">
											</div>
											<?php wp_nonce_field( 'smartshare_twiiter_form_data', 'smartshare_twiiter_form_data_nonce' ); ?>
										</form>
										<div class="ss_cred_info">
											<?php
											printf(
												wp_kses( __( '<p>You can check <a target="_blank" href="%s"><b>here</b></a> for how to get these details.</p>', 'social-media-smart-share' ),
													$allowed_tags
												), esc_url( 'http://www.thedotstore.com/docs/plugins/smart-share-wordpress-plugins/free-plugin-settings/create-social-application/create-twitter-application-smart-share-plugin/' )
											);

											?>
										</div>
									<?php } else { ?>
										<div><?php esc_html_e( 'You already added one Twitter profile.', 'social-media-smart-share' ); ?></div>
									<?php } ?>
								</div>
							</div>

							<?php if ( ! empty( $ss_user_account_data['tw_account'] ) ) { ?>
								<ul class="ss-social-account-user-list">
									<?php foreach ( $ss_user_account_data['tw_account'] as $key => $value ) {
										$acc_full_name_with_dots = strlen( $value['account_name'] ) > 18 ? substr( $value['account_name'], 0, 18 ) . ".." : $value['account_name'];
										?>
										<li>
											<label data-id="<?php echo esc_attr( $value['account_id'] ); ?>"
											       data-name="<?php echo esc_attr( $value['account_name'] ); ?>"><?php echo esc_attr( $acc_full_name_with_dots ); ?></label>
											<span class="close remove-twitter-entry"
											      data-index="<?php echo esc_attr( $key ); ?>">&times;</span>
										</li>
									<?php } ?>
								</ul>
							<?php } ?>
						</li>

						<!-- Facebook Profile -->
						<li>
							<a class="ss-account-fancybox" href="#fb-detailpopup">
								<img src="<?php echo esc_url( $facebook_icon_img ); ?>">
								<span><?php esc_html_e( 'Link Facebook Profile', 'social-media-smart-share' ); ?></span>
							</a>
							<div style="display:none">
								<div id="fb-detailpopup" class="social-account-detail-popup">
									<?php if ( 1 !== $total_fb_profile ) { ?>
										<h3><?php esc_html_e( 'Facebook Credentials', 'social-media-smart-share' ); ?></h3>
										<form method="post" id="ss_facebook_form">
											<input type="hidden" name="action" value="admin_add_user_fb_account">
											<div>
												<label for="ss_fb_app_id"><?php esc_html_e( 'App ID', 'social-media-smart-share' ); ?>
													<span style="color: red;">*</span></label>
												<input type="text" name="ss_fb_app_id" id="ss_fb_app_id"
												       value="" required>
											</div>
											<div>
												<label for="ss_fb_app_secret"><?php esc_html_e( 'App Secret', 'social-media-smart-share' ); ?>
													<span style="color: red;">*</span></label>
												<input type="text" name="ss_fb_app_secret" id="ss_fb_app_secret"
												       value="" required>
											</div>
											<div>
												<input type="button" name="ss_fb_submit" id="ss_fb_submit"
												       value="Submit" class="ss-button-primary">
												<input type="button" name="ss_fb_cancel" id="ss_fb_cancel"
												       value="Reset" class="ss-button-primary ss_form_cancel">
											</div>
											<?php wp_nonce_field( 'smartshare_facebook_form_data', 'smartshare_facebook_form_data_nonce' ); ?>
										</form>
										<div class="ss_cred_info">
											<?php
											printf(
												wp_kses( __( '<p>You can check <a target="_blank" href="%s"><b>here</b></a> for how to get these details.</p>', 'social-media-smart-share' ),
													$allowed_tags
												), 'http://www.thedotstore.com/docs/plugins/smart-share-wordpress-plugins/free-plugin-settings/create-social-application/create-facebook-application-smart-share-plugin/'
											);
											?>
										</div>
									<?php } else { ?>
										<div><?php esc_html_e( 'You already added one Facebook profile.', 'social-media-smart-share' ); ?></div>
									<?php } ?>
								</div>
							</div>

							<?php if ( ! empty( $ss_user_account_data['fb_account'] ) ) { ?>
								<ul class="ss-social-account-user-list">
									<?php foreach ( $ss_user_account_data['fb_account'] as $key => $value ) {
										$acc_full_name_with_dots = strlen( $value['account_name'] ) > 18 ? substr( $value['account_name'], 0, 18 ) . ".." : $value['account_name'];
										?>
										<li>
											<label data-id="<?php echo esc_attr( $value['account_id'] ); ?>"
											       data-name="<?php echo esc_attr( $value['account_name'] ); ?>"><?php echo esc_attr( $acc_full_name_with_dots ); ?></label>
											<span class="close remove-facebook-entry"
											      data-index="<?php echo esc_attr( $key ); ?>">&times;</span>
										</li>
									<?php } ?>
								</ul>
							<?php } ?>
						</li>

						<!--- Linkedin Profile --->
						<li>
							<a class="ss-account-fancybox" href="#li-detailpopup">
								<img src="<?php echo esc_url( $linkedin_icon_img ); ?>">
								<span><?php esc_html_e( 'Link Linked In Profile', 'social-media-smart-share' ); ?></span>
							</a>
							<div style="display:none">
								<div id="li-detailpopup" class="social-account-detail-popup">
									<?php if ( 1 !== $total_linkedin_profile ) { ?>
										<h3><?php esc_html_e( 'Linked In Credentials', 'social-media-smart-share' ); ?></h3>
										<?php $admin_post_url = get_admin_url() . 'admin-post.php'; ?>
										<form action="<?php echo esc_url( $admin_post_url ); ?>" id="ss_linkedin_form" method="post">
											<input type="hidden" name="action" value="admin_add_user_li_account">
											<div>
												<label for="ss_linkedin_client_id"><?php esc_html_e( 'Client Id', 'social-media-smart-share' ); ?>
													<span style="color: red;">*</span></label>
												<input type="text" required name="ss_linkedin_client_id"
												       id="ss_linkedin_client_id" value="">
											</div>
											<div>
												<label for="ss_linkedin_client_secret"><?php esc_html_e( 'Secret Key', 'social-media-smart-share' ); ?>
													<span style="color: red;">*</span></label>
												<input type="text" required name="ss_linkedin_client_secret" id="ss_linkedin_client_secret" value="">
											</div>
											<input type="submit" name="ss_linkedin_submit" id="ss_linkedin_submit" value="Submit" class="ss-button-primary">
											<input type="button" name="ss_linkedin_cancel" id="ss_linkedin_cancel" value="Reset" class="ss-button-primary ss_form_cancel">
											<?php wp_nonce_field( 'smartshare_linkedin_form_data', 'smartshare_linkedin_form_data_nonce' ); ?>
										</form>
										<div class="ss_cred_info">
											<?php
											printf(
												wp_kses( __( '<p>You can check <a target="_blank" href="%s"><b>here</b></a> for how to get these details.</p>', 'social-media-smart-share' ),
													$allowed_tags
												), 'http://www.thedotstore.com/docs/plugins/smart-share-wordpress-plugins/free-plugin-settings/create-social-application/create-linkedin-app-smart-share-plugin/'
											);
											?>
										</div>
									<?php } else { ?>
										<div><?php esc_html_e( 'You already added one LinkedIn profile.', 'social-media-smart-share' ); ?></div>
									<?php } ?>
								</div>
							</div>

							<?php if ( ! empty( $ss_user_account_data['linkedin_account'] ) ) { ?>
								<ul class="ss-social-account-user-list">
									<?php foreach ( $ss_user_account_data['linkedin_account'] as $key => $value ) {
										$acc_full_name           = $value['account_first_name'] . ' ' . $value['account_last_name'];
										$acc_full_name_with_dots = strlen( $acc_full_name ) > 18 ? substr( $acc_full_name, 0, 18 ) . ".." : $acc_full_name;
										?>
										<li>
											<label data-id="" data-name="<?php echo esc_attr( $acc_full_name ); ?>"><?php echo esc_attr( $acc_full_name_with_dots ); ?></label>
											<span class="close remove-linkedin-entry"
											      data-index="<?php echo esc_attr( $key ); ?>">&times;</span>
										</li>
									<?php } ?>
								</ul>
							<?php } ?>
						</li>

					</ul>

				</div>

			</div>

		</div>

	</div>
	<?php require_once plugin_dir_path( __FILE__ ) . 'header/plugin-sidebar.php'; ?>
</div>
