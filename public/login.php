<?php
require_once __DIR__ . '/../src/db.php';
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
  $email = $_POST['email'] ?? '';
  $pass = $_POST['password'] ?? '';
  $stmt = db()->prepare("SELECT * FROM users WHERE email = :e LIMIT 1");
  $stmt->execute([':e'=>$email]);
  $u = $stmt->fetch();
  if ($u && password_verify($pass, $u['password_hash'])) {
    $_SESSION['user'] = ['id'=>$u['id'], 'email'=>$u['email'], 'role'=>$u['role']];
    header('Location: /admin.php'); exit;
  }
  $err = 'Identifiants incorrects';
}
?><!doctype html><html lang="fr"><head>
<meta charset="utf-8"/><meta name="viewport" content="width=device-width, initial-scale=1"/>
<title>Connexion - EcoRide</title><script src="https://cdn.tailwindcss.com"></script>
</head><body class="bg-gray-100">
<div class="max-w-md mx-auto mt-20 bg-white p-6 rounded shadow">
  <h1 class="text-2xl font-bold mb-4">Connexion Admin/Employé</h1>
  <?php if (isset($err)): ?><p class="text-red-600 mb-2"><?php echo htmlspecialchars($err);?></p><?php endif; ?>
  <form method="post" class="space-y-3">
    <input name="email" type="email" class="w-full border px-3 py-2 rounded" placeholder="Email" required/>
    <input name="password" type="password" class="w-full border px-3 py-2 rounded" placeholder="Mot de passe" required/>
    <button class="bg-green-700 text-white px-4 py-2 rounded w-full" type="submit">Se connecter</button>
  </form>
</div></body></html>