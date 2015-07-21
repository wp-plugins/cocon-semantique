<?php

/**
 * Rewrite the virtual dir.
 */
 
if(!class_exists('CoconSemantiqueRewrite'))
{
	class CoconSemantiqueRewrite
	{
		private $prefix = '';
		
		public function __construct() {
			add_action('parse_request', array($this, 'rewrite'));
			
			$cocon = new CoconSemantique();
			$this->prefix = $cocon->prefix();
		}

		public function rewrite(&$wp) {
			// not a page for me
			$page = $_SERVER['REQUEST_URI'];
			if (!preg_match("#" . $this->prefix . "([^/]+)#", $page)) {
				return;
			}
			
			// load template
			add_action('template_redirect', array($this, 'redir'));
			
			// create virtual page
			add_filter('the_posts', array($this, 'dummy'));

			// do not add <p>
			remove_filter('the_content', 'wpautop');
		}
		
		public function redir() {
			get_template_part('page');
			exit;
		}
		
		public function dummy($posts) {
			global $wp, $wp_query;

			//create a fake post intance
			$p = new stdClass;

			// fill $p with everything a page in the database would have
			$p->ID = -1;
			$p->post_author = 1;
			$p->post_date = current_time('mysql');
			$p->post_date_gmt =  current_time('mysql', $gmt = 1);
			
			// the content is the shortcode
			$p->post_content = "[cocon_semantique_home_shortcode]";

			// the title is the cat name
			preg_match( '#^/' . $this->prefix . '(.*)$#', $_SERVER['REQUEST_URI'], $match );
			$request = explode( '/', $match[1] );
			$cat_slug = $request[0];
			$the_cat = get_category_by_slug( $cat_slug );
			$p->post_title = $the_cat->name;
			$p->post_excerpt = '';
			$p->post_status = 'publish';
			$p->ping_status = 'closed';
			$p->post_password = '';
			$p->post_name = $this->prefix . $the_cat->slug; // slug
			$p->to_ping = '';
			$p->pinged = '';
			$p->modified = $p->post_date;
			$p->modified_gmt = $p->post_date_gmt;
			$p->post_content_filtered = '';
			$p->post_parent = 0;
			$p->guid = get_home_url('/' . $p->post_name); // use url instead?
			$p->menu_order = 0;
			$p->post_type = 'page';
			$p->post_mime_type = '';
			$p->comment_status = 'closed';
			$p->comment_count = 0;
			$p->filter = 'raw';
			$p->ancestors = array(); // 3.6

			// reset wp_query properties to simulate a found page
			$wp_query->is_page = TRUE;
			$wp_query->is_singular = TRUE;
			$wp_query->is_home = FALSE;
			$wp_query->is_archive = FALSE;
			$wp_query->is_category = FALSE;
			unset($wp_query->query['error']);
			$wp->query = array();
			$wp_query->query_vars['error'] = '';
			$wp_query->is_404 = FALSE;

			$wp_query->current_post = $p->ID;
			$wp_query->found_posts = 1;
			$wp_query->post_count = 1;
			$wp_query->comment_count = 0;
			// -1 for current_comment displays comment if not logged in!
			$wp_query->current_comment = null;
			$wp_query->is_singular = 1;

			$wp_query->post = $p;
			$wp_query->posts = array($p);
			$wp_query->queried_object = $p;
			$wp_query->queried_object_id = $p->ID;
			$wp_query->current_post = $p->ID;
			$wp_query->post_count = 1;

			return array($p);
		}
	}
}

if(class_exists('CoconSemantiqueRewrite'))
{
	$CoconSemantiqueRewrite = new CoconSemantiqueRewrite();
}