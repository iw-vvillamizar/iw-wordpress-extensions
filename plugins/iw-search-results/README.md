# IW Search Results

## Estado

Plugin WordPress/WooCommerce en evolución activa. Versión documentada: `1.4.2`.

## Identificación

- Nombre: **IW Search Results**
- Versión: `1.4.2`
- Autor: IngeWeb.co
- Requiere WordPress: `6.0+`
- Requiere PHP: `7.4+`
- Requiere WooCommerce
- Paquete instalable: [iw-search-results-v1.4.2.zip](../../dist/iw-search-results-v1.4.2.zip)

## Función

El plugin reúne cuatro flujos:

1. Búsqueda global de WordPress.
2. Catálogo visual de productos WooCommerce.
3. Búsqueda exclusiva de productos.
4. Búsqueda exclusiva de entradas del blog.

La búsqueda de productos puede incluir opcionalmente las características guardadas en `_iw_product_features`, utilizadas por **IW Product Features**.

## Shortcodes principales

### Búsqueda global

```text
[iw_search_form]
[iw_search_results]
```

Opcionalmente puede incluir características de producto:

```text
[iw_search_results include_features="yes"]
```

### Productos

Formulario:

```text
[iw_product_search_form]
```

Catálogo/resultados:

```text
[iw_product_catalog]
```

Combinado:

```text
[iw_product_search]
```

Para incluir características:

```text
[iw_product_search include_features="yes"]
```

### Blog

Formulario:

```text
[iw_blog_search_form]
```

Resultados:

```text
[iw_blog_results]
```

Combinado:

```text
[iw_blog_search]
```

La búsqueda del blog trabaja exclusivamente con `post_type = post` y mantiene la búsqueda en la misma página.

## Categorías

### Productos

El formulario de productos permite un dropdown con selección múltiple de categorías WooCommerce.

- **Todas las categorías** queda seleccionada por defecto.
- Solo se muestran categorías con productos publicados.
- Si no existen categorías disponibles, el selector se oculta.
- La categoría mostrada en cada card del catálogo es clicable y filtra el catálogo en la misma pantalla.

### Blog

El formulario del blog también permite seleccionar una o varias categorías mediante checkboxes.

- Solo se muestran categorías con posts publicados.
- Si no existen categorías disponibles, el selector se oculta.
- En los resultados del blog, `.iw-search-card__meta` muestra la categoría real del artículo.
- La categoría es clicable y filtra `[iw_blog_results]` en la misma pantalla conservando el término de búsqueda.

## Exclusión de categorías por ID

Desde la versión 1.4.2 puede utilizarse:

```text
exclude_categories="12,18,25"
```

### Blog

```text
[iw_blog_search_form exclude_categories="12,18,25"]
[iw_blog_results exclude_categories="12,18,25"]
```

O con el shortcode combinado:

```text
[iw_blog_search exclude_categories="12,18,25" per_page="9" columns="3"]
```

Las categorías excluidas:

- no aparecen en el selector;
- no pueden seleccionarse;
- sus posts quedan fuera de los resultados;
- si se excluye una categoría padre, también se excluyen sus descendientes.

### Productos

```text
[iw_product_search_form exclude_categories="7,14"]
[iw_product_catalog exclude_categories="7,14"]
```

O combinado:

```text
[iw_product_search exclude_categories="7,14" include_features="yes" per_page="12" columns="4"]
```

La exclusión se aplica a:

- dropdown de categorías;
- filtros superiores del catálogo;
- resultados del catálogo;
- resultados del buscador exclusivo de productos;
- subcategorías descendientes de las categorías excluidas.

## Integración con Divi

Los formularios mantienen las clases principales del módulo de búsqueda de Divi:

- `et_pb_module`
- `et_pb_search`
- `et_pb_searchform`
- `et_pb_s`
- `et_pb_searchsubmit`

Esto permite reutilizar los estilos existentes del proyecto.

## Novedades recientes

### 1.4.2

- Nuevo atributo `exclude_categories` para blog y productos.
- Exclusión por uno o varios IDs separados por coma.
- Las categorías excluidas desaparecen de formularios, filtros y resultados.
- La exclusión incluye categorías descendientes.

### 1.4.1

- Los resultados del blog muestran la categoría real en `.iw-search-card__meta`.
- La categoría es clicable y filtra en la misma página.
- En búsqueda global, los posts también muestran su categoría real.

### 1.4.0

- Añadidos `[iw_blog_search_form]`, `[iw_blog_results]` y `[iw_blog_search]`.
- Búsqueda exclusiva de posts.
- Filtro múltiple por categorías del blog.

### 1.3.0

- Categoría clicable en cards de producto.
- Dropdown múltiple de categorías en el buscador de productos.
- Solo categorías con productos publicados.

## Instalación

1. Descarga [iw-search-results-v1.4.2.zip](../../dist/iw-search-results-v1.4.2.zip).
2. Instálalo desde **Plugins → Añadir nuevo → Subir plugin**.
3. Actívalo.
4. Inserta los shortcodes requeridos desde Divi o el editor de WordPress.
