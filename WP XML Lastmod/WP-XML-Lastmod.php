<?php
/**
 * Plugin Name: WP XML Lastmod
 * Plugin URI: https://kevin-benabdelhak.fr/plugins/wp-xml-lastmod/
 * Description: WP XML Lastmod désactive la mise à jour automatique de la balise <lastmod> dans le sitemap.xml de WordPress. Accédez à un second bouton de mise à jour manuelle.
 * Version: 1.1
 * Author: Kevin BENABDELHAK
 * License: GPL2
 */

if (!defined('ABSPATH')) {
    exit;
}




add_action('post_submitbox_misc_actions', 'ajouter_bouton_mettre_a_jour_lastmod');
function ajouter_bouton_mettre_a_jour_lastmod() {
    global $post;
    wp_nonce_field('nonce_personnalise_mettre_a_jour_lastmod', 'champ_nonce_personnalise_mettre_a_jour_lastmod');

    echo '<div id="wrapper-mettre-a-jour-lastmod" style="padding: 10px;">';
    echo '<button type="button" class="button" id="bouton_personnalise_mettre_a_jour_lastmod">' . __('Mettre à jour le sitemap') . '</button>';
    echo '</div>';
    echo '<input type="hidden" name="personnalise_mettre_a_jour_lastmod" id="personnalise_mettre_a_jour_lastmod" value="no" />';
}





add_action('admin_footer', 'ajouter_js_personnalise_mettre_a_jour_lastmod');
function ajouter_js_personnalise_mettre_a_jour_lastmod() {
    ?>
    <script type="text/javascript">
        jQuery(document).ready(function($) {
            $('#bouton_personnalise_mettre_a_jour_lastmod').click(function() {
                $('#personnalise_mettre_a_jour_lastmod').val('yes');
                $('#publish').click();
            });
        });
    </script>
    <?php
}






add_filter('wp_insert_post_data', 'controler_mise_a_jour_lastmod', 10, 2);
function controler_mise_a_jour_lastmod($data, $postarr) {
    if (!isset($_POST['champ_nonce_personnalise_mettre_a_jour_lastmod']) || !wp_verify_nonce($_POST['champ_nonce_personnalise_mettre_a_jour_lastmod'], 'nonce_personnalise_mettre_a_jour_lastmod')) {
        return $data;
    }
    if (isset($_POST['personnalise_mettre_a_jour_lastmod']) && $_POST['personnalise_mettre_a_jour_lastmod'] === 'yes') {
       
    } else {
        if (!empty($postarr['ID'])) {
            $post_id = $postarr['ID'];
            $post_original = get_post($post_id);

            if ($post_original && $post_original->post_date !== '0000-00-00 00:00:00') {
                $data['post_modified'] = $post_original->post_modified;
                $data['post_modified_gmt'] = $post_original->post_modified_gmt;
            }
        }
    }

    return $data;
}