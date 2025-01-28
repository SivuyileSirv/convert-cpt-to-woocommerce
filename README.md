# Convert CPT to WooCommerce

**Convert CPT to WooCommerce** 
 It simplifies the management of custom content by integrating it seamlessly with WooCommerce, enabling users to leverage WooCommerce's robust e-commerce functionality for their custom post types.

---

## Features

- Automatically converts specific custom post types into WooCommerce products when they are created or updated.
- Links WooCommerce products to their originating custom posts via metadata.
- Deletes linked WooCommerce products when a custom post is deleted.
- Adds WooCommerce products dynamically to the cart during checkout based on custom post selection.

---

## Target Audience

- Note: This plugin was developed for managing market-related content (Market Listings, Events, and Venues) and is tailored for specific use cases. Customizations might be required for other scenarios.

---

## Requirements

- WordPress 6.0+
- WooCommerce 7.0+
- Advanced Custom Fields (ACF) plugin for managing custom fields (optional but recommended).

---

## Installation

1. **Download the Plugin**
   - Clone or download the plugin files.

2. **Upload to WordPress**
   - Upload the `convert-cpt-to-woocommerce` folder to your WordPress installation under `/wp-content/plugins/`.

3. **Activate the Plugin**
   - Go to your WordPress Admin Dashboard > Plugins > Installed Plugins.
   - Locate `Convert CPT to WooCommerce` and click **Activate**.

4. **Ensure WooCommerce is Active**
   - Confirm that WooCommerce is installed and activated in your WordPress environment.

---

## Configuration

### Custom Post Types Supported

The plugin currently supports the following custom post types:

- `market-listing`
- `market-event`
- `market-venue`

You can add support for additional custom post types by registering the `save_post` hook in the plugin's code.

### Metadata Requirements

- Each custom post type must have metadata for the `_linked_wc_product_id` to track its linked WooCommerce product.
- ACF fields can be used to provide additional data for the WooCommerce product, such as pricing.

---

## ACF Field Configurations

To ensure proper functionality with the plugin, please configure the following ACF (Advanced Custom Fields) field:

### Field Details:
- **Field:** `stall_fee`
- **Field Name:** `stall_fee`
- **Field Type:** Number
- **Location:** 
    - Show this field group if **Post Type** is equal to:
        - `market-listing`
        - `market-event`
        - `market-venue`
- **Instructions:** Enter the fee for the stall (this will be used as the product price in WooCommerce).
- **Required:** Yes
- **Default Value:** `0.00`
- **Step:** `0.01` (for decimal precision)

---

## Usage

### 1. Creating a Custom Post

- When a custom post of the supported types is created or updated, the plugin automatically:
  - Creates a new WooCommerce product if none exists.
  - Updates the linked WooCommerce product if it already exists.

### 2. Deleting a Custom Post

- When a custom post of the supported types is deleted, the linked WooCommerce product is also deleted.

### 3. Redirecting to WooCommerce Checkout

- Add a query parameter `post_id` to the WooCommerce checkout URL (e.g., `https://yourdomain.com/checkout?post_id=123`).
- The plugin will:
  - Retrieve the WooCommerce product linked to the custom post ID.
  - Add the product to the cart.
  - Redirect to the checkout page without the query parameter.

---

## Developer Notes

### Adding Support for Additional Custom Post Types

1. Add a new `save_post` hook for your custom post type in the plugin code.
2. Update the `convert_custom_post_to_product` function to handle the new post type's metadata and field mappings.

### Debugging

- The plugin logs actions to the WordPress debug log if `WP_DEBUG` is enabled.
- Check the debug log for issues with post-product synchronization.

---

## Example Usage with JavaScript

If your theme includes a **Book Now** button for custom posts, you can redirect users to WooCommerce checkout using JavaScript:

```javascript
jQuery(document).ready(function ($) {
    $('#book_now_btn').on('click', function () {
        const postId = $(this).data('post-id');
        if (postId) {
            window.location.href = `/checkout?post_id=${postId}`;
        }
    });
});
```

---

## Changelog

### v1.0.0
- Initial release.
- Automatic WooCommerce product creation and updating for custom post types.
- Linked product deletion when custom posts are removed.
- Dynamic checkout redirection based on custom post IDs.

---

