<?php
declare(strict_types=1);

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception as MailException;

require __DIR__ . '/lib/PHPMailer/Exception.php';
require __DIR__ . '/lib/PHPMailer/PHPMailer.php';
require __DIR__ . '/lib/PHPMailer/SMTP.php';

$enAjax = (($_SERVER['HTTP_X_REQUESTED_WITH'] ?? '') === 'fetch');

function repondre(bool $ok, string $message, int $code = 200, ?string $pageMerci = null): void
{
    global $enAjax;
    if ($enAjax) {
        http_response_code($code);
        header('Content-Type: application/json; charset=utf-8');
        echo json_encode(['ok' => $ok, 'message' => $message], JSON_UNESCAPED_UNICODE);
    } elseif ($ok && $pageMerci) {
        header('Location: ' . $pageMerci, true, 303);
    } else {
        http_response_code($code);
        header('Content-Type: text/html; charset=utf-8');
        echo '<!doctype html><meta charset="utf-8"><meta name="viewport" content="width=device-width,initial-scale=1">'
           . '<title>Formulaire</title><body style="font-family:system-ui,sans-serif;max-width:560px;margin:15vh auto;padding:0 16px;line-height:1.5">'
           . '<p>' . htmlspecialchars($message, ENT_QUOTES, 'UTF-8') . '</p>'
           . '<p><a href="javascript:history.back()">← Revenir au formulaire</a></p></body>';
    }
    exit;
}

if (($_SERVER['REQUEST_METHOD'] ?? '') !== 'POST') {
    repondre(false, 'Méthode non autorisée.', 405);
}

$cheminsConfig = [dirname(__DIR__) . '/config-formulaire.php', __DIR__ . '/config-formulaire.php'];
$config = null;
foreach ($cheminsConfig as $chemin) {
    if (is_file($chemin)) { $config = require $chemin; break; }
}
if (!is_array($config)) {
    error_log('[formulaire] config-formulaire.php introuvable');
    repondre(false, "Le formulaire n'est pas encore configuré. Merci de nous contacter par téléphone. (code R1)", 500);
}
if (($config['smtp_mot_de_passe'] ?? '') === '' || $config['smtp_mot_de_passe'] === 'A_REMPLIR_SUR_L_HEBERGEMENT') {
    error_log('[formulaire] mot de passe SMTP non renseigné');
    repondre(false, "Le formulaire n'est pas encore configuré. Merci de nous contacter par téléphone. (code R2)", 500);
}

// Anti-spam : champ piège
if (trim((string)($_POST['site_web'] ?? '')) !== '') {
    repondre(true, 'Merci, votre demande a bien été envoyée.', 200, $config['page_merci'] ?? null);
}
// Anti-spam : envoi en moins de 3 secondes.
// L'heure vient de l'ordinateur du visiteur : si elle est en avance sur le serveur, l'écart est négatif
// et on ignore ce contrôle (sinon un vrai message serait perdu en silence).
$affiche = (int)($_POST['affiche_a'] ?? 0);
$ecart   = time() - intdiv($affiche, 1000);
if ($affiche > 0 && $ecart >= 0 && $ecart < 3) {
    repondre(true, 'Merci, votre demande a bien été envoyée.', 200, $config['page_merci'] ?? null);
}
// Anti-spam : limite par IP et par heure
$ip       = $_SERVER['REMOTE_ADDR'] ?? 'inconnue';
$limite   = max(1, (int)($config['limite_par_heure'] ?? 5));
$fichierL = rtrim(sys_get_temp_dir(), '/\\') . '/formulaire_' . hash('sha256', $ip . __DIR__) . '.json';
$envois   = [];
if (is_file($fichierL)) { $envois = json_decode((string)@file_get_contents($fichierL), true) ?: []; }
$envois = array_values(array_filter($envois, fn($t) => is_int($t) && $t > time() - 3600));
if (count($envois) >= $limite) {
    repondre(false, 'Trop de demandes envoyées depuis votre connexion. Merci de réessayer dans une heure ou de nous appeler.', 429);
}

function champ(string $nom, int $max): string
{
    $v = trim((string)($_POST[$nom] ?? ''));
    $v = str_replace("\0", '', $v);
    return mb_substr($v, 0, $max);
}
function uneLigne(string $v): string
{
    return trim(preg_replace('/[\r\n\t]+/', ' ', $v) ?? '');
}

$nom       = uneLigne(champ('nom', 100));
$email     = uneLigne(champ('email', 150));
$telephone = uneLigne(champ('telephone', 30));
$sujet     = uneLigne(champ('sujet', 120));
$message   = champ('message', 5000);
$consent   = isset($_POST['consentement']);

$erreurs = [];
if ($nom === '')                                $erreurs[] = 'votre nom';
if (!filter_var($email, FILTER_VALIDATE_EMAIL)) $erreurs[] = 'une adresse e-mail valide';
if ($telephone !== '' && !preg_match('/^[0-9 +().\-]{6,30}$/', $telephone)) $erreurs[] = 'un numéro de téléphone valide';
if (mb_strlen($message) < 10)                   $erreurs[] = 'un message (10 caractères minimum)';
if (!$consent)                                  $erreurs[] = "votre accord pour l'utilisation de vos données";
if ($erreurs) {
    repondre(false, 'Merci d\'indiquer ' . implode(', ', $erreurs) . '.', 422);
}

$h       = fn(string $v): string => htmlspecialchars($v, ENT_QUOTES, 'UTF-8');
$nomSite = (string)($config['nom_du_site'] ?? 'le site');
$date    = (new DateTime('now', new DateTimeZone('Europe/Paris')))->format('d/m/Y à H:i');
$objet   = 'Nouvelle demande' . ($sujet !== '' ? ' — ' . $sujet : '') . ' (' . $nomSite . ')';

$lignes = ['Nom' => $nom, 'E-mail' => $email, 'Téléphone' => $telephone !== '' ? $telephone : '—', 'Sujet' => $sujet !== '' ? $sujet : '—'];

$html  = '<div style="font-family:Arial,Helvetica,sans-serif;font-size:15px;color:#1a1a1a;line-height:1.5;max-width:600px">';
$html .= '<p style="margin:0 0 16px">Nouvelle demande reçue depuis le formulaire de <strong>' . $h($nomSite) . '</strong>, le ' . $h($date) . '.</p>';
$html .= '<table cellpadding="6" cellspacing="0" style="border-collapse:collapse;margin:0 0 16px">';
foreach ($lignes as $label => $valeur) {
    $val = $h($valeur);
    if ($label === 'E-mail')                         $val = '<a href="mailto:' . $h($email) . '">' . $val . '</a>';
    if ($label === 'Téléphone' && $telephone !== '') $val = '<a href="tel:' . $h(preg_replace('/[^0-9+]/', '', $telephone) ?? '') . '">' . $val . '</a>';
    $html .= '<tr><td style="color:#666;padding-right:16px;vertical-align:top">' . $label . '</td><td><strong>' . $val . '</strong></td></tr>';
}
$html .= '</table>';
$html .= '<p style="margin:0 0 4px;color:#666">Message :</p>';
$html .= '<div style="white-space:pre-wrap;background:#f5f5f3;border-radius:8px;padding:14px">' . nl2br($h($message), false) . '</div>';
$html .= '<p style="margin:16px 0 0;font-size:13px;color:#888">Pour répondre, utilisez simplement « Répondre » : la réponse partira vers ' . $h($email) . '.</p>';
$html .= '</div>';

$texte = "Nouvelle demande reçue depuis le formulaire de {$nomSite}, le {$date}.\n\n";
foreach ($lignes as $label => $valeur) {
    $texte .= $label . ' :' . str_repeat(' ', max(1, 12 - mb_strlen($label))) . $valeur . "\n";
}
$texte .= "\nMessage :\n{$message}\n\nPour répondre, utilisez « Répondre » : la réponse partira vers {$email}.\n";

$mail = new PHPMailer(true);
try {
    $mail->isSMTP();
    $mail->Host       = (string)$config['smtp_hote'];
    $mail->Port       = (int)$config['smtp_port'];
    $mail->SMTPAuth   = true;
    $mail->Username   = (string)$config['smtp_utilisateur'];
    $mail->Password   = (string)$config['smtp_mot_de_passe'];
    $mail->SMTPSecure = (($config['smtp_securite'] ?? 'ssl') === 'tls') ? PHPMailer::ENCRYPTION_STARTTLS : PHPMailer::ENCRYPTION_SMTPS;
    $mail->Timeout    = 15;
    $mail->CharSet    = PHPMailer::CHARSET_UTF8;
    if (!empty($config['mode_debug'])) { $mail->SMTPDebug = 2; $mail->Debugoutput = 'error_log'; }

    $mail->setFrom((string)$config['expediteur_email'], (string)($config['expediteur_nom'] ?? ''));
    $mail->addAddress((string)$config['destinataire_email'], (string)($config['destinataire_nom'] ?? ''));
    $mail->addReplyTo($email, $nom);
    if (!empty($config['copie_cachee'])) { $mail->addBCC((string)$config['copie_cachee']); }

    $mail->isHTML(true);
    $mail->Subject = $objet;
    $mail->Body    = $html;
    $mail->AltBody = $texte;
    $mail->send();
} catch (MailException $e) {
    error_log('[formulaire] échec SMTP : ' . $mail->ErrorInfo);
    $detail = !empty($config['mode_debug']) ? ' (Détail : ' . $mail->ErrorInfo . ')' : '';
    repondre(false, "Désolé, l'envoi n'a pas fonctionné. Merci de réessayer ou de nous appeler." . $detail, 502);
}

// Notification sans donnee personnelle (date + objet seulement)
if (!empty($config['notification_email'])) {
    $notif = new PHPMailer(true);
    try {
        $notif->isSMTP();
        $notif->Host       = (string)$config['smtp_hote'];
        $notif->Port       = (int)$config['smtp_port'];
        $notif->SMTPAuth   = true;
        $notif->Username   = (string)$config['smtp_utilisateur'];
        $notif->Password   = (string)$config['smtp_mot_de_passe'];
        $notif->SMTPSecure = (($config['smtp_securite'] ?? 'ssl') === 'tls') ? PHPMailer::ENCRYPTION_STARTTLS : PHPMailer::ENCRYPTION_SMTPS;
        $notif->Timeout    = 15;
        $notif->CharSet    = PHPMailer::CHARSET_UTF8;
        $notif->setFrom((string)$config['expediteur_email'], (string)($config['expediteur_nom'] ?? ''));
        $notif->addAddress((string)$config['notification_email']);
        $notif->isHTML(false);
        $domaine = preg_replace('#^https?://(www\.)?#', '', rtrim((string)($config['url_du_site'] ?? ''), '/'));
        $notif->Subject = '[Formulaire Reflekt] ' . $domaine . ' · Demande transmise · ' . (new DateTime('now', new DateTimeZone('Europe/Paris')))->format('Y-m-d H:i');
        $notif->Body    = "Une demande a été envoyée depuis le formulaire de {$nomSite}, le {$date}.\nObjet : " . ($sujet !== '' ? $sujet : '—') . "\n\n(Aucune donnée du visiteur n'est transmise.)\n";
        $notif->send();
    } catch (MailException $e) {
        error_log('[formulaire] notification non envoyée : ' . $notif->ErrorInfo);
    }
}

$envois[] = time();
@file_put_contents($fichierL, json_encode($envois), LOCK_EX);

repondre(true, 'Merci, votre demande a bien été envoyée. Nous vous répondons rapidement.', 200, $config['page_merci'] ?? null);
