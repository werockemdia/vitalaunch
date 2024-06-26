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
        font-family: 'Poppins', sans-serif;
}
.row.bannerHelp h1 {
    margin-bottom: 35px;
        font-family: 'Poppins', sans-serif;
}
section#mainHelp h4
{
         font-family: 'Poppins', sans-serif;
             font-weight: 600;
}
.searchHelp {
    border: 3px solid #000;
    border-radius: 30px;
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
.accordion button p
{
        display: contents;
}
div#myAccordion h2 {
    font-family: 'Poppins', sans-serif;
}
.accordion-item button {
    font-weight: 600;
}


.accordion-item {
    border: 0px;
    padding:0px;
}
div#myAccordion {
    padding: 0px;
}
h2#headingOne a button:hover {
    background: #f5f4f4 !important;
}
button.accordion-button.collapsed:after {
    font-family: "Font Awesome 5 Free";
    font-weight: 600;
    content: "\f061";
    background-image: none !important;
}
.row.accordianRow {
    border: 2px solid #ddd;
    margin: 30px 20px;
    border-radius: 3px;
}
a.accountBtn {
    background: #0D9B4D;
    color: #fff;
    width: max-content;
    padding: 10px 30px;
    margin: 0px 0px 30px;
    border-radius: 30px;
    font-family: 'Poppins', sans-serif;
}
@media screen and (max-width: 768px)
{
    section#mainHelp
    {
        padding: 20px 0% !IMPORTANT;
    }
}
</style>
<section id="mainHelp">
    
<div class="container">
  
    <div class="row">
        <div class="m-4">
        <?php
        $termid = get_queried_object()->term_id;
    $term = get_term( $termid ); 
    
    $image = get_field('category_image', 'term_' . $termid);
    echo '  <img style="width: 50px;margin-bottom: 16px;" src="'.$image .'" /> ';
    ?>
    <h4>
    <?php echo $term_name = $term->name; ?>
    </h4>
    <p>
    <?php echo $term_name = $term->description; ?>
    </p>
    
    <a href="/my-account/mystore/" class="accountBtn">Go Back to Account Setting</a>
        </div>
  </div>
</div>    
    
<div class="container">
    
    <div class="row accordianRow">
    <?php
    //echo $ftu = get_queried_object()->term_id;
    $termid = get_queried_object()->term_id;
    $term = get_term( $termid );
    $term_name = $term->name;


 $args = array(
    'post_type' => 'help-center',
    'posts_per_page'=> -1,
    'orderby' => 'title',
    'order' => 'ASC',
    'tax_query' => array(
        array(
        'taxonomy' => 'categories',
        'field' => 'slug',
        'terms' => $term_name
                )
            )
        );

        $products = new WP_Query( $args );
            if( $products->have_posts() ) {
                while( $products->have_posts() ) {
                    $products->the_post();
?>


    <div class="accordion" id="myAccordion">
        <div class="accordion-item">
            <h2 class="accordion-header" id="headingOne">
                <a href="<?php the_permalink(); ?>">
                <button type="button" class="accordion-button collapsed" data-bs-toggle="collapse" data-bs-target="#collapseOne"><?php the_title(); ?><br><?php the_excerpt(); ?>
                </button>
                </a>
            </h2>
        </div>
    </div>

<?php
    }
        }
            else {
                echo 'There seems to be a problem, please try searching again or contact customer support!';
            } ?>
    </div>    
</div>     







</section>

<?php
get_footer();
