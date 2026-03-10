<?php
if ( ! defined( 'ABSPATH' ) ) exit;

class AAA_Admin_Page {

    public function __construct() {
        add_action( 'admin_menu',            array( $this, 'add_chat_page' ) );
        add_action( 'admin_enqueue_scripts', array( $this, 'load_assets' ) );
        add_action( 'wp_ajax_aaa_chat',      array( $this, 'handle_chat' ) );
    }

    public function add_chat_page() {
        add_submenu_page(
            'astra-ai-assistant',
            'AI Chat',
            'Chat',
            'manage_options',
            'astra-ai-chat',
            array( $this, 'render_chat_page' )
        );
    }

    public function load_assets( $hook ) {
        if ( $hook !== 'astra-ai_page_astra-ai-chat' ) return;
        wp_enqueue_style(  'aaa-chat', AAA_URL . 'assets/css/chat.css', array(), '2.0' );
        wp_enqueue_script( 'aaa-chat', AAA_URL . 'assets/js/chat.js',  array(), '2.0', true );
        wp_localize_script( 'aaa-chat', 'AAA', array(
            'ajax_url' => admin_url( 'admin-ajax.php' ),
            'nonce'    => wp_create_nonce( 'aaa_nonce' ),
        ));
    }

    public function render_chat_page() {
        ?>
        <div class="wrap" style="font-family:'Inter',sans-serif; padding:20px;">
            <div id="aaa-wrap">

                <!-- Header -->
                <div id="aaa-header">
                    <div id="aaa-avatar">🤖</div>
                    <div id="aaa-header-text">
                        <h2>Astra AI Assistant</h2>
                        
                    </div>
                    <div id="aaa-status">
                        <div class="status-dot"></div>&nbsp;Online
                    </div>
                </div>

                <!-- Action Buttons -->
                <div id="aaa-actions">
                    <button id="aaa-clear">🗑️ Clear Chat</button>
                    <button id="aaa-export">📥 Export Chat</button>
                </div>

                <!-- Chat Box -->
                <div id="aaa-chat-box">
                    <div id="aaa-messages">
                        <div class="aaa-time">Today</div>
                        <div class="aaa-msg-row bot">
                            <div class="aaa-msg-icon bot">🤖</div>
                            <div class="aaa-msg bot">
                                👋 Hi! I am your <strong>Astra AI Assistant</strong>.
                                Ask me anything about your Astra Theme!
                            </div>
                        </div>
                    </div>

                    <!-- Quick Buttons -->
                    <div id="aaa-quick">
                        <button class="aaa-quick-btn">Change header color</button>
                        <button class="aaa-quick-btn">Full width layout</button>
                        <button class="aaa-quick-btn">Disable sidebar</button>
                        <button class="aaa-quick-btn">Change fonts</button>
                    </div>

                    <!-- Input -->
                    <div id="aaa-input-area">
                        <input type="text" id="aaa-input" placeholder="Ask about Astra Theme..." />
                        <button id="aaa-send">
                            <svg viewBox="0 0 24 24">
                                <path d="M2.01 21L23 12 2.01 3 2 10l15 2-15 2z"/>
                            </svg>
                        </button>
                    </div>
                </div>

            </div>
        </div>
        <?php
    }

    public function handle_chat() {
        check_ajax_referer( 'aaa_nonce', 'nonce' );
        $user_message = sanitize_text_field( $_POST['message'] ?? '' );
        if ( empty( $user_message ) ) wp_send_json_error( 'Empty message' );
        $context  = AAA_Context::get_astra_context();
        $response = AAA_AI_Connector::ask( $user_message, $context );
        wp_send_json_success( array( 'reply' => $response ) );
    }
}
