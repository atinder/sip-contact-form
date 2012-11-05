<?php

/**
 * Sip Contact Form Class
 * @author Atinder <shopitpress.com>
 * @link http://shopitpress.com
 * @example sip-contact-form.php
 */

// Prevent loading this file directly
defined( 'ABSPATH' ) || exit;

if ( ! class_exists( 'SipContactForm' ) ) {

	class SipContactForm {

		private $config = array();

		public function __construct($config = null){
			
			$defaults = array(
				"email" => get_bloginfo('admin_email'),
				"subject" => 'ContactForm Submission',
				"label_name" => 'Name',
				"label_email" => 'E-mail',
				"label_message" => 'Message',
				"label_submit" => 'Submit',
				"error_empty" => 'Please fill in all the required fields.',
				"error_noemail" => 'Please enter a valid e-mail address.',
				"success" => 'Thanks for your e-mail! We will get back to you as soon as we can.'
			);
			
			$this->config = wp_parse_args($this->config,$defaults);
			add_action('wp_enqueue_scripts', array($this,'enqueue'));
		}

		public function enqueue(){
    		wp_enqueue_style( 'sip-contact-form-class', plugins_url('/css/style.css',__FILE__)   );
		}
		public function getIp() {     
			if (isset($_SERVER["HTTP_X_FORWARDED_FOR"])) {
			    return $_SERVER["HTTP_X_FORWARDED_FOR"];     
			}
			elseif (isset($_SERVER["HTTP_CLIENT_IP"])) {
			    return $_SERVER["HTTP_CLIENT_IP"];     
			}
			else {
			    return $_SERVER["REMOTE_ADDR"];     
			} 
		}

		// the shortcode
		public function sip_cform($atts) {

			extract(shortcode_atts($this->config, $atts));

			if ($_SERVER['REQUEST_METHOD'] == 'POST') {
				$error = false;
				$required_fields = array("your_name", "email", "message");

				foreach ($_POST as $field => $value) {
					if (get_magic_quotes_gpc()) {
						$value = stripslashes($value);
					}
					$form_data[$field] = strip_tags($value);
				}

				foreach ($required_fields as $required_field) {
					$value = trim($form_data[$required_field]);
					if(empty($value)) {
						$error = true;
						$result = $error_empty;
					}
				}

				if(!is_email($form_data['email'])) {
					$error = true;
					$result = $error_noemail;
				}

				if ($error == false) {
					$email_message = $form_data['message'] . "\n\nIP: " . $this->getIp();
					$headers  = "From: ".$form_data['your_name']." <".$form_data['email'].">\n";
					$headers .= "Content-Type: text/plain; charset=UTF-8\n";
					$headers .= "Content-Transfer-Encoding: 8bit\n";
					wp_mail($email, $subject, $email_message, $headers);
					$result = $success;
					$sent = true;
				}
			}

			if(isset($result) && $result != "") {
				$info = '<div class="info">'.$result.'</div>';
			}
			$email_form = '<form class="contact-form" method="post" action="'.get_permalink().'">
				<div>
					<label for="cf_name">'.$label_name.':</label>
					<input type="text" name="your_name" id="cf_name" size="50" maxlength="50" value="'.(isset($form_data['your_name']) ? $form_data['your_name'] : '' ).'" />
				</div>
				<div>
					<label for="cf_email">'.$label_email.':</label>
					<input type="text" name="email" id="cf_email" size="50" maxlength="50" value="'.(isset($form_data['email']) ? $form_data['email'] : '') .'" />
				</div>

				<div>
					<label for="cf_message">'.$label_message.':</label>
					<textarea name="message" class="cf_message" cols="50" rows="15">'.(isset($form_data['message']) ? $form_data['message'] : '' ).'</textarea>
				</div>
				<div>
					<input type="submit" value="'.$label_submit.'" name="send" id="cf_send" />
				</div>
			</form>';
			
			if(isset($sent) && $sent == true) {
				return $info;
			} else {
				return $info.$email_form;
			}
		}

	}

}