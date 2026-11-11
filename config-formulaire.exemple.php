<?php
// Réglages du formulaire de contact (modèle Reflekt).
// 1. Copier ce fichier sous le nom config-formulaire.php, À CÔTÉ du dossier public_html (pas dedans).
// 2. Remplir le mot de passe UNIQUEMENT sur l'hébergement. Ne jamais l'écrire ici ni dans GitHub.
return [
    // OVHcloud : smtp.mail.ovh.net / 465 / 'ssl' (boîte mail créée dans l'espace client OVH)
    'smtp_hote'          => 'smtp.mail.ovh.net',
    'smtp_port'          => 465,
    'smtp_securite'      => 'ssl',
    'smtp_utilisateur'   => 'contact@reflexo-soins.fr',
    'smtp_mot_de_passe'  => 'A_REMPLIR_SUR_L_HEBERGEMENT',
    'expediteur_email'   => 'contact@reflexo-soins.fr', // identique à smtp_utilisateur
    'expediteur_nom'     => 'Site internet — Reflexo & Soins by MG',
    'destinataire_email' => 'magreflexo84@gmail.com',
    'destinataire_nom'   => 'Marie Gosselin',
    'notification_reflekt' => 'bonjour@reflektweb.com', // simple notification (date + objet), sans le contenu du message ; '' pour désactiver
    'nom_du_site'        => 'Reflexo & Soins by MG',
    'url_du_site'        => 'https://www.reflexo-soins.fr',
    'page_merci'         => '/merci/',
    'limite_par_heure'   => 5,
    'mode_debug'         => false,
];
