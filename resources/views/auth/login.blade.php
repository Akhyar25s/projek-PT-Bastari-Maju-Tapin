<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login - PT. Bastari Maju Tapin</title>
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        body {
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            min-height: 100vh;
            display: flex;
            background: linear-gradient(135deg, #3fc0d6 0%, #2fb0c6 20%, #a7e04b 60%, #8bc34a 85%, #7cb342 100%);
            overflow-x: hidden;
        }

        .login-container {
            display: flex;
            width: 100%;
            min-height: 100vh;
        }

        /* Left Side - Branding */
        .left-side {
            flex: 1;
            display: flex;
            flex-direction: column;
            justify-content: center;
            padding: 60px;
            color: white;
            position: relative;
        }

        .logo-section {
            display: flex;
            align-items: center;
            gap: 20px;
            margin-bottom: 60px;
        }

        .logo-section img {
            height: 80px;
            width: auto;
            filter: drop-shadow(0 4px 8px rgba(0,0,0,0.2));
        }

        .company-info {
            display: flex;
            flex-direction: column;
        }

        .company-info .company-name {
            font-size: 24px;
            font-weight: 700;
            color: white;
            margin-bottom: 4px;
        }

        .company-info .company-subtitle {
            font-size: 16px;
            color: rgba(255,255,255,0.9);
            font-weight: 400;
        }

        .welcome-text {
            margin-top: 40px;
        }

        .welcome-text h1 {
            font-size: 48px;
            font-weight: 800;
            line-height: 1.2;
            color: #FFD43B;
            text-shadow: 2px 2px 4px rgba(0,0,0,0.2);
            margin-bottom: 12px;
        }

        .welcome-text h2 {
            font-size: 48px;
            font-weight: 800;
            line-height: 1.2;
            color: #FFD43B;
            text-shadow: 2px 2px 4px rgba(0,0,0,0.2);
        }

        /* Right Side - Login Form */
        .right-side {
            flex: 1;
            display: flex;
            align-items: center;
            justify-content: center;
            padding: 40px;
            background: rgba(255,255,255,0.95);
        }

        .login-box {
            width: 100%;
            max-width: 420px;
            background: white;
            padding: 50px 40px;
            border-radius: 20px;
            box-shadow: 0 20px 60px rgba(0,0,0,0.15);
        }

        .login-title {
            font-size: 32px;
            font-weight: 700;
            color: #3fc0d6;
            margin-bottom: 35px;
            text-align: center;
            letter-spacing: 1px;
        }

        .alert {
            padding: 14px 16px;
            border-radius: 8px;
            margin-bottom: 20px;
            font-size: 14px;
        }

        .alert-success {
            background-color: #d4edda;
            color: #155724;
            border: 1px solid #c3e6cb;
        }

        .alert-danger {
            background-color: #f8d7da;
            color: #721c24;
            border: 1px solid #f5c6cb;
        }

        .alert-danger ul {
            margin: 0;
            padding-left: 20px;
        }

        .input-group {
            margin-bottom: 24px;
        }

        .input-group label {
            display: block;
            margin-bottom: 8px;
            color: #333;
            font-weight: 500;
            font-size: 14px;
        }

        .input-group input[type="text"],
        .input-group input[type="password"] {
            width: 100%;
            padding: 14px 18px;
            border: 2px solid #e0e0e0;
            border-radius: 10px;
            font-size: 15px;
            transition: all 0.3s ease;
            background: #f9f9f9;
        }

        .input-group input[type="text"]:focus,
        .input-group input[type="password"]:focus {
            outline: none;
            border-color: #3fc0d6;
            background: white;
            box-shadow: 0 0 0 3px rgba(63, 192, 214, 0.1);
        }

        .input-group input::placeholder {
            color: #999;
        }

        .error {
            color: #dc3545;
            font-size: 13px;
            margin-top: 6px;
            display: block;
        }

        .remember-me {
            display: flex;
            align-items: center;
            margin-bottom: 30px;
        }

        .remember-me input[type="checkbox"] {
            width: 18px;
            height: 18px;
            margin-right: 10px;
            cursor: pointer;
            accent-color: #3fc0d6;
        }

        .remember-me label {
            color: #666;
            font-size: 14px;
            cursor: pointer;
            margin: 0;
        }

        .btn-login {
            width: 100%;
            padding: 16px;
            background: linear-gradient(135deg, #3fc0d6 0%, #2fb0c6 100%);
            color: white;
            border: none;
            border-radius: 10px;
            font-size: 16px;
            font-weight: 600;
            cursor: pointer;
            transition: all 0.3s ease;
            box-shadow: 0 4px 15px rgba(63, 192, 214, 0.3);
        }

        .btn-login:hover {
            transform: translateY(-2px);
            box-shadow: 0 6px 20px rgba(63, 192, 214, 0.4);
        }

        .btn-login:active {
            transform: translateY(0);
        }

        .register-link {
            text-align: center;
            margin-top: 25px;
            color: #666;
            font-size: 14px;
        }

        .register-link a {
            color: #3fc0d6;
            text-decoration: none;
            font-weight: 500;
        }

        .register-link a:hover {
            text-decoration: underline;
        }

        /* Responsive */
        @media (max-width: 968px) {
            .login-container {
                flex-direction: column;
            }

            .left-side {
                padding: 40px 30px;
                min-height: auto;
            }

            .welcome-text h1,
            .welcome-text h2 {
                font-size: 36px;
            }

            .right-side {
                padding: 30px 20px;
            }

            .login-box {
                padding: 40px 30px;
            }
        }

        @media (max-width: 640px) {
            .welcome-text h1,
            .welcome-text h2 {
                font-size: 28px;
            }

            .logo-section {
                flex-direction: column;
                text-align: center;
            }

            .login-title {
                font-size: 26px;
            }
        }
    </style>
</head>
<body>
    <div class="login-container">
        <!-- Left Side - Branding -->
        <div class="left-side">
            <div class="logo-section">
                <img src="{{ asset('images/logo.png') }}" alt="Logo PT. Bastari Maju Tapin" class="logo" onerror="this.style.display='none'">
                <div class="company-info">
                    <div class="company-name">PT. Bastari Maju</div>
                    <div class="company-subtitle">Tapin</div>
                    <div class="company-subtitle">(Perseroda)</div>
                </div>
            </div>
            
            <div class="welcome-text">
                <h1>Selamat datang,</h1>
                <h2>Silahkan Login</h2>
            </div>
        </div>

        <!-- Right Side - Login Form -->
        <div class="right-side">
            <div class="login-box">
                <h2 class="login-title">LOGIN</h2>
                
                <!-- Tampilkan pesan sukses jika ada -->
                @if(session('success'))
                    <div class="alert alert-success">
                        {{ session('success') }}
                    </div>
                @endif

                <!-- Tampilkan error umum jika ada -->
                @if($errors->any())
                    <div class="alert alert-danger">
                        <ul>
                            @foreach($errors->all() as $error)
                                <li>{{ $error }}</li>
                            @endforeach
                        </ul>
                    </div>
                @endif

                <form action="{{ route('login') }}" method="POST">
                    @csrf
                    
                    <div class="input-group">
                        <input type="text" 
                               name="username" 
                               placeholder="Username" 
                               value="{{ old('username') }}" 
                               required 
                               autofocus>
                        @error('username') 
                            <span class="error">{{ $message }}</span> 
                        @enderror
                    </div>

                    <div class="input-group">
                        <input type="password" 
                               name="password" 
                               placeholder="Password" 
                               required>
                        @error('password') 
                            <span class="error">{{ $message }}</span> 
                        @enderror
                    </div>

                    <div class="remember-me">
                        <input type="checkbox" name="remember" id="remember" value="1">
                        <label for="remember">Ingat saya</label>
                    </div>

                    <button type="submit" class="btn-login">Login</button>
                </form>
            </div>
        </div>
    </div>
</body>
</html>

