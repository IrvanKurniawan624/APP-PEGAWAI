<!doctype html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>You Are Lost</title>

    <style>
        body {
            margin: 0;
            padding: 0;
            height: 100vh;
            display: flex;
            justify-content: center;
            align-items: center;
            background: #f5f6fa;
            font-family: "Segoe UI", sans-serif;
            text-align: center;
            color: #333;
        }
        .wrap {
            max-width: 500px;
        }
        img {
            width: 300px;
            margin-bottom: 20px;
        }
        h2 {
            font-size: 28px;
            margin-bottom: 10px;
        }
        p {
            font-size: 16px;
            margin-bottom: 25px;
        }
        a {
            display: inline-block;
            padding: 10px 20px;
            background: #6c5ce7;
            color: white;
            border-radius: 6px;
            text-decoration: none;
            transition: .2s;
        }
        a:hover {
            background: #4e3fd6;
        }
    </style>
</head>

<body>
    <div class="wrap">
        <img src="{{ asset("images/404-dark.png") }}" alt="Lost Illustration">

        <h2>Oops! You’re Lost</h2>
        <p>You don’t have permission to access this page.</p>

        <a href="/dashboard">Go to Dashboard</a>
    </div>
</body>
</html>
