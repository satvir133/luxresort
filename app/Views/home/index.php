<?php $title = 'Luxury Resort — Escape, Indulge, Return Renewed'; ob_start(); ?>

<!-- HERO (video with local fallback image) -->
<section class="hero">
  <?php
    $fallbackImg = url('/assets/img/hero-fallback.jpg'); // put a nice image there
    $videoSrc    = 'https://cdn.coverr.co/videos/coverr-sea-waves-9715/1080p.mp4'; // external
  ?>
  <video id="heroVideo" autoplay muted loop playsinline poster="<?= $fallbackImg ?>">
    <source src="<?= $videoSrc ?>" type="video/mp4">
  </video>
  <img id="heroImg" src="<?= $fallbackImg ?>" alt="Oceanfront view" style="display:none">
  <div class="hero-content">
    <h1 class="text-4xl md:text-6xl font-extrabold leading-tight">Wake up to waves, <span style="display:inline-block;transform:rotate(1deg)">not alarms</span>.</h1>
    <p class="mt-4" style="max-width:720px;color:#f3f4f6">
      Book oceanfront villas, private spa rituals, and chef-led tastings — all from one elegant app.
    </p>
    <div class="hero-cta">
      <a href="<?= url('/login') ?>" class="btn">Login to Book</a>
      <a href="<?= url('/register') ?>" class="btn btn-outline">Create Guest Account</a>
    </div>
    <div class="badge"><span style="width:10px;height:10px;background:#10b981;border-radius:999px;display:inline-block;animation:pulse 2s infinite"></span> Live availability refreshed every 30 seconds.</div>
  </div>
</section>

<script>
// If external video fails (network/CORS), show fallback image.
const v = document.getElementById('heroVideo'), img = document.getElementById('heroImg');
function swapToImage(){ if(v){ v.remove(); } if(img){ img.style.display='block'; } }
if (v) {
  v.addEventListener('error', swapToImage, true);
  v.addEventListener('stalled', swapToImage, true);
  setTimeout(()=>{ if(v.readyState < 2) swapToImage(); }, 1500);
}
</script>

<!-- FEATURES with 3D tilt cards -->
<section id="experiences" class="container" style="padding-top:64px;padding-bottom:64px">
  <h2 class="text-2xl md:text-3xl font-bold">Curated escapes, engineered for ease</h2>
  <p class="text-gray-600 mt-2">Secure payments, real-time inventory, and concierge-grade support.</p>

  <div class="grid grid-3" style="margin-top:24px">
    <?php
      $cards = [
        ['title'=>'Ocean Villas','desc'=>'Sunrise decks • Private plunge pool • Butler-on-call','img'=>'https://images.unsplash.com/photo-1540541338287-41700207dee6?q=80&w=1200'],
        ['title'=>'Spa & Rituals','desc'=>'Aromatherapy • Hot-stone • Ayurvedic programs','img'=>'https://images.unsplash.com/photo-1540555700478-4be289fbecef?q=80&w=1200'],
        ['title'=>'Signature Dining','desc'=>'Chef’s table • Farm-to-table • Sunset grill','img'=>'https://images.unsplash.com/photo-1504674900247-0877df9cc836?q=80&w=1200'],
      ];
      foreach($cards as $c): ?>
      <div class="card tilt-card">
        <img src="<?= $c['img'] ?>" alt="">
        <div class="body">
          <h3 class="font-semibold"><?= htmlspecialchars($c['title']) ?></h3>
          <p class="text-gray-600"><?= htmlspecialchars($c['desc']) ?></p>
          <div class="mt-2" style="font-size:.9rem;color:#666"><span style="display:inline-block;width:8px;height:8px;border-radius:50%;background:#10b981;margin-right:6px"></span>Available this season</div>
        </div>
      </div>
    <?php endforeach; ?>
  </div>
</section>

<!-- MEDIA MIX -->
<section id="rooms" style="background:linear-gradient(#f9fafb, #fff);padding:64px 0">
  <div class="container" style="display:grid;gap:24px;grid-template-columns:1fr;align-items:center">
    <div>
      <h2 class="text-2xl md:text-3xl font-bold">Rooms that breathe with the sea</h2>
      <p class="text-gray-600 mt-2">From minimalist suites to family residences—each layout is crafted for calm, light, and privacy.</p>
      <ul class="mt-3" style="color:#444">
        <li>• Dynamic pricing with best-rate guarantee</li>
        <li>• Accessibility-friendly options</li>
        <li>• Contactless check-in & digital keys*</li>
      </ul>
      <div class="hero-cta">
        <a class="btn" href="<?= url('/register') ?>">Create Account</a>
        <a class="btn btn-outline" href="<?= url('/login') ?>">Login</a>
      </div>
    </div>
    <div class="relative">
      <img style="border-radius:16px;box-shadow:0 12px 48px rgba(0,0,0,.15);max-width:100%" src="https://images.unsplash.com/photo-1505691723518-36a5ac3b2d95?q=80&w=1600" alt="Sea view room">
      <img style="position:relative;top:-24px;left:-24px;width:180px;border-radius:12px;border:4px solid #fff;box-shadow:0 10px 30px rgba(0,0,0,.15)" src="https://media.giphy.com/media/3o6Zt6818fWfT4Z7Di/giphy.gif" alt="Waves gif">
    </div>
  </div>
</section>

<!-- GALLERY -->
<section class="container" style="padding-top:64px;padding-bottom:64px">
  <h2 class="text-2xl md:text-3xl font-bold">Gallery</h2>
  <div class="gallery" style="margin-top:16px">
    <?php foreach([
      'https://images.unsplash.com/photo-1496417263034-38ec4f0b665a?q=80&w=1200',
      'https://images.unsplash.com/photo-1506744038136-46273834b3fb?q=80&w=1200',
      'https://images.unsplash.com/photo-1493558103817-58b2924bce98?q=80&w=1200',
      'https://images.unsplash.com/photo-1460353581641-37baddab0fa2?q=80&w=1200',
      'https://images.unsplash.com/photo-1496412705862-e0088f16f791?q=80&w=1200',
      'https://images.unsplash.com/photo-1496661415325-ef852f9e8e7c?q=80&w=1200',
      'https://images.unsplash.com/photo-1501117716987-c8e1ecb2101f?q=80&w=1200',
      'https://images.unsplash.com/photo-1528909514045-2fa4ac7a08ba?q=80&w=1200',
    ] as $src): ?>
      <img src="<?= $src ?>" alt="">
    <?php endforeach; ?>
  </div>
</section>

<!-- TESTIMONIALS -->
<section style="background:#111;color:#fff;padding:64px 0">
  <div class="container">
    <h2 class="text-2xl md:text-3xl font-bold">Guests who came for a weekend and stayed for a week</h2>
    <div class="grid grid-3" style="margin-top:24px">
      <?php
        $quotes = [
          ['t'=>'“The check-in took 90 seconds. The sunset, an hour.”','n'=>'Aarav P.'],
          ['t'=>'“Breakfast arrived on a floating tray. Unreal.”','n'=>'Mira S.'],
          ['t'=>'“Spa booked in-app; asleep in eucalyptus 20 minutes later.”','n'=>'Noah R.'],
        ];
        foreach($quotes as $q): ?>
        <div class="card" style="background:rgba(255,255,255,.08);border-color:rgba(255,255,255,.15);color:#fff">
          <div class="body">
            <p class="text-lg"><?= $q['t'] ?></p>
            <div style="margin-top:8px;color:#ddd">— <?= $q['n'] ?></div>
          </div>
        </div>
      <?php endforeach; ?>
    </div>
  </div>
</section>

<!-- CONTACT / LEAD FORM -->
<section id="contact" class="container" style="padding-top:64px;padding-bottom:64px">
  <h2 class="text-2xl md:text-3xl font-bold">Plan your perfect stay</h2>
  <p class="text-gray-600 mt-2">Tell us where you want to wake up — we’ll design the rest.</p>

  <form id="leadForm" method="post" action="<?= url('/lead') ?>" style="margin-top:16px;display:grid;gap:12px;grid-template-columns:1fr;max-width:900px">
    <input type="hidden" name="_csrf" value="<?= htmlspecialchars($csrf) ?>">
    <input class="border rounded-lg p-3" type="text" name="name" placeholder="Your name" required minlength="2">
    <input class="border rounded-lg p-3" type="email" name="email" placeholder="Email address" required>
    <button class="btn" type="submit">Request a plan</button>
  </form>

  <p class="text-xs text-gray-500 mt-2">We respect your privacy. No spam, ever.</p>
</section>

<?php $content = ob_get_clean(); include __DIR__.'/../layouts/base.php'; ?>

<script>
// 3D tilt for cards
document.querySelectorAll('.tilt-card').forEach(card => {
  const damp = 18;
  card.addEventListener('mousemove', (e) => {
    const r = card.getBoundingClientRect();
    const x = e.clientX - r.left, y = e.clientY - r.top;
    const rx = ((y / r.height) - 0.5) * -damp;
    const ry = ((x / r.width)  - 0.5) *  damp;
    card.style.transform = `rotateX(${rx}deg) rotateY(${ry}deg)`;
  });
  card.addEventListener('mouseleave', () => card.style.transform = 'rotateX(0) rotateY(0)');
});

// client-side validation toast
const leadForm = document.getElementById('leadForm');
function showToast(msg, ok=false){
  const t = document.createElement('div');
  t.className = 'toast' + (ok?'':' error'); t.textContent = msg;
  document.body.appendChild(t); setTimeout(()=>t.remove(), 2500);
}
if (leadForm) {
  leadForm.addEventListener('submit', (e) => {
    const name  = leadForm.querySelector('input[name="name"]').value.trim();
    const email = leadForm.querySelector('input[name="email"]').value.trim();
    if (name.length < 2 || !/^[^\s@]+@[^\s@]+\.[^\s@]+$/.test(email)) { e.preventDefault(); showToast('Please enter a valid name and email.'); }
  });
}

// flash → toast
<?php
  $err = Session::flash_get('error');
  $ok  = Session::flash_get('success');
  if ($err) echo "showToast(".json_encode($err).", false);\n";
  if ($ok)  echo "showToast(".json_encode($ok).", true);\n";
?>
</script>
