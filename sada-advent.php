<?php
/*
Plugin Name: SADA Advent
Plugin URI: http://shetlandarts.org/
Description: Declares a plugin that will create a custom post type displaying advent calendar.
Version: 1.0
Author: Jono Sandilands
Author URI: http://jonosandilands.com/
License: GPLv2
*/

//register advent
add_action( 'init', 'create_sada_advent' );

function create_sada_advent() {
    register_post_type( 'sada_advent',
        array(
            'labels' => array(
                'name' => 'Advent Calendar',
                'singular_name' => 'Advent',
                'add_new' => 'Add New',
                'add_new_item' => 'Add New Advent',
                'edit' => 'Edit',
                'edit_item' => 'Edit Advent',
                'new_item' => 'New Advent',
                'view' => 'View',
                'view_item' => 'View Advent',
                'search_items' => 'Search Advents',
                'not_found' => 'No Advents found',
                'not_found_in_trash' => 'No Advents found in Trash',
                'parent' => 'Parent Advent'
            ),
 
            'public' => true,
            'menu_position' => 15,
            'supports' => array( 'title', 'editor', 'comments', 'thumbnail', 'custom-fields' ),
            'taxonomies' => array( '' ),
            'menu_icon' => plugins_url( 'images/calendar_2.png', __FILE__ ),
            'has_archive' => true
        )
    );
}

//Custom Fields
add_action( 'admin_init', 'advent_admin' );

function advent_admin() {
    add_meta_box( 'advent_admin_meta_box',
        'Advent Details',
        'display_advent_details_meta_box',
        'sada_advent', 'normal', 'high'
    );
}

function display_advent_details_meta_box( $advent_details ) {
    // Retrieve advent number
    $advent_number = esc_html( get_post_meta( $advent_details->ID, 'advent_number', true ) );
    ?>
    <table>
        <tr>
            <td style="width: 100%">Advent Date</td>
            <td><input type="text" size="75" name="advent_date_number" value="<?php echo $advent_number; ?>" /></td>
        </tr>
    </table>
    <p> Note: input the advent day in number form ie "1", "2" "3"... "24"</p>
    <?php
}

add_action( 'save_post', 'add_advent_detail_fields', 10, 2 );

function add_advent_detail_fields( $advent_details_id, $advent_details ) {
    // Check post type for advent reviews
    if ( $advent_details->post_type == 'sada_advent' ) {
        // Store data in post meta table if present in post data
        if ( isset( $_POST['advent_date_number'] ) && $_POST['advent_date_number'] != '' ) {
            update_post_meta( $advent_details_id, 'advent_number', $_POST['advent_date_number'] );
        }
    }
}

// Display advent date in Admin
add_filter( 'manage_edit-sada_advent_columns', 'advent_columns' );

function advent_columns( $columns ) {
    $columns['advent_number'] = 'Advent Date';
    unset( $columns['comments'] );
        unset( $columns['date'] );

    return $columns;
}

add_action( 'manage_posts_custom_column', 'populate_columns' );

function populate_columns( $column ) {
    if ( 'advent_number' == $column ) {
        $advent_number = esc_html( get_post_meta( get_the_ID(), 'advent_number', true ) );
        echo $advent_number;
    }
}

add_filter( 'manage_edit-sada_advent_sortable_columns', 'sort_me' );

function sort_me( $columns ) {
    $columns['advent_number'] = 'advent_number';
 
    return $columns;
}
 
add_filter( 'request', 'column_orderby' );
 
function column_orderby ( $vars ) {
    if ( !is_admin() )
        return $vars;
    if ( isset( $vars['orderby'] ) && 'advent_number' == $vars['orderby'] ) {
        $vars = array_merge( $vars, array( 'meta_key' => 'advent_number', 'orderby' => 'meta_value_num' ) );
    }
    return $vars;
}

//template
add_filter( 'template_include', 'include_template_function', 1 );

function include_template_function( $template_path ) {
    if ( get_post_type() == 'sada_advent' ) {
        if ( is_single() ) {
            // checks if the file exists in the theme first,
            // otherwise serve the file from the plugin
            if ( $theme_file = locate_template( array ( 'single-sada_advent.php' ) ) ) {
                $template_path = $theme_file;
            } else {
                $template_path = plugin_dir_path( __FILE__ ) . 'single-sada_advent.php';
            }
        }
        elseif ( is_archive() ) {
            if ( $theme_file = locate_template( array ( 'archive-sada_advent.php' ) ) ) {
                $template_path = $theme_file;
            } else { $template_path = plugin_dir_path( __FILE__ ) . 'archive-sada_advent.php';
 
            }
        }
    }
    return $template_path;
}

/**
 * Register with hook 'wp_enqueue_scripts', which can be used for front end CSS and JavaScript
 */
add_action( 'wp_enqueue_scripts', 'prefix_add_my_stylesheet' );

/**
 * Enqueue plugin style-file
 */
function prefix_add_my_stylesheet() {
    // Respects SSL, Style.css is relative to the current file
    wp_register_style( 'prefix-style', plugins_url('sada_advent.css', __FILE__) );
    wp_enqueue_style( 'prefix-style' );
}

//shortcode - today advent
function display_today($atts){
   extract(shortcode_atts(array(
        //turn the image on(1) or off(0)
      'image' => 1,
      //turn the title on(1) or off(0)
      'title' => 1,
      //turn the content on(1) or off(0)
      'content' => 1,
      //support for 'widget' (default) or 'full' screen display 
      'size' => widget,
   ), $atts));
?>
<?php 

        $today = date("j");

        $args = array(
            'post_type'=> 'sada_advent',
            'order'    => 'ASC',
            'meta_key'  =>  'advent_number',
            'meta_value' => $today,
            'orderby'   =>  'meta_value_num'
            );    

        $the_query = new WP_Query( $args );
        if($the_query->have_posts() ) : while ( $the_query->have_posts() ) : $the_query->the_post(); 

        ?>
           <?php if ( $size == 'full' ) : ?>

            <div class="bg">
                <?php if ($image == 1){
                the_post_thumbnail( ); 
                }?>
                <div class="todays-advent">
                    <div class="todays-advent-text">
                    <h4>Today's Shetland Arts Advent</h4>
                      <?php if ($title == 1) {
                        the_title('<h1>', '</h1>' );
                    } if ($content == 1) {
                         the_content(); 
                    } ?>
                </div>
                </div>
            </div>
           <?php elseif($size == 'widget') : ?>

               <?php if ($image == 1){
                the_post_thumbnail( ); 
                }?>
                    <?php if ($title == 1) {
                        the_title('<h2>', '</h2>' );
                    } if ($content == 1) {
                        the_content(); 
                    } ?>

        <?php endif; ?>

        <?php endwhile; else: ?>

            <p>Nothing Here.</p>

        <?php endif; wp_reset_postdata(); ?>
<?php
}
add_shortcode('adventtoday', 'display_today');

add_action( 'template_redirect', 'sada_advent_redirect' );

function sada_advent_redirect()
{
    if ( ! is_singular( 'sada_advent' ) )
        return;

    wp_redirect( get_post_type_archive_link( 'sada_advent' ), 301 );
    exit;
}


?>