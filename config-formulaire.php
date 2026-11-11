<?php
// Réglages du formulaire de contact — reflexo-soins.fr
// À placer À CÔTÉ du dossier www (pas dedans).
// Le mot de passe se tape UNIQUEMENT sur l'hébergement OVH (jamais sur GitHub, ni par mail).
return [
    'smtp_hote'          => 'ssl0.ovh.net',
    'smtp_port'          => 465,
    'smtp_securite'      => 'ssl',
    'smtp_utilisateur'   => 'contact@reflexo-soins.fr',
    'smtp_mot_de_passe'  => 'A_REMPLIR_SUR_L_HEBERGEMENT',
    'expediteur_email'   => 'contact@reflexo-soins.fr', // identique à smtp_utilisateur
    'expediteur_nom'     => 'Site internet — Reflexo & Soins',
    'destinataire_email' => 'contact@reflexo-soins.fr', // adresse où Marie lit les demandes
    'destinataire_nom'   => 'Marie — Reflexo & Soins',
    'copie_cachee'       => 'bonjour@reflektweb.com', // suivi Pilotage ; '' pour la retirer
    'nom_du_site'        => 'Reflexo & Soins',
    'url_du_site'        => 'https://reflexo-soins.fr',
    'page_merci'         => 'merci.html',
    'limite_par_heure'   => 5,
    'mode_debug'         => false, // true seulement pour diagnostiquer, puis remettre false
];
