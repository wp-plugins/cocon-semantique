<?php

/**
 * Navigation: prev and next post.
 */
 
if(!class_exists('CoconSemantiqueNavigation'))
{
	class CoconSemantiqueNavigation
	{
		public function __construct() {
			add_filter('next_post_link', array($this, 'next'), 10, 2);
			add_filter('previous_post_link', array($this, 'previous'), 10, 2);
			add_filter('previous_post_rel_link', array($this, 'previous_meta'), 10, 2);
			add_filter('next_post_rel_link', array($this, 'next_meta'), 10, 2);
		}
		
		public function previous( $format, $link) {
			return $this->adjacent($format, $link, TRUE);
		}

		public function next( $format, $link) {
			return $this->adjacent($format, $link, FALSE);
		}

		public function previous_meta() {
			return $this->adjacent('', '', TRUE, TRUE);
		}

		public function next_meta() {
			return $this->adjacent('', '', FALSE, TRUE);
		}
		
		public function adjacent( $format, $link, $previous, $is_meta = FALSE) {
			$taxonomy = 'category';
			$excluded_terms = '';

			if ( $previous && is_attachment() ) {
				$post_new = get_post( get_post()->post_parent );
				$post_old = $post_new;
			} else {
				$post_new = get_adjacent_post( TRUE, $excluded_terms, $previous, $taxonomy );
				$post_old = get_adjacent_post( FALSE, $excluded_terms, $previous, $taxonomy );
			}

			if ( ! $post_new ) {
				$output = '';
			} else {
				$title_new = $post_new->post_title;
				$title_old = $post_old->post_title;
 
				if ( empty( $title_new ) ) {
					$title_new = $previous ? __( 'Previous Post' ) : __( 'Next Post' );
				}

				if ( empty( $title_old ) ) {
					$title_old = $previous ? __( 'Previous Post' ) : __( 'Next Post' );
				}
 
				$title_new = apply_filters( 'the_title', $title_new, $post_new->ID );
				$title_old = apply_filters( 'the_title', $title_old, $post_old->ID );

				$rel = $previous ? 'prev' : 'next';

				if ($is_meta) {
					$output = '<link rel="' . $rel . '" title="' . $title_new . '" href="' . get_permalink( $post_new ) . '" />';
				} else {
					$output = str_replace($title_old, $title_new, $format);
					$output = str_replace(get_permalink( $post_old ), get_permalink( $post_new ), $output);
				}
			}
 
			return $output;
		}
	}
}

if(class_exists('CoconSemantiqueNavigation'))
{
	$CoconSemantiqueNavigation = new CoconSemantiqueNavigation();
}