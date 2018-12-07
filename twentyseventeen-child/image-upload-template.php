<?php
/**
 * The template for displaying all pages
 *
 * This is the template that displays all pages by default.
 * Please note that this is the WordPress construct of pages
 * and that other 'pages' on your WordPress site may use a
 * different template.
 *
 * Template Name: Image Upload Template
 * @link https://codex.wordpress.org/Template_Hierarchy
 *
 * @package WordPress
 * @subpackage Twenty_Seventeen
 * @since 1.0
 * @version 1.0
 */

get_header();

?>

<div class = "upload-form">
    <div class= "upload-response"></div>
    <div class = "form-group">
        <label><?php __('Select Files:', 'cvf-upload'); ?></label>
        <input type = "file" name = "files[]" accept = "image/*" class = "files-data form-control" multiple />
    </div>
    <div class = "form-group">
        <input type = "submit" value = "Upload" class = "btn btn-primary btn-upload" />
    </div>
</div>


<?php get_footer(); ?>

<script>
	
jQuery(document).ready(function($) {
    // When the Upload button is clicked...
    $('body').on('click', '.upload-form .btn-upload', function(e){
        e.preventDefault;

        var fd = new FormData();
        var files_data = $('.upload-form .files-data'); // The <input type="file" /> field
        
        // Loop through each data and create an array file[] containing our files data.
        $.each($(files_data), function(i, obj) {
            $.each(obj.files,function(j,file){
                fd.append('files[' + j + ']', file);
            })
        });
        
        // our AJAX identifier
        fd.append('action', 'cvf_upload_files');  
        
        // Remove this code if you do not want to associate your uploads to the current page.
        fd.append('post_id', <?php echo $post->ID; ?>); 

        $.ajax({
            type: 'POST',
            url: '<?php echo admin_url( 'admin-ajax.php' ); ?>',
            data: fd,
            contentType: false,
            processData: false,
            success: function(response){
                $('.upload-response').html(response); // Append Server Response
            }
        });
    });
});

</script>