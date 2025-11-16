<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login - PT Mitra Panel Cherbond</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">

    <style>
        body {
            background-color: #f8f9fa;
            height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
        }
        .login-card {
            border-radius: 15px;
            box-shadow: 0 5px 15px rgba(0,0,0,0.1);
            border: none;
        }
        .logo-container {
            margin-bottom: 2rem; /* Jarak bawah */
        }
        .logo-img {
            max-width: 100px; /* Ukuran maksimal logo */
            height: auto;
            margin-bottom: 10px;
        }
        .text-primary {
            color: #0d6efd !important;
        }
    </style>
</head>
<body>
    <div class="container">
        <div class="row justify-content-center">
            <div class="col-md-5 col-lg-4">
                <div class="card login-card">
                    <div class="card-body p-5">

                        <div class="text-center logo-container">
                            {{-- Ganti 'images/logo-pt.png' dengan path logo Anda yang sebenarnya --}}
                            <img src="{{ asset('images/logompc.jpeg') }}" alt="Logo PT Mitra Panel Cherbond" class="logo-img">
                            {{-- <h4 class="fw-bold mb-0 text-primary">PT Mitra Panel Cherbond</h4> --}}
                            {{-- <p class="text-muted small">Sistem Manajemen Harga Rongsokan</p> --}}
                        </div>

                        @if ($errors->any())
                            <div class="alert alert-danger alert-dismissible fade show" role="alert">
                                <strong>Gagal Login!</strong> {{ $errors->first() }}
                                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                            </div>
                        @endif

                        <form method="POST" action="{{ route('login.post') }}">
                            @csrf
                            <div class="mb-3">
                                <label for="username" class="form-label visually-hidden">Username</label>
                                <div class="input-group input-group-lg">
                                    <span class="input-group-text bg-light"><i class="fas fa-user"></i></span>
                                    <input type="text" class="form-control" id="username" name="username" value="{{ old('username') }}" placeholder="Username" required autofocus>
                                </div>
                            </div>

                            <div class="mb-4">
                                <label for="password" class="form-label visually-hidden">Password</label>
                                <div class="input-group input-group-lg">
                                    <span class="input-group-text bg-light"><i class="fas fa-lock"></i></span>
                                    <input type="password" class="form-control" id="password" name="password" placeholder="Password" required>
                                </div>
                            </div>

                            <div class="d-flex justify-content-between align-items-center mb-4">
                                <div class="form-check">
                                    <input type="checkbox" class="form-check-input" id="remember" name="remember">
                                    <label class="form-check-label" for="remember">Ingat Saya</label>
                                </div>
                            </div>

                            <button type="submit" class="btn btn-primary w-100 py-3 fw-bold">
                                <i class="fas fa-sign-in-alt me-2"></i> MASUK
                            </button>
                        </form>

                        {{-- <div class="mt-5 pt-3 text-center border-top">
                            <small class="text-muted">
                                **Info Akun Demo:**<br>
                                Admin: `admin` / `admin123`<br>
                                Supplier: `supplier1` / `password123`
                            </small>
                        </div> --}}
                    </div>
                </div>
            </div>
        </div>
    </div>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
