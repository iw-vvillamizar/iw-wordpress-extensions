# WooCommerce WhatsApp Button Pro

## Estado

Plugin WordPress versión 1.0. Esta documentación describe el archivo recibido. Requiere revisión técnica y pruebas en un sitio de pruebas antes de utilizarse o distribuirse.

## Identificación

- Nombre declarado: WooCommerce WhatsApp Button Pro.
- Versión declarada: `1.0`.
- Autor declarado: IngeWeb.
- Archivo principal: `wc-whatsapp-button.php`.
- Código fuente: esta carpeta.
- ZIP recibido: [`dist/wc-whatsapp-button.zip`](../../dist/wc-whatsapp-button.zip).
- El ZIP contiene un único archivo PHP en la raíz, sin carpeta del plugin ni documentación adicional.

## Función observada

Agrega un botón **Comprar por WhatsApp** después del botón de añadir al carrito en páginas de producto. El administrador configura un número con código de país desde un submenú de WooCommerce. Al pulsar el botón, un script intenta abrir `wa.me` con nombre, precio, variaciones, cantidad y enlace del producto.

## Instalación y uso

1. El ZIP recibido no incluye la estructura habitual de carpeta del plugin. Prueba su instalación en un entorno aislado; si WordPress no lo instala correctamente, empaqueta el PHP en una carpeta `wc-whatsapp-button/` y vuelve a probar.
2. Verifica que WooCommerce esté instalado y activo.
3. Configura el teléfono desde el submenú **WooCommerce → WhatsApp Button**, usando el código de país sin `+` ni espacios.
4. Prueba productos simples y variables, cantidad, precio y apertura de WhatsApp en escritorio y móvil.

## Revisión y pruebas pendientes

- Confirmar las versiones compatibles de WordPress, PHP y WooCommerce; no se declaran requisitos mínimos.
- Añadir una validación explícita para cuando WooCommerce no está activo. El código llama a `is_product()` durante `wp_enqueue_scripts` sin comprobar que esa función exista.
- Validar y normalizar el teléfono en la opción registrada, por ejemplo limitando el valor a dígitos y una longitud razonable.
- Codificar correctamente el texto del mensaje y cada dato del producto para URL antes de construir el enlace `wa.me`. Los valores se concatenan actualmente en JavaScript.
- Probar que el script inline asociado a `jquery` se cargue en los temas objetivo y que la ventana no quede bloqueada por el navegador.
- Revisar selector de variaciones, textos localizados, cambios de precio, disponibilidad y restablecimiento de opciones.
- Corregir o confirmar la clase HTML duplicada `button button` y probar estilos con los temas WooCommerce soportados.
- No se encontró una declaración de licencia en el archivo ni en el ZIP; confirmar los derechos de distribución.
- No se ejecutaron pruebas de instalación, funcionales ni de sintaxis PHP en este entorno. PHP no está disponible aquí.
