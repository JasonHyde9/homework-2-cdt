<?php
// vulnerable_app.php
// Purpose: Validates that the LAMP stack (Linux, Apache, Redis, PHP) is operational.

$redis_status = "Unknown";
$msg = "";

try {
    $redis = new Redis();
    // We connect to localhost because this script runs internally on the server
    // even though the vulnerability is that it's bound to 0.0.0.0 externally.
    if ($redis->connect('127.0.0.1', 6379)) {
        $redis_status = "Online";
        $msg = "Successfully connected to Redis Server v" . $redis->info()['redis_version'];
    } else {
        $redis_status = "Offline";
        $msg = "Could not connect to Redis instance.";
    }
} catch (Exception $e) {
    $redis_status = "Error";
    $msg = $e->getMessage();
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Internal Service Dashboard</title>
    <style>
        body { font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif; background-color: #f4f4f4; padding: 50px; }
        .container { background: white; padding: 30px; border-radius: 8px; box-shadow: 0 0 10px rgba(0,0,0,0.1); max-width: 600px; margin: auto; }
        h1 { color: #333; }
        .status-box { padding: 15px; margin-top: 20px; border-radius: 4px; color: white; font-weight: bold; }
        .online { background-color: #28a745; }
        .offline { background-color: #dc3545; }
        .details { margin-top: 15px; font-size: 0.9em; color: #666; }
    </style>
</head>
<body>
    <div class="container">
        <h1>Legacy App Dashboard</h1>
        <p>Monitoring internal caching services.</p>
        <hr>
        
        <p><strong>PHP Version:</strong> <?php echo phpversion(); ?></p>
        
        <div class="status-box <?php echo ($redis_status == 'Online') ? 'online' : 'offline'; ?>">
            Redis Service Status: <?php echo $redis_status; ?>
        </div>
        
        <div class="details">
            Diagnostic Message: <?php echo $msg; ?>
        </div>
    </div>
</body>
</html>