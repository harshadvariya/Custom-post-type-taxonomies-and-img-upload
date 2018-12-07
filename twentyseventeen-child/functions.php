<?php
function my_theme_enqueue_styles() {

    $parent_style = 'parent-style'; // This is 'twentyfifteen-style' for the Twenty Fifteen theme.

    wp_enqueue_style( $parent_style, get_template_directory_uri() . '/style.css' );
    wp_enqueue_style( 'child-style',
        get_stylesheet_directory_uri() . '/style.css',
        array( $parent_style ),
        wp_get_theme()->get('Version')
    );

    //Add Font-awesome
    wp_register_style( 'Font_Awesome', 'https://maxcdn.bootstrapcdn.com/font-awesome/4.6.3/css/font-awesome.min.css' );
	wp_enqueue_style('Font_Awesome');

	//Add Custom JS
	wp_enqueue_script( 'sunset-custom-css-script', get_stylesheet_directory_uri() . '/custom-js.js', array('jquery'), '1.0.0', true );
}
add_action( 'wp_enqueue_scripts', 'my_theme_enqueue_styles' );


/*
   ===================
	Custom Post Type
   ===================
*/

function awesome_custom_post_type (){
	$labels = array(
		'name' => 'Portfolio',
		'singular_name' => 'Portfolio',
		'add_new' => 'Add Item',
		'all_items' => 'All Items',
		'add_new_item' => 'Add Item',
		'edit_item' => 'Edit Item',
		'new_item' => 'New Item',
		'view_item' => 'View Item',
		'search_item' => 'Search Portfolio',
		'not_found' => 'No items found',
		'not_found_in_trash' => 'No items found in trash',
		'parent_item_colon' => 'Parent Item'
	);

	$args = array(
		'labels' => $labels,
		'public' => true,
		'has_archive' => true,
		'publicly_queryable' => true,
		'query_var' => true,
		'rewrite' => true,
		'capability_type' => 'post',
		'hierarchical' => false,
		'supports' => array(
			'title',
			'editor',
			'excerpt',
			'thumbnail',
			'revisions',
		),
		// 'taxonomies' => array('category', 'post_tag'),
		'menu_position' => 5,
		'exclude_from_search' => false
	);

	register_post_type('portfolio',$args);
}
add_action('init','awesome_custom_post_type');

/*
   ===================
	Custom Taxonomies
   ===================
*/

function awesome_custom_taxonomies() {
	
	//add new taxonomy hierarchical
	$labels = array(
		'name' => 'Fields',
		'singular_name' => 'Field',
		'search_items' => 'Search Fields',
		'all_items' => 'All Fields',
		'parent_item' => 'Parent Field',
		'parent_item_colon' => 'Parent Field:',
		'edit_item' => 'Edit Field',
		'update_item' => 'Update Field',
		'add_new_item' => 'Add New Work Field',
		'new_item_name' => 'New Field Name',
		'menu_name' => 'Fields'
	);
	
	$args = array(
		'hierarchical' => true,
		'labels' => $labels,
		'show_ui' => true,
		'show_admin_column' => true,
		'query_var' => true,
		'rewrite' => array( 'slug' => 'field' )
	);
	//Must need to check slug name or taxonomy first parameter "register_taxonomy" - field (always put both are same name)
	register_taxonomy('field', array('portfolio'), $args);
	

	//add new taxonomy NOT hierarchical
	register_taxonomy('software', 'portfolio', array(
		'label' => 'Software',
		'rewrite' => array( 'slug' => 'software' ),
		'hierarchical' => false,
		'show_admin_column' => true
	) );
	
}
add_action( 'init' , 'awesome_custom_taxonomies' );

/*
   ===================
	Custom Terms Function
   ===================
*/
function awesome_get_terms( $postID, $term ){
	
	$terms_list = wp_get_post_terms($postID, $term); 
	$output = '';
					
	$i = 0;
	foreach( $terms_list as $term ){ $i++;
		if( $i > 1 ){ $output .= ', '; }
		$output .= '<a class="tag_categories" href="' . get_term_link( $term ) . '">' . $term->name .'</a>';
	}	
	return $output;
}

//Include AJAX File
add_action( 'wp_ajax_nopriv_sunset_load_more', 'sunset_load_more' );
add_action( 'wp_ajax_sunset_load_more', 'sunset_load_more' );
function sunset_load_more(){
    $paged = $_POST["page"]+1;
	
	$query = new WP_Query( array(
		'post_type' => 'portfolio',
		'post_status' => 'publish',
		'paged' => $paged
	) );

    if ($query->have_posts()):

    while ($query->have_posts()): $query->the_post();
        ?>
	<div class="portfolio-wrap">
		<a href="<?php echo the_permalink(); ?>"> 
			<?php echo get_the_title(); ?> 
		</a>
	
		<!-- Start Show texonomies here -->
	<?php
	//This below code is used for Show "Categories" Lists
	 $terms_list = wp_get_post_terms(get_the_ID(), 'field');
	 $i = 0;
	 foreach($terms_list as $term){
	 	$i++;
	 	if($i > 1){
	 		echo ', ';
	 	}
	 	echo $term->name;
	 }

	 $terms_list = wp_get_post_terms(get_the_ID(), 'software');
	 $i = 0;
	 foreach($terms_list as $term){
	 	$i++;
	 	if($i > 1){
	 		echo ', ';
	 	}
	 	echo $term->name;
	 }
	 // End Show texonomies here

      the_post_thumbnail();
      the_content(); 
      
     ?>

	</div> <?php
    endwhile;

else :

	echo 0;

endif;

wp_reset_postdata();
exit();

}



// Image Upload using ajax
add_action('wp_ajax_cvf_upload_files', 'cvf_upload_files');
add_action('wp_ajax_nopriv_cvf_upload_files', 'cvf_upload_files'); // Allow front-end submission 

function cvf_upload_files(){
    
    $parent_post_id = isset( $_POST['post_id'] ) ? $_POST['post_id'] : 0;  // The parent ID of our attachments
    $valid_formats = array("jpg", "png", "gif", "bmp", "jpeg"); // Supported file types
    $max_file_size = 1024 * 500; // in kb
    $max_image_upload = 10; // Define how many images can be uploaded to the current post
    $wp_upload_dir = wp_upload_dir();
    $path = $wp_upload_dir['path'] . '/';
    $count = 0;

    $attachments = get_posts( array(
        'post_type'         => 'attachment',
        'posts_per_page'    => -1,
        'post_parent'       => $parent_post_id,
        'exclude'           => get_post_thumbnail_id() // Exclude post thumbnail to the attachment count
    ) );

    // Image upload handler
    if( $_SERVER['REQUEST_METHOD'] == "POST" ){
        
        // Check if user is trying to upload more than the allowed number of images for the current post
        if( ( count( $attachments ) + count( $_FILES['files']['name'] ) ) > $max_image_upload ) {
            $upload_message[] = "Sorry you can only upload " . $max_image_upload . " images for each Ad";
        } else {
            
            foreach ( $_FILES['files']['name'] as $f => $name ) {
                $extension = pathinfo( $name, PATHINFO_EXTENSION );
                // Generate a randon code for each file name
                $new_filename = cvf_td_generate_random_code( 20 )  . '.' . $extension;
                
                if ( $_FILES['files']['error'][$f] == 4 ) {
                    continue; 
                }
                
                if ( $_FILES['files']['error'][$f] == 0 ) {
                    // Check if image size is larger than the allowed file size
                    if ( $_FILES['files']['size'][$f] > $max_file_size ) {
                        $upload_message[] = "$name is too large!.";
                        continue;
                    
                    // Check if the file being uploaded is in the allowed file types
                    } elseif( ! in_array( strtolower( $extension ), $valid_formats ) ){
                        $upload_message[] = "$name is not a valid format";
                        continue; 
                    
                    } else{ 
                        // If no errors, upload the file...
                        if( move_uploaded_file( $_FILES["files"]["tmp_name"][$f], $path.$new_filename ) ) {
                            
                            $count++; 

                            $filename = $path.$new_filename;
                            $filetype = wp_check_filetype( basename( $filename ), null );
                            $wp_upload_dir = wp_upload_dir();
                            $attachment = array(
                                'guid'           => $wp_upload_dir['url'] . '/' . basename( $filename ), 
                                'post_mime_type' => $filetype['type'],
                                'post_title'     => preg_replace( '/\.[^.]+$/', '', basename( $filename ) ),
                                'post_content'   => '',
                                'post_status'    => 'inherit'
                            );
                            // Insert attachment to the database
                            $attach_id = wp_insert_attachment( $attachment, $filename, $parent_post_id );

                            require_once( ABSPATH . 'wp-admin/includes/image.php' );
                            
                            // Generate meta data
                            $attach_data = wp_generate_attachment_metadata( $attach_id, $filename ); 
                            wp_update_attachment_metadata( $attach_id, $attach_data );
                            echo "<pre>";
                            print_r($attach_data);
                            echo "</pre>";
                            die;
                        }
                    }
                }
            }
        }
    }
    // Loop through each error then output it to the screen
    if ( isset( $upload_message ) ) :
        foreach ( $upload_message as $msg ){        
            printf( __('<p class="bg-danger">%s</p>', 'wp-trade'), $msg );
        }
    endif;
    
    // If no error, show success message
    if( $count != 0 ){
        printf( __('<p class = "bg-success">%d files added successfully!</p>', 'wp-trade'), $count );   
    }
    
    exit();
}

// Random code generator used for file names.
function cvf_td_generate_random_code($length=10) {
   $string = '';
   $characters = "23456789ABCDEFHJKLMNPRTVWXYZabcdefghijklmnopqrstuvwxyz";
   for ($p = 0; $p < $length; $p++) {
       $string .= $characters[mt_rand(0, strlen($characters)-1)];
   }
   return $string;
}