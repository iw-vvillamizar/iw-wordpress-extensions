<?php
/**
 * Plugin Name: Indicadores Económicos de Colombia
 * Description: Muestra TRM, inflación mensual, tasa de intervención del Banco de la República y UVR mediante el shortcode [indicadores_colombia].
 * Version: 1.0.1
 * Author: IngeWeb
 * Text Domain: indicadores-economicos-colombia
 */

if (!defined('ABSPATH')) {
    exit;
}

class Indicadores_Economicos_Colombia {
    const TRANSIENT_KEY = 'iec_indicadores_cache_v2';
    const CACHE_TIME = 6 * HOUR_IN_SECONDS;

    public function __construct() {
        add_shortcode('indicadores_colombia', [$this, 'render_shortcode']);
        add_action('wp_enqueue_scripts', [$this, 'register_styles']);
    }

    public function register_styles() {
        wp_register_style('iec-styles', false, [], '1.0.1');
        wp_add_inline_style('iec-styles', $this->get_css());
    }

    public function render_shortcode($atts = []) {
        wp_enqueue_style('iec-styles');

        $atts = shortcode_atts([
            'cache' => 'true',
        ], $atts, 'indicadores_colombia');

        $use_cache = $atts['cache'] !== 'false';
        $data = $this->get_indicators($use_cache);

        if (empty($data)) {
            return '<div class="iec-box iec-error">No fue posible consultar los indicadores económicos en este momento.</div>';
        }

        ob_start();
        ?>
        <div class="iec-wrapper" aria-label="Indicadores económicos de Colombia">
            <?php echo $this->render_card('Dólar TRM', $data['dolar'] ?? null, '$'); ?>
            <?php echo $this->render_card('Inflación mensual', $data['inflacion'] ?? null, '', '%'); ?>
            <?php echo $this->render_card('Tasa BanRep', $data['tasa_republica'] ?? null, '', '%'); ?>
            <?php echo $this->render_card('UVR', $data['uvr'] ?? null, '$'); ?>
        </div>
        <?php
        return ob_get_clean();
    }

    private function render_card($title, $item, $prefix = '', $suffix = '') {
        if (empty($item) || !isset($item['valor']) || $item['valor'] === null || $item['valor'] === '') {
            return sprintf(
                '<div class="iec-card"><span class="iec-title">%s</span><strong class="iec-value">No disponible</strong></div>',
                esc_html($title)
            );
        }

        $estado = $item['estado'] ?? 'igual';
        $estado_class = sanitize_html_class($estado);
        $estado_text = $this->estado_text($estado);
        $fecha = !empty($item['fecha']) ? '<small class="iec-date">' . esc_html($item['fecha']) . '</small>' : '';

        return sprintf(
            '<div class="iec-card iec-%s"><span class="iec-title">%s</span><strong class="iec-value">%s%s%s</strong><span class="iec-trend">%s</span>%s</div>',
            esc_attr($estado_class),
            esc_html($title),
            esc_html($prefix),
            esc_html($this->format_number($item['valor'])),
            esc_html($suffix),
            esc_html($estado_text),
            $fecha
        );
    }

    private function get_indicators($use_cache = true) {
        if ($use_cache) {
            $cached = get_transient(self::TRANSIENT_KEY);
            if ($cached !== false) {
                return $cached;
            }
        }

        // La República se usa como respaldo porque publica TRM, UVR, IPC mensual y tasa en una sola página.
        // Datos.gov.co se conserva para TRM por ser una fuente más estructurada.
        $lr = $this->get_larepublica_indicators();
        $trm = $this->get_trm();
        $uvr = $this->get_uvr();

        $data = [
            'dolar' => $trm ?: ($lr['dolar'] ?? null),
            'inflacion' => $lr['inflacion'] ?? null,
            'tasa_republica' => $lr['tasa_republica'] ?? null,
            'uvr' => $uvr ?: ($lr['uvr'] ?? null),
        ];

        set_transient(self::TRANSIENT_KEY, $data, self::CACHE_TIME);
        return $data;
    }

    private function get_trm() {
        $url = 'https://www.datos.gov.co/resource/32sa-8pi3.json?$limit=2&$order=vigenciadesde%20DESC';
        $rows = $this->remote_json($url);

        if (empty($rows[0]['valor'])) {
            return null;
        }

        $actual = $this->to_number($rows[0]['valor']);
        $anterior = isset($rows[1]['valor']) ? $this->to_number($rows[1]['valor']) : $actual;

        return [
            'valor' => $actual,
            'anterior' => $anterior,
            'estado' => $this->trend($actual, $anterior),
            'fecha' => isset($rows[0]['vigenciadesde']) ? substr($rows[0]['vigenciadesde'], 0, 10) : '',
        ];
    }

    private function get_uvr() {
        // En algunos servidores datos.gov.co responde 403 para este recurso. Si falla, se usa La República como respaldo.
        $url = 'https://www.datos.gov.co/resource/mtic-nvgq.json?$limit=2&$order=fecha%20DESC';
        $rows = $this->remote_json($url);

        if (empty($rows[0])) {
            return null;
        }

        $valor_key = $this->first_existing_key($rows[0], ['valor', 'unidad_de_valor_real_uvr', 'uvr']);
        $fecha_key = $this->first_existing_key($rows[0], ['fecha', 'vigencia']);

        if (!$valor_key || empty($rows[0][$valor_key])) {
            return null;
        }

        $actual = $this->to_number($rows[0][$valor_key]);
        $anterior = isset($rows[1][$valor_key]) ? $this->to_number($rows[1][$valor_key]) : $actual;

        return [
            'valor' => $actual,
            'anterior' => $anterior,
            'estado' => $this->trend($actual, $anterior),
            'fecha' => $fecha_key && !empty($rows[0][$fecha_key]) ? substr($rows[0][$fecha_key], 0, 10) : '',
        ];
    }

    private function get_larepublica_indicators() {
        $html = $this->remote_text('https://www.larepublica.co/indicadores-economicos');
        if (!$html) {
            return [];
        }

        $text = html_entity_decode(wp_strip_all_tags($html), ENT_QUOTES, 'UTF-8');
        $text = preg_replace('/\s+/u', ' ', $text);

        return [
            'dolar' => $this->extract_currency_indicator($text, 'TRM HOY'),
            'inflacion' => $this->extract_percent_indicator($text, 'IPC Mensual'),
            'tasa_republica' => $this->extract_percent_indicator($text, 'TASA DE INTERVENCIÓN DE POLÍTICA MONETARIA', false),
            'uvr' => $this->extract_currency_indicator($text, 'UVR'),
        ];
    }

    private function extract_currency_indicator($text, $label) {
        $pattern = '/' . preg_quote($label, '/') . '.{0,180}?\$\s*([0-9\.]+,[0-9]+)/iu';
        if (!preg_match($pattern, $text, $match)) {
            return null;
        }

        $actual = $this->to_number($match[1]);
        $change = null;
        $date = '';

        $after = substr($text, strpos($text, $match[0]) + strlen($match[0]), 220);

        if (preg_match('/([+-])\s*\$\s*([0-9\.]+,[0-9]+)/u', $after, $cm)) {
            $change = $this->to_number($cm[1] . $cm[2]);
        }

        if (preg_match('/(\d{1,2}\/\d{1,2}\/\d{4})/u', $after, $dm)) {
            $date = $dm[1];
        }

        return [
            'valor' => $actual,
            'cambio' => $change,
            'estado' => is_null($change) ? 'igual' : ($change > 0 ? 'subio' : ($change < 0 ? 'bajo' : 'igual')),
            'fecha' => $date,
        ];
    }

    private function extract_percent_indicator($text, $label, $with_change = true) {
        $pattern = '/' . preg_quote($label, '/') . '.{0,220}?([0-9]+(?:[\.,][0-9]+)?)\s*%/iu';
        if (!preg_match($pattern, $text, $match)) {
            return null;
        }

        $actual = $this->to_number($match[1]);
        $change = null;
        $date = '';
        $after = substr($text, strpos($text, $match[0]) + strlen($match[0]), 220);

        if ($with_change && preg_match('/([+-])\s*([0-9]+(?:[\.,][0-9]+)?)\s*%/u', $after, $cm)) {
            $change = $this->to_number($cm[1] . $cm[2]);
        }

        if (preg_match('/Mes:\s*([^\s]+\.?)/iu', $after, $dm)) {
            $date = 'Mes: ' . $dm[1];
        }

        return [
            'valor' => $actual,
            'cambio' => $change,
            'estado' => is_null($change) ? 'igual' : ($change > 0 ? 'subio' : ($change < 0 ? 'bajo' : 'igual')),
            'fecha' => $date,
        ];
    }

    private function remote_json($url) {
        $response = wp_remote_get($url, [
            'timeout' => 20,
            'redirection' => 5,
            'headers' => [
                'Accept' => 'application/json',
                'User-Agent' => 'Mozilla/5.0 WordPress Indicadores Economicos Colombia',
            ],
        ]);

        if (is_wp_error($response) || wp_remote_retrieve_response_code($response) !== 200) {
            return null;
        }

        $json = json_decode(wp_remote_retrieve_body($response), true);
        return is_array($json) ? $json : null;
    }

    private function remote_text($url) {
        $response = wp_remote_get($url, [
            'timeout' => 20,
            'redirection' => 5,
            'headers' => [
                'Accept' => 'text/html,application/xhtml+xml',
                'User-Agent' => 'Mozilla/5.0 WordPress Indicadores Economicos Colombia',
            ],
        ]);

        if (is_wp_error($response) || wp_remote_retrieve_response_code($response) !== 200) {
            return null;
        }

        return wp_remote_retrieve_body($response);
    }

    private function first_existing_key($array, $keys) {
        foreach ($keys as $key) {
            if (array_key_exists($key, $array)) {
                return $key;
            }
        }
        return null;
    }

    private function to_number($value) {
        $value = trim((string) $value);
        $value = str_replace(['$', '%', ' '], '', $value);

        if (strpos($value, ',') !== false && strpos($value, '.') !== false) {
            $value = str_replace('.', '', $value);
            $value = str_replace(',', '.', $value);
        } elseif (strpos($value, ',') !== false) {
            $value = str_replace(',', '.', $value);
        }

        return (float) preg_replace('/[^0-9\.-]/', '', $value);
    }

    private function format_number($value) {
        return number_format((float) $value, 2, ',', '.');
    }

    private function trend($actual, $anterior) {
        if ($actual > $anterior) {
            return 'subio';
        }
        if ($actual < $anterior) {
            return 'bajo';
        }
        return 'igual';
    }

    private function estado_text($estado) {
        switch ($estado) {
            case 'subio':
                return 'Subió';
            case 'bajo':
                return 'Bajó';
            default:
                return 'Igual';
        }
    }

    private function get_css() {
        return '
            .iec-wrapper {
                display: grid;
                grid-template-columns: repeat(auto-fit, minmax(180px, 1fr));
                gap: 16px;
                margin: 20px 0;
            }
            .iec-card {
                border: 1px solid #e5e7eb;
                border-radius: 14px;
                padding: 16px;
                background: #fff;
                box-shadow: 0 6px 18px rgba(0,0,0,.06);
            }
            .iec-title {
                display: block;
                font-size: 14px;
                color: #555;
                margin-bottom: 6px;
            }
            .iec-value {
                display: block;
                font-size: 24px;
                line-height: 1.2;
                color: #111827;
            }
            .iec-trend {
                display: inline-block;
                margin-top: 10px;
                font-size: 13px;
                font-weight: 600;
                border-radius: 999px;
                padding: 4px 10px;
                background: #f3f4f6;
                color: #374151;
            }
            .iec-subio .iec-trend {
                background: #dcfce7;
                color: #166534;
            }
            .iec-bajo .iec-trend {
                background: #fee2e2;
                color: #991b1b;
            }
            .iec-date {
                display: block;
                margin-top: 8px;
                color: #6b7280;
                font-size: 12px;
            }
            .iec-error {
                padding: 12px;
                border: 1px solid #fecaca;
                background: #fef2f2;
                color: #991b1b;
                border-radius: 10px;
            }
        ';
    }
}

new Indicadores_Economicos_Colombia();
