<?php
require_once __DIR__ . '/../src/db.php';
if (!isset($_SESSION['user']) || $_SESSION['user']['role'] !== 'admin') { header('Location: /login.php'); exit; }
?><!DOCTYPE html><html lang="fr"><head>
<meta charset="UTF-8"/><meta name="viewport" content="width=device-width, initial-scale=1"/>
<title>EcoRide - Admin</title>
<script src="https://cdn.tailwindcss.com"></script>
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
</head><body class="bg-gray-100 text-gray-900">
<header class="bg-green-700 text-white p-4 flex justify-between"><h1 class="font-bold">Administration EcoRide</h1>
<div><span class="mr-3">Connecté: <?php echo htmlspecialchars($_SESSION['user']['email']); ?></span>
<a class="bg-red-600 px-3 py-1 rounded" href="/logout.php">Déconnexion</a></div></header>
<main class="p-4 max-w-6xl mx-auto">
<section class="grid md:grid-cols-3 gap-4 mb-6">
  <div class="bg-white rounded shadow p-4"><h3 class="font-semibold mb-2">Crédits gagnés (total)</h3><p id="credits-total" class="text-3xl font-bold">…</p><p class="text-sm text-gray-500">2 crédits par réservation confirmée</p></div>
  <div class="bg-white rounded shadow p-4 col-span-2"><h3 class="font-semibold mb-2">Actions</h3>
    <form id="create-employee" class="flex gap-2">
      <input class="border px-3 py-2 rounded w-full" name="email" type="email" placeholder="E-mail employé" required/>
      <button class="bg-green-600 text-white px-4 py-2 rounded" type="submit">Créer compte employé</button>
    </form>
  </div>
</section>
<section class="grid md:grid-cols-2 gap-6 mb-6">
  <div class="bg-white rounded shadow p-4"><h3 class="font-semibold mb-2">Covoiturages par jour</h3><canvas id="chart-trips"></canvas></div>
  <div class="bg-white rounded shadow p-4"><h3 class="font-semibold mb-2">Crédits par jour</h3><canvas id="chart-credits"></canvas></div>
</section>
<section class="grid md:grid-cols-2 gap-6">
  <div class="bg-white rounded shadow p-4"><h3 class="font-semibold mb-4">Utilisateurs</h3>
    <table class="w-full text-sm" id="table-users"><thead><tr><th class="text-left">ID</th><th>Email</th><th>Rôle</th><th>Suspension</th><th>Action</th></tr></thead><tbody></tbody></table>
  </div>
  <div class="bg-white rounded shadow p-4"><h3 class="font-semibold mb-4">Trajets</h3>
    <table class="w-full text-sm" id="table-trips"><thead><tr><th>ID</th><th>Chauffeur</th><th>Départ</th><th>Arrivée</th><th>Début</th><th>Places</th></tr></thead><tbody></tbody></table>
  </div>
</section>
</main>
<script>
async function fetchJSON(url, opts={}){
  const res = await fetch(url, Object.assign({headers: {'Accept':'application/json'}}, opts));
  if(!res.ok) throw new Error(await res.text());
  return res.json();
}
async function refresh(){
  const [stats, users, trips] = await Promise.all([
    fetchJSON('/api/admin_stats.php'),
    fetchJSON('/api/users.php'),
    fetchJSON('/api/trips.php')
  ]);
  document.getElementById('credits-total').textContent = stats.total_credits;
  const ctx1 = document.getElementById('chart-trips').getContext('2d');
  const ctx2 = document.getElementById('chart-credits').getContext('2d');
  new Chart(ctx1, {type:'line', data:{labels:stats.by_day.labels, datasets:[{label:'Covoiturages', data:stats.by_day.trips}]}});
  new Chart(ctx2, {type:'bar', data:{labels:stats.by_day.labels, datasets:[{label:'Crédits', data:stats.by_day.credits}]}});
  const tu = document.querySelector('#table-users tbody'); tu.innerHTML='';
  users.forEach(u=>{
    const tr = document.createElement('tr');
    tr.innerHTML = `<td>${u.id}</td><td>${u.email}</td><td>${u.role}</td>
      <td>${u.suspended ? 'Oui' : 'Non'}</td>
      <td><button data-id="${u.id}" data-act="${u.suspended?'unsuspend':'suspend'}" class="px-2 py-1 rounded bg-red-600 text-white">${u.suspended?'Réactiver':'Suspendre'}</button></td>`;
    tu.appendChild(tr);
  });
  const tt = document.querySelector('#table-trips tbody'); tt.innerHTML='';
  trips.forEach(t=>{
    const tr = document.createElement('tr');
    tr.innerHTML = `<td>${t.id}</td><td>${t.driver_email}</td><td>${t.depart}</td><td>${t.arrivee}</td><td>${t.start_datetime}</td><td>${t.places}</td>`;
    tt.appendChild(tr);
  });
}
document.addEventListener('click', async (e)=>{
  if(e.target.matches('button[data-act]')){
    const id = e.target.getAttribute('data-id');
    const act = e.target.getAttribute('data-act');
    await fetchJSON('/api/suspend.php', {method:'POST', headers:{'Content-Type':'application/json'}, body: JSON.stringify({id, act})});
    await refresh();
  }
});
document.getElementById('create-employee').addEventListener('submit', async (e)=>{
  e.preventDefault();
  const email = new FormData(e.target).get('email');
  const res = await fetchJSON('/api/create_employee.php', {method:'POST', headers:{'Content-Type':'application/json'}, body: JSON.stringify({email})});
  alert(`Employé créé:\n${res.email}\nMot de passe: ${res.password}`);
  e.target.reset();
  await refresh();
});
refresh();
</script>
</body></html>