<?php
/**
* This is where you can copy and paste your functions !
*/

/*custom credits   		*/

add_filter('tc_credits_display', 'my_custom_credits');
function my_custom_credits(){ 
$credits = '';
$newline_credits = 'Logo created using <a href="http://www.logogarden.com/">logogarden</a>';
return '
<div class="span4 credits">
    		    	<p> &middot; &copy; '.esc_attr( date( 'Y' ) ).' <a href="'.esc_url( home_url() ).'" title="'.esc_attr(get_bloginfo()).'" rel="bookmark">'.esc_attr(get_bloginfo()).'</a> &middot; '.($credits ? $credits : 'Designed by <a href="http://www.themesandco.com/">Themes &amp; Co</a>').' &middot;  <a href="http://www.ashutoshdas.com/about/"> about/contact </a> &middot; '.($newline_credits ? '<br />&middot; '.$newline_credits.' &middot;' : '').'</p>		</div>';
}

/*End of custom credits	*/

/*custom comment bubble*/

add_filter('tc_bubble_comment' , 'my_custom_comment_buble');
function my_custom_comment_buble() {
 if ( 0 == get_comments_number() ) 
     return '';

 return sprintf('<span class="my-custom-bubble">%1$s %2$s</span>',
                                    get_comments_number(),
                                    sprintf( _n( 'comment' , 'comments' , get_comments_number(), 'customizr' ),
                      number_format_i18n( get_comments_number(), 'customizr' )
                       )
     );
 
}

/*End of custom comment bubble*/

/*Displaying the recent posts of the current category*/
 
add_filter( 'widget_posts_args', 'my_widget_posts_args');
function my_widget_posts_args($args) {
 if ( is_category() ) { //adds the category parameter in the query if we display a category
 $cat = get_queried_object();
 return array(
 'posts_per_page' => 10,//set the number you want here 
 'no_found_rows' => true, 
 'post_status' => 'publish', 
 'ignore_sticky_posts' => true,
 'cat' => $cat -> term_id//the current category id
 );
 }
 else {
	//keeps the normal behaviour if we are not in category context
 return $args;
 }
}
/*End of Displaying the recent posts of the current category*/

/*Restrict post navigation to same catagory*/

add_filter( 'get_next_post_join', 'navigate_in_same_taxonomy_join', 20);
add_filter( 'get_previous_post_join', 'navigate_in_same_taxonomy_join', 20 );
function navigate_in_same_taxonomy_join() {
 global $wpdb;
 return " INNER JOIN $wpdb->term_relationships AS tr ON p.ID = tr.object_id INNER JOIN $wpdb->term_taxonomy tt ON tr.term_taxonomy_id = tt.term_taxonomy_id";
}
 
 
add_filter( 'get_next_post_where' , 'navigate_in_same_taxonomy_where' );
add_filter( 'get_previous_post_where' , 'navigate_in_same_taxonomy_where' );
function navigate_in_same_taxonomy_where( $original ) {
 global $wpdb, $post;
 $where = '';
 $taxonomy   = 'category';
 $op = ('get_previous_post_where' == current_filter()) ? '<' : '>';
 $where = $wpdb->prepare( "AND tt.taxonomy = %s", $taxonomy );
 if ( ! is_object_in_taxonomy( $post->post_type, $taxonomy ) )
 return $original ;
 
 $term_array = wp_get_object_terms( $post->ID, $taxonomy, array( 'fields' => 'ids' ) );
 
 $term_array = array_map( 'intval', $term_array );
 
 if ( ! $term_array || is_wp_error( $term_array ) )
 return $original ;
 
 $where = " AND tt.term_id IN (" . implode( ',', $term_array ) . ")";
 return $wpdb->prepare( "WHERE p.post_date $op %s AND p.post_type = %s AND p.post_status = 'publish' $where", $post->post_date, $post->post_type );
}
/*End of Restrict post navigation to same catagory*/

/*Custom comments-notes*/
add_filter('comment_form_defaults' , 'custom_allowed_html_tags_note', 30);
function custom_allowed_html_tags_note( $defaults ) {
 //returns the modified array
 return array_replace( $defaults, array('comment_notes_after' => '<div class="alert">
<strong>Need to share some code?</strong> To display it in a nice looking syntax highlighter, <span class="label">wrap your code between the following tags (c code in this example)</span> : <code>&lt;pre class="lang:c"&gt;YOUR CODE&lt;/pre&gt;</code> <i>( possible code language acronyms : c, c#, c++, python, css, php, xhtml, javascript, sql)</i>
</div><p class="form-allowed-tags" id="form-allowed-tags">' . sprintf( __( 'You may use these <abbr title="HyperText Markup Language">HTML</abbr> tags and attributes: %s' ), ' <code>' . allowed_tags() . '</code>' ) . '</p>' ) );
}
/*END of Custom comments-notes*/
/*<p class="form-allowed-tags" id="form-allowed-tags">' . sprintf( __( 'You may use these <abbr title="HyperText Markup Language">HTML</abbr> tags and attributes: %s' ), ' <code>' . allowed_tags() . '</code>' ) . '</p>' ) */

/*Add text above posts in separate blog page*/

// Add content of page called "blog" to the page that contains the list of blog posts
add_action  ( '__before_loop', 'add_before_uncategorized');
function add_before_uncategorized() {
 if ( is_category( 'uncategorized' )) {
 $post = get_page_by_path( '/free/uncategorized' );
 echo wpautop($post->post_content);
 }
}

add_action  ( '__before_loop', 'add_before_opensource');
function add_before_opensource() {
 if ( is_category( 'open-source' )) {
 $post = get_page_by_path( '/free/open-source' );
 echo wpautop($post->post_content);
 }
}

add_action  ( '__before_loop', 'add_before_jarvsh');
function add_before_jarvsh() {
 if ( is_category( 'jarvsh' )) {
 $post = get_page_by_path( '/free/jarvsh' );
 echo wpautop($post->post_content);
 }
}

add_action  ( '__before_loop', 'add_before_lfs');
function add_before_lfs() {
 if ( is_category( 'linux-from-scratch' )) {
 $post = get_page_by_path( '/free/open-source/linux-from-scratch' );
 echo wpautop($post->post_content);
 }
}
/*Use is_home() to add content in home page*/
/*END of Add text above posts in separate blog page*/