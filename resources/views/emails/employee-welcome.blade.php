<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Welcome to Feedtan Store</title>
    <style>
        body { font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif; line-height: 1.6; color: #333; max-width: 600px; margin: 0 auto; padding: 20px; }
        .container { background: #f9fafb; border-radius: 12px; padding: 40px; }
        .header { text-align: center; margin-bottom: 30px; }
        .logo { max-width: 180px; height: auto; margin-bottom: 20px; }
        .title { color: #1e40af; font-size: 28px; font-weight: bold; margin: 0 0 10px; }
        .subtitle { color: #6b7280; font-size: 16px; margin: 0; }
        .card { background: white; border-radius: 8px; padding: 24px; margin: 20px 0; box-shadow: 0 1px 3px rgba(0,0,0,0.1); }
        .credentials { background: #eff6ff; border: 1px solid #bfdbfe; border-radius: 8px; padding: 20px; }
        .cred-item { margin: 12px 0; }
        .cred-label { font-size: 14px; color: #6b7280; text-transform: uppercase; letter-spacing: 0.5px; }
        .cred-value { font-size: 18px; font-weight: bold; color: #1e40af; font-family: monospace; }
        .btn { display: inline-block; background: #1e40af; color: white; padding: 14px 28px; border-radius: 8px; text-decoration: none; font-weight: 600; margin: 20px 0; }
        .btn:hover { background: #1e3a8a; }
        .footer { text-align: center; margin-top: 30px; padding-top: 20px; border-top: 1px solid #e5e7eb; color: #9ca3af; font-size: 14px; }
        .note { background: #fef3c7; border: 1px solid #fcd34d; border-radius: 8px; padding: 16px; margin: 20px 0; color: #92400e; font-size: 14px; }
    </style>
</head>
<body>
    <div class="container">
        <div class="header">
            <img src="https://feedtanstore.com/feedtanstorelogo.png" alt="Feedtan Store" class="logo">
            <h1 class="title">Welcome to Feedtan Store!</h1>
            <p class="subtitle">Your employee account has been created successfully</p>
        </div>

        <div class="card">
            <p>Dear <strong>{{ $employee->name }}</strong>,</p>
            <p>Congratulations! You have been registered as a team member at Feedtan Store. Your account with the role of <strong>{{ ucfirst(str_replace('_', ' ', $employee->role)) }}</strong> is now active.</p>
        </div>

        <div class="credentials">
            <h3 style="margin: 0 0 16px; color: #1e40af;">Your Login Credentials</h3>

            <div class="cred-item">
                <div class="cred-label">Email / Username</div>
                <div class="cred-value">{{ $employee->email }}</div>
            </div>

            <div class="cred-item">
                <div class="cred-label">Temporary Password</div>
                <div class="cred-value">{{ $password }}</div>
            </div>
        </div>

        <div class="note">
            <strong>Important:</strong> For security, please change your password after your first login.
        </div>

        <div style="text-align: center;">
            <a href="{{ $loginUrl }}" class="btn">Login to Your Account</a>
        </div>

        <div class="footer">
            <p>If you have any questions, contact our support team.</p>
            <p>&copy; {{ date('Y') }} Feedtan Store. All rights reserved.</p>
        </div>
    </div>
</body>
</html>
