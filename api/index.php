<?php

// Force APP_DEBUG to true for debugging Vercel deployment
putenv('APP_DEBUG=true');
$_ENV['APP_DEBUG'] = 'true';
$_SERVER['APP_DEBUG'] = 'true';

// Redirect Laravel storage, cache and logs to writable /tmp or stderr on Vercel
putenv('LOG_CHANNEL=stderr');
$_ENV['LOG_CHANNEL'] = 'stderr';
$_SERVER['LOG_CHANNEL'] = 'stderr';

putenv('VIEW_COMPILED_PATH=/tmp');
$_ENV['VIEW_COMPILED_PATH'] = '/tmp';
$_SERVER['VIEW_COMPILED_PATH'] = '/tmp';

putenv('CACHE_STORE=array');
$_ENV['CACHE_STORE'] = 'array';
$_SERVER['CACHE_STORE'] = 'array';

putenv('CACHE_DRIVER=array');
$_ENV['CACHE_DRIVER'] = 'array';
$_SERVER['CACHE_DRIVER'] = 'array';

putenv('SESSION_DRIVER=cookie');
$_ENV['SESSION_DRIVER'] = 'cookie';
$_SERVER['SESSION_DRIVER'] = 'cookie';

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
