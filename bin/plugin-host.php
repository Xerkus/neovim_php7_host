#!/usr/bin/env zts-php
<?php

chdir(dirname(__DIR__));
// Assert env meets minimum requirements
if (!php_sapi_name() === 'cli') {
    throw new RuntimeException('Only cli sapi is allowed');
    exit(1);
}
if (PHP_MAJOR_VERSION !== 7) {
    fwrite(STDERR, "PHP version 7 is required\n");
    exit(1);
}
if (!in_array('pthreads', get_loaded_extensions())) {
    fwrite(STDERR, "Pthreads extension is not loaded\n");
    fwrite(STDERR, "Did you forget to install it or used non-zts php build?\n");
    exit(1);
}
if (!in_array('msgpack', get_loaded_extensions())) {
    fwrite(STDERR, "Plugin host needs msgpack extension to talk to neovim\n");
    exit(1);
}
// I want to keep core plugin host at 0 external deps. Will try to get rid of
// composer later
if (!file_exists(__DIR__ . '/../vendor/autoload.php')) {
    fwrite(STDERR, "Dependencies are not installed. Run composer install\n");
    exit(1);
}
include __DIR__ . '/../vendor/autoload.php';

//remove script path
array_shift($argv);

if (count($argv) < 1) {
    fwrite(STDERR, "Must specify at least one plugin as argument\n");
    exit(1);
}

try {
    // currently only stdin/stdout server is supported by rpcstart()
    // @TODO add logging
    $session = new \Xerkus\Neovim\MsgpackRpc\Session(
        new \Xerkus\Neovim\MsgpackRpc\Stream(
            new \Xerkus\Neovim\MsgpackRpc\Stream\StdServer()
        )
    );
    $pluginHost = new \Xerkus\Neovim\Plugin\Host($session);
    $pluginHost->start($argv);
} catch (\Throwable $e) {
    fwrite(STDERR, (string)$e . "\n");
    exit(1);
}

