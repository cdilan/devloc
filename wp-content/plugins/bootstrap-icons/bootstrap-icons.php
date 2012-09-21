<?php
/*
Plugin Name: Bootstrap Icons
Plugin URI: http://www.pagelines.com/
Description: A free plugin with the open sourced icons from Bootstrap as shortcodes for PageLines
Version: 1.0
Author: PageLines
Author URI: http://www.pagelines.com
PageLines: true
Tags: extension
*/

/**
* Initialize
*/

class PL_Bootstrap_Icons {
	
	
	function __construct() {
		
		add_action( 'init', array( &$this, 'icons_init' ) );
		add_action( 'wp_enqueue_scripts', array( &$this, 'icons_css' ) );	
	}
	
	function icons_css() {
		
		wp_enqueue_style( 'bootstrap-icons' );
	}
	
	
	function icons_init() {

		// Set Global Path
		$file = sprintf( '%s/%s/bootstrap.min.css', WP_PLUGIN_URL,  basename( dirname( __FILE__ ) ) );

	    //Register Style
		wp_register_style( 'bootstrap-icons', $file, false);
		add_shortcode( 'pl_icon', array( &$this, 'pl_icon_shortcode' ) );		
	}
	
	/**
	* Bootstrap Icon Shortcode
	* 
	* @example <code>[pl_icon type="" color=""]</code> is the default usage
	* @example <code>[pl_icon type="leaf" color="black"]</code>
	* @example Color attributes include black and white
	* @example See http://twitter.github.com/bootstrap/base-css.html#icons for available icon types
	*/
	function pl_icon_shortcode( $atts, $content = null ) {

		$defaults = array('type' => 'leaf','color' =>'black');
		$atts = shortcode_atts( $defaults, $atts );

		$out = sprintf( '<i class="icon-%s icon-%s"></i>',$atts['type'],$atts['color']);
		return $out;
	}
	
}
new PL_Bootstrap_Icons;