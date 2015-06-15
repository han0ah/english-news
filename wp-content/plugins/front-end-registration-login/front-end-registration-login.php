<?php
/*
Plugin Name: Front End Registration and Login
Plugin URI: https://pippinsplugins.com/creating-custom-front-end-registration-and-login-forms
Description: Provides simple front end registration and login forms
Version: 1.0
Author: Pippin Williamson
Author URI: https://pippinsplugins.com 
*/


function wpc_auto_redirect_after_logout(){  
  wp_redirect( home_url() );  
  exit();  
}  
add_action('wp_logout','wpc_auto_redirect_after_logout');  


//user registration login form
function pippin_registration_form() {
	if (!is_user_logged_in()) {

		global $pippin_load_css;

		// set this to true so the CSS is loaded
		$pippin_load_css = true;

		// check to make sure user registration is enabled
		$registration_enabled = get_option('users_can_register');

		// only show the registration form if allowed
		if ($registration_enabled) {
			$output = pippin_registration_form_fields();
		} else {
			$output = __('User registration is not enabled');
		}

		return $output;
	}
}
add_shortcode('register_form', 'pippin_registration_form');

//user login form
function pippin_login_form() {
	if (!is_user_logged_in()) {

		global $pippin_load_css;

		$pippin_load_css = true;

		$output = pippin_login_form_fields();
	} else {
		// could show some logged in user info here
		// $output = 'user info here';
	}
	return $output;
}
add_shortcode('login_form', 'pippin_login_form');

// get registration form fields
function pippin_registration_form_fields() {
 
	ob_start(); ?>	
		<h3 class="pippin_header"><?php _e('Register New Account'); ?></h3>
 
		<?php 
		// show any error messages after form submission
		pippin_show_error_messages(); ?>
 
		<form id="pippin_registration_form" class="pippin_form" action="" method="POST">
			<fieldset>
				<p>
					<label for="pippin_user_Login">ID</label>
					<input name="pippin_user_login" id="pippin_user_login" class="required" type="text"/>
				</p>
				<p>
					<label for="password"><?php _e('Password'); ?></label>
					<input name="pippin_user_pass" id="password" class="required" type="password"/>
				</p>
				<p>
					<label for="password_again">비밀번호 확인</label>
					<input name="pippin_user_pass_confirm" id="password_again" class="required" type="password"/>
				</p>
				<p>
					<label for="pippin_user_nickname">이름</label>
					<input name="pippin_user_nickname" id="pippin_user_nickname" class="required" type="text"/>
				</p>
				<p>
					<label for="pippin_user_email"><?php _e('Email'); ?></label>
					<input name="pippin_user_email" id="pippin_user_email" class="required" type="email"/>
				</p>
				<p>
					<input type="hidden" name="pippin_register_nonce" value="<?php echo wp_create_nonce('pippin-register-nonce'); ?>"/>
					<input type="submit" value="<?php _e('Register Your Account'); ?>"/>
				</p>
			</fieldset>
		</form>
	<?php
	return ob_get_clean();
}

// login form fields
function pippin_login_form_fields() {
 
	ob_start(); ?>
		<h3 class="pippin_header"><?php _e('Login'); ?></h3>
 
		<?php
		// show any error messages after form submission
		pippin_show_error_messages(); ?>
 
		<form id="pippin_login_form"  class="pippin_form" action="" method="post">
			<fieldset>
				<p>
					<label for="pippin_user_Login">ID</label>
					<input name="pippin_user_login" id="pippin_user_login" class="required" type="text"/>
				</p>
				<p>
					<label for="pippin_user_pass">비밀번호</label>
					<input name="pippin_user_pass" id="pippin_user_pass" class="required" type="password"/>
				</p>
				<p>
					<input type="hidden" name="pippin_login_nonce" value="<?php echo wp_create_nonce('pippin-login-nonce'); ?>"/>
					<input id="pippin_login_submit" type="submit" value="Login"/>
				</p>
			</fieldset>
		</form>
	<?php
	return ob_get_clean();
}

// logs a member in after submitting a form
function pippin_login_member() {
 
	if(isset($_POST['pippin_user_login']) && wp_verify_nonce($_POST['pippin_login_nonce'], 'pippin-login-nonce')) {
 
		// this returns the user ID and other info from the user name
		$user = get_userdatabylogin($_POST['pippin_user_login']);
 
		if(!$user) {
			// if the user name doesn't exist
			pippin_errors()->add('empty_username', __('Invalid username'));
		}
 
		if(!isset($_POST['pippin_user_pass']) || $_POST['pippin_user_pass'] == '') {
			// if no password was entered
			pippin_errors()->add('empty_password', __('Please enter a password'));
		}
 
		// check the user's login with their password
		if(!wp_check_password($_POST['pippin_user_pass'], $user->user_pass, $user->ID)) {
			// if the password is incorrect for the specified user
			pippin_errors()->add('empty_password', __('Incorrect password'));
		}
 
		// retrieve all error messages
		$errors = pippin_errors()->get_error_messages();
 
		// only log the user in if there are no errors
		if(empty($errors)) {
 
			wp_setcookie($_POST['pippin_user_login'], $_POST['pippin_user_pass'], true);
			wp_set_current_user($user->ID, $_POST['pippin_user_login']);	
			do_action('wp_login', $_POST['pippin_user_login']);
 
			wp_redirect(home_url()); exit;
		}
	}
}
add_action('init', 'pippin_login_member');

// register a new user
function pippin_add_new_member() {
  	if (isset( $_POST["pippin_user_login"] ) && wp_verify_nonce($_POST['pippin_register_nonce'], 'pippin-register-nonce')) {
		$user_login		= $_POST["pippin_user_login"];	
		$user_email		= $_POST["pippin_user_email"];
		$user_pass		= $_POST["pippin_user_pass"];
		$user_nickname  = $_POST["pippin_user_nickname"];
		$pass_confirm 	= $_POST["pippin_user_pass_confirm"];
 
		// this is required for username checks
		require_once(ABSPATH . WPINC . '/registration.php');
 
		if(username_exists($user_login)) {
			// Username already registered
			pippin_errors()->add('username_unavailable', '이미 존재하는 아이디입니다.');
		}
		if(!validate_username($user_login)) {
			// invalid username
			pippin_errors()->add('username_invalid', '사용할 수 없는 아이디입니다.');
		}
		if($user_login == '') {
			// empty username
			pippin_errors()->add('username_empty', 'ID를 입력하세요.');
		}
		if($user_nickname == '') {
			// empty nickname
			pippin_errors()->add('usernickname_empty','이름을 입력해 주세요.');
		}
		if(!is_email($user_email)) {
			//invalid email
			pippin_errors()->add('email_invalid', '올바른 이메일을 입력해 주세요.');
		}
		if(email_exists($user_email)) {
			//Email address already registered
			pippin_errors()->add('email_used', '이미 사용되고 있는 이메일입니다.');
		}
		if($user_pass == '') {
			// passwords do not match
			pippin_errors()->add('password_empty', '비밀번호를 입력해 주세요');
		}
		if($user_pass != $pass_confirm) {
			// passwords do not match
			pippin_errors()->add('password_mismatch', '비밀번호가 일치하지 않습니다.');
		}
 
		$errors = pippin_errors()->get_error_messages();
 
		// only create the user in if there are no errors
		if(empty($errors)) {
 
			$new_user_id = wp_insert_user(array(
					'user_login'		=> $user_login,
					'nickname'			=> $user_nickname,
					'user_pass'	 		=> $user_pass,
					'user_email'		=> $user_email,
					'user_registered'	=> date('Y-m-d H:i:s'),
					'role'				=> 'author'
				)
			);
			if($new_user_id) {
				// send an email to the admin alerting them of the registration
				wp_new_user_notification($new_user_id);
 
				// log the new user in
				wp_setcookie($user_login, $user_pass, true);
				wp_set_current_user($new_user_id, $user_login);	
				do_action('wp_login', $user_login);
 
				// send the newly created user to the home page after logging them in
				wp_redirect(home_url()); exit;
			}
 
		}
 
	}
}
add_action('init', 'pippin_add_new_member');


// used for tracking error messages
function pippin_errors(){
    static $wp_error; // Will hold global variable safely
    return isset($wp_error) ? $wp_error : ($wp_error = new WP_Error(null, null, null));
}

// displays error messages from form submissions
function pippin_show_error_messages() {
	if($codes = pippin_errors()->get_error_codes()) {
		echo '<div class="pippin_errors">';
		    // Loop error codes and display errors
		   foreach($codes as $code){
		        $message = pippin_errors()->get_error_message($code);
		        echo '<span class="error"><strong>' . __('Error') . '</strong>: ' . $message . '</span><br/>';
		    }
		echo '</div>';
	}	
}


// register our form css
function pippin_register_css() {
	wp_register_style('pippin-form-css', plugin_dir_url( __FILE__ ) . 'css/form.css');
}
add_action('init', 'pippin_register_css');

// load our form css
function pippin_print_css() {
	global $pippin_load_css;
 
	// this variable is set to TRUE if the short code is used on a page/post
	if ( ! $pippin_load_css )
		return; // this means that neither short code is present, so we get out of here
 
	wp_print_styles('pippin-form-css');
}
add_action('wp_footer', 'pippin_print_css');

?>