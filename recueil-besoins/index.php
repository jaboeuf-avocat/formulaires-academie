<?php
// Page formulaire recueil de besoins — Académie Jaboeuf-Avocat
// Déployer sur : /var/www/academie/recueil-besoins/index.php

$token     = htmlspecialchars($_GET['token']    ?? '');
$prenom    = htmlspecialchars($_GET['prenom']   ?? 'Apprenant');
$coursename = htmlspecialchars($_GET['course']  ?? 'votre formation');
$n8n_submit_url = 'https://n8n.jaboeuf-avocat.eu/webhook/recueil-besoins/submit';

if (!$token) {
    http_response_code(400);
    die('Lien invalide ou expiré.');
}

$success = false;
$error   = false;

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $payload = [
        'token'                    => $_POST['token']                    ?? '',
        'firstname'                => $_POST['firstname']                ?? '',
        'email'                    => $_POST['email']                    ?? '',
        'coursename'               => $_POST['coursename']               ?? '',
        'niveau_initial'           => $_POST['niveau_initial']           ?? '',
        'objectifs'                => implode(',', (array)($_POST['objectifs'] ?? [])),
        'cadre_suivi'              => $_POST['cadre_suivi']              ?? '',
        'acces_technique'          => isset($_POST['acces_technique']) && $_POST['acces_technique'] === 'oui' ? 'true' : 'false',
        'contraintes_accessibilite'=> $_POST['contraintes_accessibilite'] ?? '',
        'disponibilites_hebdo'     => $_POST['disponibilites_hebdo']     ?? '',
    ];

    $ch = curl_init($n8n_submit_url);
    curl_setopt($ch, CURLOPT_POST, true);
    curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($payload));
    curl_setopt($ch, CURLOPT_HTTPHEADER, ['Content-Type: application/json']);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_TIMEOUT, 10);
    $response = curl_exec($ch);
    $httpcode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
    curl_close($ch);

    if ($httpcode === 200) {
        $success = true;
    } else {
        $error = true;
    }
}
?>
<!DOCTYPE html>
<html lang="fr">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>Recueil de besoins — Académie Jaboeuf-Avocat</title>
<style>
* { box-sizing: border-box; margin: 0; padding: 0; }
body { font-family: Georgia, serif; background: #f5f0e8; color: #1C2B4A; min-height: 100vh; display: flex; align-items: flex-start; justify-content: center; padding: 40px 16px; }
.card { background: #fff; max-width: 680px; width: 100%; padding: 48px 56px; box-shadow: 0 2px 16px rgba(0,0,0,0.08); }
.header { border-bottom: 2px solid #C8A84B; padding-bottom: 20px; margin-bottom: 32px; }
.logo { font-size: 18px; font-weight: bold; letter-spacing: 3px; text-transform: uppercase; }
.logo-sub { font-size: 11px; color: #888; margin-top: 4px; letter-spacing: 1px; }
h1 { font-size: 22px; margin-bottom: 6px; }
.subtitle { font-size: 14px; color: #666; margin-bottom: 32px; }
.formation-name { font-style: italic; color: #C8A84B; font-weight: bold; }
.question { margin-bottom: 28px; }
.question label { display: block; font-size: 14px; font-weight: bold; margin-bottom: 10px; }
.question .sub { font-size: 12px; color: #888; margin-bottom: 8px; font-style: italic; font-weight: normal; }
.radios label, .checks label { display: flex; align-items: center; gap: 10px; font-size: 14px; font-weight: normal; margin-bottom: 8px; cursor: pointer; }
.radios input[type=radio], .checks input[type=checkbox] { width: 18px; height: 18px; accent-color: #C8A84B; }
textarea { width: 100%; border: 1px solid #ddd; padding: 10px 12px; font-size: 14px; font-family: inherit; resize: vertical; min-height: 80px; color: #1C2B4A; }
textarea:focus { outline: none; border-color: #C8A84B; }
.select-wrap select { width: 100%; border: 1px solid #ddd; padding: 10px 12px; font-size: 14px; font-family: inherit; color: #1C2B4A; background: #fff; }
.btn { background: #1C2B4A; color: #fff; border: none; padding: 14px 40px; font-size: 15px; font-family: Georgia, serif; cursor: pointer; width: 100%; margin-top: 8px; letter-spacing: 1px; }
.btn:hover { background: #C8A84B; color: #1C2B4A; }
.success { background: #f0f8f0; border-left: 4px solid #2d7a2d; padding: 24px; margin-bottom: 24px; }
.success h2 { color: #2d7a2d; margin-bottom: 8px; }
.error { background: #fff3f3; border-left: 4px solid #c0392b; padding: 16px; margin-bottom: 24px; font-size: 14px; color: #c0392b; }
.required { color: #c0392b; }
.divider { border: none; border-top: 1px solid #eee; margin: 32px 0; }
.footer { font-size: 11px; color: #aaa; text-align: center; margin-top: 32px; border-top: 1px solid #eee; padding-top: 16px; }
@media (max-width: 640px) { .card { padding: 32px 24px; } }
</style>
</head>
<body>
<div class="card">

  <div class="header">
    <div class="logo">Académie Jaboeuf-Avocat</div>
    <div class="logo-sub">Organisme de formation professionnelle — NDA 11756636475</div>
  </div>

  <?php if ($success): ?>
    <div class="success">
      <h2>✓ Questionnaire envoyé</h2>
      <p>Merci <?= $prenom ?>, vos réponses ont bien été enregistrées. Vous recevrez un email de confirmation dans quelques instants.</p>
    </div>
    <p style="font-size:14px;color:#666;">Vous pouvez fermer cette page et accéder à votre espace de formation sur <a href="https://academie.jaboeuf-avocat.eu" style="color:#C8A84B;">academie.jaboeuf-avocat.eu</a>.</p>

  <?php else: ?>

    <h1>Recueil de vos besoins et attentes</h1>
    <p class="subtitle">Formation : <span class="formation-name"><?= $coursename ?></span></p>
    <p style="font-size:13px;color:#666;margin-bottom:28px;">Ce questionnaire nous permet d'adapter votre parcours de formation. Il ne prend que 5 minutes.</p>

    <?php if ($error): ?>
      <div class="error">Une erreur est survenue lors de l'envoi. Veuillez réessayer ou contacter <a href="mailto:contact@jaboeuf-avocat.eu">contact@jaboeuf-avocat.eu</a>.</div>
    <?php endif; ?>

    <form method="POST" action="">
      <input type="hidden" name="token"      value="<?= $token ?>">
      <input type="hidden" name="firstname"  value="<?= $prenom ?>">
      <input type="hidden" name="coursename" value="<?= $coursename ?>">
      <input type="hidden" name="email"      value="<?= htmlspecialchars($_GET['email'] ?? '') ?>">

      <!-- Q1 : Niveau initial -->
      <div class="question">
        <label>1. Quel est votre niveau actuel dans le domaine couvert par cette formation ? <span class="required">*</span></label>
        <div class="radios">
          <label><input type="radio" name="niveau_initial" value="débutant" required> Débutant — peu ou pas de connaissances préalables</label>
          <label><input type="radio" name="niveau_initial" value="intermédiaire"> Intermédiaire — quelques connaissances de base</label>
          <label><input type="radio" name="niveau_initial" value="avancé"> Avancé — pratique régulière dans mon activité</label>
        </div>
      </div>

      <hr class="divider">

      <!-- Q2 : Objectifs -->
      <div class="question">
        <label>2. Quels sont vos principaux objectifs pour cette formation ? <span class="sub">(Plusieurs choix possibles)</span></label>
        <div class="checks">
          <label><input type="checkbox" name="objectifs[]" value="Mise à niveau réglementaire"> Mise à niveau réglementaire (obligations légales)</label>
          <label><input type="checkbox" name="objectifs[]" value="Développement de compétences métier"> Développement de compétences métier</label>
          <label><input type="checkbox" name="objectifs[]" value="Préparation à une mission client"> Préparation à une mission client</label>
          <label><input type="checkbox" name="objectifs[]" value="Obtention d'une attestation de formation"> Obtention d'une attestation de formation</label>
          <label><input type="checkbox" name="objectifs[]" value="Financement OPCO / FIF-PL"> Financement OPCO / FIF-PL</label>
          <label><input type="checkbox" name="objectifs[]" value="Curiosité / culture générale"> Curiosité / culture générale</label>
        </div>
      </div>

      <hr class="divider">

      <!-- Q3 : Cadre de suivi -->
      <div class="question">
        <label>3. Dans quel cadre suivez-vous cette formation ? <span class="required">*</span></label>
        <div class="radios">
          <label><input type="radio" name="cadre_suivi" value="Salarié (prise en charge employeur)" required> Salarié — prise en charge par mon employeur</label>
          <label><input type="radio" name="cadre_suivi" value="Travailleur indépendant / libéral (FIF-PL)"> Travailleur indépendant / libéral (FIF-PL)</label>
          <label><input type="radio" name="cadre_suivi" value="Autofinancement personnel"> Autofinancement personnel</label>
          <label><input type="radio" name="cadre_suivi" value="Demandeur d'emploi"> Demandeur d'emploi</label>
          <label><input type="radio" name="cadre_suivi" value="Autre"> Autre</label>
        </div>
      </div>

      <hr class="divider">

      <!-- Q4 : Accès technique -->
      <div class="question">
        <label>4. Disposez-vous d'un accès internet stable et d'un ordinateur personnel ? <span class="required">*</span></label>
        <div class="radios">
          <label><input type="radio" name="acces_technique" value="oui" required> Oui, sans difficulté</label>
          <label><input type="radio" name="acces_technique" value="non"> Non — je rencontre des contraintes techniques</label>
        </div>
      </div>

      <hr class="divider">

      <!-- Q5 : Contraintes accessibilité -->
      <div class="question">
        <label>5. Avez-vous des besoins particuliers en matière d'accessibilité ou des contraintes que nous devons prendre en compte ?</label>
        <div class="sub">Exemples : handicap, difficulté de lecture, besoin d'adaptation de format.</div>
        <textarea name="contraintes_accessibilite" placeholder="Précisez si nécessaire (ou laissez vide)"></textarea>
      </div>

      <hr class="divider">

      <!-- Q6 : Disponibilités -->
      <div class="question">
        <label>6. Quelles sont vos disponibilités hebdomadaires estimées pour cette formation ? <span class="required">*</span></label>
        <div class="radios">
          <label><input type="radio" name="disponibilites_hebdo" value="Moins de 2h par semaine" required> Moins de 2h par semaine</label>
          <label><input type="radio" name="disponibilites_hebdo" value="2 à 4h par semaine"> 2 à 4h par semaine</label>
          <label><input type="radio" name="disponibilites_hebdo" value="Plus de 4h par semaine"> Plus de 4h par semaine</label>
        </div>
      </div>
<hr class="divider">

<div class="question">
  <label>7. Email de votre responsable, employeur ou OPCO référent <span class="sub">(facultatif — permettra de recueillir son avis sur votre formation)</span></label>
  <input type="email" name="employeur_email" placeholder="prenom.nom@entreprise.fr" style="width:100%;border:1px solid #ddd;padding:10px 12px;font-size:14px;font-family:inherit;color:#1C2B4A;">
</div>
      <button type="submit" class="btn">Envoyer mes réponses →</button>
    </form>

  <?php endif; ?>

  <div class="footer">
    Académie Jaboeuf-Avocat · NDA 11756636475 · contact@jaboeuf-avocat.eu<br>
    Vos données sont traitées conformément au RGPD et à notre <a href="https://jaboeuf-avocat.eu/politique-de-confidentialite" style="color:#C8A84B;">politique de confidentialité</a>.
  </div>

</div>
</body>
</html>
<?php // v1.1 ?>
