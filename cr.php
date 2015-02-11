<?php 

/*
Plugin Name: Custom Registration
Plugin URI: http://burak-aydin.com
Description: This plugin disable auto-generated registration password and allow users to create own password on registration page. 
Author: Burak Aydin
Author URI: http://burak-aydin.com
Version: 1.0
License: GPLv2 or later
*/




// Call css file
function cr_adding_style(){

	wp_enqueue_style('cr-css',plugins_url('css/style.css',__FILE__));

}
add_action('init','cr_adding_style');




// Disable new user notification and auto-generated password emails. 
if(!function_exists('wp_new_user_notification')){

	function wp_new_user_notification( $user_id, $plaintext_pass ){
		return;
	}
	
}



// Disable "update password" notice on top. 
function cr_remove_default(){
	remove_action('admin_notices','default_password_nag');
}

add_action('admin_notices','cr_remove_default',9);




// Adding new element for register form 
function cr_register_form(){ 

		$user_pass=(!empty($_POST['user_pass'])) ? sanitize_text_field($_POST['user_pass']) : '';

		$confirm_pass=(!empty($_POST['confirm_pass'])) ? sanitize_text_field($_POST['confirm_pass']) : '';

	?>

	<p>		
		<label for="user_pass">New Password</label>
		<input type="password" class="input" name="user_pass" value="<?php echo esc_attr($user_pass); ?>">
	</p>

	<p>		
		<label for="confirm_pass">Repeat Password</label>
		<input type="password" class="input" name="confirm_pass" value="<?php echo esc_attr($confirm_pass); ?>">
	</p>

	<p>
		<div id="pass-strength-result"><?php _e('Strength indicator'); ?></div>
	</p>

<?php }

add_action('register_form','cr_register_form');



// Adding validation
function cr_registration_errors($error){

	if(empty($_POST['user_pass'])){
		$error->add('user_pass_error','<strong>ERROR: </strong> You should fill out the password field.');
	}

	if(empty($_POST['confirm_pass'])){
		$error->add('confirm_pass_error','<strong>ERROR: </strong> You should fill out the password field.');
	}

	if($_POST['user_pass'] != $_POST['confirm_pass']){
		$error->add('confirm_pass_error','<strong>ERROR: </strong> Passwords don\'t match each others');
	}

	return $error;

}

add_action('registration_errors','cr_registration_errors');



// Saving user password
function cr_saving_password($user_id){

	wp_update_user(array(
			'ID' 		=> $user_id,
			'user_pass' => $_POST['user_pass']
		));


	wp_redirect(site_url('wp-login.php'));

	exit;

}

add_action('user_register','cr_saving_password');