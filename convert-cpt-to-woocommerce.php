<?php
/**
 * Plugin Name: Convert Custom Post Types to WooCommerce Products
 * Description: Automatically converts Market Listings, Events, and Venues into WooCommerce products.
 * Version: 1.1
 * Author: Sivuyile 
 * Author URI: https://sivuyileparkies.co.za/
 * License: GPL2
 * Disclaimer: This plugin is tailored for company-specific use cases and may require customizations for other users.
 */

add_action('save_post_market-listing', 'convert_custom_post_to_product', 10, 3);
add_action('save_post_market-event', 'convert_custom_post_to_product', 10, 3);
add_action('save_post_market-venue', 'convert_custom_post_to_product', 10, 3);

function convert_custom_post_to_product($post_id, $post, $update) {
    // Avoid recursion or autosave
    if (defined('DOING_AUTOSAVE') && DOING_AUTOSAVE) {
        return;
    }

    // Ensure WooCommerce is active
    if (!class_exists('WC_Product')) {
        return;
    }

    // Check for the ACF field 'stall_fee' or default price
    $stall_fee = get_field('stall_fee', $post_id) ?: '0.00';

    // Check if a WooCommerce product already exists
    $existing_product_id = get_post_meta($post_id, '_linked_wc_product_id', true);

    if ($existing_product_id && wc_get_product($existing_product_id)) {
        // Update the existing product
        $product = wc_get_product($existing_product_id);
    } else {
        // Create a new product
        $product = new WC_Product_Simple();
    }

    // Set product properties
    $product->set_name($post->post_title);
    $product->set_regular_price($stall_fee); // Set the price from the ACF field
    $product->set_description($post->post_content);
    $product->set_short_description($post->post_excerpt);
    $product->set_status('publish');
    $product->set_catalog_visibility('visible');
    $product->set_sku($post->post_type . '-' . $post_id); // Unique SKU
    $product->save();

    // Link the product ID back to the custom post
    update_post_meta($post_id, '_linked_wc_product_id', $product->get_id());
}

add_action('before_delete_post', 'delete_linked_wc_product');

function delete_linked_wc_product($post_id) {
    // Get the linked WooCommerce product ID
    $product_id = get_post_meta($post_id, '_linked_wc_product_id', true);
    if ($product_id) {
        wp_delete_post($product_id, true); // Force delete the product
    }
}

add_action('template_redirect', 'handle_checkout_redirect');

function handle_checkout_redirect() {
    // Check if we're on the checkout page and the post_id parameter exists
    if (is_checkout() && isset($_GET['post_id'])) {
        $post_id = absint($_GET['post_id']);

        // Get the linked WooCommerce product ID
        $product_id = get_post_meta($post_id, '_linked_wc_product_id', true);

        if ($product_id) {
            $product = wc_get_product($product_id);
            if ($product) {
                // Add the product to the cart
                WC()->cart->empty_cart(); // Optional: Clear the cart first
                WC()->cart->add_to_cart($product_id);

                // Remove the query parameter to avoid duplicate actions on refresh
                wp_safe_redirect(remove_query_arg('post_id'));
                exit;
            }
        }
    }
}
