<?php
/**
 * Plugin Name: WP XML Lastmod
 * Plugin URI: https://kevin-benabdelhak.fr/
 * Description: WP XML Lastmod désactive la mise à jour automatique de la balise <lastmod> dans le sitemap.xml de WordPress. Accédez à un second bouton de mise à jour manuelle.
 * Version: 1.0
 * Author: Kevin BENABDELHAK
 * License: GPL2
 */





if (!defined('ABSPATH')) {
    exit;
}





// Ajouter le bouton 'Mettre à jour le lastmod' à l'écran d'édition de l'article
add_action('post_submitbox_misc_actions', 'ajouter_bouton_mettre_a_jour_lastmod');

function ajouter_bouton_mettre_a_jour_lastmod() {
    global $post;

    // Ajouter une nonce pour la sécurité.
    wp_nonce_field('nonce_personnalise_mettre_a_jour_lastmod', 'champ_nonce_personnalise_mettre_a_jour_lastmod');

    echo '<div id="wrapper-mettre-a-jour-lastmod" style="padding: 10px 0;">';
    echo '<button type="button" class="button" id="bouton_personnalise_mettre_a_jour_lastmod">' . __('Mettre à jour + lastmod') . '</button>';
    echo '</div>';

    // Ajouter un input caché pour spécifier si le bouton lastmod a été cliqué
    echo '<input type="hidden" name="personnalise_mettre_a_jour_lastmod" id="personnalise_mettre_a_jour_lastmod" value="no" />';
}

// Ajouter du JavaScript pour gérer le clic sur le bouton et la soumission du formulaire
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

// Accrocher pour filtrer les données de l'article avant qu'elles ne soient mises à jour dans la base de données
add_filter('wp_insert_post_data', 'controler_mise_a_jour_lastmod', 10, 2);

function controler_mise_a_jour_lastmod($data, $postarr) {
    // Vérifier la nonce pour assurer une soumission sécurisée du formulaire.
    if (!isset($_POST['champ_nonce_personnalise_mettre_a_jour_lastmod']) || !wp_verify_nonce($_POST['champ_nonce_personnalise_mettre_a_jour_lastmod'], 'nonce_personnalise_mettre_a_jour_lastmod')) {
        return $data;
    }

    // Vérifier si nous devons mettre à jour le lastmod.
    if (isset($_POST['personnalise_mettre_a_jour_lastmod']) && $_POST['personnalise_mettre_a_jour_lastmod'] === 'yes') {
        // Mettre à jour les dates lastmod (par comportement par défaut) si le bouton personnalisé a été cliqué.
    } else {
        // Conserver les dates originales post_modified et post_modified_gmt si le bouton personnalisé n'a pas été cliqué.
        if (!empty($postarr['ID'])) {
            $post_id = $postarr['ID'];
            $post_original = get_post($post_id);

            // Réinitialiser les dates uniquement si c'est un article existant, pas un nouveau.
            if ($post_original && $post_original->post_date !== '0000-00-00 00:00:00') {
                $data['post_modified'] = $post_original->post_modified;
                $data['post_modified_gmt'] = $post_original->post_modified_gmt;
            }
        }
    }

    return $data;
}