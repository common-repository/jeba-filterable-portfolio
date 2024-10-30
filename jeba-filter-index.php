<?php
/*
Plugin Name: Jeba Filter Portfolio
Plugin URI: http://prowpexpert.com/jeba-filter-portfolio
Description: This is Jeba cute wordpress filterable portfolio plugin really looking awesome filtering. Everyone can use the cute filter plugin easily like other wordpress plugin. By using [jeba_filter] shortcode use the slider every where post, page and template.
Author: Md Jahed
Version: 1.0
Author URI: http://prowpexpert.com/
*/
function jeba_filter_wp_latest_jquery() {
	wp_enqueue_script('jquery');
}
add_action('init', 'jeba_filter_wp_latest_jquery');

function plugin_function_jeba_filter() {
    wp_enqueue_script( 'jebacute-js', plugins_url( '/js/filterable.pack.js', __FILE__ ), true);
    wp_enqueue_style( 'jeba-filter-css', plugins_url( '/js/portfolio.css', __FILE__ ));
}

add_action('init','plugin_function_jeba_filter');

function plugin_functions_jeba_filters() {
    wp_enqueue_script( 'jebacutess-js', plugins_url( '/js/filterable.js', __FILE__ ), true);
   
}

add_action('wp_footer','plugin_functions_jeba_filters');


function jeba_filter_plugin_function () {?>
        <script type="text/javascript">
                
				jQuery(document).ready(function(){
	
	jQuery('#portfolio-list').filterable();

});
				
        </script>
		
<?php
}

add_action('wp_footer','jeba_filter_plugin_function');

function jeba_filter_shortcode($atts){ 
	extract( shortcode_atts( array(
		'post_type' => 'jeba-filter'
	), $atts) );
	
    $q = new WP_Query(
        array('posts_per_page' => '-1', 'post_type' => $post_type)
        );		

	$args = array(
		'post_type' => $post_type,
		'paged' => $paged,
		'posts_per_page' => $data['portfolio_items'],
	);

	$portfolio = new WP_Query($args);
	
	if(is_array($portfolio->posts) && !empty($portfolio->posts)) {
		foreach($portfolio->posts as $gallery_post) {
			$post_taxs = wp_get_post_terms($gallery_post->ID, 'jeba-filter_category', array("fields" => "all"));
			if(is_array($post_taxs) && !empty($post_taxs)) {
				foreach($post_taxs as $post_tax) {
					$portfolio_taxs[$post_tax->slug] = $post_tax->name;
				}
			}
		}
	}
	
	if(is_array($portfolio_taxs) && !empty($portfolio_taxs) && get_post_meta($post->ID, 'pyre_portfolio_filters', true) != 'no'):
?>
		<ul id="portfolio-filter">
			<li><a href="#all" title="">All</a></li>
			
			<?php foreach($portfolio_taxs as $portfolio_tax_slug => $portfolio_tax_name): ?>
				<li><a href="#<?php echo $portfolio_tax_slug; ?>" title="" rel="<?php echo $portfolio_tax_slug; ?>"><?php echo $portfolio_tax_name; ?></a></li>
				<?php endforeach; ?>
		</ul>

		<?php endif; ?>

<?php

	$list = '		
			<ul id="portfolio-list">';
	while($q->have_posts()) : $q->the_post();
		$idd = get_the_ID();
		$jeba_img_thumb = wp_get_attachment_image_src( get_post_thumbnail_id( $post->ID ), 'thumbnail' );

		$item_classes = '';
		$item_cats = get_the_terms($post->ID, 'jeba-filter_category');
		if($item_cats):
		foreach($item_cats as $item_cat) {
			$item_classes .= $item_cat->slug . ' ';
		}
		endif;
			
	
		$list .= '
		
			<li style="display: block;" class="'.$item_classes.'">
									<a href="'.get_permalink().'" title=""><img src="'.$jeba_img_thumb[0].'" alt=""></a>
					<p>
						'.get_the_title().'
					</p>
				</li>
		';        
	endwhile;
	$list.= '		
				<li style="overflow: hidden; clear: both; height: 0px; position: relative; float: none; display: block;"></li>
		</ul>
		';
		
	wp_reset_query();
	return $list;
}
add_shortcode('jeba_filter', 'jeba_filter_shortcode');



add_action( 'init', 'jeba_filter_custom_post' );
function jeba_filter_custom_post() {

	register_post_type( 'jeba-filter',
		array(
			'labels' => array(
				'name' => __( 'JebaFilter' ),
				'singular_name' => __( 'JebaFilter' )
			),
			'public' => true,
			'supports' => array('title', 'editor', 'thumbnail'),
			'has_archive' => true,
			'rewrite' => array('slug' => 'jeba-filters'),
		)
	);	
	}


function jeba_custom_post_taxonomy_filter() {

	register_taxonomy(
		'jeba-filter_category',  //The name of the taxonomy. Name should be in slug form (must not contain capital letters or spaces).
		'jeba-filter',                  //post type name
		array(
			'hierarchical'          => true,
			'label'                         => 'Mixitup Category',  //Display name
			'query_var'             => true,
			'show_admin_column'             => true,
			'rewrite'                       => array(
				'slug'                  => 'jeba-cat', // This controls the base slug that will display before each term
				'with_front'    => true // Don't display the category base before
				)
			)
	);

}
add_action( 'init', 'jeba_custom_post_taxonomy_filter'); 
	

add_theme_support( 'post-thumbnails', array( 'post', 'jeba-filter' ) );

?>