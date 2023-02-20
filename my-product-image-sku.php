<?php
/*
Plugin Name: My Product Image SKU
Plugin URI: https://example.com/my-product-image-sku
Description: Automatically adds product SKU to the filename and bottom of product images.
Version: 1.0
Author: Your Name
Author URI: https://example.com
*/

function my_product_image_sku_add_sku_to_image( $metadata, $attachment_id ) {
    $attachment = get_post( $attachment_id );
    $product_id = $attachment->post_parent;
    $product = wc_get_product( $product_id );
    $sku = $product->get_sku();
    if ( $sku ) {
        $file = get_attached_file( $attachment_id );
        $pathinfo = pathinfo( $file );
        $new_filename = $pathinfo['dirname'] . '/' . $sku . '-' . $pathinfo['basename'];
        rename( $file, $new_filename );
        update_attached_file( $attachment_id, $new_filename );
        $image = wp_get_image_editor( $new_filename );
        if ( ! is_wp_error( $image ) ) {
            $image->set_quality( 100 );
            $image->get_image_mime();
            $image->save( $new_filename );
            $image_size = $image->get_size();
            $image->resize( $image_size['width'], $image_size['height'] + 30 );
            $text_color = imagecolorallocate( $image->image, 255, 255, 255 );
            $font_path = plugin_dir_path( __FILE__ ) . 'arial.ttf';
            imagettftext( $image->image, 16, 0, 10, $image_size['height'] + 22, $text_color, $font_path, 'SKU: ' . $sku );
            $image->save( $new_filename );
        }
    }
    return $metadata;
}
add_filter( 'wp_generate_attachment_metadata', 'my_product_image_sku_add_sku_to_image', 10, 2 );


add_filter( 'wp_generate_attachment_metadata', 'my_product_image_sku_add_sku_to_image', 10, 2 );
