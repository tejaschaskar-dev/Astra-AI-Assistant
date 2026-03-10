<?php
if ( ! defined( 'ABSPATH' ) ) exit;

class AAA_Settings {

    public function __construct() {
        add_action( 'admin_menu', array( $this, 'add_settings_page' ) );
        add_action( 'admin_post_aaa_save_settings', array( $this, 'save_settings' ) );
    }

    public function add_settings_page() {
        add_menu_page( 'Astra AI Assistant', 'Astra AI', 'manage_options', 'astra-ai-assistant', array( $this, 'render_settings_page' ), 'dashicons-format-chat', 80 );
        add_submenu_page( 'astra-ai-assistant', 'Settings', 'Settings', 'manage_options', 'astra-ai-assistant', array( $this, 'render_settings_page' ) );
    }

    public function save_settings() {
        if ( ! current_user_can( 'manage_options' ) ) return;
        if ( ! isset( $_POST['aaa_nonce'] ) || ! wp_verify_nonce( $_POST['aaa_nonce'], 'aaa_save_settings' ) ) return;
        $api_key = sanitize_text_field( $_POST['aaa_api_key'] );
        update_option( 'aaa_api_key', $api_key );
        wp_redirect( admin_url( 'admin.php?page=astra-ai-assistant&saved=1' ) );
        exit;
    }

    public function render_settings_page() {
        $api_key = get_option( 'aaa_api_key', '' );
        echo '<div class=wrap>';
        echo '<h1>Astra AI Assistant - Settings</h1>';
        if ( isset( $_GET['saved'] ) ) { echo '<div class=notice notice-success><p>API Key Saved!</p></div>'; }
        echo '<form method=post action=' . admin_url( 'admin-post.php' ) . '>';
        echo '<input type=hidden name=action value=aaa_save_settings />';
        wp_nonce_field( 'aaa_save_settings', 'aaa_nonce' );
        echo '<table class=form-table><tr><th>OpenRouter API Key</th><td>';
        echo '<input type=text name=aaa_api_key value=' . esc_attr( $api_key ) . ' style=width:400px placeholder=sk-or-xxxxxxxxxxxxxxxx />';
        echo '<p style=color:gray>Get your free API key from openrouter.ai</p>';
        echo '</td></tr></table>';
        echo '<input type=submit value=Save class=button button-primary />';
        echo '</form>';
        if ( $api_key ) { echo '<div style=background:#d4edda;padding:12px>Key saved! <a href=' . admin_url('admin.php?page=astra-ai-chat') . '>Go to Chat</a></div>'; }
        echo '</div>';
    }
}