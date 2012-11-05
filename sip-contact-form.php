<?php
/*
Plugin Name: Sip Contact Form Demo
Plugin URI: http://shopitpress.com
Author: atinder
Version: 1.0
*/

// Prevent loading this file directly
defined( 'ABSPATH' ) || exit;

require_once dirname( __FILE__ ) . '/class.sip.contact.form.php';



class SipContactFormTest{
	private $_wrapper;
	public function _construct(){
		$this->_wrapper = new SipContactForm();
		add_action('after_theme_setup',array($this,'add_shortcode'));

	}


	public function add_shortcode(){

		add_shortcode('sip_contact',array( $this->_wrapper, 'sip_cform' ));

	}

}

new SipContactFormTest();