<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <title>Install Location Check App</title>
  <style>
    * {
      box-sizing: border-box;
      margin: 0;
      padding: 0;
    }

    body {
      font-family: "Inter", "Segoe UI", Tahoma, Geneva, Verdana, sans-serif;
      background: linear-gradient(135deg, #ffe6e6, #f8f9fa);
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
      border-radius: 16px;
      box-shadow: 0 12px 32px rgba(0, 0, 0, 0.1);
      max-width: 460px;
      width: 100%;
      text-align: center;
      animation: fadeIn 0.7s ease-out;
      position: relative;
    }

    .icon-badge {
      position: absolute;
      top: -30px;
      left: 50%;
      transform: translateX(-50%);
      background: #ff4d4f;
      color: #fff;
      width: 60px;
      height: 60px;
      border-radius: 50%;
      display: flex;
      justify-content: center;
      align-items: center;
      font-size: 32px;
      box-shadow: 0 4px 12px rgba(0,0,0,0.2);
      animation: pop 0.6s ease-out;
    }

    h1 {
      font-size: 24px;
      color: #ff4d4f;
      margin-bottom: 10px;
      margin-top: 30px;
    }

    p {
      font-size: 15px;
      color: #666;
      margin-bottom: 30px;
    }

    .button {
      background: linear-gradient(135deg, #ff4d4f, #ff7875);
      color: #fff;
      padding: 14px 35px;
      border-radius: 8px;
      text-decoration: none;
      font-size: 15px;
      font-weight: 600;
      display: inline-block;
      box-shadow: 0 4px 14px rgba(255,77,79,0.4);
      transition: background 0.3s ease, transform 0.2s ease, box-shadow 0.3s ease;
    }

    .button:hover {
      background: linear-gradient(135deg, #d9363e, #ff4d4f);
      transform: translateY(-2px);
      box-shadow: 0 6px 18px rgba(255,77,79,0.5);
    }

    @keyframes fadeIn {
      from { opacity: 0; transform: translateY(15px); }
      to { opacity: 1; transform: translateY(0); }
    }

    @keyframes pop {
      from { transform: translateX(-50%) scale(0.8); opacity: 0; }
      to { transform: translateX(-50%) scale(1); opacity: 1; }
    }
  </style>
</head>
<body>

  <div class="container">
    <div class="icon-badge">⚠️</div>
    <h1>App Not Installed</h1>
    <p>The <strong>Location Check App</strong> is not installed for <strong>{{ $shop }}</strong>. Please install it to enable checkout restrictions by location.</p>
    <a class="button" href="/shopify/install?shop={{ $shop }}">Install App</a>
  </div>

</body>
</html>
