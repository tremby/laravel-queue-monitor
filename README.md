Laravel queue monitor
=====================

This adds various tools to a project for monitoring its queue.

More details TODO

Laravel version
---------------

This branch is for Laravel 5.

Laravel 4 version TODO

Installation
------------

Require it in your Laravel project:

    composer require tremby/laravel-queue-monitor

Register the service provider in your `config/app.php` file:

    'providers' => [
        ...
        \Tremby\QueueMonitor\ServiceProvider::class,
    ],

Use
---

TODO

Supported queue drivers
-----------------------

- redis

Pull requests are welcome for others.
