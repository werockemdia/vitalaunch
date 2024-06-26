<?php
/**
 * The template for displaying archive pages
 *
 * @link https://developer.wordpress.org/themes/basics/template-hierarchy/
 *
 * @package suxnix
 */

get_header();
?>
<style>
section#mainHelp {
    background-image: linear-gradient(180deg, #FCBE8552 0%, #fff 100%);
    padding: 80px 10% !IMPORTANT;
}
.row.bannerHelp h1 {
        margin-bottom: 35px;
    font-family: 'Poppins', sans-serif;
    font-weight: 600;
}
.searchHelp .sidebar-search-form input {
    background: transparent !IMPORTANT;
}
.searchHelp button[type="submit"] {
    background: transparent;
}
.searchHelp button[type="submit"] i {
    color: #000;
    font-size: 25px;
}
.searchHelp {
    border: 3px solid #000;
    border-radius: 30px;
    margin-bottom: 50px;
}
.categoryBlock {
    border: 2px solid #000;
    padding: 25px;
    height: 210px;
    border-radius: 20px;
    margin: 20px 0px;
    font-family: 'Poppins', sans-serif;
}
.categoryBlock img {
    width: 40px;
    height: 40px;
    margin-bottom: 10px;
}
.categoryBlock h4 {
    font-family: 'Poppins', sans-serif;
    font-weight: 600;
}
a.accountBtn {
    background: #0D9B4D;
    color: #fff;
    width: max-content;
    padding: 10px 30px;
    margin: 0px 15px 30px;
    border-radius: 30px;
     font-family: 'Poppins', sans-serif;
}
</style>
<section id="mainHelp">
<div class="container">
    <?php if(is_user_logged_in()){ ?>
    <div class="row bannerHelp">
        <h1>Hi Test! How can we help you today?</h1>
        <!--<div class="searchHelp"><?php get_search_form(); ?></div>-->
        <a href="/my-account/mystore/" class="accountBtn">Go Back to Account Setting</a>
    </div>  
    
    <div class="row">
    <?php
$taxonomy = 'categories';
$terms = get_terms($taxonomy);

if ( $terms && !is_wp_error( $terms ) ) :
?>
 
        <?php foreach ( $terms as $term ) { ?>
           <div class="col-md-4">
               <div class="categoryBlock">
                   
      <?php 
      $category_id = $term->term_id;
$image = get_field('category_image', 'term_' . $category_id);
echo '  <img src="'.$image .'" /> ';
//echo $image = get_field('category_image');
//$size = 'full'; // (thumbnail, medium, large, full or custom size)
//if( $image ) {
 //   echo $term->wp_get_attachment_image( $image, $size );
//} ?>

           <h4> <a href="<?php echo get_term_link($term->slug, $taxonomy); ?>"><?php echo $term->name; ?></a> </h4>
            <p style="height:40px;"><?php echo $term->description; ?></p>
            <p style="text-align:right;"><?php echo $term->count." articles"; ?></p>
         
            </div>
            </div>
        <?php } ?>
    
<?php endif;?>
    </div>   
    <?php }else{
        echo "<div class='row bannerHelp'><h1>You Are Not Logged In Please Login.</h1><br><a href='/login' class='accountBtn'>Log In</a></div>";
    } ?>
</div>        
</section>

<?php
get_footer();
