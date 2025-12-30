<!DOCTYPE html>
<html lang="id">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1">
<title>Masuk - BSU Lamber</title>

<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
<link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css" rel="stylesheet">

<style>
body {
    margin:0;
    padding:0;
    min-height:100vh;
    background: linear-gradient(180deg, #f5f8f5 0%, #e8f5e8 25%, #81c784 55%, #43a047 85%, #1b5e20 100%);
    display:flex;
    align-items:center;
    justify-content:center;
    font-family:'Segoe UI',sans-serif;
}

.card-auth {
    width:380px;
    max-width:90vw;
    background:white;
    border-radius:36px;
    box-shadow:0 30px 70px rgba(0,0,0,0.42);
    padding:32px 32px 38px;
    text-align:center;
}

.logo-img {
    height:68px;
    margin-bottom:10px;
}

.title {
    font-size:1.55rem;
    font-weight:900;
    color:#1b5e20;
    margin-bottom:40px;
}

.form-control {
    height:54px;
    border-radius:50px;
    border:2.8px solid #a5d6a7;
    background:#f8fff8;
    padding:0 60px;
    margin-bottom:20px;
}

.input-icon {
    position:absolute;
    left:20px;
    top:50%;
    transform:translateY(-50%);
    color:#2e7d32;
    font-size:1.25rem;
}

.eye-icon {
    position:absolute;
    right:20px;
    top:50%;
    transform:translateY(-50%);
    color:#2e7d32;
    cursor:pointer;
    font-size:1.25rem;
}
</style>
</head>

<body>

<div class="card-auth">

    <img src="{{ asset('images/Logo-BSU.png') }}" class="logo-img">
    <div class="title">SELAMAT DATANG</div>

    {{-- PESAN SUKSES --}}
    @if (session('success'))
        <div class="alert alert-success rounded-pill py-2">
            {{ session('success') }}
        </div>
    @endif

    {{-- PESAN ERROR --}}
    @if ($errors->any())
        <div class="alert alert-danger rounded-pill py-2">
            {{ $errors->first() }}
        </div>
    @endif

    <!-- FORM LOGIN -->
    <form method="POST" action="{{ route('login') }}" autocomplete="off">
        @csrf

        <!-- DUMMY FIELD UNTUK BLOK AUTOFILL BROWSER -->
        <input type="text" name="fakeusernameremembered" style="display:none">
        <input type="password" name="fakepasswordremembered" style="display:none">

        <!-- EMAIL -->
        <div class="position-relative">
            <i class="fas fa-envelope input-icon"></i>
            <input
                type="email"
                name="email"
                class="form-control"
                placeholder="Email"
                autocomplete="new-email"
                autocorrect="off"
                autocapitalize="off"
                spellcheck="false"
                required>
        </div>

        <!-- PASSWORD -->
        <div class="position-relative mb-4">
            <i class="fas fa-lock input-icon"></i>
            <input
                type="password"
                name="password"
                id="loginPass"
                class="form-control"
                placeholder="Password"
                autocomplete="new-password"
                required>
            <i class="fas fa-eye-slash eye-icon" onclick="togglePass('loginPass', this)"></i>
        </div>

        <button type="submit" class="btn btn-success w-100 mb-2" style="height:54px; border-radius:50px">
            Masuk
        </button>

        <a href="#" class="text-success fw-bold" data-bs-toggle="modal" data-bs-target="#forgotModal">
            Lupa Password?
        </a>
    </form>
</div>

<!-- MODAL LUPA PASSWORD -->
<div class="modal fade" id="forgotModal" tabindex="-1">
  <div class="modal-dialog">
    <div class="modal-content rounded-4">
      <div class="modal-header">
        <h5 class="modal-title">Reset Password</h5>
        <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
      </div>

      <form method="POST" action="{{ url('/forgot-password') }}" autocomplete="off">
        @csrf
        <div class="modal-body">
          <label class="form-label">Masukkan email untuk reset password</label>
          <input
              type="email"
              name="email"
              class="form-control"
              autocomplete="new-email"
              required>
        </div>
        <div class="modal-footer">
          <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Batal</button>
          <button type="submit" class="btn btn-success">Kirim Link Reset</button>
        </div>
      </form>

    </div>
  </div>
</div>

<script>
function togglePass(id, el) {
    const field = document.getElementById(id);
    if (field.type === "password") {
        field.type = "text";
        el.classList.replace("fa-eye-slash", "fa-eye");
    } else {
        field.type = "password";
        el.classList.replace("fa-eye", "fa-eye-slash");
    }
}
</script>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>

</body>
</html>
