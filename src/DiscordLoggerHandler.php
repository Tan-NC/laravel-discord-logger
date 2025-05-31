<?php

namespace TanNc\DiscordLogger;

use Monolog\Handler\AbstractProcessingHandler;
use Monolog\Logger;
use Illuminate\Support\Facades\Http;

class DiscordLoggerHandler extends AbstractProcessingHandler
{
    protected $webhookUrl;

    public function __construct($level = Logger::DEBUG, $bubble = true)
    {
        parent::__construct($level, $bubble);
        $this->webhookUrl = config('discord-logger.webhook_url');
    }

    protected function write(array $record): void
    {
        $level = strtoupper($record['level_name']);
        $message = $record['message'];
        $context = $record['context'] ?? [];

        $color = match ($level) {
            'DEBUG', 'INFO' => 3066993,
            'WARNING'       => 16776960,
            'ERROR'         => 15158332,
            'CRITICAL', 'ALERT', 'EMERGENCY' => 10038562,
            default         => 3447003,
        };

        $embed = [
            'title' => "[$level] Thông báo từ Laravel",
            'description' => $message,
            'color' => $color,
            'fields' => [],
            'timestamp' => now()->toIso8601String(),
        ];

        foreach ($context as $key => $value) {
            $embed['fields'][] = [
                'name' => ucfirst($key),
                'value' => is_scalar($value) ? (string) $value : json_encode($value),
                'inline' => false,
            ];
        }

        Http::post($this->webhookUrl, [
            'embeds' => [$embed],
        ]);
    }
}