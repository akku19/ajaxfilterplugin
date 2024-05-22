<?php
/**
 * Plugin Name: Product Categories AJAX
 * Description: Displays WooCommerce product categories and loads products via AJAX when a category is clicked.
 * Version: 1.0
 * Author: akash sen
 */

// Prevent direct access
if (!defined('ABSPATH')) {
    exit;
}

// Register shortcode
function register_my_shortcode() {
    add_shortcode('my_shortcode', 'wc_product_categories_shortcode');
}
add_action('init', 'register_my_shortcode');
function wc_product_categories_shortcode() {
    ob_start();
    ?>
    <div class="container mt-5">
        <div class="row">
            <div class="col-12">

            <ul class="nav nav-tabs" id="myTab" role="tablist">
  <li class="nav-item" role="presentation">
    <button class="nav-link active" id="ringselection" data-bs-toggle="tab" data-bs-target="#ring" type="button" role="tab" aria-controls="home" aria-selected="true">Ring Selection</button>
  </li>
  <li class="nav-item" role="presentation">
    <button class="nav-link" id="profile-tab" data-bs-toggle="tab" data-bs-target="#profile" type="button" role="tab" aria-controls="profile" aria-selected="false">dimand selection</button>
  </li>
  <li class="nav-item" role="presentation">
    <button class="nav-link" id="contact-tab" data-bs-toggle="tab" data-bs-target="#contact" type="button" role="tab" aria-controls="contact" aria-selected="false">Contact</button>
  </li>
</ul>
<div class="tab-content" id="myTabContent">
  <div class="tab-pane fade show active" id="ring" role="tabpanel" aria-labelledby="ringselection">
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
  <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
                
                <?php
                // Fetch all product categories
                $terms = get_terms(array(
                    'taxonomy' => 'product_cat',
                    'hide_empty' => false,
                ));

                if (!empty($terms) && !is_wp_error($terms)) {
                    echo '<ul>';
                    foreach ($terms as $term) {
                        echo '<li><label><input type="checkbox" class="category-checkbox" value="' . esc_attr($term->term_id) . '"> ' . esc_html($term->name) . '</label></li>';
                    }
                    echo '</ul>';
                    // echo '<button id="load-products-btn">Load Products</button>';
                } else {
                    echo 'No product categories found';
                }
                ?>
                <div id="product-list">
                    <?php
                    // Fetch and display all products by default
                    $args = array(
                        'post_type' => 'product',
                        'posts_per_page' => 10,
                    );

                    $query = new WP_Query($args);

                    if ($query->have_posts()) {
                        while ($query->have_posts()) {
                            $query->the_post();
                            echo '<div class="product">';
                            echo '<h3>' . get_the_title() . '</h3>';
                            echo '<div class="product-image">' . get_the_post_thumbnail() . '</div>';
                            echo '<div class="product-details"><a href="' . get_permalink() . '">Details</a></div>';
                            echo '</div>';
                        }
                        wp_reset_postdata();
                    } else {
                        echo 'No products found';
                    }
                    ?>
                </div>
            </div>
        </div>
    </div>
  </div>
  <div class="tab-pane fade" id="profile" role="tabpanel" aria-labelledby="profile-tab">
    <h3>Profile</h3>
    <p>This is the profile tab content.</p>
  </div>
  <div class="tab-pane fade" id="contact" role="tabpanel" aria-labelledby="contact-tab">
    <h3>Contact</h3>
    <p>This is the contact tab content.</p>
  </div>
</div>

    <?php
    return ob_get_clean();
}

// Enqueue scripts
function enqueue_custom_scripts() {
    wp_enqueue_script('custom-ajax-script', plugin_dir_url(__FILE__) . 'js/custom-ajax.js', array('jquery'), null, true);
    wp_localize_script('custom-ajax-script', 'ajax_object', array('ajax_url' => admin_url('admin-ajax.php')));
}
add_action('wp_enqueue_scripts', 'enqueue_custom_scripts');

// AJAX handler
function load_products_ajax() {
    $cat_ids = isset($_POST['cat_ids']) ? array_map('intval', $_POST['cat_ids']) : array();

    if (empty($cat_ids)) {
        echo 'No categories selected';
        wp_die();
    }

    $args = array(
        'post_type' => 'product',
        'posts_per_page' => 10,
        'tax_query' => array(
            array(
                'taxonomy' => 'product_cat',
                'field' => 'term_id',
                'terms' => $cat_ids,
            ),
        ),
    );

    $query = new WP_Query($args);

    if ($query->have_posts()) {
        while ($query->have_posts()) {
            $query->the_post();
            echo '<div class="product">';
            echo '<h3>' . get_the_title() . '</h3>';
            echo '<div class="product-image">' . get_the_post_thumbnail() . '</div>';
            echo '<div class="product-details"><a href="' . get_permalink() . '">Details</a></div>';
            echo '</div>';
        }
        wp_reset_postdata();
    } else {
        echo 'No products found in these categories';
    }

    wp_die();
}
add_action('wp_ajax_load_products', 'load_products_ajax');
add_action('wp_ajax_nopriv_load_products', 'load_products_ajax');


