<?php

/**
 * Vercel Serverless PHP Entry Point
 * Routes all requests through Laravel's public/index.php
 */

// Production security: Never leak PHP warnings or deprecation notices to client output
ini_set('display_errors', '0');
ini_set('display_startup_errors', '0');
error_reporting(E_ALL & ~E_DEPRECATED & ~E_USER_DEPRECATED & ~E_NOTICE);

$_SERVER['SCRIPT_FILENAME'] = __DIR__ . '/../public/index.php';
$_SERVER['DOCUMENT_ROOT']   = __DIR__ . '/../public';

// Ensure /tmp directories exist for serverless
$tmpDirs = [
    '/tmp/storage/framework/sessions',
    '/tmp/storage/framework/views',
    '/tmp/storage/framework/cache',
    '/tmp/storage/framework/cache/data',
    '/tmp/storage/logs',
    '/tmp/storage/app',
    '/tmp/storage/app/public',
    '/tmp/storage/app/public/investors',
    '/tmp/storage/app/public/investors/ids',
    '/tmp/storage/app/public/investors/licenses',
    '/tmp/storage/app/public/apartments',
    '/tmp/storage/app/public/apartments/images',
    '/tmp/storage/app/public/apartments/videos',
    '/tmp/storage/app/public/receipts',
    '/tmp/storage/app/private',
];

foreach ($tmpDirs as $dir) {
    if (!is_dir($dir)) {
        @mkdir($dir, 0777, true);
    }
}

// Forward to public/index.php
require __DIR__ . '/../public/index.php';
