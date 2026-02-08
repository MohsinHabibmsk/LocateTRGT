<?php
// 1. If the frontend sent a POST request, set the cookie first
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['consent'])) {
    $choice = $_POST['consent'];
    setcookie("user_consent", $choice, time() + (86400 * 30), "/");
    echo "Cookie command sent to browser: " . $choice . "\n";
}

echo "--- Cookie Status ---\n";

// 2. Check if the cookie exists in the $_COOKIE superglobal
if (isset($_COOKIE['user_consent'])) {
    echo "The saved cookie value is: " . $_COOKIE['user_consent'];
} else {
    echo "No cookie found yet. (If you just clicked 'Accept', refresh the page to see it here).";
}
?>
