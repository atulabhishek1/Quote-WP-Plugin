<?php
/**
 * Plugin Name: ChatGPT Integration
 * Description: Adds a ChatGPT-powered chat widget using OpenAI and a WordPress REST endpoint.
 * Version: 1.0.0
 * Author: GitHub Copilot
 * Text Domain: chatgpt-integration
 */

if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

const CHATGPT_INTEGRATION_OPTION = 'chatgpt_integration_options';
const CHATGPT_INTEGRATION_NONCE  = 'chatgpt_integration_nonce';

function chatgpt_integration_load_dotenv() {
    static $loaded = false;

    if ( $loaded ) {
        return;
    }

    $dotenv_file = ABSPATH . '.env';
    if ( ! file_exists( $dotenv_file ) || ! is_readable( $dotenv_file ) ) {
        return;
    }

    $lines = file( $dotenv_file, FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES );
    if ( false === $lines ) {
        return;
    }

    foreach ( $lines as $line ) {
        $line = trim( $line );
        if ( $line === '' || str_starts_with( $line, '#' ) ) {
            continue;
        }

        if ( false === strpos( $line, '=' ) ) {
            continue;
        }

        list( $name, $value ) = explode( '=', $line, 2 );
        $name  = trim( $name );
        $value = trim( $value );

        if ( strlen( $value ) >= 2 ) {
            $first = $value[0];
            $last  = substr( $value, -1 );
            if ( ( '"' === $first && '"' === $last ) || ( "'" === $first && "'" === $last ) ) {
                $value = substr( $value, 1, -1 );
            }
        }

        if ( getenv( $name ) === false ) {
            putenv( "$name=$value" );
            $_ENV[ $name ]    = $value;
            $_SERVER[ $name ] = $value;
        }
    }

    $loaded = true;
}

add_action( 'init', 'chatgpt_integration_load_dotenv' );

function chatgpt_integration_add_admin_menu() {
    add_options_page(
        __( 'ChatGPT Integration', 'chatgpt-integration' ),
        __( 'ChatGPT Integration', 'chatgpt-integration' ),
        'manage_options',
        'chatgpt-integration',
        'chatgpt_integration_options_page'
    );
}
add_action( 'admin_menu', 'chatgpt_integration_add_admin_menu' );

function chatgpt_integration_settings_init() {
    register_setting( 'chatgptIntegration', CHATGPT_INTEGRATION_OPTION, 'chatgpt_integration_sanitize_options' );

    add_settings_section(
        'chatgpt_integration_section',
        __( 'OpenAI Settings', 'chatgpt-integration' ),
        'chatgpt_integration_section_callback',
        'chatgptIntegration'
    );

    add_settings_field(
        'chatgpt_integration_api_key',
        __( 'OpenAI API Key', 'chatgpt-integration' ),
        'chatgpt_integration_api_key_render',
        'chatgptIntegration',
        'chatgpt_integration_section'
    );

    add_settings_field(
        'chatgpt_integration_model',
        __( 'Model', 'chatgpt-integration' ),
        'chatgpt_integration_model_render',
        'chatgptIntegration',
        'chatgpt_integration_section'
    );
}
add_action( 'admin_init', 'chatgpt_integration_settings_init' );

function chatgpt_integration_sanitize_options( $input ) {
    $output = array();

    if ( isset( $input['openai_api_key'] ) ) {
        $output['openai_api_key'] = sanitize_text_field( $input['openai_api_key'] );
    }

    if ( isset( $input['openai_model'] ) ) {
        $output['openai_model'] = sanitize_text_field( $input['openai_model'] );
    }

    return $output;
}

function chatgpt_integration_section_callback() {
    echo '<p>' . esc_html__( 'Enter your OpenAI API key and select a model for ChatGPT. You may also define OPENAI_API_KEY and OPENAI_MODEL in wp-config.php or as environment variables.', 'chatgpt-integration' ) . '</p>';
}

function chatgpt_integration_get_api_key() {
    chatgpt_integration_load_dotenv();

    $options = get_option( CHATGPT_INTEGRATION_OPTION );

    if ( defined( 'OPENAI_API_KEY' ) && OPENAI_API_KEY ) {
        return OPENAI_API_KEY;
    }

    $env_key = getenv( 'OPENAI_API_KEY' );
    if ( $env_key ) {
        return $env_key;
    }

    return isset( $options['openai_api_key'] ) ? $options['openai_api_key'] : '';
}

function chatgpt_integration_get_model() {
    chatgpt_integration_load_dotenv();

    if ( defined( 'OPENAI_MODEL' ) && OPENAI_MODEL ) {
        return OPENAI_MODEL;
    }

    $env_model = getenv( 'OPENAI_MODEL' );
    if ( $env_model ) {
        return $env_model;
    }

    $options = get_option( CHATGPT_INTEGRATION_OPTION );
    return isset( $options['openai_model'] ) ? $options['openai_model'] : 'gpt-3.5-turbo';
}

function chatgpt_integration_api_key_render() {
    $options = get_option( CHATGPT_INTEGRATION_OPTION );
    $api_key = isset( $options['openai_api_key'] ) ? $options['openai_api_key'] : '';
    $disabled = defined( 'OPENAI_API_KEY' ) && OPENAI_API_KEY ? 'disabled' : '';

    printf(
        '<input type="password" name="%1$s[openai_api_key]" value="%2$s" class="regular-text" autocomplete="off" %3$s />',
        esc_attr( CHATGPT_INTEGRATION_OPTION ),
        esc_attr( $api_key ),
        esc_attr( $disabled )
    );

    if ( defined( 'OPENAI_API_KEY' ) && OPENAI_API_KEY ) {
        echo '<p class="description">' . esc_html__( 'Using OPENAI_API_KEY from wp-config.php.', 'chatgpt-integration' ) . '</p>';
    }
}

function chatgpt_integration_model_render() {
    $model = chatgpt_integration_get_model();
    $models = array(
        'gpt-3.5-turbo' => 'gpt-3.5-turbo',
        'gpt-4' => 'gpt-4',
    );
    echo '<select name="' . esc_attr( CHATGPT_INTEGRATION_OPTION ) . '[openai_model]">';
    foreach ( $models as $value => $label ) {
        printf(
            '<option value="%1$s" %2$s>%3$s</option>',
            esc_attr( $value ),
            selected( $model, $value, false ),
            esc_html( $label )
        );
    }
    echo '</select>';

    if ( defined( 'OPENAI_MODEL' ) && OPENAI_MODEL ) {
        echo '<p class="description">' . esc_html__( 'Using OPENAI_MODEL from wp-config.php.', 'chatgpt-integration' ) . '</p>';
    }
}

function chatgpt_integration_options_page() {
    if ( ! current_user_can( 'manage_options' ) ) {
        return;
    }

    ?>
    <div class="wrap">
        <h1><?php esc_html_e( 'ChatGPT Integration', 'chatgpt-integration' ); ?></h1>
        <form action="options.php" method="post">
            <?php
            settings_fields( 'chatgptIntegration' );
            do_settings_sections( 'chatgptIntegration' );
            submit_button();
            ?>
        </form>
        <h2><?php esc_html_e( 'Shortcode', 'chatgpt-integration' ); ?></h2>
        <p><?php esc_html_e( 'Add the chat widget to any page or post with:', 'chatgpt-integration' ); ?></p>
        <pre>[chatgpt_chat]</pre>
    </div>
    <?php
}

function chatgpt_integration_enqueue_scripts() {
    if ( ! has_shortcode( get_post_field( 'post_content', get_the_ID() ), 'chatgpt_chat' ) ) {
        return;
    }

    wp_enqueue_style(
        'chatgpt-integration-style',
        plugin_dir_url( __FILE__ ) . 'chatgpt-integration.css',
        array(),
        '1.0.0'
    );

    wp_enqueue_script(
        'chatgpt-integration-script',
        plugin_dir_url( __FILE__ ) . 'chatgpt-integration.js',
        array( 'wp-api-fetch' ),
        '1.0.0',
        true
    );

    wp_localize_script( 'chatgpt-integration-script', 'ChatGPTIntegration', array(
        'restUrl' => esc_url_raw( rest_url( 'chatgpt/v1/ask' ) ),
        'nonce'   => wp_create_nonce( CHATGPT_INTEGRATION_NONCE ),
    ) );
}
add_action( 'wp_enqueue_scripts', 'chatgpt_integration_enqueue_scripts' );

function chatgpt_integration_enqueue_admin_assets( $hook ) {
    if ( 'settings_page_chatgpt-integration' !== $hook ) {
        return;
    }

    wp_enqueue_style(
        'chatgpt-integration-admin-style',
        plugin_dir_url( __FILE__ ) . 'chatgpt-integration.css',
        array(),
        '1.0.0'
    );
}
add_action( 'admin_enqueue_scripts', 'chatgpt_integration_enqueue_admin_assets' );

function chatgpt_integration_shortcode( $atts ) {
    $atts = shortcode_atts( array(
        'title' => __( 'Chat with ChatGPT', 'chatgpt-integration' ),
    ), $atts, 'chatgpt_chat' );

    $html  = '<div class="chatgpt-integration-widget">';
    $html .= '<div class="chatgpt-integration-header">' . esc_html( $atts['title'] ) . '</div>';
    $html .= '<div class="chatgpt-integration-messages" id="chatgpt-messages"></div>';
    $html .= '<div class="chatgpt-integration-form">';
    $html .= '<textarea id="chatgpt-input" placeholder="' . esc_attr__( 'Ask a question...', 'chatgpt-integration' ) . '"></textarea>';
    $html .= '<button type="button" id="chatgpt-submit">' . esc_html__( 'Send', 'chatgpt-integration' ) . '</button>';
    $html .= '</div>';
    $html .= '</div>';

    return $html;
}
add_shortcode( 'chatgpt_chat', 'chatgpt_integration_shortcode' );

function chatgpt_integration_register_route() {
    register_rest_route( 'chatgpt/v1', '/ask', array(
        'methods'             => 'POST',
        'callback'            => 'chatgpt_integration_handle_request',
        'permission_callback' => '__return_true',
    ) );
}
add_action( 'rest_api_init', 'chatgpt_integration_register_route' );

function chatgpt_integration_handle_request( WP_REST_Request $request ) {
    $nonce = $request->get_header( 'x-wp-nonce' );
    if ( ! wp_verify_nonce( $nonce, CHATGPT_INTEGRATION_NONCE ) ) {
        return new WP_REST_Response( array( 'error' => __( 'Invalid request.', 'chatgpt-integration' ) ), 403 );
    }

    $body = json_decode( wp_json_encode( $request->get_json_params() ), true );
    $message = isset( $body['message'] ) ? sanitize_textarea_field( wp_unslash( $body['message'] ) ) : '';

    if ( empty( $message ) ) {
        return new WP_REST_Response( array( 'error' => __( 'Please provide a message.', 'chatgpt-integration' ) ), 400 );
    }

    $api_key = chatgpt_integration_get_api_key();
    $model   = chatgpt_integration_get_model();

    if ( empty( $api_key ) ) {
        return new WP_REST_Response( array( 'error' => __( 'OpenAI API key is not configured. Set OPENAI_API_KEY in wp-config.php, environment variables, or plugin settings.', 'chatgpt-integration' ) ), 500 );
    }

    $response = wp_remote_post(
        'https://api.openai.com/v1/chat/completions',
        array(
            'headers' => array(
                'Content-Type'  => 'application/json',
                'Authorization' => 'Bearer ' . $api_key,
            ),
            'body'    => wp_json_encode( array(
                'model'    => $model,
                'messages' => array(
                    array(
                        'role'    => 'user',
                        'content' => $message,
                    ),
                ),
                'temperature' => 0.7,
                'max_tokens'  => 600,
            ) ),
            'timeout' => 30,
        )
    );

    if ( is_wp_error( $response ) ) {
        return new WP_REST_Response( array( 'error' => $response->get_error_message() ), 500 );
    }

    $code = wp_remote_retrieve_response_code( $response );
    $body = wp_remote_retrieve_body( $response );
    $data = json_decode( $body, true );

    if ( 200 !== $code || empty( $data['choices'][0]['message']['content'] ) ) {
        $error_message = isset( $data['error']['message'] ) ? $data['error']['message'] : __( 'Unable to fetch ChatGPT response.', 'chatgpt-integration' );
        return new WP_REST_Response( array( 'error' => $error_message ), $code );
    }

    $reply = sanitize_textarea_field( wp_unslash( $data['choices'][0]['message']['content'] ) );

    return new WP_REST_Response( array( 'reply' => $reply ), 200 );
}

function chatgpt_integration_plugin_action_links( $actions, $plugin_file, $plugin_data, $context ) {
    if ( plugin_basename( __FILE__ ) === $plugin_file ) {
        $settings_link = '<a href="options-general.php?page=chatgpt-integration">' . esc_html__( 'Settings', 'chatgpt-integration' ) . '</a>';
        array_unshift( $actions, $settings_link );
    }
    return $actions;
}
add_filter( 'plugin_action_links', 'chatgpt_integration_plugin_action_links', 10, 4 );
