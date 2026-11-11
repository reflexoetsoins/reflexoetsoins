# Formulaire de contact — reflexo-soins.fr

Envoi par la boîte OVH contact@reflexo-soins.fr (ssl0.ovh.net, port 465).
Réception : contact@reflexo-soins.fr. Copie cachée : bonjour@reflektweb.com (suivi Pilotage).

## Où mettre les fichiers (FTP OVH)
- Le contenu du dossier `www/` va dans le dossier `www` de l'hébergement.
- `config-formulaire.php` va À CÔTÉ du dossier `www` (à la racine du FTP), jamais dedans.
- `formulaire-a-integrer.html` : à coller dans la page Contact du site (ne pas mettre en ligne tel quel).

## Le mot de passe
Ouvrir `config-formulaire.php` SUR L'HÉBERGEMENT (FileZilla : clic droit > Afficher/Éditer)
et remplacer A_REMPLIR_SUR_L_HEBERGEMENT par le mot de passe de contact@reflexo-soins.fr.
Ne jamais mettre ce fichier sur GitHub, ni l'envoyer par mail.

## Tests
1. Une fausse demande depuis l'ordinateur, une depuis le téléphone.
2. Vérifier l'arrivée dans contact@ (et pas en spam) + la copie dans bonjour@.
3. « Répondre » doit écrire au visiteur.
4. Envoyer avec un champ vide : un message d'erreur doit s'afficher.

En cas d'échec : 'mode_debug' => true, refaire un essai pour lire l'erreur, puis remettre false.
Causes habituelles : mot de passe, port, expéditeur différent de l'identifiant.
