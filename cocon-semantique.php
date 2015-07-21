<?php
/*
Plugin Name: Cocon Semantique
Plugin URI: http://amauri.champeaux.fr/
Description: Optimisation SEO en modifiant la structure de votre blog en silos semantiques.
Version: 0.1
Author: Amauri CHAMPEAUX
Author URI: http://amauri.champeaux.fr/a-propos
*/

if(!class_exists('CoconSemantique'))
{
	class CoconSemantique
	{
		public static $prefix = '___/';
		
		public function __construct() {
			require_once(sprintf("%s/Link.php", dirname(__FILE__)));
			require_once(sprintf("%s/Navigation.php", dirname(__FILE__)));
			require_once(sprintf("%s/Rewrite.php", dirname(__FILE__)));
			require_once(sprintf("%s/Shortcode.php", dirname(__FILE__)));
			require_once(sprintf("%s/Widget.php", dirname(__FILE__)));
		}
		
		public static function activate() {
			// Create post object
			$my_post = array(
				'post_title'    => get_bloginfo('name'),
				'post_type'		=> 'page',
				'post_content'  => '[cocon_semantique_home_shortcode]',
				'post_status'   => 'publish',
				'post_name'		=> 'cocon_semantique_home',
				'ping_status' 	=> 'closed',
				'comment_status'=> 'closed'
			);

			// Insert the post into the database
			if (get_page_by_path('cocon_semantique_home') == null) {
				wp_insert_post( $my_post );
			}
			
			$page = get_page_by_path('cocon_semantique_home');
			update_option( 'page_on_front', $page->ID );
			update_option( 'show_on_front', 'page' );
		}
		
		public static function deactivate() {
			// delete the home page
			$page = get_page_by_path('cocon_semantique_home');
			wp_delete_post($page->ID, TRUE);
			
			update_option( 'show_on_front', 'posts' );
		}
		
		public function prefix() {
			return self::$prefix;
		}
	}
}

if(class_exists('CoconSemantique'))
{
	register_activation_hook(__FILE__, array('CoconSemantique', 'activate'));
	register_deactivation_hook(__FILE__, array('CoconSemantique', 'deactivate'));
	
	$CoconSemantique = new CoconSemantique();
}