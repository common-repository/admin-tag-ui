<?php
if ( ! defined( 'WP_UNINSTALL_PLUGIN' ) ) { exit; }

// Single site uninstall
// Also, don't run uninstall script on large networks (more than 10,000 users or more than 10,000 sites). Just uninstall from main site
if  ( ! is_multisite() || ( is_multisite() && wp_is_large_network() ) )
{
    admin_tag_ui_uninstall();
}

// Multisite uninstall
else
{
    global $wpdb;
    $blog_ids = $wpdb->get_col( "SELECT blog_id FROM $wpdb->blogs" );
    $original_blog_id = get_current_blog_id();

    foreach ( $blog_ids as $blog_id )
    {
        switch_to_blog( $blog_id );
        admin_tag_ui_uninstall();
    }

    switch_to_blog( $original_blog_id );
}

function admin_tag_ui_uninstall()
{
    //delete_option('atui_version');
    delete_option( 'atui_settings' );
}
