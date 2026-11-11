/* Reflexo & Soins by MG — script commun à toutes les pages (V3 multi-pages) */
(function () {
  var reduit = window.matchMedia && matchMedia("(prefers-reduced-motion: reduce)").matches;
  var fin = window.matchMedia && matchMedia("(hover:hover) and (pointer:fine)").matches;
  var $ = function (id) { return document.getElementById(id); };
  function esc(t) { return String(t).replace(/[&<>"]/g, function (c) { return { "&": "&amp;", "<": "&lt;", ">": "&gt;", '"': "&quot;" }[c]; }); }

  /* ---------- Menu mobile ---------- */
  var menu = $("menu"), mb = $("menu-btn");
  function fermerMenu() { if (menu) { menu.classList.remove("ouvert"); mb.setAttribute("aria-expanded", "false"); } }
  if (mb) mb.addEventListener("click", function () { var o = menu.classList.toggle("ouvert"); mb.setAttribute("aria-expanded", o ? "true" : "false"); });

  /* ---------- Apparitions au défilement ---------- */
  if (!reduit && "IntersectionObserver" in window) {
    var obs = new IntersectionObserver(function (es) { es.forEach(function (e) { if (e.isIntersecting) { e.target.classList.remove("attente"); obs.unobserve(e.target); } }); }, { rootMargin: "0px 0px -8% 0px", threshold: .05 });
    var n = 0;
    document.querySelectorAll("[data-r]").forEach(function (el) {
      if (el.getBoundingClientRect().top > innerHeight * .92) { el.classList.add("revele", "attente"); el.style.transitionDelay = ((n++ % 4) * .07) + "s"; obs.observe(el); }
    });
  }

  /* ---------- Compteurs ---------- */
  if (!reduit) document.querySelectorAll("[data-compte]").forEach(function (el) {
    var finVal = +el.getAttribute("data-compte"), t0 = null;
    function pas(t) { if (!t0) t0 = t; var k = Math.min(1, (t - t0) / 1500); el.textContent = Math.round(finVal * (1 - Math.pow(1 - k, 3))); if (k < 1) requestAnimationFrame(pas); }
    requestAnimationFrame(pas);
  });

  /* ---------- Horaires : met en gras le jour courant ---------- */
  (function () {
    var d = new Date(), j = d.getDay(), m = d.getHours() * 60 + d.getMinutes();
    document.querySelectorAll('#horaires [data-j="' + j + '"]').forEach(function (x) { x.classList.add("auj"); });
    var ouvert = (j >= 1 && j <= 5 && m >= 510 && m < 1110) || (j === 6 && m >= 540 && m < 900);
    var o = $("ouvert");
    if (o) { o.classList.toggle("ferme", !ouvert); o.lastElementChild.textContent = ouvert ? "Ouvert en ce moment" : "Fermé · lun–ven 8h30–18h30, sam 9h–15h"; }
    document.querySelectorAll(".statut").forEach(function (x) { x.classList.toggle("ferme", !ouvert); x.lastElementChild.textContent = ouvert ? "Ouvert maintenant" : "Fermé en ce moment"; });
  })();

  /* ---------- Accueil : sélecteur de besoin ---------- */
  var choix = $("choix"), rep = $("reponse"), donnees = $("soins-data");
  if (choix && rep && donnees) {
    var D = JSON.parse(donnees.textContent);
    function besoin(k) {
      choix.querySelectorAll("button").forEach(function (b) { b.setAttribute("aria-pressed", b.getAttribute("data-b") === k ? "true" : "false"); });
      rep.innerHTML = D.soins.filter(function (s) { return s.b.indexOf(k) >= 0; }).map(function (s, i) {
        return '<a class="mini" href="' + esc(s.lien) + '" style="animation-delay:' + (i * 0.07) + 's"><img src="' + esc(s.img) + '" alt="" width="64" height="64" loading="lazy"><span><b>' + esc(s.nom) + '</b><small>' + esc(s.duree) + ' · ' + esc(s.dprix) + '</small></span></a>';
      }).join("") + '<a class="mini" href="' + esc(D.tous) + '" style="justify-content:center;background:transparent;box-shadow:inset 0 0 0 1px var(--trait)"><span><b>Voir les 7 soins →</b></span></a>';
    }
    choix.addEventListener("click", function (e) { var b = e.target.closest("button"); if (b) besoin(b.getAttribute("data-b")); });
    besoin("detente");
  }

  /* ---------- Page Soins : filtres + fiche détaillée ---------- */
  var grille = $("grille");
  if (grille) {
    var cartes = grille.querySelectorAll(".soin");
    grille.classList.add("tous"); grille.setAttribute("data-n", cartes.length);
    var filtres = $("filtres");
    if (filtres) filtres.addEventListener("click", function (e) {
      var b = e.target.closest("button"); if (!b) return;
      var f = b.getAttribute("data-f");
      filtres.querySelectorAll("button").forEach(function (x) { x.setAttribute("aria-pressed", x === b ? "true" : "false"); });
      var nb = 0;
      cartes.forEach(function (c, i) {
        var ok = f === "tous" || c.getAttribute("data-b").split(" ").indexOf(f) >= 0;
        if (ok) nb++;
        c.classList.toggle("cache", !ok); c.classList.remove("entre", "attente");
        if (ok && !reduit) { void c.offsetWidth; c.style.animationDelay = (i * 0.04) + "s"; c.classList.add("entre"); }
      });
      grille.classList.toggle("tous", f === "tous"); grille.setAttribute("data-n", nb);
    });

    var pn = $("panneau"), voile = $("voile"), dernierFocus = null;
    function ouvrir(c) {
      if (!c) return;
      dernierFocus = document.activeElement;
      var img = c.querySelector(".im img");
      var im = $("pn-img"); im.src = img.getAttribute("src"); im.alt = img.getAttribute("alt");
      $("pn-titre").innerHTML = c.querySelector("h2").innerHTML;
      $("pn-tags").innerHTML = c.getAttribute("data-tags").split("|").filter(Boolean).map(function (t) { return "<span>" + esc(t) + "</span>"; }).join("");
      $("pn-tarif").innerHTML = '<span class="p">' + esc(c.getAttribute("data-prix")) + "</span><span>" + esc(c.getAttribute("data-duree")) + "</span>";
      $("pn-contenu").innerHTML = c.querySelector(".detail").innerHTML;
      pn.scrollTop = 0; pn.classList.add("on"); voile.classList.add("on"); pn.setAttribute("aria-hidden", "false");
      if (history.replaceState) history.replaceState(null, "", "#" + c.id);
      setTimeout(function () { $("pn-fermer").focus(); }, 50);
    }
    function fermer() {
      if (!pn.classList.contains("on")) return;
      pn.classList.remove("on"); voile.classList.remove("on"); pn.setAttribute("aria-hidden", "true");
      if (history.replaceState) history.replaceState(null, "", location.pathname + location.search);
      if (dernierFocus && dernierFocus.focus) dernierFocus.focus();
    }
    grille.addEventListener("click", function (e) { var c = e.target.closest(".soin"); if (c) ouvrir(c); });
    $("pn-fermer").addEventListener("click", fermer); voile.addEventListener("click", fermer);
    document.addEventListener("keydown", function (e) { if (e.key === "Escape") fermer(); });
    var h = decodeURIComponent((location.hash || "").slice(1));
    if (h && $(h) && $(h).classList.contains("soin")) setTimeout(function () { ouvrir($(h)); }, 350);

    /* cartes inclinées */
    if (fin && !reduit) {
      grille.addEventListener("mousemove", function (e) {
        var c = e.target.closest(".soin"); if (!c) return;
        var r = c.getBoundingClientRect(), x = (e.clientX - r.left) / r.width, y = (e.clientY - r.top) / r.height;
        c.classList.add("incline");
        c.style.setProperty("--ry", ((x - .5) * 10) + "deg"); c.style.setProperty("--rx", ((.5 - y) * 8) + "deg");
        c.style.setProperty("--mx", (x * 100) + "%"); c.style.setProperty("--my", (y * 100) + "%");
      });
      grille.addEventListener("mouseout", function (e) { var c = e.target.closest(".soin"); if (c && !c.contains(e.relatedTarget)) c.classList.remove("incline"); });
    }
  }
  document.addEventListener("keydown", function (e) { if (e.key === "Escape") fermerMenu(); });

  /* ---------- Galerie du cabinet ---------- */
  var dh = $("defile-h");
  if (dh) {
    $("g-prec").addEventListener("click", function () { dh.scrollBy({ left: -dh.clientWidth * .7, behavior: reduit ? "auto" : "smooth" }); });
    $("g-suiv").addEventListener("click", function () { dh.scrollBy({ left: dh.clientWidth * .7, behavior: reduit ? "auto" : "smooth" }); });
  }

  /* ---------- Diplômes : agrandissement ---------- */
  (function () {
    var v = $("dip-vis"), im = $("dip-vis-img"), lg = $("dip-vis-leg"), f = $("dip-fermer"), dern = null;
    if (!v) return;
    function ouvrirD(b) { dern = b; var i = b.querySelector("img"); im.src = i.src; im.alt = i.alt; lg.textContent = i.alt; v.hidden = false; requestAnimationFrame(function () { v.classList.add("on"); }); f.focus(); }
    function fermerD() { v.classList.remove("on"); setTimeout(function () { v.hidden = true; }, 300); if (dern) dern.focus(); }
    document.querySelectorAll(".dip").forEach(function (b) { b.addEventListener("click", function () { ouvrirD(b); }); });
    f.addEventListener("click", fermerD); v.addEventListener("click", function (e) { if (e.target === v) fermerD(); });
    document.addEventListener("keydown", function (e) { if (e.key === "Escape" && !v.hidden) fermerD(); });
  })();

  /* ---------- Chèque cadeau → pré-remplit le formulaire ---------- */
  var cad = $("cadeau-btn");
  if (cad) cad.addEventListener("click", function (e) { e.preventDefault(); $("rf-sujet").value = "Chèque cadeau"; $("rf-nom").focus(); });

  /* ---------- Boutons aimantés ---------- */
  if (fin && !reduit) {
    document.addEventListener("mousemove", function (e) {
      var b = e.target.closest && e.target.closest(".btn");
      document.querySelectorAll(".btn.aimant").forEach(function (x) { if (x !== b) { x.classList.remove("aimant"); x.style.transform = ""; } });
      if (!b) return;
      var r = b.getBoundingClientRect(), dx = e.clientX - (r.left + r.width / 2), dy = e.clientY - (r.top + r.height / 2);
      b.classList.add("aimant"); b.style.transform = "translate(" + dx * .18 + "px," + dy * .3 + "px)";
    }, { passive: true });
  }

  /* ---------- Accueil : citation mot à mot + photo au défilement ---------- */
  var bq = document.querySelector(".citation blockquote"), mots = [];
  if (bq) { bq.innerHTML = bq.textContent.split(" ").map(function (w) { return '<span class="w">' + esc(w) + "</span>"; }).join(" "); mots = bq.querySelectorAll(".w"); }
  var photo = document.querySelector(".hero .photo"), tic = false;
  function defil() {
    tic = false; if (reduit) return;
    var y = scrollY;
    if (photo && innerWidth > 980) { var k = Math.min(1, y / 500); photo.style.setProperty("--ph-r", (220 * (1 - k)) + "px"); var pi = photo.querySelector("img"); pi.style.animation = "none"; pi.style.transform = "scale(" + (1.03 + k * .08) + ") translateY(" + (y * .06) + "px)"; }
    if (mots.length) { var r = bq.getBoundingClientRect(), p = (innerHeight * .85 - r.top) / (innerHeight * .6); var nb = Math.round(Math.max(0, Math.min(1, p)) * mots.length); mots.forEach(function (m, i) { m.classList.toggle("on", i < nb); }); }
  }
  if (photo || mots.length) { addEventListener("scroll", function () { if (!tic) { tic = true; requestAnimationFrame(defil); } }, { passive: true }); defil(); }

  /* ---------- Formulaire de contact (modèle Reflekt) ---------- */
  var form = $("rf-form");
  if (form) {
    var MAQUETTE = form.getAttribute("data-maquette") === "true"; /* "true" dans l'aperçu, "false" sur l'hébergeur */
    var statut = $("rf-statut"), bouton = $("rf-bouton"), texteBouton = bouton.textContent;
    $("rf-affiche").value = Date.now();
    function afficher(type, msg) { statut.className = "rf-statut " + type; statut.textContent = msg; }
    form.addEventListener("submit", function (e) {
      e.preventDefault();
      if (!form.checkValidity()) { afficher("erreur", "Merci de remplir le nom, une adresse e-mail valide, le message et de cocher la case."); form.reportValidity(); return; }
      if (MAQUETTE) { afficher("ok", "Version test : à la mise en ligne, ce message partira directement dans la boîte mail de Marie."); return; }
      bouton.disabled = true; bouton.textContent = "Envoi en cours…"; statut.className = "rf-statut";
      fetch(form.action, { method: "POST", body: new FormData(form), headers: { "X-Requested-With": "fetch" } })
        .then(function (r) { return r.json().catch(function () { return { ok: false, message: "L'envoi n'a pas fonctionné. Merci de réessayer ou de m'appeler." }; }); })
        .then(function (data) { afficher(data.ok ? "ok" : "erreur", data.message); if (data.ok) { form.reset(); $("rf-affiche").value = Date.now(); } })
        .catch(function () { afficher("erreur", "Connexion impossible. Vérifiez votre réseau ou appelez-moi au 06 59 18 56 58."); })
        .finally(function () { bouton.disabled = false; bouton.textContent = texteBouton; });
    });
  }
})();
