# IW Search Results

## Estado

Plugin WordPress/WooCommerce en evolución activa. Versión documentada: `1.3.0`.

## Identificación

- Nombre: **IW Search Results**
- Versión: `1.3.0`
- Autor: IngeWeb.co
- Requiere WordPress: `6.0+`
- Requiere PHP: `7.4+`
- Requiere WooCommerce
- Paquete instalable: [iw-search-results-v1.3.0.zip](../../dist/iw-search-results-v1.3.0.zip)

## Función

Unifica tres necesidades:

1. Resultados de búsqueda global separados entre productos y contenido.
2. Catálogo visual de WooCommerce con filtros por categoría y paginación.
3. Buscador exclusivo de productos que puede permanecer en la misma URL donde se inserta.

El buscador de productos puede incluir opcionalmente las características guardadas en `_iw_product_features`, utilizadas por **IW Product Features**.

## Shortcodes

### Búsqueda global

```text
[iw_search_results]
[iw_search_form]
```

Para incluir características de producto:

```text
[iw_search_results include_features="yes"]
```

### Catálogo

```text
[iw_product_catalog]
```

Incluye filtros por categoría, paginación y soporte para búsqueda exclusiva de productos. La categoría mostrada sobre el título de cada producto es clicable y abre el catálogo filtrado por esa categoría.

### Buscador exclusivo de productos

Solo título y SKU:

```text
[iw_product_search_form]
```

Título, SKU y características:

```text
[iw_product_search_form include_features="yes"]
```

Para mostrar resultados en la misma página:

```text
[iw_product_search_form include_features="yes"]
[iw_product_catalog]
```

También existe el shortcode combinado:

```text
[iw_product_search include_features="yes" per_page="12" columns="4" categories="yes"]
```

## Filtro por categorías en la búsqueda de productos

Desde la versión 1.3.0, `[iw_product_search_form]` incluye un selector desplegable de categorías WooCommerce:

- **Todas las categorías** queda seleccionada por defecto.
- El usuario puede seleccionar una o varias categorías mediante checkboxes.
- Solo se listan categorías con productos publicados.
- Si no hay categorías con productos, el selector no se muestra.
- La búsqueda combina término + categorías seleccionadas + SKU + características opcionales.

Para ocultar el selector:

```text
[iw_product_search_form category_selector="no"]
```

## Integración con Divi

Los formularios conservan las clases principales del módulo de búsqueda de Divi:

- `et_pb_module`
- `et_pb_search`
- `et_pb_searchform`
- `et_pb_s`
- `et_pb_searchsubmit`

El formulario global conserva el comportamiento estándar de WordPress/Divi; el formulario exclusivo de productos utiliza `iw_product_search` y mantiene la búsqueda en la misma página.

## Diseño

Las tarjetas de búsqueda y catálogo comparten la misma estructura visual. El CSS consume las variables globales del proyecto (`--color-primary`, `--color-accent`, `--font-heading`, `--border-radius-card`, etc.) para integrarse con la identidad visual existente.

## Novedades 1.3.0

- Categoría clicable en las cards del catálogo.
- Dropdown de categorías en `[iw_product_search_form]`.
- Selección múltiple de categorías mediante checkboxes.
- Solo categorías con productos publicados.
- Ocultación automática del selector si no hay categorías disponibles.
- Filtrado combinado por término, categorías y características opcionales.
- El shortcode `[iw_product_search]` hereda el mismo selector.

## Instalación

1. Descarga [iw-search-results-v1.3.0.zip](../../dist/iw-search-results-v1.3.0.zip).
2. Instálalo desde **Plugins → Añadir nuevo → Subir plugin**.
3. Actívalo.
4. Inserta los shortcodes requeridos desde Divi o el editor de WordPress.
