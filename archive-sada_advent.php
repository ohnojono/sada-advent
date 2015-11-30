<?php get_header(); ?>
<section id="primary">
    <div id="content" role="main" style="width: 100%">
    <?php 
    query_posts($query_string . '&meta_key=advent_number&orderby=meta_value_num&order=ASC&posts_per_page=24');

    if ( have_posts() ) : ?>
        <header class="page-header">
            <h1 class="page-title">Advent Calendar</h1>
            <p>Find out when a new door on the advent calendar is open by following Shetland Arts on Twitter and Facebook!<p>
                <script>window.twttr = (function(d, s, id) {
  var js, fjs = d.getElementsByTagName(s)[0],
    t = window.twttr || {};
  if (d.getElementById(id)) return t;
  js = d.createElement(s);
  js.id = id;
  js.src = "https://platform.twitter.com/widgets.js";
  fjs.parentNode.insertBefore(js, fjs);
 
  t._e = [];
  t.ready = function(f) {
    t._e.push(f);
  };
 
  return t;
}(document, "script", "twitter-wjs"));</script>
<a class="twitter-follow-button" href="https://twitter.com/ShetlandArts"> Follow @ShetlandArts</a>
       
       <div id="fb-root"></div>
<script>(function(d, s, id) {
  var js, fjs = d.getElementsByTagName(s)[0];
  if (d.getElementById(id)) return;
  js = d.createElement(s); js.id = id;
  js.src = "//connect.facebook.net/en_GB/sdk.js#xfbml=1&version=v2.5&appId=125727667513247";
  fjs.parentNode.insertBefore(js, fjs);
}(document, 'script', 'facebook-jssdk'));</script>
<div class="fb-like" data-href="https://www.facebook.com/shetlandarts" data-layout="standard" data-action="like" data-show-faces="false" data-share="true"></div>
        </header>
        
            <div class="sada_advent-holder">
                <!-- Start the Loop -->
                <?php 
                $adventsCount = 0;
                $today = date("j");
                $month = date("m");

                while ( have_posts() ) : the_post(); ?>
                    
                    <?php if($adventsCount < $today && $month == 12): ?>
                    <?php $adventsCount++; ?>
                    <!-- Display advent image and info -->
                        <div class="sada-advent-door">
                            <div class="sada-advent-image">
                                <?php the_post_thumbnail( ); ?>
                            <div class="sada-advent-info">
                                <div class="sada-advent-date"><?php echo esc_html( get_post_meta( get_the_ID(), 'advent_number', true ) ); ?></td></div>
                                <div class="sada-advent-title"><?php the_title(); ?></div>
                                <div class="sada-advent-details"><?php the_content(); ?></div>
                            </div>
                            </div>
                        </div>
                    <?php endif; ?>
                <?php endwhile; ?>
                <?php for ($adventsComing = $adventsCount+1; $adventsComing <= 24; $adventsComing++): ?>
                    <div class="sada-advent-door">
                        <div class="sada-advent-image">
                            <div class="sada-advent-coming">
                                <div class="sada-advent-date">
                                    <?php echo $adventsComing; ?>
                                </div>
                                <div class="sada-advent-details">Will be revealed December <?php echo $adventsComing; ?></div>
                            </div>
                        </div>
                    </div>
                <?php endfor ?>

            </div>
 
            <!-- Display page navigation -->
 
        </table>
        <?php global $wp_query;
        if ( isset( $wp_query->max_num_pages ) && $wp_query->max_num_pages > 1 ) { ?>
            <nav id="<?php echo $nav_id; ?>">
                <div class="nav-previous"><?php next_posts_link( '<span class="meta-nav">&larr;</span> Older reviews'); ?></div>
                <div class="nav-next"><?php previous_posts_link( 'Newer reviews <span class= "meta-nav">&rarr;</span>' ); ?></div>
            </nav>
        <?php };
    endif; ?>
    </div>
</section>
<br /><br />
<?php get_footer(); ?>