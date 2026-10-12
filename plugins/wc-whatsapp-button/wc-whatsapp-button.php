<?php
/**
 * Plugin Name: WooCommerce WhatsApp Button Pro
 * Description: Agrega un botón de compra por WhatsApp junto al botón de WooCommerce.
 * Version: 1.0
 * Author: IngeWeb
 */

if (!defined('ABSPATH')) {
    exit;
}

class WC_WhatsApp_Button_Pro {

    private $option_name = 'wc_whatsapp_phone';

    public function __construct() {

        /* FRONTEND */
        add_action('wp_enqueue_scripts', [$this,'enqueue_script']);
        add_action('woocommerce_after_add_to_cart_button', [$this,'render_button']);

        /* ADMIN */
        add_action('admin_menu', [$this,'add_admin_menu']);
        add_action('admin_init', [$this,'register_settings']);
    }

    /*
    =========================
    FRONTEND
    =========================
    */

    public function enqueue_script(){

        if(!is_product()) return;

        wp_add_inline_script('jquery',$this->get_js());
    }

    private function get_js(){

        return <<<JS
jQuery(function($){

    $('.wc-whatsapp-button').on('click', function(e){

        e.preventDefault();

        var qty = $('input.qty').val() || 1;

        var productUrl = $(this).data('product-url');
        var productName = $(this).data('product-name');
        var basePrice = $(this).data('product-price');
        var phone = $(this).data('phone');

        var variationPrice = $('.woocommerce-variation-price').text().trim();

        var finalPrice = variationPrice !== '' ? variationPrice : basePrice;

        var variationText = '';

        $('.variations select').each(function(){

            var label = $(this).closest('tr').find('label').text();
            var val = $(this).find('option:selected').text();

            if(val && val !== 'Choose an option'){
                variationText += '- ' + label + ': ' + val + '%0A';
            }

        });

        var message = 'Hola, estoy interesado en este producto:%0A%0A' +
                      'Producto: ' + productName + '%0A' +
                      'Precio: ' + finalPrice + '%0A' +
                      (variationText ? 'Variación:%0A' + variationText : '') +
                      'Cantidad: ' + qty + '%0A%0A' +
                      productUrl;

        var url = 'https://wa.me/' + phone + '?text=' + message;

        window.open(url,'_blank');

    });

});
JS;
    }

    public function render_button(){

        global $product;

        if(!$product) return;

        $phone = get_option($this->option_name);

        if(!$phone) return;

        $price = wc_price($product->get_price());

        echo '<a href="#"
        class="button button single_add_to_cart_button wc-whatsapp-button"
        style="background:#25D366;color:white;"
        data-phone="'.esc_attr($phone).'"
        data-product-url="'.esc_url(get_permalink($product->get_id())).'"
        data-product-name="'.esc_attr($product->get_name()).'"
        data-product-price="'.esc_attr(strip_tags($price)).'">
        Comprar por WhatsApp
        </a>';
    }

    /*
    =========================
    ADMIN
    =========================
    */

    public function add_admin_menu(){

        add_submenu_page(
            'woocommerce',
            'WhatsApp Button',
            'WhatsApp Button',
            'manage_options',
            'wc-whatsapp-button',
            [$this,'settings_page']
        );

    }

    public function register_settings(){

        register_setting(
            'wc_whatsapp_settings_group',
            $this->option_name
        );

    }

    public function settings_page(){
        ?>

        <div class="wrap">

            <h1>WooCommerce WhatsApp Button</h1>

            <form method="post" action="options.php">

                <?php settings_fields('wc_whatsapp_settings_group'); ?>

                <table class="form-table">

                    <tr>

                        <th scope="row">
                            Número de WhatsApp
                        </th>

                        <td>

                            <input
                            type="text"
                            name="<?php echo $this->option_name ?>"
                            value="<?php echo esc_attr(get_option($this->option_name)) ?>"
                            placeholder="573001234567"
                            style="width:300px"
                            />

                            <p class="description">
                            Incluir código de país sin + ni espacios.
                            </p>

                        </td>

                    </tr>

                </table>

                <?php submit_button(); ?>

            </form>

        </div>

        <?php
    }

}

new WC_WhatsApp_Button_Pro();