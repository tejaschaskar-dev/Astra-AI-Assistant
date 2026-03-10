<?php
if ( ! defined( "ABSPATH" ) ) exit;

class AAA_Context {
    public static function get_astra_context() {
        if ( ! function_exists( "astra_get_option" ) ) {
            return "Astra Theme is not active on this site.";
        }
        $settings = [
            "Header Layout"      => astra_get_option( "header-layouts", "Default" ),
            "Primary Color"      => astra_get_option( "theme-color", "#0066cc" ),
            "Body Font"          => astra_get_option( "body-font-family", "Default" ),
            "Body Font Size"     => astra_get_option( "font-size-body", "15" ) . "px",
            "Container Layout"   => astra_get_option( "site-layout", "boxed" ),
            "Sidebar Position"   => astra_get_option( "site-sidebar-layout", "right-sidebar" ),
            "Sticky Header"      => astra_get_option( "sticky-header", false ) ? "Enabled" : "Disabled",
            "Transparent Header" => astra_get_option( "transparent-header", false ) ? "Enabled" : "Disabled",
        ];
        $context = "Current Astra Theme settings:\n\n";
        foreach ( $settings as $key => $value ) {
            $context .= "- {$key}: {$value}\n";
        }
        return $context;
    }
}
