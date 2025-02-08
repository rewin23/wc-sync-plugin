<?php
if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

class WC_Sync_Settings {

    /**
     * Nombre de la opción en la base de datos.
     *
     * @var string
     */
    private $option_name = 'wc_sync_options';

    public function __construct() {
        add_action( 'admin_menu', array( $this, 'add_settings_page' ) );
        add_action( 'admin_init', array( $this, 'register_settings' ) );
    }

    /**
     * Agrega la página de configuración al menú de Ajustes.
     */
    public function add_settings_page() {
        add_options_page(
            __( 'Sincronización WooCommerce', 'wc-sync' ),
            __( 'Sincronización WooCommerce', 'wc-sync' ),
            'manage_options',
            'wc-sync-settings',
            array( $this, 'render_settings_page' )
        );
    }

    /**
     * Registra las opciones y campos de configuración.
     */
    public function register_settings() {
        register_setting( $this->option_name, $this->option_name, array( $this, 'sanitize_options' ) );

        add_settings_section(
            'wc_sync_main_section',
            __( 'Configuración de Sincronización', 'wc-sync' ),
            null,
            'wc-sync-settings'
        );

        add_settings_field(
            'remote_site_url',
            __( 'URL del Sitio Remoto', 'wc-sync' ),
            array( $this, 'remote_site_url_callback' ),
            'wc-sync-settings',
            'wc_sync_main_section'
        );

        add_settings_field(
            'remote_consumer_key',
            __( 'Consumer Key', 'wc-sync' ),
            array( $this, 'remote_consumer_key_callback' ),
            'wc-sync-settings',
            'wc_sync_main_section'
        );

        add_settings_field(
            'remote_consumer_secret',
            __( 'Consumer Secret', 'wc-sync' ),
            array( $this, 'remote_consumer_secret_callback' ),
            'wc-sync-settings',
            'wc_sync_main_section'
        );
    }

    /**
     * Sanitiza las opciones antes de guardarlas.
     *
     * @param array $options
     * @return array
     */
    public function sanitize_options( $options ) {
        $sanitized = array();
        $sanitized['remote_site_url'] = isset( $options['remote_site_url'] ) ? esc_url_raw( $options['remote_site_url'] ) : '';
        $sanitized['remote_consumer_key'] = isset( $options['remote_consumer_key'] ) ? sanitize_text_field( $options['remote_consumer_key'] ) : '';
        $sanitized['remote_consumer_secret'] = isset( $options['remote_consumer_secret'] ) ? sanitize_text_field( $options['remote_consumer_secret'] ) : '';
        return $sanitized;
    }

    /**
     * Renderiza el campo para la URL del sitio remoto.
     */
    public function remote_site_url_callback() {
        $options = get_option( $this->option_name );
        ?>
        <input type="url" name="<?php echo esc_attr( $this->option_name ); ?>[remote_site_url]" value="<?php echo isset( $options['remote_site_url'] ) ? esc_attr( $options['remote_site_url'] ) : ''; ?>" class="regular-text" placeholder="https://tusitioremoto.com">
        <?php
    }

    /**
     * Renderiza el campo para el Consumer Key.
     */
    public function remote_consumer_key_callback() {
        $options = get_option( $this->option_name );
        ?>
        <input type="text" name="<?php echo esc_attr( $this->option_name ); ?>[remote_consumer_key]" value="<?php echo isset( $options['remote_consumer_key'] ) ? esc_attr( $options['remote_consumer_key'] ) : ''; ?>" class="regular-text">
        <?php
    }

    /**
     * Renderiza el campo para el Consumer Secret.
     */
    public function remote_consumer_secret_callback() {
        $options = get_option( $this->option_name );
        ?>
        <input type="text" name="<?php echo esc_attr( $this->option_name ); ?>[remote_consumer_secret]" value="<?php echo isset( $options['remote_consumer_secret'] ) ? esc_attr( $options['remote_consumer_secret'] ) : ''; ?>" class="regular-text">
        <?php
    }

    /**
     * Renderiza la página de configuración.
     */
    public function render_settings_page() {
        ?>
        <div class="wrap">
            <h1><?php _e( 'Configuración de Sincronización WooCommerce', 'wc-sync' ); ?></h1>
            <form method="post" action="options.php">
                <?php
                settings_fields( $this->option_name );
                do_settings_sections( 'wc-sync-settings' );
                submit_button();
                ?>
            </form>
        </div>
        <?php
    }
}
