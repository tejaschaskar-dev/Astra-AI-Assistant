<?php
if ( ! defined( 'ABSPATH' ) ) exit;

class AAA_Context {

    public static function get_astra_context() {

        // Check if Astra is active
        if ( ! function_exists( 'astra_get_option' ) ) {
            return "Astra Theme is not active on this site.";
        }

        // === SITE INFO ===
        $site_name = get_bloginfo( 'name' );
        $site_url  = get_site_url();
        $wp_version = get_bloginfo( 'version' );

        // === ASTRA SETTINGS ===

        // Layout
        $site_layout       = astra_get_option( 'site-layout', 'full-width' );
        $container_width   = astra_get_option( 'site-content-width', 1200 );
        $sidebar_layout    = astra_get_option( 'site-sidebar-layout', 'right-sidebar' );
        $sidebar_width     = astra_get_option( 'site-sidebar-width', 30 );

        // Header
        $header_layout     = astra_get_option( 'header-layouts', 'header-main-layout-1' );
        $sticky_header     = astra_get_option( 'sticky-header-on-devices', 'none' );
        $transparent_header = astra_get_option( 'transparent-header', false );
        $header_bg_color   = astra_get_option( 'header-bg-color-responsive', array( 'desktop' => '', 'tablet' => '', 'mobile' => '' ) );
        $logo_width        = astra_get_option( 'ast-header-responsive-logo-width', array( 'desktop' => 150, 'tablet' => 120, 'mobile' => 100 ) );

        // Colors
        $theme_color       = astra_get_option( 'theme-color', '#0066cc' );
        $link_color        = astra_get_option( 'link-color', '' );
        $heading_color     = astra_get_option( 'heading-color', '' );
        $text_color        = astra_get_option( 'text-color', '' );
        $bg_color          = astra_get_option( 'site-layout-outside-bg-color', '' );

        // Typography
        $body_font         = astra_get_option( 'body-font-family', 'Default' );
        $body_font_size    = astra_get_option( 'font-size-body', array( 'desktop' => 15, 'tablet' => 15, 'mobile' => 15 ) );
        $body_font_weight  = astra_get_option( 'body-font-weight', 'inherit' );
        $heading_font      = astra_get_option( 'headings-font-family', 'Default' );
        $h1_font_size      = astra_get_option( 'font-size-h1', array( 'desktop' => 40 ) );
        $h2_font_size      = astra_get_option( 'font-size-h2', array( 'desktop' => 30 ) );

        // Footer
        $footer_layout     = astra_get_option( 'footer-layouts', '1' );
        $footer_bg_color   = astra_get_option( 'footer-bg-color', '' );
        $footer_color      = astra_get_option( 'footer-color', '' );

        // Blog
        $blog_layout       = astra_get_option( 'blog-layout', 'blog-layout-1' );
        $blog_sidebar      = astra_get_option( 'archive-sidebar-layout', 'right-sidebar' );

        // WooCommerce (if active)
        $woo_active        = class_exists( 'WooCommerce' ) ? 'Yes' : 'No';
        $woo_sidebar       = astra_get_option( 'woo-sidebar-layout', 'no-sidebar' );
        $woo_columns       = astra_get_option( 'woo-archive-columns', 3 );

        // Active plugins count
        $active_plugins    = count( get_option( 'active_plugins', array() ) );

        // Build context string
        $context  = "=== WORDPRESS SITE INFORMATION ===\n";
        $context .= "Site Name: {$site_name}\n";
        $context .= "Site URL: {$site_url}\n";
        $context .= "WordPress Version: {$wp_version}\n";
        $context .= "Active Plugins: {$active_plugins}\n\n";

        $context .= "=== ASTRA THEME SETTINGS ===\n\n";

        $context .= "--- LAYOUT ---\n";
        $context .= "Site Layout: {$site_layout}\n";
        $context .= "Container Width: {$container_width}px\n";
        $context .= "Sidebar Position: {$sidebar_layout}\n";
        $context .= "Sidebar Width: {$sidebar_width}%\n\n";

        $context .= "--- HEADER ---\n";
        $context .= "Header Layout: {$header_layout}\n";
        $context .= "Sticky Header: {$sticky_header}\n";
        $context .= "Transparent Header: " . ( $transparent_header ? 'Enabled' : 'Disabled' ) . "\n";
        $context .= "Logo Width Desktop: " . ( is_array($logo_width) ? $logo_width['desktop'] : $logo_width ) . "px\n\n";

        $context .= "--- COLORS ---\n";
        $context .= "Primary Color: {$theme_color}\n";
        $context .= "Link Color: " . ( $link_color ?: 'Using Primary Color' ) . "\n";
        $context .= "Heading Color: " . ( $heading_color ?: 'Default' ) . "\n";
        $context .= "Text Color: " . ( $text_color ?: 'Default' ) . "\n";
        $context .= "Background Color: " . ( $bg_color ?: 'Default' ) . "\n\n";

        $context .= "--- TYPOGRAPHY ---\n";
        $context .= "Body Font: {$body_font}\n";
        $context .= "Body Font Size: " . ( is_array($body_font_size) ? $body_font_size['desktop'] : $body_font_size ) . "px\n";
        $context .= "Body Font Weight: {$body_font_weight}\n";
        $context .= "Heading Font: {$heading_font}\n";
        $context .= "H1 Font Size: " . ( is_array($h1_font_size) ? $h1_font_size['desktop'] : $h1_font_size ) . "px\n";
        $context .= "H2 Font Size: " . ( is_array($h2_font_size) ? $h2_font_size['desktop'] : $h2_font_size ) . "px\n\n";

        $context .= "--- FOOTER ---\n";
        $context .= "Footer Layout: {$footer_layout} Column\n";
        $context .= "Footer Background: " . ( $footer_bg_color ?: 'Default' ) . "\n\n";

        $context .= "--- BLOG ---\n";
        $context .= "Blog Layout: {$blog_layout}\n";
        $context .= "Blog Sidebar: {$blog_sidebar}\n\n";

        $context .= "--- WOOCOMMERCE ---\n";
        $context .= "WooCommerce Active: {$woo_active}\n";
        if ( $woo_active === 'Yes' ) {
            $context .= "Shop Sidebar: {$woo_sidebar}\n";
            $context .= "Products Per Row: {$woo_columns}\n";
        }

        return $context;
    }
}