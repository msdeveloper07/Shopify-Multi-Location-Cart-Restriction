<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <title>App Installed Successfully</title>
  <style>
    * {
      box-sizing: border-box;
      margin: 0;
      padding: 0;
    }

    body {
      font-family: "Inter", "Segoe UI", Tahoma, Geneva, Verdana, sans-serif;
      background: linear-gradient(135deg, #e0f7ff, #f0f2f5);
      color: #333;
      display: flex;
      justify-content: center;
      align-items: center;
      min-height: 100vh;
      padding: 20px;
    }

    .container {
      background: #fff;
      padding: 50px 40px;
      border-radius: 14px;
      box-shadow: 0 10px 30px rgba(0, 0, 0, 0.1);
      max-width: 480px;
      width: 100%;
      text-align: center;
      animation: fadeIn 0.6s ease-out;
    }

    .success-icon {
      font-size: 60px;
      color: #28a745;
      margin-bottom: 20px;
      animation: pop 0.6s ease-out;
    }

    .container h1 {
      font-size: 26px;
      color: #2c7be5;
      margin-bottom: 12px;
    }

    .container p {
      font-size: 16px;
      color: #555;
      margin-bottom: 30px;
    }

    .button {
      background: linear-gradient(135deg, #2c7be5, #5a8dee);
      color: #fff;
      padding: 14px 30px;
      border-radius: 8px;
      text-decoration: none;
      font-size: 15px;
      font-weight: 600;
      display: inline-block;
      transition: background 0.3s ease, transform 0.2s ease;
    }

    .button:hover {
      background: linear-gradient(135deg, #1a68d1, #407ed1);
      transform: translateY(-2px);
    }

    @keyframes fadeIn {
      from { opacity: 0; transform: translateY(10px); }
      to { opacity: 1; transform: translateY(0); }
    }

    @keyframes pop {
      from { transform: scale(0.8); opacity: 0; }
      to { transform: scale(1); opacity: 1; }
    }

    .footer {
      margin-top: 20px;
      font-size: 12px;
      color: #999;
    }
  </style>
</head>
<body>

  <div class="container">
    <div class="success-icon">✅</div>
    <h1>App Installed Successfully!</h1>
    <p>Your app has been installed for <strong>{{ $shop }}</strong>. You’re ready to go!</p>
    <a class="button" href="https://{{ $shop }}/admin/apps">Open App in Admin</a>
    <div class="footer">&copy; {{ date('Y') }} Location Check App. All rights reserved.</div>
  </div>

</body>
</html>
