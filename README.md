# Sincronización de Productos WooCommerce

Sincronización de Productos WooCommerce es un plugin para WordPress que permite sincronizar productos entre dos sitios WooCommerce. Cada vez que se crea o actualiza un producto en el sitio principal, el plugin se encarga de replicar la información en un sitio remoto a través de la API REST de WooCommerce.

## Características

- **Sincronización automática:** Cada vez que se crea o actualiza un producto (post type `product`), se sincroniza en el sitio remoto.
- **Identificación por SKU:** Verifica si el producto existe en el sitio remoto utilizando el SKU y, según el caso, actualiza o crea el producto.
- **Configuración sencilla:** Panel de administración para ingresar la URL del sitio remoto, el Consumer Key y el Consumer Secret de la API REST.
- **Código modular y extensible:** La funcionalidad se encuentra separada en diferentes archivos y clases, lo que facilita el mantenimiento y la extensión del plugin.

## Requisitos

- **WordPress:** 5.0 o superior.
- **WooCommerce:** 3.0 o superior.
- **PHP:** 7.2 o superior.
- Acceso a la API REST de WooCommerce en ambos sitios (principal y remoto).

## Instalación

1. **Descargar el plugin:**
   - Descarga el archivo ZIP o clona el repositorio.
2. **Instalación manual:**
   - Copia la carpeta `wc-sync` en el directorio `/wp-content/plugins/` de tu instalación de WordPress.
3. **Activar el plugin:**
   - Accede al panel de administración de WordPress, ve a la sección de *Plugins* y activa **Sincronización de Productos WooCommerce**.
4. **Configurar el plugin:**
   - Dirígete a **Ajustes → Sincronización WooCommerce** para ingresar la URL del sitio remoto, el Consumer Key y el Consumer Secret.

## Configuración

1. **Acceder a la página de configuración:**
   - Una vez activado el plugin, ve a **Ajustes → Sincronización WooCommerce** en el área de administración.
2. **Ingresar las credenciales de la API:**
   - **URL del Sitio Remoto:** Ingresa la URL base del sitio WooCommerce remoto (por ejemplo, `https://tusitioremoto.com`).
   - **Consumer Key:** Ingresa el Consumer Key de la API REST del sitio remoto.
   - **Consumer Secret:** Ingresa el Consumer Secret de la API REST del sitio remoto.
3. **Guardar cambios:**
   - Haz clic en el botón "Guardar cambios" para almacenar la configuración.

## Uso

- **Sincronización en tiempo real:**  
  Cada vez que se crea o actualiza un producto en el sitio principal, el plugin se activa automáticamente y sincroniza los datos (nombre, precio, descripción, SKU, etc.) en el sitio remoto.
  
- **Actualización o creación de producto en el sitio remoto:**  
  Si el producto ya existe en el sitio remoto (identificado por su SKU), se actualiza; en caso contrario, se crea un nuevo producto en el sitio remoto.

## Estructura del Plugin

El plugin está organizado en la siguiente estructura de carpetas y archivos:


- **wc-sync.php:** Archivo de entrada que define constantes, carga los archivos necesarios y registra las clases.
- **admin/class-wc-sync-settings.php:** Contiene la clase que maneja la creación y renderización de la página de configuración.
- **includes/class-wc-sync-manager.php:** Contiene la lógica para detectar la creación o actualización de productos y realizar la sincronización mediante la API REST.

## Contribuir

¡Las contribuciones son bienvenidas! Si deseas colaborar en el desarrollo del plugin, sigue estos pasos:

1. Haz un *fork* del repositorio.
2. Crea una nueva rama para tus cambios:
   ```bash
   git checkout -b feature/mi-nueva-funcionalidad
