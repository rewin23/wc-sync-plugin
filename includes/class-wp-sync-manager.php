<?php
if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

class WC_Sync_Manager {

    public function __construct() {
        // Hook para sincronizar el producto cuando se guarda o actualiza.
        add_action( 'save_post_product', array( $this, 'sync_product' ), 10, 3 );
    }

    /**
     * Sincroniza el producto con el sitio remoto.
     *
     * @param int     $post_ID ID del producto.
     * @param WP_Post $post    Objeto del producto.
     * @param bool    $update  Indica si es actualización (true) o creación (false).
     */
    public function sync_product( $post_ID, $post, $update ) {
        // Evitar bucles de sincronización.
        if ( defined( 'WC_SYNC_RUNNING' ) && WC_SYNC_RUNNING ) {
            return;
        }

        // Obtener el producto de WooCommerce.
        $product = wc_get_product( $post_ID );
        if ( ! $product ) {
            return;
        }

        // Obtener opciones de configuración.
        $options = get_option( 'wc_sync_options', array() );
        $remote_site_url       = ! empty( $options['remote_site_url'] ) ? esc_url_raw( $options['remote_site_url'] ) : '';
        $remote_consumer_key   = ! empty( $options['remote_consumer_key'] ) ? sanitize_text_field( $options['remote_consumer_key'] ) : '';
        $remote_consumer_secret= ! empty( $options['remote_consumer_secret'] ) ? sanitize_text_field( $options['remote_consumer_secret'] ) : '';

        // Si no se han configurado las credenciales, salir.
        if ( empty( $remote_site_url ) || empty( $remote_consumer_key ) || empty( $remote_consumer_secret ) ) {
            return;
        }

        // Preparar los datos a sincronizar (ajusta según tus necesidades).
        $data = array(
            'name'              => $product->get_name(),
            'regular_price'     => $product->get_regular_price(),
            'sale_price'        => $product->get_sale_price(),
            'description'       => $product->get_description(),
            'short_description' => $product->get_short_description(),
            'sku'               => $product->get_sku(),
            // Puedes agregar más campos si lo requieres.
        );

        // Verificar si el producto ya existe en el sitio remoto (por ejemplo, usando el SKU).
        $remote_product_id = $this->get_remote_product_id( $product->get_sku(), $remote_site_url, $remote_consumer_key, $remote_consumer_secret );

        if ( $remote_product_id ) {
            // Actualizar producto existente.
            $endpoint = trailingslashit( $remote_site_url ) . 'wp-json/wc/v3/products/' . $remote_product_id;
            $method   = 'PUT';
        } else {
            // Crear nuevo producto.
            $endpoint = trailingslashit( $remote_site_url ) . 'wp-json/wc/v3/products';
            $method   = 'POST';
        }

        // Agregar parámetros de autenticación a la URL.
        $auth_params = array(
            'consumer_key'    => $remote_consumer_key,
            'consumer_secret' => $remote_consumer_secret,
        );
        $endpoint = add_query_arg( $auth_params, $endpoint );

        $args = array(
            'method'  => $method,
            'body'    => json_encode( $data ),
            'headers' => array(
                'Content-Type' => 'application/json',
            ),
            'timeout' => 60,
        );

        // Definir constante para evitar recursividad.
        if ( ! defined( 'WC_SYNC_RUNNING' ) ) {
            define( 'WC_SYNC_RUNNING', true );
        }

        $response = wp_remote_request( $endpoint, $args );

        if ( is_wp_error( $response ) ) {
            error_log( sprintf( 'Error al sincronizar producto ID %d: %s', $post_ID, $response->get_error_message() ) );
        } else {
            error_log( sprintf( 'Producto ID %d sincronizado correctamente.', $post_ID ) );
        }
    }

    /**
     * Obtiene el ID del producto remoto usando el SKU.
     *
     * @param string $sku SKU del producto.
     * @param string $remote_site_url URL del sitio remoto.
     * @param string $remote_consumer_key Consumer Key.
     * @param string $remote_consumer_secret Consumer Secret.
     * @return mixed ID del producto remoto o false si no existe.
     */
    private function get_remote_product_id( $sku, $remote_site_url, $remote_consumer_key, $remote_consumer_secret ) {
        if ( empty( $sku ) ) {
            return false;
        }

        $auth_params = array(
            'consumer_key'    => $remote_consumer_key,
            'consumer_secret' => $remote_consumer_secret,
            'sku'             => $sku,
        );

        $endpoint = add_query_arg( $auth_params, trailingslashit( $remote_site_url ) . 'wp-json/wc/v3/products' );
        $response = wp_remote_get( $endpoint, array( 'timeout' => 60 ) );

        if ( is_wp_error( $response ) ) {
            error_log( 'Error al obtener producto remoto por SKU: ' . $response->get_error_message() );
            return false;
        }

        $body     = wp_remote_retrieve_body( $response );
        $products = json_decode( $body, true );
        if ( is_array( $products ) && ! empty( $products ) ) {
            // Asumimos que el primer producto es el correcto.
            return $products[0]['id'];
        }

        return false;
    }
}
