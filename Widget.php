<?php

defined( 'ABSPATH' ) or die( 'No script kiddies please!' );

if(!class_exists('CoconSemantique_Widget'))
{
    class CoconSemantique_Widget extends WP_Widget {
    
        public function __construct() {
            parent::__construct(
                'CoconSemantique_Widget',
                'Cocon sémantique',
                array( 'description' => __( 'Affichage de la structure du cocon.', 'CoconSemantique' ), )
            );
        }
	
        public function widget( $args, $instance ) {
			
			$shortcode = new CoconSemantiqueShortcode();
			
            extract( $args );
		
            echo $before_widget;
			echo '<ul>';
			echo '<li><a href="' . get_home_url() . '"><b>' . get_bloginfo('name') . '</b></a></li>';
			echo $shortcode->shortcode(TRUE);
			echo '</ul>';
            echo $after_widget;
        }
    }
}

// Initialisation du widget
add_action('widgets_init', function() {
    register_widget('CoconSemantique_Widget');
});