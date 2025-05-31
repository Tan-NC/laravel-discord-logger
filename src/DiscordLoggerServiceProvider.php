<?php

namespace TanNC\DiscordLogger;

use Illuminate\Support\ServiceProvider;
use Monolog\Logger;

class DiscordLoggerServiceProvider extends ServiceProvider
{
    public function register()
    {
        $this->mergeConfigFrom(__DIR__.'/../config/discord-logger.php', 'discord-logger');
    }

    public function boot()
    {
        $this->publishes([
            __DIR__.'/../config/discord-logger.php' => config_path('discord-logger.php'),
        ], 'config');

        $this->app['log']->extend('discord', function ($app, array $config) {
            return new Logger('discord', [
                new DiscordLoggerHandler(
                    Logger::toMonologLevel($config['level'] ?? 'debug')
                ),
            ]);
        });
    }
}
