# Reflexo & Soins by MG — site V3

Site vitrine de Marie Gosselin, réflexologue à Saint-Raphaël et Puget-sur-Argens.
Réalisé par Reflekt. Version 3 : site multi-pages optimisé pour le référencement.
Adresse : **https://www.reflexo-soins.fr** · Hébergement : **OVHcloud** (au nom de Marie).

## Contenu du dossier

```
reflexo-soins-v3/
  LISEZ-MOI.md                    ce fichier
  .gitignore                      empêche d'envoyer le vrai fichier de réglages sur GitHub
  config-formulaire.exemple.php   réglages du formulaire (OVH), SANS mot de passe
  public_html/                    tout ce qui va en ligne
    index.html                    accueil  →  /
    soins/                        les 7 soins  →  /soins/
    premiere-seance/              déroulé + FAQ  →  /premiere-seance/
    a-propos/                     Marie & le cabinet  →  /a-propos/
    contact/                      tarifs, horaires, adresses, formulaire  →  /contact/
    mentions-legales/, confidentialite/   pages légales (non indexées)
    merci/                        page après envoi du formulaire (non indexée)
    404.html                      page d'erreur
    sitemap.xml, robots.txt       pour Google
    assets/css, assets/js, assets/fonts   style, script, polices hébergées sur le site
    img/                          images en WebP, logo (logo-reflexo-et-soins.svg), favicon et icônes
    envoi.php, lib/PHPMailer/     envoi du formulaire par SMTP (modèle Reflekt)
    .htaccess                     page 404, HTTPS, protection des réglages, cache
```

## État

Version finale du 11/11/2026 : bandeau « Version test » retiré, plus aucun champ jaune, formulaire branché (envoi.php).

## Mise en ligne chez OVHcloud (appel avec Marie)

1. Hébergement **Starter** commandé le 10/11/2026 (adresse technique kjbxozk.cluster129.hosting.ovh.net). Vérifier que le domaine **reflexo-soins.fr** est bien commandé et rattaché à l'hébergement : Hébergements → Multisite, avec et sans www.
2. Domaine et hébergement étant chez OVH, les réglages anti-spam des mails (SPF, DKIM) sont gérés par OVH : vérifier qu'ils sont actifs dans l'espace client (zone DNS du domaine).
3. Envoyer le contenu de `public_html/` dans le dossier `www` de l'hébergement (FTP ou gestionnaire de fichiers OVH).
4. Activer le certificat SSL gratuit (Let's Encrypt) dans l'espace client OVH, rubrique Hébergements → Informations générales → Certificat SSL.
5. Vérifier que le site s'ouvre en https://www.reflexo-soins.fr (le .htaccess redirige automatiquement vers HTTPS).

## Formulaire de contact

Deux façons d'envoyer, au choix :

- **Sans rien configurer (par défaut)** : si aucun fichier `config-formulaire.php` n'est présent, `envoi.php` envoie avec la fonction mail() d'OVH, depuis contact@reflexo-soins.fr. Aucun mot de passe, tout passe par GitHub. Il faut seulement que l'offre e-mail OVH soit active sur le domaine (SPF OK).
- **SMTP authentifié (facultatif, plus fiable)** : copier `config-formulaire.exemple.php` en `config-formulaire.php` au-dessus du dossier racine du site (par FTP, jamais dans GitHub) et y mettre le mot de passe de contact@reflexo-soins.fr (serveur smtp.mail.ovh.net, port 465).

Dans les deux cas : Marie reçoit la demande complète, Reflekt seulement une notification (date + objet), « Répondre » renvoie vers le visiteur.
Tests : fausse demande depuis un ordinateur et un téléphone, vérifier les spams.

## Après la mise en ligne

- Google Search Console : ajouter le domaine, envoyer `https://www.reflexo-soins.fr/sitemap.xml`, demander l'indexation des 5 pages.
- Tester les données structurées : https://search.google.com/test/rich-results
- Tester l'aperçu de partage : https://developers.facebook.com/tools/debug/
- Vitesse : https://pagespeed.web.dev
- Fiches Google (Saint-Raphaël et Puget) : ajouter le lien du site, mêmes nom, adresses, téléphone et horaires que sur le site.

## Modifier le site

Le site est généré par un script (`build.py`, conservé par Reflekt) à partir d'un gabarit commun.
Pour une petite correction de texte, on peut modifier directement la page `.html` concernée.

## Règles de contenu (validées avec Marie le 27/10/2026)

- Pas le mot « massage » (Marie n'est ni masseuse ni massothérapeute) : écrire « soin », « technique manuelle ».
- Aucune promesse médicale : jamais « traiter », « guérir », « diagnostic » ; écrire « accompagner », « soulager les tensions ».
- Garder la phrase « Ces soins sont des pratiques de bien-être. Ils accompagnent mais ne remplacent pas un traitement médical. »
- Aucun faux avis, aucune information inventée. Aucun mot de passe ni clé dans le code.
