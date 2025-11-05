<?php
/**
 * Redirect to the public directory where the front controller is located
 */

// Get the server name
$server = $_SERVER['SERVER_NAME'] ?? 'localhost';
// Get the base URL path (everything after the domain name)
$baseUrl = dirname($_SERVER['PHP_SELF']);

// Build the redirect URL
$redirectUrl = "https://{$server}{$baseUrl}/public/";

// Perform the redirect
header("Location: $redirectUrl");
exit;
?>

<!-- If the redirect fails, provide a manual link -->
<html>
<head>
    <title>Redirecting...</title>
    <meta http-equiv="refresh" content="0;URL='public/'" />
</head>
<body>
    <p>If you are not redirected automatically, follow this <a href="public/">link to the application</a>.</p>
</body>
</html>
