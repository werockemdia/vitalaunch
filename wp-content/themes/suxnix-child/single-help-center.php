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
    padding: 80px 20% !IMPORTANT;
}
section#mainHelp h1 {
    font-family: 'Poppins', sans-serif;
    font-size: 30px;
    font-weight: 700;
}
section#mainHelp h4
{
    font-family: 'Poppins', sans-serif;
     font-weight: 500;
     margin-bottom:0px;
}
.content-here p
{
    font-family: 'Poppins', sans-serif;
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

@media screen and (max-width: 1024px)
{
    section#mainHelp {
    padding: 50px 0 !IMPORTANT;
}
}
</style>
<section id="mainHelp">
<div class="container">
  <div class="row">
    
<?php while ( have_posts() ) : the_post(); ?>

  <h1><?php echo get_the_title(); ?></h1>
  <h4><?php the_excerpt(); ?></h4>
  <a href="/my-account/mystore/" class="accountBtn">Go Back to Account Setting</a>
  
   <div class="content-here">
                <?php  the_content();  ?>
                </div>
<?php endwhile; ?>
  </div>      
</div>        
</section>

<?php
get_footer();
