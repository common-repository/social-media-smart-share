"use strict";
(function( $ ) {

  image_priority_order();
  facebook_data_submit();
  remove_user_profile();
  reset_popup_field_data();
  create_schedule();
  share_page();

	$( '.ss-account-fancybox' ).fancybox();
	$( '.ss_delete_schedule_btn' ).fancybox();
	$( '.ss_pause_resume_schedule_btn' ).fancybox();
	$( '.ss-categories' ).select2();

	function image_priority_order(){
		$( '#ss_schedule_img_sortable' ).sortable( {
			update: function( event, ui ) {
				getOrder();
			}
		} );
	}

	/**
	 * Facebook User Activation
	 */
	function facebook_data_submit() {
		$( document ).on( 'click', '#ss_fb_submit', function(e) {

			var ss_fb_app_id = $( '#ss_fb_app_id' ).val();

			if(ss_fb_app_id == ''){
				var check_ss_fb_app_id = $( '#ss_fb_app_id' )[0];
				check_ss_fb_app_id.checkValidity();
				check_ss_fb_app_id.reportValidity();
				return false;
			}

			var ss_fb_app_secret = $( '#ss_fb_app_secret' ).val();
			if(ss_fb_app_secret == ''){
				var check_ss_fb_app_secret = $( '#ss_fb_app_secret' )[0];
				check_ss_fb_app_secret.checkValidity();
				check_ss_fb_app_secret.reportValidity();
				return false;
			}

			var ss_fb_submit = $( this ).attr( 'id' );

			if ( ss_fb_app_id !== '' && ss_fb_app_secret !== '' ) {
				var data = {
					'action': 'ss_user_fb_account_sync',
					'security': SS_object.ajax_nonce,
					'ss_fb_app_id': ss_fb_app_id,
					'ss_fb_app_secret': ss_fb_app_secret,
					'ss_fb_submit': ss_fb_submit
				};

				$.ajax( {
					dataType: 'JSON',
					url: SS_object.ajax_url,
					type: 'POST',
					data: data,
					success: function( response ) {
						if ( response[ 'data' ][ 'response' ] === 'ss-user-fb-profile-synced' ) {
							setTimeout(function(){
                location.href = response[ 'data' ][ 'fb_login_url' ];
							}, 500);
						}
					}
				} );
			}
		} );
	}

	/**
	 * Remove user profile
	 */
	function remove_user_profile() {
		$( document ).on( 'click', '.ss-social-account-user-list li span.close', function() {

			var profileName = $( this ).parent().find( 'label' ).attr( 'data-name' );
			var profileDataID = $( this ).parent().find( 'label' ).attr( 'data-id' );
			var profileAccount = '';

			// Set account key name for remove entry
			if ( $( this ).hasClass( 'remove-twitter-entry' ) ) {
				profileAccount = 'tw_account';
			} else if ( $( this ).hasClass( 'remove-facebook-entry' ) ) {
				profileAccount = 'fb_account';
			} else if ( $( this ).hasClass( 'remove-linkedin-entry' ) ) {
				profileAccount = 'linkedin_account';
			}

			var profileAccountIndex = $( this ).attr( 'data-index' );

			var data = {
				'action': 'ss_remove_user_profile',
				'ss_profile_data_id': profileDataID,
				'ss_profile_name': profileName,
				'ss_profile_account': profileAccount,
				'ss_profile_index': profileAccountIndex
			};

			$.ajax( {
				dataType: 'JSON',
				url: SS_object.ajax_url,
				type: 'POST',
				data: data,
				success: function( response ) {
					if ( 'ss-user-profile-deleted' === response[ 'data' ][ 'response' ]) {
            location.href = SS_object.ss_remove_profile_url;
					}
				}
			} );

		} );

	}

	/**
	 * Reset form data.
	 */
	function reset_popup_field_data(){
		$( document ).on( 'click', '.ss_form_cancel', function() {
			var form_id = $( this ).closest( 'form' ).prop( 'id' );
			$( '#' + form_id ).trigger( 'reset' );
		} );
	};

	/**
	 * Create Schedule functions
	 */
	function create_schedule(){
		$( document ).on( 'click', '.ss-schedule-form', function( e ) {

			var current_action = $( this ).attr( 'id' );
			var currSchedule = $( '#curr_scheduler' ).val();
			if ( currSchedule != '' && 'ss-schedule-create' === current_action ) {
				e.preventDefault();
				$.fancybox( $( '#ss_reschedule_modal' ) );
				return false;
			} else if ( currSchedule != '' && 'ss-schedule-reset' === current_action ) {
				e.preventDefault();
				$.fancybox( $( '#ss_reset_modal' ) );
				return false;
			} else {
				jQuery( 'body' ).append( '<div class="ss-loader"></div>' );
				$( '#ss-schedule-form' ).submit();
			}
		} );

		//Pause and Resumes Queue
		$( document ).on( 'click', '#ss_pause_resume_queue_yes', function() {

			$.fancybox.close();
			jQuery( 'body' ).append( '<div class="ss-loader"></div>' );

			var currSchedule = $( '#curr_scheduler' ).val();
			var current_queue_status = $( '#curr_scheduler_status' ).val();

			var data = {
				'action': 'ss_pause_resume_current_queue',
				'ss_current_queue_id': currSchedule,
				'ss_current_queue_status': current_queue_status,
			};

			$.ajax( {
				dataType: 'JSON',
				url: SS_object.ajax_url,
				type: 'POST',
				data: data,
				success: function() {
					jQuery( '.ss-loader' ).remove();
					location.reload();
				}
			} );

		} );

		$( document ).on( 'click', '#ss_pause_resume_queue_no', function() {
			$.fancybox.close();
		} );

		// Delete Queue
		$( document ).on( 'click', '#ss_del_queue_yes', function() {

			$.fancybox.close();
			jQuery( 'body' ).append( '<div class="ss-loader"></div>' );

			var currSchedule = $( '#curr_scheduler' ).val();

			var data = {
				'action': 'ss_delete_current_queue',
				'ss_current_queue_id': currSchedule,
			};

			$.ajax( {
				dataType: 'JSON',
				url: SS_object.ajax_url,
				type: 'POST',
				data: data,
				success: function() {
					jQuery( '.ss-loader' ).remove();
					location.reload();
				}
			} );

		} );

		$( document ).on( 'click', '#ss_del_queue_no', function() {
			$.fancybox.close();
		} );
	};

	/**
	 * Share Page JS
	 */
	function share_page(){

		$( document ).on( 'click', '.ss_skip_post_btn', function() {
			$.fancybox( $( '#ss-skip-queue-post' ) );
			var ss_current_post_id = $( this ).attr( 'data-post-id' );
			var ss_social_media = $( this ).attr( 'data-platform' );
			$( '#ss_skip_post_yes' ).attr( 'data-post-id', ss_current_post_id );
			$( '#ss_skip_post_yes' ).attr( 'data-platform', ss_social_media );
		} );

		$( document ).on( 'click', '#ss_skip_post_yes', function() {

			$.fancybox.close();
			jQuery( 'body' ).append( '<div class="ss-loader"></div>' );

			var ss_current_post_id = $( this ).attr( 'data-post-id' );
			var ss_social_media = $( this ).attr( 'data-platform' );

			var data = {
				'action': 'ss_skip_queue_post',
				'ss_social_media': ss_social_media,
				'ss_current_post_id': ss_current_post_id,
			};

			$.ajax( {
				dataType: 'JSON',
				url: SS_object.ajax_url,
				type: 'POST',
				data: data,
				success: function() {
					jQuery( '.ss-loader' ).remove();
					location.reload();
				}
			} );

		} );

		$( document ).on( 'click', '#ss_skip_post_no', function() {
			$( '#ss_skip_post_yes' ).attr( 'data-post-id', '' );
			$( '#ss_skip_post_yes' ).attr( 'data-platform', '' );
			$.fancybox.close();
		} );

		$( document ).on( 'click', '.delete-post-skipped', function() {
			$.fancybox( $( '#ss-delete-skipped-post' ) );
			var ss_current_post_id = $( this ).attr( 'data-post-id' );
			var ss_social_media = $( this ).attr( 'data-platform' );
			$( '#ss_delete_post_yes' ).attr( 'data-post-id', ss_current_post_id );
			$( '#ss_delete_post_yes' ).attr( 'data-platform', ss_social_media );
		} );

		$( document ).on( 'click', '#ss_delete_post_yes', function() {

			$.fancybox.close();
			jQuery( 'body' ).append( '<div class="ss-loader"></div>' );

			var ss_current_post_id = $( this ).attr( 'data-post-id' );
			var ss_social_media = $( this ).attr( 'data-platform' );

			var data = {
				'action': 'ss_delete_skipped_post',
				'ss_social_media': ss_social_media,
				'ss_current_post_id': ss_current_post_id,
			};

			$.ajax( {
				dataType: 'JSON',
				url: SS_object.ajax_url,
				type: 'POST',
				data: data,
				success: function() {
					jQuery( '.ss-loader' ).remove();
					location.reload();
				}
			} );

		} );

		$( document ).on( 'click', '#ss_delete_post_no', function() {
			$.fancybox.close();
			$( '#ss_delete_post_yes' ).attr( 'data-post-id', '' );
			$( '#ss_delete_post_yes' ).attr( 'data-platform', '' );
		} );

		$( document ).on( 'click', '.ss_instant_share', function() {
			$.fancybox( $( '#ss-instant-share-popup' ) );
			var ss_current_post_id = $( this ).attr( 'data-post-id' );
			var ss_social_media = $( this ).attr( 'data-platform' );
			$( '#ss_instant_share_post_yes' ).attr( 'data-post-id', ss_current_post_id );
			$( '#ss_instant_share_post_yes' ).attr( 'data-platform', ss_social_media );
		} );

		$( document ).on( 'click', '#ss_instant_share_post_yes', function() {
			$.fancybox.close();
			jQuery( 'body' ).append( '<div class="ss-loader"></div>' );
			var ss_current_post_id = $( this ).attr( 'data-post-id' );
			var ss_social_media = $( this ).attr( 'data-platform' );

			var data = {
				'action': 'ss_instant_share_post',
				'ss_social_media': ss_social_media,
				'ss_current_post_id': ss_current_post_id,
			};

			$.ajax( {
				dataType: 'JSON',
				url: SS_object.ajax_url,
				type: 'POST',
				data: data,
				success: function() {
					jQuery( '.ss-loader' ).remove();
					location.reload();
				},
				error: function() {
					alert( 'Error in sharing a post, Please try again later..!' );
					jQuery( '.ss-loader' ).remove();
				}
			} );

		} );

		$( document ).on( 'click', '#ss_instant_share_post_no', function() {
			$( '#ss_instant_share_post_yes' ).attr( 'data-post-id', '' );
			$( '#ss_instant_share_post_yes' ).attr( 'data-platform', '' );
			$.fancybox.close();
		} );

		$( document ).on( 'click', '.ss_queue_post_btn', function() {
			$.fancybox( $( '#ss-queue-failed-post' ) );
			var ss_current_post_id = $( this ).attr( 'data-post-id' );
			var ss_social_media = $( this ).attr( 'data-platform' );
			$( '#ss_queue_post_yes' ).attr( 'data-post-id', ss_current_post_id );
			$( '#ss_queue_post_yes' ).attr( 'data-platform', ss_social_media );
		} );

		$( document ).on( 'click', '#ss_queue_post_yes', function() {

			$.fancybox.close();
			jQuery( 'body' ).append( '<div class="ss-loader"></div>' );

			var ss_current_post_id = $( this ).attr( 'data-post-id' );
			var ss_social_media = $( this ).attr( 'data-platform' );

			var data = {
				'action': 'ss_queue_failed_post',
				'ss_social_media': ss_social_media,
				'ss_current_post_id': ss_current_post_id,
			};

			$.ajax( {
				dataType: 'JSON',
				url: SS_object.ajax_url,
				type: 'POST',
				data: data,
				success: function() {
					jQuery( '.ss-loader' ).remove();
					location.reload();
				}
			} );

		} );

		$( document ).on( 'click', '#ss_queue_post_no', function() {
			$( '#ss_skip_post_yes' ).attr( 'data-post-id', '' );
			$( '#ss_skip_post_yes' ).attr( 'data-platform', '' );
			$.fancybox.close();
		} );

	}

})( jQuery );

function getOrder() {
	var sortableOrder = jQuery( '#ss_schedule_img_sortable' ).sortable( 'toArray' );
	var sortableOrderString = sortableOrder.toString();
	jQuery( '#ss_img_priority' ).val( JSON.stringify( sortableOrderString ) );
}

jQuery('.ss_notice_msg').fadeIn('slow').delay(10000).fadeOut('slow');

jQuery("#ss-schedule-form .ss-schedule-post-tooltip-icon").click(function(){
	jQuery(this).parent().siblings(".ss-schedule-post-tooltip-block-box").toggle().css("disply", "block");
});
