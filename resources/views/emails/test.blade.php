<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Strict//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-strict.dtd">
<html xmlns="http://www.w3.org/1999/xhtml" xmlns:v="urn:schemas-microsoft-com:vml" xmlns:o="urn:schemas-microsoft-com:office:office">
<head>
    <meta http-equiv="Content-Type" content="text/html; charset=UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="x-apple-disable-message-reformatting">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <title>{{ $emailSubject }}</title>
    <style type="text/css">
        /* Reset styles */
        html, body {
            margin: 0;
            padding: 0;
            height: 100%;
            width: 100%;
        }
        table {
            border-spacing: 0;
            border-collapse: collapse;
        }
        img {
            border: 0;
            line-height: 100%;
            outline: none;
            text-decoration: none;
            display: block;
        }
        p {
            margin: 0;
            padding: 0;
        }
        body {
            background-color: #f3f4f6;
            font-family: -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, Helvetica, Arial, sans-serif;
        }
        .email-wrapper {
            background-color: #f3f4f6;
            padding: 40px 20px;
        }
        .email-container {
            max-width: 600px;
            margin: 0 auto;
            background-color: #ffffff;
            border-radius: 12px;
            box-shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.1), 0 2px 4px -1px rgba(0, 0, 0, 0.06);
        }
        .header {
            background: linear-gradient(135deg, #10b981 0%, #059669 100%);
            padding: 30px;
            border-radius: 12px 12px 0 0;
            text-align: center;
        }
        .logo {
            max-width: 180px;
            height: auto;
            margin: 0 auto;
        }
        .content {
            padding: 40px 30px;
            color: #1f2937;
            font-size: 16px;
            line-height: 1.6;
        }
        .content h1 {
            font-size: 24px;
            margin: 0 0 20px 0;
            color: #064e3b;
        }
        .content p {
            margin-bottom: 16px;
        }
        .footer {
            background-color: #f9fafb;
            padding: 20px 30px;
            border-radius: 0 0 12px 12px;
            text-align: center;
            color: #6b7280;
            font-size: 14px;
        }
        .footer p {
            margin-bottom: 8px;
        }
        .footer a {
            color: #10b981;
            text-decoration: none;
        }
    </style>
</head>
<body>
    <div class="email-wrapper">
        <table class="email-container" width="100%" cellpadding="0" cellspacing="0">
            <!-- Header -->
            <tr>
                <td class="header" align="center">
                    <img src="https://store.feedtancmg.org/feedtanstorelogo.png" alt="Feedtan Store Logo" class="logo">
                </td>
            </tr>
            
            <!-- Content -->
            <tr>
                <td class="content">
                    <h1>{{ $emailSubject }}</h1>
                    <p>Hello,</p>
                    <p>{!! nl2br(e($emailMessage)) !!}</p>
                    <p>Best regards,<br>The Feedtan Store Team</p>
                </td>
            </tr>
            
            <!-- Footer -->
            <tr>
                <td class="footer">
                    <p>&copy; {{ date('Y') }} Feedtan Store. All rights reserved.</p>
                    <p><a href="https://store.feedtancmg.org">Visit our store</a></p>
                </td>
            </tr>
        </table>
    </div>
</body>
</html>
