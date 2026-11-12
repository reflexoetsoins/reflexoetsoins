<?php
// Réglages du formulaire de contact — reflexo-soins.fr
// À placer À CÔTÉ du dossier www (à la racine du FTP), JAMAIS dedans, JAMAIS sur GitHub.
// Le mot de passe se tape UNIQUEMENT sur l'hébergement OVH.
return [
    'smtp_hote'          => 'ssl0.ovh.net',
    'smtp_port'          => 465,
    'smtp_securite'      => 'ssl',
    'smtp_utilisateur'   => 'contact@reflexo-soins.fr',
    'smtp_mot_de_passe'  => 'A_REMPLIR_SUR_L_HEBERGEMENT',
    'expediteur_email'   => 'contact@reflexo-soins.fr', // identique à smtp_utilisateur
    'expediteur_nom'     => 'Site internet — Reflexo & Soins',
    'destinataire_email' => 'contact@reflexo-soins.fr', // redirigée vers le Gmail de Marie
    'destinataire_nom'   => 'Marie Gosselin',
    'copie_cachee'       => '', // pas de copie complète à Reflekt (voir la page Confidentialité)
    'notification_email' => 'bonjour@reflektweb.com', // notification sans donnée personnelle ; '' pour couper
    'nom_du_site'        => 'Reflexo & Soins',
    'url_du_site'        => 'https://www.reflexo-soins.fr',
    'page_merci'         => '/merci/',
    'limite_par_heure'   => 5,
    'mode_debug'         => false, // true seulement pour diagnostiquer, puis remettre false
];
