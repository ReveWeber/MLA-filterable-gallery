<?php
/*
Plugin Name: Filterable Gallery for Media Library Assistant
Plugin URI:  http://rweber.net
Description: A plugin to add a shortcode that displays a gallery with front-end filtration, powered by Media Library Assistant's gallery extension and custom taxonomies.
Version:     0.1
Author:      Rebecca Weber
Author URI:  http://rweber.net
License:     GPL2
License URI: https://www.gnu.org/licenses/gpl-2.0.html
Domain Path: /languages
Text Domain: mla-filterable-gallery
*/

// check that MLA is installed and activated.

add_action( 'admin_init', 'child_plugin_has_parent_plugin' );
function child_plugin_has_parent_plugin() {
    if ( is_admin() && current_user_can( 'activate_plugins' ) &&  !is_plugin_active( 'media-library-assistant/index.php' ) ) {
        add_action( 'admin_notices', 'child_plugin_notice' );
        deactivate_plugins( plugin_basename( __FILE__ ) ); 
        if ( isset( $_GET['activate'] ) ) {
            unset( $_GET['activate'] );
        }
    }
}

function child_plugin_notice(){
    ?><div class="error"><p>Sorry, but Filterable Gallery requires the plugin Media Library Assistant to be installed and active.</p></div><?php
}

add_action( 'wp_enqueue_scripts', 'fmlag_enqueue' );
function fmlag_enqueue( ) {
    wp_enqueue_script( 'fmlag-menu',
        plugins_url( '/gallery-menu.js', __FILE__ ),
        array( 'jquery' )
    );
    wp_enqueue_style( 'fmlag-style',
        plugins_url( '/style.css', __FILE__ )
    );
}

function add_fmlag_custom_menu() {
    //add an item to the menu
    add_media_page (
        'Filterable MLA Gallery Help and Settings',
        'Filterable MLA Gallery',
        'manage_options',
        'fmlag-options',
        'fmlag_admin_page_function'
    );
}

add_action( 'admin_menu', 'add_fmlag_custom_menu' );

function fmlag_admin_page_function() {
    ?>
    <div class="wrap">
        <h2>Filterable MLA Gallery Help and Settings</h2>
        
        <p>This plugin extends the <pre>[mla_gallery]</pre> shortcode to a <pre>[filterable_mla_gallery]</pre> shortcode, which adds a menu allowing front-end filtration by Att. Category.</p>

        <p>Attributes: You may set the default album to display by setting <pre>default="att-category-slug"</pre> inside the shortcode.</p>
        
    </div>
    <?php
}


add_shortcode( 'filterable_mla_gallery', 'filterable_gallery_output' );
function filterable_gallery_output( $atts ) {
    if ( ! shortcode_exists( 'mla_gallery' ) ) {
        return;
    }
    $return_value = '<div class="filtration-gallery" id="filtration-gallery">';
    $return_value .= '<div class="album-selector" id="album-selector">';
    $return_value .= '<div class="album-button" id="album-button"> Select an Album </div>';

    $args = array(
        'order'             => 'ASC',
        'parent'            => '0',
    ); 
    $terms = get_terms('attachment_category', $args);
    $first_term_slug = $terms[0]->slug;
    $first_term_name = $terms[0]->name;
    
    $linkstart = '<li><a href="' . esc_url( get_page_link() . '/?album=' );
    $linkslug = '" data-slug="';
    $linkend = ' <span class="album-arrows">&rang;&rang;</span></a></li>';

    $return_value .= '<ul class="mla-parent-categories" id="mla-parent-categories">';
    foreach ($terms as $value) {
        $slug = $value->slug;
        $return_value .= $linkstart . $slug . $linkslug . $slug . '" class="parent-link">' . $value->name . '</a>';
        $return_value .= '<ul class="mla-sub-categories">';
        $return_value .= $linkstart . $slug . $linkslug . $slug . '">All' . $linkend;
        $newargs = array( 'parent' => $value->term_id );
        $subterms = get_terms('attachment_category', $newargs);
        foreach ($subterms as $subvalue) {
                $s_slug = $subvalue->slug;
                $return_value .= $linkstart . $s_slug . $linkslug . $s_slug . '">' . $subvalue->name . $linkend;
        }
        $return_value .= '</ul> <!-- .mla-sub-categories -->';
        $return_value .= '</li>';
    }
    $return_value .= '</ul> <!-- .mla-parent-category -->';
    $return_value .= '</div> <!-- .album-selector -->';

    $return_value .= '<div id="current-album-wrapper">';
    $return_value .= '<div class="current-album" id="current-album">';
    
    $filterable_gallery_atts = shortcode_atts( array(
        'default' => $first_term_slug,
    ), $atts );

    if ( isset( $_GET["album"] ) && term_exists( $_GET["album"], 'attachment_category' ) ) {
        $slugarray = array( 'slug' => $_GET["album"], );
        $albumarray = get_terms( 'attachment_category', $slugarray );
        $return_value .= '<h2>' . $albumarray[0]->name . '</h2>';
        $return_value .= do_shortcode('[mla_gallery attachment_category=' . $_GET["album"] . ' size=thumbnail link=large mla_caption=""]');
    } else {
        $default_gallery_name = $first_term_name;
        if ( $filterable_gallery_atts["default"] != $first_term_slug ) {
            if( term_exists( $filterable_gallery_atts["default"] ) ) {
                $default_term = get_term_by( 'name', $filterable_gallery_atts["default"], 'attachment_category' );
                $default_gallery_name = $default_term->name;
            }
        }
        $return_value .= '<h2>' . $default_gallery_name . '</h2>';
        $return_value .= do_shortcode('[mla_gallery attachment_category=' . $filterable_gallery_atts["default"] . ' size=thumbnail link=large mla_caption=""]');
    }

    $return_value .= '</div> <!-- .current-album -->';
    $return_value .= '</div> <!-- #current-album-wrapper -->';
    $return_value .= '</div> <!-- .filtration-gallery -->';
    return $return_value;
}