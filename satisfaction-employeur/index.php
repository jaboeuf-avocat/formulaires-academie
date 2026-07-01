<?php
// Page formulaire satisfaction employeurs — Académie Jaboeuf-Avocat
// Déployer via GitHub : satisfaction-employeur/index.php

$token    = htmlspecialchars($_GET['token']    ?? '');
$course   = htmlspecialchars($_GET['course']   ?? 'votre formation');
$apprenant = htmlspecialchars($_GET['apprenant'] ?? 'votre collaborateur');
$n8n_submit_url = 'https://n8n.jaboeuf-avocat.eu/webhook/satisfaction-employeur/submit';

if (!$token) {
    http_response_code(400);
    die('Lien invalide ou expiré.');
}

$success = false;
$error   = false;

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $payload = [
        'token'                    => $_POST['token']                    ?? '',
        'q1_objectifs_atteints'    => $_POST['q1_objectifs_atteints']    ?? '',
        'q2_competences_acquises'  => $_POST['q2_competences_acquises']  ?? '',
        'q3_qualite_formation'     => $_POST['q3_qualite_formation']     ?? '',
        'q4_recommandation'        => $_POST['q4_recommandation']        ?? '',
        'q5_commentaire'           => $_POST['q5_commentaire']           ?? '',
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
<title>Satisfaction employeur — Académie Jaboeuf-Avocat</title>
<style>
* { box-sizing: border-box; margin: 0; padding: 0; }
body { font-family: Georgia, serif; background: #f5f0e8; color: #1C2B4A; min-height: 100vh; display: flex; align-items: flex-start; justify-content: center; padding: 40px 16px; }
.card { background: #fff; max-width: 680px; width: 100%; padding: 48px 56px; box-shadow: 0 2px 16px rgba(0,0,0,0.08); }
.header { border-bottom: 2px solid #C8A84B; padding-bottom: 20px; margin-bottom: 32px; }
.logo { font-size: 18px; font-weight: bold; letter-spacing: 3px; text-transform: uppercase; }
.logo-sub { font-size: 11px; color: #888; margin-top: 4px; letter-spacing: 1px; }
h1 { font-size: 22px; margin-bottom: 6px; }
.subtitle { font-size: 14px; color: #666; margin-bottom: 8px; }
.formation-name { font-style: italic; color: #C8A84B; font-weight: bold; }
.apprenant-name { font-weight: bold; color: #1C2B4A; }
.intro { font-size: 13px; color: #666; margin-bottom: 32px; }
.question { margin-bottom: 32px; }
.question label.main { display: block; font-size: 14px; font-weight: bold; margin-bottom: 12px; }
.stars { display: flex; gap: 8px; flex-wrap: wrap; }
.stars input[type=radio] { display: none; }
.stars label { display: flex; flex-direction: column; align-items: center; gap: 4px; cursor: pointer; padding: 8px 12px; border: 1px solid #ddd; font-size: 12px; color: #888; transition: all 0.15s; min-width: 60px; text-align: center; }
.stars label .note { font-size: 18px; font-weight: bold; color: #1C2B4A; }
.stars input[type=radio]:checked + label { border-color: #C8A84B; background: #faf8f2; color: #1C2B4A; }
.stars label:hover { border-color: #C8A84B; }
textarea { width: 100%; border: 1px solid #ddd; padding: 10px 12px; font-size: 14px; font-family: inherit; resize: vertical; min-height: 100px; color: #1C2B4A; }
textarea:focus { outline: none; border-color: #C8A84B; }
.btn { background: #1C2B4A; color: #fff; border: none; padding: 14px 40px; font-size: 15px; font-family: Georgia, serif; cursor: pointer; width: 100%; margin-top: 8px; letter-spacing: 1px; }
.btn:hover { background: #C8A84B; color: #1C2B4A; }
.success { background: #f0f8f0; border-left: 4px solid #2d7a2d; padding: 24px; margin-bottom: 24px; }
.success h2 { color: #2d7a2d; margin-bottom: 8px; }
.error { background: #fff3f3; border-left: 4px solid #c0392b; padding: 16px; margin-bottom: 24px; font-size: 14px; color: #c0392b; }
.divider { border: none; border-top: 1px solid #eee; margin: 28px 0; }
.required { color: #c0392b; }
.likert { display: grid; grid-template-columns: repeat(5, 1fr); gap: 6px; }
.likert input[type=radio] { display: none; }
.likert label { display: flex; flex-direction: column; align-items: center; gap: 4px; cursor: pointer; padding: 10px 4px; border: 1px solid #ddd; font-size: 11px; color: #888; text-align: center; }
.likert label .note { font-size: 20px; font-weight: bold; color: #1C2B4A; }
.likert input[type=radio]:checked + label { border-color: #C8A84B; background: #faf8f2; color: #1C2B4A; }
.likert label:hover { border-color: #C8A84B; }
.scale-labels { display: flex; justify-content: space-between; font-size: 11px; color: #999; margin-top: 4px; }
.footer { font-size: 11px; color: #aaa; text-align: center; margin-top: 32px; border-top: 1px solid #eee; padding-top: 16px; }
@media (max-width: 640px) { .card { padding: 32px 24px; } .likert { grid-template-columns: repeat(5, 1fr); } }
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
      <h2>✓ Merci pour votre avis</h2>
      <p>Votre évaluation a bien été enregistrée. Elle contribue à l'amélioration continue de nos formations.</p>
    </div>
    <p style="font-size:14px;color:#666;">Vous pouvez fermer cette page.</p>

  <?php else: ?>

    <h1>Évaluation de la formation</h1>
    <p class="subtitle">Formation : <span class="formation-name"><?= $course ?></span></p>
    <p class="subtitle">Apprenant : <span class="apprenant-name"><?= $apprenant ?></span></p>
    <p class="intro">Dans le cadre de notre démarche qualité (certification Qualiopi — indicateur 27), nous vous invitons à évaluer la formation suivie par votre collaborateur. Ce questionnaire prend <strong>2 minutes</strong>.</p>

    <?php if ($error): ?>
      <div class="error">Une erreur est survenue. Veuillez réessayer ou contacter <a href="mailto:contact@jaboeuf-avocat.eu">contact@jaboeuf-avocat.eu</a>.</div>
    <?php endif; ?>

    <form method="POST" action="">
      <input type="hidden" name="token" value="<?= $token ?>">

      <!-- Q1 : Objectifs atteints -->
      <div class="question">
        <label class="main">1. Les objectifs de la formation ont-ils été atteints par votre collaborateur ? <span class="required">*</span></label>
        <div class="likert">
          <?php foreach ([1=>'Pas du tout', 2=>'Peu', 3=>'Moyennement', 4=>'Bien', 5=>'Tout à fait'] as $v => $l): ?>
          <input type="radio" name="q1_objectifs_atteints" id="q1_<?=$v?>" value="<?=$v?>" required>
          <label for="q1_<?=$v?>"><span class="note"><?=$v?></span><?=$l?></label>
          <?php endforeach; ?>
        </div>
        <div class="scale-labels"><span>Pas du tout</span><span>Tout à fait</span></div>
      </div>

      <hr class="divider">

      <!-- Q2 : Compétences acquises -->
      <div class="question">
        <label class="main">2. Les compétences acquises sont-elles applicables dans le cadre de son activité professionnelle ? <span class="required">*</span></label>
        <div class="likert">
          <?php foreach ([1=>'Pas du tout', 2=>'Peu', 3=>'Partiellement', 4=>'Bien', 5=>'Tout à fait'] as $v => $l): ?>
          <input type="radio" name="q2_competences_acquises" id="q2_<?=$v?>" value="<?=$v?>" required>
          <label for="q2_<?=$v?>"><span class="note"><?=$v?></span><?=$l?></label>
          <?php endforeach; ?>
        </div>
        <div class="scale-labels"><span>Pas du tout</span><span>Tout à fait</span></div>
      </div>

      <hr class="divider">

      <!-- Q3 : Qualité formation -->
      <div class="question">
        <label class="main">3. Comment évaluez-vous la qualité générale de cette formation ? <span class="required">*</span></label>
        <div class="likert">
          <?php foreach ([1=>'Très mauvaise', 2=>'Mauvaise', 3=>'Correcte', 4=>'Bonne', 5=>'Excellente'] as $v => $l): ?>
          <input type="radio" name="q3_qualite_formation" id="q3_<?=$v?>" value="<?=$v?>" required>
          <label for="q3_<?=$v?>"><span class="note"><?=$v?></span><?=$l?></label>
          <?php endforeach; ?>
        </div>
        <div class="scale-labels"><span>Très mauvaise</span><span>Excellente</span></div>
      </div>

      <hr class="divider">

      <!-- Q4 : Recommandation -->
      <div class="question">
        <label class="main">4. Recommanderiez-vous cette formation à d'autres collaborateurs ou confrères ? <span class="required">*</span></label>
        <div class="likert">
          <?php foreach ([1=>'Certainement pas', 2=>'Probablement pas', 3=>'Peut-être', 4=>'Probablement', 5=>'Certainement'] as $v => $l): ?>
          <input type="radio" name="q4_recommandation" id="q4_<?=$v?>" value="<?=$v?>" required>
          <label for="q4_<?=$v?>"><span class="note"><?=$v?></span><?=$l?></label>
          <?php endforeach; ?>
        </div>
        <div class="scale-labels"><span>Certainement pas</span><span>Certainement</span></div>
      </div>

      <hr class="divider">

      <!-- Q5 : Commentaire -->
      <div class="question">
        <label class="main">5. Commentaires, suggestions ou observations <span style="font-weight:normal;font-size:12px;color:#888;">(facultatif)</span></label>
        <textarea name="q5_commentaire" placeholder="Vos remarques nous aident à améliorer nos formations..."></textarea>
      </div>

      <button type="submit" class="btn">Envoyer mon évaluation →</button>
    </form>

  <?php endif; ?>

  <div class="footer">
    Académie Jaboeuf-Avocat · NDA 11756636475 · contact@jaboeuf-avocat.eu<br>
    Vos données sont traitées conformément au RGPD et à notre <a href="https://jaboeuf-avocat.eu/politique-de-confidentialite" style="color:#C8A84B;">politique de confidentialité</a>.
  </div>

</div>
</body>
</html>
