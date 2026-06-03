<?php

// Force APP_DEBUG to true for debugging Vercel deployment
putenv('APP_DEBUG=true');
$_ENV['APP_DEBUG'] = 'true';
$_SERVER['APP_DEBUG'] = 'true';

if (empty($_ENV['APP_KEY']) && empty(getenv('APP_KEY'))) {
    echo "<h1>Laravel Diagnostic Error</h1>";
    echo "<p><strong>Error:</strong> APP_KEY is not defined in Vercel Environment Variables.</p>";
    echo "<p>Please add <code>APP_KEY</code> to your Vercel Project Environment Variables (e.g. from your local .env file) and redeploy.</p>";
    exit;
}

try {
    // Forward Vercel request to Laravel public index
    require __DIR__ . '/../public/index.php';
} catch (\Throwable $e) {
    echo "<h1>Laravel Bootstrapping Exception caught in api/index.php</h1>";
    echo "<p><strong>Message:</strong> " . htmlspecialchars($e->getMessage()) . "</p>";
    echo "<p><strong>File:</strong> " . htmlspecialchars($e->getFile()) . " (Line " . $e->getLine() . ")</p>";
    echo "<pre>" . htmlspecialchars($e->getTraceAsString()) . "</pre>";
    exit;
}
