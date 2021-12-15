<?php

// enqueue parent styles
function to_enqueue_styles() {
    $parenthandle = 'parent-style';
    $theme = wp_get_theme();
    wp_enqueue_style( $parenthandle, get_template_directory_uri() . '/style.css',
        array(),
        $theme->parent()->get('Version')
    );
    wp_enqueue_style( 'child-style', get_stylesheet_uri(),
        array( $parenthandle ),
        $theme->get('Version')
    );
}
add_action( 'wp_enqueue_scripts', 'to_enqueue_styles' );

add_filter( 'wc_memberships_allow_cumulative_member_discounts', '__return_false' );
add_filter( 'big_image_size_threshold', '__return_false' );

function custom_downloads_columns( $columns ){
    // Removing "Download expires" column
    if(isset($columns['download-expires']))
        unset($columns['download-expires']);

    // Removing "Download remaining" column
    if(isset($columns['download-remaining']))
        unset($columns['download-remaining']);

    return $columns;
}
add_action( 'woocommerce_account_downloads_columns', 'custom_downloads_columns', 10, 1 ); // Orders and account
add_action( 'woocommerce_email_downloads_columns', 'custom_downloads_columns', 10, 1 ); // Email notifications

// add pinterest verify code
function add_pinterest_verify() {
   echo '<!-- Pinterest Verify -->';
   echo '<meta name="p:domain_verify" content="be12f449630292b6997c0c48f3aecba9"/>';
}
add_action('wp_head', 'add_pinterest_verify');

// Add WooCommerce Checkbox checkout
function bt_add_checkout_checkbox() {
    woocommerce_form_field( 'checkout-checkbox', array( // CSS ID
       'type'          => 'checkbox',
       'class'         => array('form-row mycheckbox'), // CSS Class
       'label_class'   => array('woocommerce-form__label woocommerce-form__label-for-checkbox checkbox'),
       'input_class'   => array('woocommerce-form__input woocommerce-form__input-checkbox input-checkbox'),
       'required'      => true, // Mandatory or Optional
       'label'         => 'I understand that any recurring charges will auto-charge at the end of my subscription (yearly or monthly) on this date unless I cancel my subscription, and I am able to cancel at anytime.', // Label and Link
    ));
}
add_action( 'woocommerce_review_order_before_submit', 'bt_add_checkout_checkbox', 10 );

// Alert if checkbox not checked
function bt_add_checkout_checkbox_warning() {
    if ( ! (int) isset( $_POST['checkout-checkbox'] ) ) {
        wc_add_notice( __( 'Please acknowledge the auto-charge statement.' ), 'error' );
    }
}
add_action( 'woocommerce_checkout_process', 'bt_add_checkout_checkbox_warning' );