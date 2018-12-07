<?php
/**
 * The template for displaying all pages
 *
 * This is the template that displays all pages by default.
 * Please note that this is the WordPress construct of pages
 * and that other 'pages' on your WordPress site may use a
 * different template.
 *
 * Template Name: Portfolio Template
 * @link https://codex.wordpress.org/Template_Hierarchy
 *
 * @package WordPress
 * @subpackage Twenty_Seventeen
 * @since 1.0
 * @version 1.0
 */

get_header();?>
<style>
	.portfolio-wrap{margin-bottom: 20px;}
	.portfolio-wrap > a{display: block;}
</style>

<div class="wrap">
	<div id="primary" class="content-area">
		<main id="main" class="site-main" role="main">
			<div class="container sunset-posts-container">
			
			<?php
$args = array(
    'post_type' => 'portfolio', //slug name
    'post_per_page' => 5, // display number of posts
);

$loop = new WP_Query($args);

if ($loop->have_posts()):

    while ($loop->have_posts()): $loop->the_post();
        ?>
		<div class="portfolio-wrap">
			<a href="<?php the_permalink();?>"> <?php echo get_the_title(); ?> </a>

	<!-- Start Show texonomies here -->
	<?php
	//This below code is used for Show "Categories" Lists
	 // $terms_list = wp_get_post_terms($post->ID, 'field');
	 // $i = 0;
	 // foreach($terms_list as $term){
	 // 	$i++;
	 // 	if($i > 1){
	 // 		echo ', ';
	 // 	}
	 // 	echo $term->name;
	 // }
	 echo "<div class='show_cats'>";
	 	echo '<b>Categories: </b>' . awesome_get_terms($post->ID, 'field');
	 echo "</div>";

	 //This below code is used for Show "Tag" Lists
	  echo "<div class='show_tags'>";
	  	echo '<b>Tags:</b> ' . awesome_get_terms($post->ID, 'software');
	  echo "</div>";
	 // $terms_list = wp_get_post_terms($post->ID, 'software');
	 // $i = 0;
	 // foreach($terms_list as $term){
	 // 	$i++;
	 // 	if($i > 1){
	 // 		echo ', ';
	 // 	}
	 // 	echo $term->name;
	 // }
	 // End Show texonomies here

        the_post_thumbnail();
        the_content();?>
				</div> <?php
    endwhile; // End of the loop.

endif;
?>
</div>
<!-- Start Load more block -->
<div class="container text-center">
	<a href="javascript:void(0)" class="btn-sunset-load sunset-load-more" data-page="1" data-url="<?php echo admin_url('admin-ajax.php'); ?>">
		<span class="sunset-icon sunset-loading">
			<i class="fa fa-spinner" aria-hidden="true"></i>
		</span> 
		<span class="text">Load More</span>
	</a>
</div>

<!-- End Load more block -->
		  
		</main><!-- #main -->
	</div><!-- #primary -->
</div><!-- .wrap -->

<?php get_footer();
