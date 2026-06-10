<?php
require_once 'config/config.php';
require_once 'config/helpers.php';

// ── Handle modal form submissions ──────────────────────────────
$modal_errors  = [];
$modal_success = '';
$active_modal  = '';

// Customer Login
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['form_type']) && $_POST['form_type'] === 'customer_login') {
    $active_modal = 'customer_login';
    $username = trim($_POST['username'] ?? '');
    $password = trim($_POST['password'] ?? '');
    if ($username === '' || $password === '') {
        $modal_errors[] = 'Please enter both username and password.';
    } else {
        $result = login_user($pdo, 'customer', $username, $password);
        if ($result['success']) {
            redirect_by_role('customer');
        } else {
            $modal_errors[] = $result['message'];
        }
    }
}

// Customer Register
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['form_type']) && $_POST['form_type'] === 'customer_register') {
    $active_modal = 'customer_register';
    $fullName   = trim($_POST['full_name']   ?? '');
    $icPassport = trim($_POST['ic_passport'] ?? '');
    $gender     = trim($_POST['gender']      ?? '');
    $dob        = trim($_POST['dob']         ?? '');
    $phone      = trim($_POST['phone']       ?? '');
    $email      = trim($_POST['email']       ?? '');
    $address    = trim($_POST['address']     ?? '');
    $uname      = trim($_POST['username']    ?? '');
    $pass       = trim($_POST['password']    ?? '');

    if ($fullName===''||$icPassport===''||$gender===''||$dob===''||$phone===''||$address===''||$uname===''||$pass==='') {
        $modal_errors[] = 'Please fill in all required fields.';
    } elseif ($email !== '' && !filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $modal_errors[] = 'Please enter a valid email address.';
    } else {
        $check = $pdo->prepare("SELECT CustomerID FROM CUSTOMER WHERE Username = ? OR IC_PassportNo = ? LIMIT 1");
        $check->execute([$uname, $icPassport]);
        if ($check->fetch()) {
            $modal_errors[] = 'Username or IC/Passport already exists.';
        } else {
            $stmt = $pdo->prepare("INSERT INTO CUSTOMER (FullName,IC_PassportNo,Gender,DateOfBirth,PhoneNo,Email,Address,Username,Password) VALUES (?,?,?,?,?,?,?,?,?)");
            $stmt->execute([$fullName,$icPassport,$gender,$dob,$phone,$email,$address,$uname,$pass]);
            $modal_success = 'Registration successful! You can now log in.';
            $active_modal  = 'customer_login';
        }
    }
}

// Staff Login
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['form_type']) && $_POST['form_type'] === 'staff_login') {
    $active_modal = 'staff_login';
    $username = trim($_POST['username'] ?? '');
    $password = trim($_POST['password'] ?? '');
    if ($username === '' || $password === '') {
        $modal_errors[] = 'Please enter both username and password.';
    } else {
        $result = login_user($pdo, 'staff', $username, $password);
        if ($result['success']) {
            redirect_by_role('staff');
        } else {
            $modal_errors[] = $result['message'];
        }
    }
}

// Admin Login
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['form_type']) && $_POST['form_type'] === 'admin_login') {
    $active_modal = 'admin_login';
    $username = trim($_POST['username'] ?? '');
    $password = trim($_POST['password'] ?? '');
    if ($username === '' || $password === '') {
        $modal_errors[] = 'Please enter both username and password.';
    } else {
        $result = login_user($pdo, 'admin', $username, $password);
        if ($result['success']) {
            redirect_by_role('admin');
        } else {
            $modal_errors[] = $result['message'];
        }
    }
}

$pageTitle = 'GrandStay Hotel';
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8"/>
  <meta name="viewport" content="width=device-width, initial-scale=1.0"/>
  <title><?php echo h($pageTitle); ?></title>
  <link rel="preconnect" href="https://fonts.googleapis.com">
  <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
  <link href="https://fonts.googleapis.com/css2?family=Playfair+Display:wght@400;600;700&family=Inter:wght@300;400;500;600&display=swap" rel="stylesheet">
  <style>
    *, *::before, *::after { box-sizing: border-box; margin: 0; padding: 0; }

    :root {
      --navy:  #0B1628;
      --navy2: #162038;
      --slate: #2A3A52;
      --gold:  #C9A84C;
      --gold2: #e6c96e;
      --cream: #F0EBE1;
      --muted: #9AA5B4;
      --white: #ffffff;
      --error: #e07070;
      --ok:    #6fcf97;
    }

    html { scroll-behavior: smooth; }
    body { font-family: 'Inter', sans-serif; background: var(--navy); color: var(--cream); overflow-x: hidden; }

    /* ── Navbar ── */
    .navbar {
      position: fixed; top: 0; left: 0; right: 0; z-index: 200;
      display: flex; align-items: center; justify-content: space-between;
      padding: 1.1rem 5vw;
      background: rgba(11,22,40,0.88); backdrop-filter: blur(14px);
      border-bottom: 1px solid rgba(201,168,76,0.15);
    }
    .navbar-brand { font-family:'Playfair Display',serif; font-size:1.25rem; font-weight:700; color:var(--gold); text-decoration:none; letter-spacing:.04em; }
    .navbar-brand span { color:var(--cream); font-weight:400; }
    .navbar-links { display:flex; gap:1.6rem; list-style:none; align-items:center; }
    .navbar-links a { color:var(--muted); text-decoration:none; font-size:.82rem; font-weight:500; letter-spacing:.07em; text-transform:uppercase; transition:color .2s; }
    .navbar-links a:hover { color:var(--gold); }
    .navbar-links .nav-btn {
      border:1px solid rgba(201,168,76,.5); color:var(--gold); padding:.4rem 1.1rem;
      border-radius:3px; font-size:.78rem; letter-spacing:.07em; text-transform:uppercase;
      text-decoration:none; font-weight:600; transition:all .2s; cursor:pointer;
      background:transparent;
    }
    .navbar-links .nav-btn:hover { background:var(--gold); color:var(--navy); border-color:var(--gold); }

    /* ── Session banner ── */
    .session-banner {
      position:fixed; top:68px; left:50%; transform:translateX(-50%); z-index:199;
      background:rgba(201,168,76,.12); border:1px solid var(--gold); color:var(--gold2);
      padding:.5rem 1.4rem; border-radius:50px; font-size:.8rem; white-space:nowrap;
      animation:fadeDown .4s ease;
    }
    @keyframes fadeDown {
      from { opacity:0; transform:translateX(-50%) translateY(-8px); }
      to   { opacity:1; transform:translateX(-50%) translateY(0); }
    }

    /* ── Hero ── */
    .hero {
      min-height:100vh; display:flex; flex-direction:column;
      align-items:center; justify-content:center; text-align:center;
      padding:8rem 2rem 5rem; position:relative; overflow:hidden;
    }
    .hero::before {
      content:''; position:absolute; inset:0;
      background: radial-gradient(ellipse 80% 60% at 50% 30%, rgba(201,168,76,.08) 0%, transparent 70%),
                  radial-gradient(ellipse 60% 40% at 20% 80%, rgba(42,58,82,.55) 0%, transparent 60%);
      pointer-events:none;
    }
    .hero-rule { display:flex; align-items:center; gap:1rem; margin-bottom:1.8rem; }
    .hero-rule::before,.hero-rule::after { content:''; height:1px; width:55px; background:var(--gold); opacity:.5; }
    .hero-eyebrow { font-size:.7rem; letter-spacing:.22em; text-transform:uppercase; color:var(--gold); font-weight:500; }
    .hero h1 { font-family:'Playfair Display',serif; font-size:clamp(2.6rem,7vw,5.2rem); font-weight:700; line-height:1.1; color:var(--cream); margin-bottom:.35em; }
    .hero h1 .shimmer {
      background:linear-gradient(90deg,var(--gold) 0%,#f5e09a 40%,var(--gold) 60%,#b8922e 100%);
      background-size:200% auto; -webkit-background-clip:text; -webkit-text-fill-color:transparent; background-clip:text;
      animation:shimmer 3.5s linear infinite;
    }
    @keyframes shimmer { to { background-position:200% center; } }
    .hero-sub { font-size:clamp(.9rem,2vw,1.1rem); color:var(--muted); max-width:480px; line-height:1.75; margin-bottom:2.4rem; font-weight:300; }
    .hero-cta-group { display:flex; gap:1rem; flex-wrap:wrap; justify-content:center; }
    .btn-primary { background:var(--gold); color:var(--navy); padding:.8rem 2rem; border-radius:3px; text-decoration:none; font-size:.85rem; font-weight:600; letter-spacing:.06em; text-transform:uppercase; transition:background .2s,transform .15s; border:none; cursor:pointer; }
    .btn-primary:hover { background:var(--gold2); transform:translateY(-2px); }
    .btn-outline { border:1px solid rgba(201,168,76,.5); color:var(--gold); padding:.8rem 2rem; border-radius:3px; text-decoration:none; font-size:.85rem; font-weight:500; letter-spacing:.06em; text-transform:uppercase; transition:all .2s; cursor:pointer; background:transparent; }
    .btn-outline:hover { border-color:var(--gold); color:var(--gold2); transform:translateY(-2px); }
    .scroll-hint { position:absolute; bottom:2.2rem; left:50%; transform:translateX(-50%); display:flex; flex-direction:column; align-items:center; gap:.4rem; color:var(--muted); font-size:.68rem; letter-spacing:.12em; text-transform:uppercase; opacity:.55; animation:bounce 2s ease-in-out infinite; }
    .scroll-hint svg { width:16px; height:16px; }
    @keyframes bounce { 0%,100%{transform:translateX(-50%) translateY(0)}50%{transform:translateX(-50%) translateY(6px)} }

    /* ── Stats ── */
    .stats { background:var(--navy2); border-top:1px solid rgba(201,168,76,.12); border-bottom:1px solid rgba(201,168,76,.12); display:flex; justify-content:center; flex-wrap:wrap; }
    .stat-item { padding:2rem 3rem; text-align:center; border-right:1px solid rgba(201,168,76,.1); flex:1; min-width:140px; }
    .stat-item:last-child { border-right:none; }
    .stat-number { font-family:'Playfair Display',serif; font-size:2rem; font-weight:700; color:var(--gold); line-height:1; }
    .stat-label { font-size:.7rem; letter-spacing:.14em; text-transform:uppercase; color:var(--muted); margin-top:.4rem; }

    /* ── Section shared ── */
    .section { padding:5.5rem 5vw; }
    .section-header { text-align:center; margin-bottom:3rem; }
    .section-eyebrow { font-size:.68rem; letter-spacing:.22em; text-transform:uppercase; color:var(--gold); font-weight:500; margin-bottom:.7rem; }
    .section-title { font-family:'Playfair Display',serif; font-size:clamp(1.7rem,4vw,2.5rem); color:var(--cream); font-weight:600; line-height:1.2; }

    /* ── Portal cards ── */
    .modules { background:var(--navy2); }
    .modules-grid { display:grid; grid-template-columns:repeat(auto-fit,minmax(280px,1fr)); gap:1.5px; border:1.5px solid rgba(201,168,76,.15); border-radius:5px; overflow:hidden; max-width:1100px; margin:0 auto; }
    .module-card { background:var(--slate); padding:2.6rem 2.2rem; display:flex; flex-direction:column; gap:.9rem; position:relative; overflow:hidden; transition:background .25s; }
    .module-card::before { content:''; position:absolute; top:0; left:0; right:0; height:2px; background:var(--gold); transform:scaleX(0); transform-origin:left; transition:transform .3s ease; }
    .module-card:hover { background:#304460; }
    .module-card:hover::before { transform:scaleX(1); }
    .module-icon { width:42px; height:42px; background:rgba(201,168,76,.1); border-radius:50%; display:flex; align-items:center; justify-content:center; color:var(--gold); flex-shrink:0; }
    .module-icon svg { width:19px; height:19px; }
    .module-tag { font-size:.63rem; letter-spacing:.18em; text-transform:uppercase; color:var(--gold); font-weight:600; }
    .module-card h3 { font-family:'Playfair Display',serif; font-size:1.4rem; font-weight:600; color:var(--cream); }
    .module-card p { font-size:.86rem; color:var(--muted); line-height:1.65; flex:1; }
    .module-features { list-style:none; display:flex; flex-direction:column; gap:.4rem; margin-bottom:.3rem; }
    .module-features li { font-size:.79rem; color:var(--muted); display:flex; align-items:center; gap:.5rem; }
    .module-features li::before { content:''; width:5px; height:5px; border-radius:50%; background:var(--gold); opacity:.6; flex-shrink:0; }
    .module-actions { display:flex; gap:.7rem; flex-wrap:wrap; margin-top:.4rem; }
    .module-link { display:inline-flex; align-items:center; gap:.45rem; font-size:.78rem; font-weight:600; letter-spacing:.08em; text-transform:uppercase; color:var(--gold); text-decoration:none; background:none; border:none; cursor:pointer; padding:0; transition:gap .2s,color .2s; font-family:'Inter',sans-serif; }
    .module-link:hover { gap:.8rem; color:var(--gold2); }
    .module-link svg { width:13px; height:13px; }
    .module-link.secondary { color:var(--muted); }
    .module-link.secondary:hover { color:var(--cream); }

    /* ── Features ── */
    .features-grid { display:grid; grid-template-columns:repeat(auto-fit,minmax(210px,1fr)); gap:2rem; max-width:980px; margin:0 auto; }
    .feature-item { display:flex; flex-direction:column; gap:.75rem; }
    .feature-line { width:30px; height:2px; background:var(--gold); opacity:.7; }
    .feature-item h4 { font-size:.92rem; font-weight:600; color:var(--cream); }
    .feature-item p { font-size:.8rem; color:var(--muted); line-height:1.65; }

    /* ── Footer ── */
    .site-footer { background:#07101e; border-top:1px solid rgba(201,168,76,.12); padding:2.2rem 5vw; display:flex; align-items:center; justify-content:space-between; flex-wrap:wrap; gap:1rem; }
    .footer-brand { font-family:'Playfair Display',serif; font-size:.95rem; color:var(--gold); }
    .footer-note { font-size:.72rem; color:var(--muted); }
    .footer-note a { color:var(--gold); text-decoration:none; }

    /* ── Modal overlay ── */
    .modal-overlay {
      position:fixed; inset:0; z-index:500;
      background:rgba(7,16,30,.82); backdrop-filter:blur(6px);
      display:flex; align-items:center; justify-content:center;
      padding:1rem;
      opacity:0; pointer-events:none; transition:opacity .25s;
    }
    .modal-overlay.open { opacity:1; pointer-events:all; }
    .modal {
      background:var(--navy2); border:1px solid rgba(201,168,76,.2);
      border-radius:6px; width:100%; max-width:460px;
      max-height:90vh; overflow-y:auto;
      padding:2.4rem 2.2rem;
      transform:translateY(18px); transition:transform .28s ease;
      position:relative;
    }
    .modal-overlay.open .modal { transform:translateY(0); }
    .modal-close {
      position:absolute; top:1rem; right:1.1rem;
      background:none; border:none; color:var(--muted); font-size:1.4rem;
      cursor:pointer; line-height:1; transition:color .2s;
    }
    .modal-close:hover { color:var(--cream); }
    .modal-eyebrow { font-size:.65rem; letter-spacing:.2em; text-transform:uppercase; color:var(--gold); font-weight:600; margin-bottom:.6rem; }
    .modal h2 { font-family:'Playfair Display',serif; font-size:1.6rem; font-weight:600; color:var(--cream); margin-bottom:1.4rem; }
    .modal-tabs { display:flex; gap:0; margin-bottom:1.6rem; border-bottom:1px solid rgba(201,168,76,.18); }
    .modal-tab { background:none; border:none; padding:.6rem 1.1rem; font-size:.8rem; font-weight:600; letter-spacing:.07em; text-transform:uppercase; color:var(--muted); cursor:pointer; border-bottom:2px solid transparent; margin-bottom:-1px; transition:color .2s,border-color .2s; font-family:'Inter',sans-serif; }
    .modal-tab.active { color:var(--gold); border-bottom-color:var(--gold); }

    /* ── Forms inside modal ── */
    .form-section { display:none; }
    .form-section.active { display:block; }
    .form-group { display:flex; flex-direction:column; gap:.4rem; margin-bottom:1rem; }
    .form-group label { font-size:.78rem; font-weight:500; color:var(--muted); letter-spacing:.04em; }
    .form-group input,
    .form-group select {
      background:rgba(255,255,255,.05); border:1px solid rgba(201,168,76,.2);
      color:var(--cream); padding:.65rem .85rem; border-radius:3px; font-size:.88rem;
      font-family:'Inter',sans-serif; transition:border-color .2s;
      appearance:none; -webkit-appearance:none;
    }
    .form-group input:focus,
    .form-group select:focus { outline:none; border-color:var(--gold); }
    .form-group select option { background:var(--navy2); color:var(--cream); }
    .form-row { display:grid; grid-template-columns:1fr 1fr; gap:.8rem; }
    .modal-submit { width:100%; margin-top:.5rem; padding:.85rem; font-size:.88rem; }
    .modal-footer-link { text-align:center; margin-top:1rem; font-size:.8rem; color:var(--muted); }
    .modal-footer-link button { background:none; border:none; color:var(--gold); cursor:pointer; font-size:.8rem; font-family:'Inter',sans-serif; text-decoration:underline; }
    .modal-footer-link button:hover { color:var(--gold2); }
    .alert-error { background:rgba(224,112,112,.12); border:1px solid var(--error); color:var(--error); padding:.7rem 1rem; border-radius:3px; font-size:.82rem; margin-bottom:1rem; }
    .alert-success { background:rgba(111,207,151,.12); border:1px solid var(--ok); color:var(--ok); padding:.7rem 1rem; border-radius:3px; font-size:.82rem; margin-bottom:1rem; }
    .sample-creds { background:rgba(255,255,255,.04); border:1px solid rgba(201,168,76,.12); border-radius:3px; padding:.8rem 1rem; margin-top:1.2rem; font-size:.76rem; color:var(--muted); line-height:1.8; }
    .sample-creds strong { color:var(--cream); }

    /* ── Scroll reveal ── */
    .reveal { opacity:0; transform:translateY(22px); transition:opacity .5s ease,transform .5s ease; }
    .reveal.visible { opacity:1; transform:translateY(0); }

    @media (max-width:640px) {
      .navbar-links .hide-mobile { display:none; }
      .stat-item { padding:1.5rem 1.2rem; }
      .module-card { padding:2rem 1.5rem; }
      .form-row { grid-template-columns:1fr; }
      .site-footer { flex-direction:column; text-align:center; }
    }
    @media (prefers-reduced-motion:reduce) {
      .reveal,.shimmer,.scroll-hint { animation:none; transition:none; }
      .reveal { opacity:1; transform:none; }
    }
  </style>
</head>
<body>

<?php
// Which modal should open on page load (after a failed POST)
$open_modal = '';
if ($active_modal === 'customer_login' || $active_modal === 'customer_register') $open_modal = 'customer';
elseif ($active_modal === 'staff_login') $open_modal = 'staff';
elseif ($active_modal === 'admin_login') $open_modal = 'admin';
?>

<!-- ── Navbar ───────────────────────────────────────────────── -->
<nav class="navbar">
  <a class="navbar-brand" href="index.php">Grand<span>Stay</span></a>
  <ul class="navbar-links">
    <li class="hide-mobile"><a href="#portals">Portals</a></li>
    <li class="hide-mobile"><a href="#features">Features</a></li>
    <?php if (isset($_SESSION['role'])): ?>
      <li><a href="logout.php" class="nav-btn">Sign Out</a></li>
    <?php else: ?>
      <li><button class="nav-btn" onclick="openModal('customer')">Guest Login</button></li>
    <?php endif; ?>
  </ul>
</nav>

<?php if (isset($_SESSION['role'])): ?>
<div class="session-banner">
  Signed in as <strong><?php echo h($_SESSION['display_name']); ?></strong> &mdash; <?php echo ucfirst(h($_SESSION['role'])); ?>
</div>
<?php endif; ?>

<!-- ── Hero ─────────────────────────────────────────────────── -->
<section class="hero">
  <div class="hero-rule"><span class="hero-eyebrow">Welcome to GrandStay</span></div>
  <h1>Where Every Stay<br>Becomes a <span class="shimmer">Memory</span></h1>
  <p class="hero-sub">Book rooms, manage reservations, and handle invoices — all in one place for guests, staff, and administrators.</p>
  <div class="hero-cta-group">
    <button class="btn-primary" onclick="openModal('customer')">Book a Room</button>
    <a href="#portals" class="btn-outline">Explore Portals</a>
  </div>
  <div class="scroll-hint">
    <svg fill="none" stroke="currentColor" stroke-width="1.5" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M19 9l-7 7-7-7"/></svg>
  </div>
</section>

<!-- ── Stats ─────────────────────────────────────────────────── -->
<div class="stats reveal">
  <div class="stat-item"><div class="stat-number">3</div><div class="stat-label">User Roles</div></div>
  <div class="stat-item"><div class="stat-number">100%</div><div class="stat-label">PHP &amp; MySQL</div></div>
  <div class="stat-item"><div class="stat-number">∞</div><div class="stat-label">Room Types</div></div>
  <div class="stat-item"><div class="stat-number">24/7</div><div class="stat-label">Reservations</div></div>
</div>

<!-- ── Portals ───────────────────────────────────────────────── -->
<section class="section modules" id="portals">
  <div class="section-header reveal">
    <p class="section-eyebrow">Access Portals</p>
    <h2 class="section-title">Three Roles, One System</h2>
  </div>

  <div class="modules-grid">

    <!-- Customer -->
    <div class="module-card reveal">
      <div class="module-icon">
        <svg fill="none" stroke="currentColor" stroke-width="1.5" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M15.75 6a3.75 3.75 0 11-7.5 0 3.75 3.75 0 017.5 0zM4.501 20.118a7.5 7.5 0 0114.998 0A17.933 17.933 0 0112 21.75c-2.676 0-5.216-.584-7.499-1.632z"/></svg>
      </div>
      <span class="module-tag">Guest</span>
      <h3>Customer Portal</h3>
      <p>Register a new account or log in to browse rooms, make reservations, and view your invoices.</p>
      <ul class="module-features">
        <li>Register &amp; log in securely</li>
        <li>Browse &amp; filter room types</li>
        <li>Make &amp; cancel reservations</li>
        <li>View invoices &amp; history</li>
      </ul>
      <div class="module-actions">
        <button class="module-link" onclick="openModal('customer','customer_login')">
          Log In <svg fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M13.5 4.5L21 12m0 0l-7.5 7.5M21 12H3"/></svg>
        </button>
        <button class="module-link secondary" onclick="openModal('customer','customer_register')">Register</button>
      </div>
    </div>

    <!-- Staff -->
    <div class="module-card reveal" style="transition-delay:.1s">
      <div class="module-icon">
        <svg fill="none" stroke="currentColor" stroke-width="1.5" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M11.42 15.17L17.25 21A2.652 2.652 0 0021 17.25l-5.877-5.877M11.42 15.17l2.496-3.03c.317-.384.74-.626 1.208-.766M11.42 15.17l-4.655 5.653a2.548 2.548 0 11-3.586-3.586l5.654-4.654m0 0l3.029-2.497c.14-.468.38-.891.766-1.208"/></svg>
      </div>
      <span class="module-tag">Operations</span>
      <h3>Staff Portal</h3>
      <p>Handle day-to-day front desk operations — check guests in and out, manage reservations and invoices.</p>
      <ul class="module-features">
        <li>View all reservations</li>
        <li>Check-in &amp; check-out guests</li>
        <li>Update invoice status</li>
        <li>Search customer records</li>
      </ul>
      <div class="module-actions">
        <button class="module-link" onclick="openModal('staff')">
          Staff Login <svg fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M13.5 4.5L21 12m0 0l-7.5 7.5M21 12H3"/></svg>
        </button>
      </div>
    </div>

    <!-- Admin -->
    <div class="module-card reveal" style="transition-delay:.2s">
      <div class="module-icon">
        <svg fill="none" stroke="currentColor" stroke-width="1.5" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M9 12.75L11.25 15 15 9.75m-3-7.036A11.959 11.959 0 013.598 6 11.99 11.99 0 003 9.749c0 5.592 3.824 10.29 9 11.623 5.176-1.332 9-6.03 9-11.622 0-1.31-.21-2.571-.598-3.751h-.152c-3.196 0-6.1-1.248-8.25-3.285z"/></svg>
      </div>
      <span class="module-tag">Management</span>
      <h3>Admin Portal</h3>
      <p>Full system control — manage rooms, room types, staff accounts, customer records and reports.</p>
      <ul class="module-features">
        <li>Room &amp; room type management</li>
        <li>Staff &amp; customer accounts</li>
        <li>System-wide reports</li>
        <li>Full reservation oversight</li>
      </ul>
      <div class="module-actions">
        <button class="module-link" onclick="openModal('admin')">
          Admin Login <svg fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M13.5 4.5L21 12m0 0l-7.5 7.5M21 12H3"/></svg>
        </button>
      </div>
    </div>

  </div>
</section>

<!-- ── Features ─────────────────────────────────────────────── -->
<section class="section" id="features">
  <div class="section-header reveal">
    <p class="section-eyebrow">Built-in Capabilities</p>
    <h2 class="section-title">Everything You Need</h2>
  </div>
  <div class="features-grid">
    <div class="feature-item reveal">
      <div class="feature-line"></div>
      <h4>Role-Based Access</h4>
      <p>Separate dashboards for customers, staff, and admins — each locked to the right permissions.</p>
    </div>
    <div class="feature-item reveal" style="transition-delay:.07s">
      <div class="feature-line"></div>
      <h4>Reservation Engine</h4>
      <p>Real-time room availability, date-based booking, and automatic status tracking end-to-end.</p>
    </div>
    <div class="feature-item reveal" style="transition-delay:.14s">
      <div class="feature-line"></div>
      <h4>Invoice Management</h4>
      <p>Auto-generated invoices per reservation with payment status managed by staff at checkout.</p>
    </div>
    <div class="feature-item reveal" style="transition-delay:.21s">
      <div class="feature-line"></div>
      <h4>Room Catalogue</h4>
      <p>Admin-managed room types and rooms with pricing, capacity, and live availability settings.</p>
    </div>
  </div>
</section>

<!-- ── Footer ───────────────────────────────────────────────── -->
<footer class="site-footer">
  <div class="footer-brand">GrandStay</div>
  <div class="footer-note">Diploma Project &mdash; PHP &amp; MySQL &mdash; <a href="logout.php">Sign Out</a></div>
</footer>

<!-- ════════════════════════════════════════════════════════════
     MODAL: Customer (Login + Register tabs)
     ════════════════════════════════════════════════════════════ -->
<div class="modal-overlay" id="modal-customer" role="dialog" aria-modal="true" aria-label="Customer Portal">
  <div class="modal">
    <button class="modal-close" onclick="closeModal('customer')" aria-label="Close">&times;</button>
    <p class="modal-eyebrow">Guest Access</p>
    <h2>Customer Portal</h2>

    <div class="modal-tabs">
      <button class="modal-tab" id="tab-customer_login"    onclick="switchTab('customer','customer_login')">Login</button>
      <button class="modal-tab" id="tab-customer_register" onclick="switchTab('customer','customer_register')">Register</button>
    </div>

    <!-- Login tab -->
    <div class="form-section" id="section-customer_login">
      <?php if ($active_modal==='customer_login' && $modal_errors): ?>
        <div class="alert-error"><?php foreach($modal_errors as $e) echo h($e).'<br>'; ?></div>
      <?php endif; ?>
      <?php if ($active_modal==='customer_login' && $modal_success): ?>
        <div class="alert-success"><?php echo h($modal_success); ?></div>
      <?php endif; ?>
      <form method="POST" action="index.php#portals">
        <input type="hidden" name="form_type" value="customer_login">
        <div class="form-group">
          <label for="cl_username">Username</label>
          <input type="text" id="cl_username" name="username" placeholder="Enter your username" required
            value="<?php echo ($active_modal==='customer_login') ? h($_POST['username']??'') : ''; ?>">
        </div>
        <div class="form-group">
          <label for="cl_password">Password</label>
          <input type="password" id="cl_password" name="password" placeholder="Enter your password" required>
        </div>
        <button type="submit" class="btn-primary modal-submit">Login</button>
      </form>
      <div class="sample-creds">Sample: <strong>adief01</strong> / <strong>customer123</strong></div>
      <div class="modal-footer-link">
        No account? <button onclick="switchTab('customer','customer_register')">Register here</button>
      </div>
    </div>

    <!-- Register tab -->
    <div class="form-section" id="section-customer_register">
      <?php if ($active_modal==='customer_register' && $modal_errors): ?>
        <div class="alert-error"><?php foreach($modal_errors as $e) echo h($e).'<br>'; ?></div>
      <?php endif; ?>
      <form method="POST" action="index.php#portals">
        <input type="hidden" name="form_type" value="customer_register">
        <div class="form-group">
          <label>Full Name</label>
          <input type="text" name="full_name" placeholder="Your full name" required
            value="<?php echo ($active_modal==='customer_register') ? h($_POST['full_name']??'') : ''; ?>">
        </div>
        <div class="form-row">
          <div class="form-group">
            <label>IC / Passport No</label>
            <input type="text" name="ic_passport" placeholder="e.g. 990101-14-1234" required
              value="<?php echo ($active_modal==='customer_register') ? h($_POST['ic_passport']??'') : ''; ?>">
          </div>
          <div class="form-group">
            <label>Gender</label>
            <select name="gender" required>
              <option value="">Select</option>
              <option value="Male"   <?php echo (($active_modal==='customer_register')&&($_POST['gender']??'')==='Male')   ?'selected':'';?>>Male</option>
              <option value="Female" <?php echo (($active_modal==='customer_register')&&($_POST['gender']??'')==='Female') ?'selected':'';?>>Female</option>
            </select>
          </div>
        </div>
        <div class="form-row">
          <div class="form-group">
            <label>Date of Birth</label>
            <input type="date" name="dob" required
              value="<?php echo ($active_modal==='customer_register') ? h($_POST['dob']??'') : ''; ?>">
          </div>
          <div class="form-group">
            <label>Phone Number</label>
            <input type="text" name="phone" placeholder="01x-xxxxxxx" required
              value="<?php echo ($active_modal==='customer_register') ? h($_POST['phone']??'') : ''; ?>">
          </div>
        </div>
        <div class="form-group">
          <label>Email <span style="color:var(--muted);font-size:.7rem">(optional)</span></label>
          <input type="email" name="email" placeholder="your@email.com"
            value="<?php echo ($active_modal==='customer_register') ? h($_POST['email']??'') : ''; ?>">
        </div>
        <div class="form-group">
          <label>Address</label>
          <input type="text" name="address" placeholder="Full address" required
            value="<?php echo ($active_modal==='customer_register') ? h($_POST['address']??'') : ''; ?>">
        </div>
        <div class="form-row">
          <div class="form-group">
            <label>Username</label>
            <input type="text" name="username" placeholder="Choose a username" required
              value="<?php echo ($active_modal==='customer_register') ? h($_POST['username']??'') : ''; ?>">
          </div>
          <div class="form-group">
            <label>Password</label>
            <input type="password" name="password" placeholder="Choose a password" required>
          </div>
        </div>
        <button type="submit" class="btn-primary modal-submit">Create Account</button>
      </form>
      <div class="modal-footer-link">
        Already have an account? <button onclick="switchTab('customer','customer_login')">Log in</button>
      </div>
    </div>
  </div>
</div>

<!-- ════════════════════════════════════════════════════════════
     MODAL: Staff Login
     ════════════════════════════════════════════════════════════ -->
<div class="modal-overlay" id="modal-staff" role="dialog" aria-modal="true" aria-label="Staff Login">
  <div class="modal">
    <button class="modal-close" onclick="closeModal('staff')" aria-label="Close">&times;</button>
    <p class="modal-eyebrow">Operations Access</p>
    <h2>Staff Login</h2>
    <?php if ($active_modal==='staff_login' && $modal_errors): ?>
      <div class="alert-error"><?php foreach($modal_errors as $e) echo h($e).'<br>'; ?></div>
    <?php endif; ?>
    <form method="POST" action="index.php#portals">
      <input type="hidden" name="form_type" value="staff_login">
      <div class="form-group">
        <label for="sl_username">Staff Username</label>
        <input type="text" id="sl_username" name="username" placeholder="Enter staff username" required
          value="<?php echo ($active_modal==='staff_login') ? h($_POST['username']??'') : ''; ?>">
      </div>
      <div class="form-group">
        <label for="sl_password">Password</label>
        <input type="password" id="sl_password" name="password" placeholder="Enter password" required>
      </div>
      <button type="submit" class="btn-primary modal-submit">Login as Staff</button>
    </form>
    <div class="sample-creds">Sample: <strong>siti_staff</strong> / <strong>staff123</strong></div>
  </div>
</div>

<!-- ════════════════════════════════════════════════════════════
     MODAL: Admin Login
     ════════════════════════════════════════════════════════════ -->
<div class="modal-overlay" id="modal-admin" role="dialog" aria-modal="true" aria-label="Admin Login">
  <div class="modal">
    <button class="modal-close" onclick="closeModal('admin')" aria-label="Close">&times;</button>
    <p class="modal-eyebrow">System Management</p>
    <h2>Admin Login</h2>
    <?php if ($active_modal==='admin_login' && $modal_errors): ?>
      <div class="alert-error"><?php foreach($modal_errors as $e) echo h($e).'<br>'; ?></div>
    <?php endif; ?>
    <form method="POST" action="index.php#portals">
      <input type="hidden" name="form_type" value="admin_login">
      <div class="form-group">
        <label for="al_username">Admin Username</label>
        <input type="text" id="al_username" name="username" placeholder="Enter admin username" required
          value="<?php echo ($active_modal==='admin_login') ? h($_POST['username']??'') : ''; ?>">
      </div>
      <div class="form-group">
        <label for="al_password">Password</label>
        <input type="password" id="al_password" name="password" placeholder="Enter password" required>
      </div>
      <button type="submit" class="btn-primary modal-submit">Login as Admin</button>
    </form>
    <div class="sample-creds">Sample: <strong>hotel_admin</strong> / <strong>admin123</strong></div>
  </div>
</div>

<!-- ── Scripts ───────────────────────────────────────────────── -->
<script>
  // ── Scroll reveal
  const observer = new IntersectionObserver(entries => {
    entries.forEach(e => { if (e.isIntersecting) { e.target.classList.add('visible'); observer.unobserve(e.target); } });
  }, { threshold: 0.1 });
  document.querySelectorAll('.reveal').forEach(el => observer.observe(el));

  // ── Modal helpers
  function openModal(name, tab) {
    document.querySelectorAll('.modal-overlay').forEach(m => m.classList.remove('open'));
    const overlay = document.getElementById('modal-' + name);
    if (!overlay) return;
    overlay.classList.add('open');
    document.body.style.overflow = 'hidden';
    if (tab) switchTab(name, tab);
    else {
      // activate first tab if tabbed modal
      const firstTab = overlay.querySelector('.modal-tab');
      if (firstTab) {
        const firstId = firstTab.id.replace('tab-', '');
        switchTab(name, firstId);
      } else {
        // non-tabbed modal, just show first form-section
        const sec = overlay.querySelector('.form-section');
        if (sec) sec.classList.add('active');
      }
    }
  }

  function closeModal(name) {
    const overlay = document.getElementById('modal-' + name);
    if (overlay) overlay.classList.remove('open');
    document.body.style.overflow = '';
  }

  function switchTab(modalName, tabId) {
    const overlay = document.getElementById('modal-' + modalName);
    if (!overlay) return;
    overlay.querySelectorAll('.modal-tab').forEach(t => t.classList.toggle('active', t.id === 'tab-' + tabId));
    overlay.querySelectorAll('.form-section').forEach(s => s.classList.toggle('active', s.id === 'section-' + tabId));
  }

  // Close on overlay click
  document.querySelectorAll('.modal-overlay').forEach(overlay => {
    overlay.addEventListener('click', e => { if (e.target === overlay) closeModal(overlay.id.replace('modal-', '')); });
  });

  // Close on Escape
  document.addEventListener('keydown', e => {
    if (e.key === 'Escape') document.querySelectorAll('.modal-overlay.open').forEach(m => m.classList.remove('open'));
  });

  // ── Auto-open modal if PHP set one (after failed POST)
  const openOnLoad = <?php echo $open_modal ? json_encode($open_modal) : 'null'; ?>;
  const openTab    = <?php echo $active_modal ? json_encode($active_modal) : 'null'; ?>;
  if (openOnLoad) openModal(openOnLoad, openTab);
</script>

</body>
</html>
