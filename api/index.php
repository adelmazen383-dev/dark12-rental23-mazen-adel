<?php

/**
 * Vercel Serverless PHP Entry Point
 * Routes all requests through Laravel's public/index.php
 */

$_SERVER['SCRIPT_FILENAME'] = __DIR__ . '/../public/index.php';
$_SERVER['DOCUMENT_ROOT']   = __DIR__ . '/../public';

// Ensure /tmp directories exist for serverless
$tmpDirs = [
    '/tmp/storage/framework/sessions',
    '/tmp/storage/framework/views',
    '/tmp/storage/framework/cache',
    '/tmp/storage/framework/cache/data',
    '/tmp/storage/logs',
];

foreach ($tmpDirs as $dir) {
    if (!is_dir($dir)) {
        @mkdir($dir, 0755, true);
    }
}

// Copy SQLite database to /tmp if not already there
$srcDb  = __DIR__ . '/../database/database.sqlite';
$tmpDb  = '/tmp/database.sqlite';
if (file_exists($srcDb) && !file_exists($tmpDb)) {
    @copy($srcDb, $tmpDb);
}

// Forward to public/index.php
require __DIR__ . '/../public/index.php';
