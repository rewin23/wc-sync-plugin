<?php
/*
Plugin Name: Sincronización de Productos WooCommerce
Description: Sincroniza productos entre dos sitios WooCommerce mediante la API REST. Incluye una vista de configuración para ingresar las credenciales de la API.
Version: 1.0
Author: Erwin Navarrete
License: GPL2
Text Domain: wc-sync
*/

if ( ! defined( 'ABSPATH' ) ) {
    exit; 
}

// Definir constantes del plugin.
define( 'WC_SYNC_PLUGIN_DIR', plugin_dir_path( __FILE__ ) );
define( 'WC_SYNC_PLUGIN_URL', plugin_dir_url( __FILE__ ) );

// Incluir archivos de funcionalidad.
require_once WC_SYNC_PLUGIN_DIR . 'includes/class-wc-sync-manager.php';
require_once WC_SYNC_PLUGIN_DIR . 'admin/class-wc-sync-settings.php';

// Inicializar el plugin.
function wc_sync_init() {
    new WC_Sync_Manager();

    // Solo cargamos el panel de configuración en el área de administración.
    if ( is_admin() ) {
        new WC_Sync_Settings();
    }
}
add_action( 'plugins_loaded', 'wc_sync_init' );
