// js/app.js - fonctions AJAX + helpers
const API_ROOT = window.location.origin + '/api';

async function performSearch() {
  const depart = document.getElementById('search-depart').value;
  const arrivee = document.getElementById('search-arrivee').value;
  const date = document.getElementById('search-date').value;
  await searchRides(depart, arrivee, date);
}

function performSearchFromForm() {
  const depart = document.getElementById('depart').value;
  const arrivee = document.getElementById('arrivee').value;
  const date = document.getElementById('date').value;
  const eco = document.getElementById('filter-eco').value;
  const prixMax = document.getElementById('filter-prix').value;
  const noteMin = document.getElementById('filter-note').value;
  const durMax = document.getElementById('filter-duree').value;
  searchRides(depart, arrivee, date, { prixMax, ecologique: eco, noteMin, durationMax: durMax });
}

async function searchRides(depart, arrivee, date, filters = {}) {
  try {
    const params = new URLSearchParams({ depart, arrivee, date, ...filters });
    const res = await fetch(API_ROOT + '/searchRides.php?' + params.toString());
    const json = await res.json();
    if (!json.success) { console.error(json.message); return; }
    displayRides(json.data);
  } catch (e) { console.error(e); }
}

function displayRides(rides) {
  const container = document.getElementById('rides-container') || document.getElementById('mes-voyages-list');
  if(!container) return;
  container.innerHTML = '';
  if (rides.length === 0) {
    container.innerHTML = '<p>Aucun covoiturage trouvé.</p>';
    return;
  }
  rides.forEach(r => {
    const card = document.createElement('div');
    card.className = 'col-md-4 mb-4';
    card.innerHTML = `
      <div class="card h-100">
        <img src="${r.photo || 'src/images/chauffeur.jpg'}" class="card-img-top" alt="Chauffeur">
        <div class="card-body d-flex flex-column">
          <h5 class="card-title">${r.pseudo} ★${r.note_moyenne || '—'}</h5>
          <p class="mb-1">${r.places_restantes} places – ${r.prix}€</p>
          <p class="mb-1">${r.depart_city} → ${r.arrivee_city}</p>
          ${r.ecologique ? '<span class="badge bg-success mb-2">Écologique</span>' : ''}
          <a href="detail.html?id=${r.id}" class="btn btn-outline-success mt-auto">Détails</a>
        </div>
      </div>`;
    container.appendChild(card);
  });
}

// getRideDetails
async function getRideDetails(id) {
  try {
    const res = await fetch(API_ROOT + '/getRideDetails.php?id=' + encodeURIComponent(id));
    const json = await res.json();
    if (!json.success) { console.error(json.message); return; }
    const ride = json.data.ride;
    const reviews = json.data.reviews;
    const info = document.getElementById('ride-info');
    info.innerHTML = `
      <p><strong>Chauffeur :</strong> ${ride.pseudo} ★${ride.note_moyenne || ''}</p>
      <p><strong>Véhicule :</strong> ${ride.marque || ''} ${ride.modele || ''} – ${ride.energie || ''}</p>
      <p><strong>Prix :</strong> ${ride.prix}€</p>
      <p><strong>Départ :</strong> ${ride.depart_city} ${ride.time_depart} → ${ride.arrivee_city} ${ride.time_arrivee}</p>
      <p><strong>Préférences :</strong> ${ride.preferences || 'Aucune'}</p>
      <button class="btn btn-success" onclick="participate(${ride.id}, prompt('Votre userId (test) ?'))">Participer</button>
    `;
    const list = document.getElementById('reviews-list');
    list.innerHTML = '';
    reviews.forEach(rv => {
      const li = document.createElement('li');
      li.className = 'list-group-item';
      li.textContent = rv.comment + ' — ' + rv.pseudo;
      list.appendChild(li);
    });
  } catch (e) { console.error(e); }
}

// participate
async function participate(rideId, userId, seats=1) {
  try {
    const res = await fetch(API_ROOT + '/participate.php', {
      method: 'POST',
      headers: {'Content-Type':'application/json'},
      body: JSON.stringify({ rideId, userId, seats })
    });
    const json = await res.json();
    alert(json.message || 'Réponse reçue');
  } catch (e) { console.error(e); }
}

// auth: createAccount + login
async function createAccount(pseudo, email, password) {
  try {
    const res = await fetch(API_ROOT + '/createAccount.php', {
      method: 'POST', headers:{'Content-Type':'application/json'},
      body: JSON.stringify({ pseudo, email, password })
    });
    const json = await res.json();
    alert(json.message);
  } catch(e){ console.error(e); }
}

async function login(email, password) {
  try {
    const res = await fetch(API_ROOT + '/login.php', {
      method: 'POST', headers:{'Content-Type':'application/json'},
      body: JSON.stringify({ email, password })
    });
    const json = await res.json();
    if (json.success) {
      localStorage.setItem('ecoride_token', json.token);
      localStorage.setItem('ecoride_userId', json.userId);
      alert('Connecté');
      window.location.href = 'espace.html';
    } else {
      alert(json.message);
    }
  } catch(e){ console.error(e); }
}

// createRide (from espace)
async function createRide() {
  const chauffeur_id = localStorage.getItem('ecoride_userId') || 0;
  const depart = document.getElementById('cv-depart').value;
  const arrivee = document.getElementById('cv-arrivee').value;
  const date = document.getElementById('cv-date').value;
  const time_depart = document.getElementById('cv-time-depart').value;
  const places = document.getElementById('cv-places').value;
  const prix = document.getElementById('cv-prix').value;
  const body = { chauffeur_id, depart_city: depart, arrivee_city: arrivee, date_depart: date, time_depart, places, prix };
  const res = await fetch(API_ROOT + '/createRide.php', {
    method: 'POST', headers:{'Content-Type':'application/json'}, body: JSON.stringify(body)
  });
  const json = await res.json();
  alert(json.message);
}

// helper to populate user space
async function loadUserSpace() {
  const token = localStorage.getItem('ecoride_token');
  if (!token) return;
  const res = await fetch(API_ROOT + '/userSpace.php', { headers: { 'Authorization': 'Bearer ' + token } });
  const json = await res.json();
  if (!json.success) { console.error(json.message); return; }
  document.getElementById('credits-amount').textContent = json.data.user.credits + ' crédits';
  const list = document.getElementById('mes-voyages-list');
  if (list) displayRides(json.data.participations || []);
}

// on load of espace.html try to load
if (window.location.pathname.endsWith('espace.html')) {
  loadUserSpace();
}
