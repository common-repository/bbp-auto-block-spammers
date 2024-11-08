<?php

if ( ! defined( 'ABSPATH' ) ) exit;


function bbpab_admin_setting( $settings ) {

	$settings['bbp_settings_users']['_bbp_spam_limit'] = array(
			'title'             => __( 'Spam Limit', 'bbpress' ),
			'callback'          => 'bbp_admin_setting_callback_spam_limit',
			'sanitize_callback' => 'intval',
			'args'              => array()
		);

	return $settings;

}
add_filter( 'bbp_admin_get_settings_fields', 'bbpab_admin_setting', 99 );


function bbp_admin_setting_callback_spam_limit() {
?>

	<input name="_bbp_spam_limit" id="_bbp_spam_limit" type="number" min="0" step="1" value="<?php bbp_form_option( '_bbp_spam_limit', '0' ); ?>" class="small-text"<?php bbp_maybe_admin_setting_disabled( '_bbp_spam_limit' ); ?> />
	<label for="_bbp_spam_limit"><?php esc_html_e( "Entries marked as spam. When exceeded, user's forum role will be changed to 'Blocked'", 'bbpress' ); ?></label>

<?php
}



// add a field to user edit screen only if not on a network admin screen
if ( ! is_network_admin() ) {
	add_action( 'edit_user_profile', 'bbp_admin_user_spam_entries_count', 99 );
	add_action( 'edit_user_profile_update', 'bbp_admin_user_spam_entries_count_update', 99 );
}



function bbp_admin_user_spam_entries_count( $profileuser ) {

	if ( ! current_user_can( 'edit_user', $profileuser->ID ) ) {
		return;
	}

	$spam_count = get_user_option( 'bbpress_spam_count', $profileuser->ID );
	if ( empty( $spam_count ) ) {
		$spam_count = 0;
	} else {
		$spam_count = intval( $spam_count );
	}
	
	?>

	<h3><?php esc_html_e( 'Forums - Spam Count', 'bbpress' ); ?></h3>

	<table class="form-table">
		<tbody>
			<tr>
				<th><label for="bbp-forums-spam-count"><?php esc_html_e( 'Number of Entries', 'bbpress' ); ?></label></th>
				<td>

					<input name="bbp_spam_count" id="bbp_spam_count" type="number" min="0" step="1" value="<?php echo $spam_count; ?>" class="small-text" />
					<p class="description"><?php _e("Changing this number will not affect user's Forum Role. <br>Please use the selector above to change the user's Forum Role in this context.", "bbpress");?></p>

				</td>
			</tr>

		</tbody>
	</table>

	<?php
}



// save spam count field value on user edit screen
function bbp_admin_user_spam_entries_count_update( $user_id ) {

	if ( !current_user_can( 'edit_user', $user_id ) ) {
		return false;
	}

	if ( $_POST['bbp_spam_count'] == '0' || empty( $_POST['bbp_spam_count'] ) ) {

		delete_user_option( $user_id, 'bbpress_spam_count' );

	} else {
		$spam_count = intval( $_POST['bbp_spam_count'] );
		update_user_option( $user_id, 'bbpress_spam_count', $spam_count  );

	}

}
add_action( 'edit_user_profile_update', 'bbp_admin_user_spam_entries_count_update', 99 );
