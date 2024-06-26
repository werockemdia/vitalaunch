<?php
/**
 * The template for displaying the footer
 *
 * Contains the closing of the #content div and all content after.
 *
 * @link https://developer.wordpress.org/themes/basics/template-files/#template-partials
 *
 * @package suxnix
 */
?>

  <script type="text/javascript">

var t = {};
t.query = jQuery.noConflict( true );

</script>

    </main>
    <!-- main-area-end -->

    <?php
        do_action( 'suxnix_footer_style' );

        wp_footer();?>
        
        <script>
        $(document).mouseup(function(e) 
{
    
    var container = $(".dashMenu");

    // if the target of the click isn't the container nor a descendant of the container
    if (!container.is(e.target) && container.has(e.target).length === 0) 
    {
        container.hide();
    }
});

</script>

    </body>
</html>


