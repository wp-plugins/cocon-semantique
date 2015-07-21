<?php

/**
 * Link: add some link on archive page.
 */
 
if(!class_exists('CoconSemantiqueLink'))
{
	class CoconSemantiqueLink
	{
		private $prefix = '';
		
		public function __construct() {
			add_filter( 'get_the_archive_title', array($this, 'archive_title'), 10, 1);
			
			$cocon = new CoconSemantique();
			$this->prefix = $cocon->prefix();
		}
		
		public function archive_title($title) {
			if( is_category() && isset(get_queried_object()->term_id)) {
				$child_term = get_term_by('id', get_queried_object()->term_id, 'category');
				$parent_term = get_term( $child_term->parent, 'category' );
				if (isset($parent_term->slug)) {
					$url = '/' . $this->prefix . $parent_term->slug . '/';
					$title = '<a href="' . $url . '">' . $parent_term->name . '</a> &#x2771; ' . single_cat_title( '', false );
				}
			}
			return $title;
		}
	}
}

if(class_exists('CoconSemantiqueLink'))
{
	$CoconSemantiqueLink = new CoconSemantiqueLink();
}