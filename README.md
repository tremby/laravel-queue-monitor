Laravel queue monitor
=====================

This adds various tools to a project for monitoring its queue.

Laravel version
---------------

This branch and the `2.*` line of tags are for Laravel 5. For the Laravel 4
version [see the laravel4 branch][l4] and the `1.*` line of tags.

If you are using Laravel 5.5 or later, and are using PHP 7.1 or later,
you may instead consider using [Laravel Horizon][horizon],
which is an official tool and solves the same issue as this package.

[l4]: https://github.com/tremby/laravel-queue-monitor/tree/laravel4
[horizon]: https://laravel.com/docs/5.5/horizon

Installation
------------

Require it in your Laravel project:

    composer require tremby/laravel-queue-monitor

Register the service provider in your `config/app.php` file:

```php
'providers' => [
    ...
    Tremby\QueueMonitor\ServiceProvider::class,
],
```

Use
---

Add a cron job which runs the `queue:queuecheck` Artisan task for each queue you
want to monitor. A queue name can be passed as an argument, or the default queue
name is used if none is given. See `./artisan queue:queuecheck --help` for full
details.

Example cron job to check the default queue every 15 minutes:

    */15 * * * * php /home/forge/example.com/artisan queue:queuecheck

This task records in the application cache (for one day) that a check for this
queue is pending, then pushes a job to this queue. This job changes that cached
status to "OK", so if the job doesn't run for whatever reason the status will be
left at "pending".

The status of all queue monitors can be checked by rendering one of the provided
status views. The markup of the provided views are [Twitter
Bootstrap](http://getbootstrap.com/)-friendly and if the `status-page` view is
used Bootstrap is loaded from a CDN.

```php
Route::get('queue-monitor', function () {
    return Response::view('queue-monitor::status-page');
});
```

Other views available are `queue-monitor::status-panel`, which is the `.panel`
element and its contents; and `queue-monitor::status`, which is just the `table`
element. Either of these could be used to plug this monitor into a larger
monitoring panel. A `panel_class` option can be passed, which defaults to
`panel-default`.

There's also `queue-monitor::status-json`, which renders JSON suitable for
machine consumption. This allows rendering options to be passed to the
underlying `json_encode` and can be used like this:

```php
Route::get('queue-monitor.json', function () {
    $response = Response::view('queue-monitor::status-json', [
        'options' => \JSON_PRETTY_PRINT,
    ]);
    $response->header('Content-Type', 'application/json');
    return $response;
});
```

In practice you might set the cron job to run every 15 minutes, and then
automate another job (such as with a remote health checker) to run a few minutes
later, consume the JSON, and ensure all queues have the `ok` status. If any
don't, it could send an alert with a link to the HTML queue status view. It
could also check that the date at which the last check was queued is reasonable,
and so that the cron job has not stopped working.
