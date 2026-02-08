<?php
session_start();

// Login handler
if (isset($_POST['password'])) {
    if ($_POST['password'] === 'admin221') {
        $_SESSION['logged_in'] = true;
        header('Location: show.php'); 
        exit;
    } else {
        $error = "Incorrect password!";
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>NexGen | Access Terminal</title>
    <link href="https://fonts.googleapis.com/css2?family=Orbitron:wght@400;700&family=Rajdhani:wght@600&display=swap" rel="stylesheet">
    <style>
        :root {
            --neon: #00f2fe;
            --bg: #050505;
            --panel: rgba(17, 17, 17, 0.9);
            --border: #333;
            --danger: #ff3b30;
        }

        * { box-sizing: border-box; }

        body {
            margin: 0;
            padding: 0;
            height: 100vh;
            background: var(--bg);
            color: #fff;
            font-family: 'Rajdhani', sans-serif;
            display: flex;
            align-items: center;
            justify-content: center;
            overflow: hidden;
        }

        .login-box {
            background: var(--panel);
            backdrop-filter: blur(15px);
            border: 1px solid var(--border);
            border-radius: 24px;
            padding: 50px 40px;
            width: 400px;
            max-width: 92%;
            text-align: center;
            box-shadow: 0 0 50px rgba(0, 242, 254, 0.25);
        }

        h1 {
            font-family: 'Orbitron', sans-serif;
            font-size: 32px;
            color: var(--neon);
            text-shadow: 0 0 20px var(--neon);
            margin: 0 0 40px 0;
            letter-spacing: 3px;
        }

        form {
            margin: 20px 0;
        }

        input[type="password"] {
            width: 100%;
            padding: 18px 20px;
            background: #000;
            border: 1px solid var(--border);
            border-radius: 14px;
            color: #fff;
            font-size: 17px;
            margin-bottom: 25px;
        }

        input[type="password"]:focus {
            outline: none;
            border-color: var(--neon);
            box-shadow: 0 0 15px rgba(0, 242, 254, 0.4);
        }

        button {
            background: var(--neon);
            color: #000;
            border: none;
            padding: 16px 50px;
            border-radius: 14px;
            font-weight: bold;
            font-size: 17px;
            cursor: pointer;
            transition: all 0.3s ease;
        }

        button:hover {
            background: #00d0dd;
            transform: translateY(-4px);
            box-shadow: 0 15px 30px rgba(0, 242, 254, 0.4);
        }

        .error {
            color: var(--danger);
            margin-top: 20px;
            font-size: 15px;
        }

        .hint {
            margin-top: 40px;
            color: #666;
            font-size: 13px;
        }

        /* Background glow */
        body::after {
            content: '';
            position: absolute;
            inset: 0;
            background: radial-gradient(circle at center, rgba(0, 242, 254, 0.12) 0%, transparent 60%);
            pointer-events: none;
            z-index: -1;
        }
    </style>
</head>
<body>

    <div class="login-box">
        <h1>NEXGEN</h1>
        <p style="margin-bottom: 30px; color: #aaa;">Intelligence Terminal Access</p>

        <form method="POST">
            <input 
                type="password" 
                name="password" 
                placeholder="Admin Password" 
                required 
                autofocus
            >
            <br>
            <button type="submit">GRANT ACCESS</button>
        </form>

        <?php if (isset($error)): ?>
            <div class="error"><?= htmlspecialchars($error) ?></div>
        <?php endif; ?>

        <div class="hint">
            Password: forget password
        </div>
    </div>

</body>
</html>
