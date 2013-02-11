<?php
/*
Plugin Name: Trip visualizer
Plugin URI: http://github.com/gboone
Description: This plugin lets you input locations to output as paths on a map through the Google Maps API.
Version: 0.1.0
Author: Greg Boone
Author URI: http://harmsboone.org/author/greg-boone
License: GPL2: http://www.gnu.org/licenses/old-licenses/gpl-2.0.html
*/
ob_start();
//add_action('wp_load', $mappyPlugin->registerTaxes());
$mappyPlugin = new mappyPlugin;
//add_action('wp_enqueue_scripts', $mappyPlugin->add_scripts());
//add_action('wp_enqueue_scripts', $mappyPlugin->add_scripts());

	
function trip_viz_enqueue_script() {
	wp_enqueue_script( 'gmaps', 'http://maps.google.com/maps/api/js?sensor=false', false );
	wp_enqueue_script('geocode', 'http://google-maps-utility-library-v3.googlecode.com/svn/tags/infobox/1.1.9/src/infobox.js', false );
	wp_enqueue_script('jquery');
	wp_enqueue_script('draw', plugins_url('/js/map.js',__FILE__), 'jquery');
}

add_action('init',$mappyPlugin->register());
if (!is_admin()) {	
	add_action( 'wp_enqueue_scripts', 'trip_viz_enqueue_script' );
	//add_action('the_content',$mappyPlugin->addBodyClass());
	add_filter('the_content', 'do_shortcode', 11);
	add_filter('template_include','start_buffer_capture',1);

	function start_buffer_capture($template) {
		ob_start('end_buffer_capture');
		return $template;
	}


function end_buffer_capture($buffer) {
	//if (is_page('map') OR is_page('home')){
		return str_replace('<body', '<body' . body_class() . ' onload="initialize();"', $buffer);
	//}
}
}
class mappyPlugin {

	function register() {
			//hook into the init action and call create_book_taxonomies when it fires
			add_action( 'init', 'create_taxonomies', 0 );

			//create two taxonomies, Countries and States for the post type "book"
			function create_taxonomies() 
			{
				if (! taxonomy_exists('country')){
				  // Add new taxonomy, make it hierarchical (like categories)
				  $labels = array(
				    'name'                => _x( 'Countries', 'taxonomy general name' ),
				    'singular_name'       => _x( 'Country', 'taxonomy singular name' ),
				    'search_items'        => __( 'Search Countries' ),
				    'all_items'           => __( 'All Countries' ),
				    'parent_item'         => __( 'Parent Country' ),
				    'parent_item_colon'   => __( 'Parent Country:' ),
				    'edit_item'           => __( 'Edit Country' ), 
				    'update_item'         => __( 'Update Country' ),
				    'add_new_item'        => __( 'Add New Country' ),
				    'new_item_name'       => __( 'New Country Name' ),
				    'menu_name'           => __( 'Country' ),
				  ); 	
				
				  $args = array(
				    'labels'              => $labels,
				    'public'			  => true,
				    'show_in_nav_menus'	  => true,
				    'show_ui'             => true,
				    'show_tagcloud'		  => true,
				    'hierarchical'        => false,
				    'rewrite'             => array( 'slug' => 'country' ),
				    'query_var'           => 'country'
/* 				    'show_admin_column'   => true, */
				  );
				
				  register_taxonomy( 'country', array( 'post' ), $args );
				} 
				if (! taxonomy_exists('state')) {
				  // Add new taxonomy, NOT hierarchical (like tags)
				  $labels = array(
				    'name'                         => _x( 'States', 'taxonomy general name' ),
				    'singular_name'                => _x( 'State', 'taxonomy singular name' ),
				    'search_items'                 => __( 'Search States' ),
				    'popular_items'                => __( 'Popular States' ),
				    'all_items'                    => __( 'All States' ),
				    'parent_item'                  => null,
				    'parent_item_colon'            => null,
				    'edit_item'                    => __( 'Edit State' ), 
				    'update_item'                  => __( 'Update State' ),
				    'add_new_item'                 => __( 'Add New State' ),
				    'new_item_name'                => __( 'New State Name' ),
				    'separate_items_with_commas'   => __( 'Separate States with commas' ),
				    'add_or_remove_items'          => __( 'Add or remove States' ),
				    'choose_from_most_used'        => __( 'Choose from the most used States' ),
				    'menu_name'                    => __( 'States' )
				  ); 
				
				  $args = array(
				    'public'			  => true,
				    'hierarchical'            => false,
				    'labels'                  => $labels,
				    'show_ui'                 => true,
				    'show_admin_column'       => true,
				    'update_count_callback'   => '_update_post_term_count',
				    'query_var'               => true,
				    'rewrite'                 => array( 'slug' => 'state' )
				  );
				
				  register_taxonomy( 'state', 'post', $args );
				} 
				if (!taxonomy_exists('city')) {
				 $labels = array(
				    'name'                         => _x( 'Cities', 'taxonomy general name' ),
				    'singular_name'                => _x( 'City', 'taxonomy singular name' ),
				    'search_items'                 => __( 'Search Cities' ),
				    'popular_items'                => __( 'Popular Cities' ),
				    'all_items'                    => __( 'All Cities' ),
				    'parent_item'                  => null,
				    'parent_item_colon'            => null,
				    'edit_item'                    => __( 'Edit City' ), 
				    'update_item'                  => __( 'Update City' ),
				    'add_new_item'                 => __( 'Add New City' ),
				    'new_item_name'                => __( 'New City Name' ),
				    'separate_items_with_commas'   => __( 'Separate Cities with commas' ),
				    'add_or_remove_items'          => __( 'Add or remove Cities' ),
				    'choose_from_most_used'        => __( 'Choose from the most used Cities' ),
				    'menu_name'                    => __( 'Cities' )
				  ); 
				
				  $args = array(
				    'public'			  => true,
				    'hierarchical'            => false,
				    'labels'                  => $labels,
				    'show_ui'                 => true,
				    'show_admin_column'       => true,
				    'update_count_callback'   => '_update_post_term_count',
				    'query_var'               => true,
				    'rewrite'                 => array( 'slug' => 'city' )
				  );
				
				  register_taxonomy( 'city', 'post', $args );
				  } else {return;} 		
		}//end create()
	
	}//end register()	
	
} // end mappyPlugin class

function map_shortcode($content) {
	$terms = get_terms(array("country","city","state"));
	$count = count($terms);
	if ( $count > 0 ){
	    echo "<ul id=\"points\" style=\"display:none\" onload=\"initialize();\">";
	    foreach ( $terms as $term ) {
	      echo "<li>" . $term->name . "</li>";
	       
	    }
	    echo "</ul>";
	}	

	$mapBody = __('
			<style type="text/css">
				#map_canvas { width: inherit; height: 400px; }
				#mapCanvas label { width: auto; display:inline; }
				.entry-content img { max-width: none !important; box-shadow:none; border-radius: none;}
		  	</style>
			<div id="geocode">
		    	<button onclick="populateMap()">See where we\'ve been</button></div>
		    	<div id="map_canvas"></div> 
		    <noscript><p><b>JavaScript must be enabled in order for you to use Google Maps.</b> However, it seems JavaScript is either disabled or not supported by your browser. To view Google Maps, enable JavaScript by changing your browser options, and then try again.</p>
		    </noscript> ');
    //var_dump( $mapData);
    //var_dump($taxonomies);
    echo $mapBody;

}

add_shortcode('map', 'map_shortcode');

// hook $mappyPlugin -> populateList() into plugin activation activation

?>