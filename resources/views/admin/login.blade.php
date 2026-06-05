<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin System Identity - RevoDevice</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-weight/6.4.0/css/all.min.css">
    <style>
        * { box-sizing: border-box; margin: 0; padding: 0; font-family: 'Segoe UI', system-ui, sans-serif; }
        body { background: #07070a; color: #e2e8f0; display: flex; align-items: center; justify-content: center; min-height: 100vh; padding: 20px; }
        .login-card { background: #111118; border: 1px solid #1e1e2a; width: 100%; max-width: 420px; border-radius: 24px; padding: 40px; box-shadow: 0 20px 40px rgba(0,0,0,0.5); }
        .logo-wrap { display: flex; align-items: center; justify-content: center; gap: 12px; margin-bottom: 30px; }
        .logo-box { width: 42px; height: 42px; background: linear-gradient(135deg, #ec4899, #8b5cf6); border-radius: 12px; display: flex; align-items: center; justify-content: center; }
        .title { font-size: 24px; font-weight: 800; background: linear-gradient(135deg, #f472b6, #c084fc); -webkit-background-clip: text; color: transparent; }
        .form-group { margin-bottom: 20px; position: relative; }
        .form-group i { position: absolute; left: 16px; top: 50%; transform: translateY(-50%); color: #64748b; }
        .form-control { width: 100%; padding: 14px 16px 14px 48px; background: #1a1a2e; border: 1px solid #2a2a3a; border-radius: 14px; color: white; outline: none; transition: 0.2s; font-size: 15px; }
        .form-control:focus { border-color: #ec4899; box-shadow: 0 0 0 2px rgba(236,72,153,0.15); }
        .btn-submit { width: 100%; padding: 14px; background: linear-gradient(135deg, #ec4899, #8b5cf6); color: white; border: none; border-radius: 14px; font-weight: 600; font-size: 16px; cursor: pointer; transition: 0.2s; margin-top: 10px; }
        .btn-submit:hover { opacity: 0.9; }
        .alert { padding: 12px 16px; border-radius: 12px; font-size: 14px; margin-bottom: 20px; text-align: center; }
        .alert-danger { background: rgba(239,68,68,0.15); border: 1px solid rgba(239,68,68,0.25); color: #f87171; }
    </style>
</head>
<body>

<div class="login-card">
    <div class="logo-wrap">
        <div class="logo-box"><i class="fas fa-user-shield" style="color: white; font-size: 20px;"></i></div>
        <h1 class="title">RevoAdmin</h1>
    </div>

    @if(session('error'))
        <div class="alert alert-danger">{{ session('error') }}</div>
    @endif

    <form action="{{ route('admin.login.submit') }}" method="POST">
        @csrf
        <div class="form-group">
            <i class="fas fa-user"></i>
            <input type="text" name="username" class="form-control" placeholder="Admin Username" required autocomplete="off" value="{{ old('username') }}">
        </div>
        <div class="form-group">
            <i class="fas fa-lock"></i>
            <input type="password" name="password" class="form-control" placeholder="Security Password" required>
        </div>
        <button type="submit" class="btn-submit">Authenticate Terminal</button>
    </form>
</div>

</body>
</html>