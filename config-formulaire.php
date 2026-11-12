<?php
// Réglages du formulaire de contact — reflexo-soins.fr
// Version GitHub : ce fichier est à côté de envoi.php. Le .htaccess empêche de l'ouvrir depuis Internet.
// Dépôt GitHub OBLIGATOIREMENT privé. Mot de passe à taper directement sur GitHub (crayon), jamais par mail.
return [
    'smtp_hote'          => 'ssl0.ovh.net',
    'smtp_port'          => 465,
    'smtp_securite'      => 'ssl',
    'smtp_utilisateur'   => 'contact@reflexo-soins.fr',
    'smtp_mot_de_passe'  => 'Reflexomail',
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
