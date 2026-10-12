# Indicadores Económicos de Colombia

## Estado

Plugin WordPress versión 1.0.1. Esta documentación describe el archivo recibido. Requiere revisión técnica y pruebas en un sitio de pruebas antes de utilizarse o distribuirse.

## Identificación

- Nombre declarado: Indicadores Económicos de Colombia.
- Versión declarada: `1.0.1`.
- Autor declarado: IngeWeb.
- Archivo principal: `indicadores-economicos-colombia.php`.
- Código fuente: esta carpeta.
- ZIP recibido: [`dist/indicadores-economicos-colombia-v1.0.1.zip`](../../dist/indicadores-economicos-colombia-v1.0.1.zip).
- El ZIP contiene un directorio con un único archivo PHP.

## Función observada

Registra el shortcode `[indicadores_colombia]` y muestra cuatro tarjetas: TRM, inflación mensual, tasa de intervención del Banco de la República y UVR. El atributo `cache="false"` omite la lectura del transient existente; las respuestas recuperadas se guardan durante seis horas.

Consulta los endpoints JSON de Datos.gov.co para TRM y UVR y usa como fuente de respaldo una extracción mediante expresiones regulares del texto de La República. El HTML y los valores se convierten y escapan antes de mostrarse en las tarjetas.

## Instalación y uso

1. Instala el ZIP desde **Plugins → Añadir nuevo → Subir plugin**.
2. Actívalo y agrega `[indicadores_colombia]` a una página o entrada.
3. Para solicitar datos sin leer la caché previa, prueba `[indicadores_colombia cache="false"]`.

Confirma el procedimiento de instalación con una versión actual de WordPress; el paquete contiene solo el plugin y no incluye guía de instalación ni información sobre requisitos.

## Revisión y pruebas pendientes

- Confirmar las versiones compatibles de WordPress y PHP; el archivo no declara requisitos mínimos.
- Validar en vivo el esquema, orden, disponibilidad y límites de las APIs de Datos.gov.co.
- Verificar las expresiones regulares contra la página real de La República. La extracción depende del texto, las etiquetas y formatos publicados por terceros y puede dejar de funcionar si cambian.
- Revisar cómo se cachean resultados parciales o fallidos y cómo se recuperan cuando una fuente externa no responde.
- Probar formatos numéricos, variaciones de fecha, datos vacíos, errores HTTP, bloqueos y tiempos de espera en diferentes configuraciones del servidor.
- Revisar presentación móvil, accesibilidad y compatibilidad con temas, constructores de páginas y caché del sitio.
- No se encontró una declaración de licencia en el archivo ni en el ZIP; confirmar los derechos de distribución.
- No se ejecutaron pruebas de instalación, funcionales ni de sintaxis PHP en este entorno. PHP no está disponible aquí.
