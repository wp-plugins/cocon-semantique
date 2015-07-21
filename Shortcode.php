<?php

/**
 * Shortcode to create structure of the cocon.
 */
 
if(!class_exists('CoconSemantiqueShortcode'))
{
	class CoconSemantiqueShortcode
	{
		private $prefix = '';
		
		public function __construct() {
			add_shortcode('cocon_semantique_home_shortcode', array($this, 'shortcode'));
			
			$cocon = new CoconSemantique();
			$this->prefix = $cocon->prefix();
		}

		public function shortcode($isWidget = FALSE) {
			// sub category?
			$cat = '';
			$parent_id = 0;
			if ( preg_match( '#^/' . $this->prefix . '(.*)$#', $_SERVER['REQUEST_URI'], $match ) ) {
				$request = explode( '/', $match[1] );
				$cat = $request[0];
				
				$term = get_category_by_slug( $cat );
				$parent_id = $term->term_id;
			}
			
			$echo = '';
			$widgetIsPopulated = false;
			
			if ($isWidget) {
				if ($cat != '') {
					$echo .= '<li><a href="/' . $this->prefix . $cat . '">&nbsp;&#x276f;&nbsp;' . $term->name . '</a></li>';
				} else if(is_archive() || is_single()) {
					$widgetIsPopulated = true;
					
					if (is_single()) {
						$category = get_the_category();
						$child_term_id = $category[0]->cat_ID;
					}
					else {
						$child_term_id = get_queried_object()->term_id;
					}
					
					$child_term = get_term_by('id', $child_term_id, 'category');
					
					if (isset($child_term->parent)) {
						$parent_term = get_term( $child_term->parent, 'category' );
						if (isset($parent_term->name)) {
							$url = '/' . $this->prefix . $parent_term->slug . '/';
							$echo .= '<li><a href="' . $url . '">&nbsp;&#x276f;&nbsp;' . $parent_term->name . '</a>';
						}
					}
					
					$child_term_term_id = 0;
					if (isset($child_term->name)) {
						$echo .= '<li><a href="' . get_term_link($child_term) . '">&nbsp;&nbsp;&nbsp;&#x276d;&nbsp;' . $child_term->name . '</a>';
						$child_term_term_id = $child_term->term_id;
					}
					
					
					$args = array(
						'numberposts' => 10,
						'offset' => 0,
						'category' => $child_term_term_id,
						'orderby' => 'post_date',
						'order' => 'DESC',
						'post_type' => 'post',
						'post_status' => 'publish',
						'suppress_filters' => true
					);
	
					$echo .= '<ul>';
					$recent_posts = wp_get_recent_posts( $args, OBJECT );
					foreach($recent_posts as $p) {
						$before = '';
						$after = '';
						if($p->ID == get_the_ID() && is_single()) {
							$before = '<b>';
							$after = '</b>';
						}
						$echo .= '<li><a style="font-size:0.8em" href="' . get_permalink($p->ID) . '">' . $before . '&nbsp;&nbsp;&nbsp;&nbsp;&bull;&nbsp;' . $p->post_title . $after . '</a>';
					}
					$echo .= '</ul>';
				}
			}
			
			$categories = get_terms( 'category', array('hide_empty' => 0, 'parent' => $parent_id));
			foreach($categories as $categorie) {
				// post found or has child
				$child_count = 0;
				if (count(get_term_children($categorie->term_id, 'category')) > 0) {
					foreach(get_term_children($categorie->term_id, 'category') as $child) {
						$child_cat = get_category($child);
						$child_count += $child_cat->count;
					}
				}
				if($categorie->count > 0 || $child_count > 0) {

					// has children, continu
					if (count(get_term_children($categorie->term_id, 'category')) > 0) {
						$url = '/' . $this->prefix . $categorie->slug . '/';
					
					// no child, use defaut template
					} else {
						$url = get_term_link($categorie);
					}
					
					if ($isWidget) {
						if (!$widgetIsPopulated) {
							$echo .= '<li><a href="' . $url . '">&nbsp;&nbsp;&nbsp;&#x276d;&nbsp;' . $categorie->name . '</a></li>';
						}
					} else {
						$echo .= '<h2><a href="' . $url . '">' . $categorie->name . '</a></h2>
						<p>' . nl2br(wp_trim_words($categorie->description)) . '</p>';
					}
				}
			}
			return $echo;
		}
	}
}

if(class_exists('CoconSemantiqueShortcode'))
{
	$CoconSemantiqueShortcode = new CoconSemantiqueShortcode();
}