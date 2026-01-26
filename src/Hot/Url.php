<?php

namespace Hot;
use RuntimeException;

class Url{
    /* ===============================
     * Environment detection
     * =============================== */

    /**
     * Check if PHP is running from CLI (terminal)
     */
    protected static function isCli(): bool
    {
        return PHP_SAPI === 'cli';
    }

    /* ===============================
     * Internal helpers
     * =============================== */

    /**
     * Detect request scheme (http or https)
     * Supports reverse proxies (X-Forwarded-Proto)
     */
    public static function scheme(): string
    {
        // CLI has no HTTP request, so fallback
        if (self::isCli()) {
            return 'http';
        }

        return $_SERVER['HTTP_X_FORWARDED_PROTO']
            ?? (
                // Standard HTTPS detection
                (!empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] !== 'off'
                || ($_SERVER['SERVER_PORT'] ?? null) == 443)
                ? 'https'
                : 'http'
            );
    }

    /**
     * Detect request host (domain)
     * Supports reverse proxies (X-Forwarded-Host)
     */
    public static function host(): string
    {
        // CLI fallback
        if (self::isCli()) {
            return 'localhost';
        }

        return $_SERVER['HTTP_X_FORWARDED_HOST']
            ?? $_SERVER['HTTP_HOST']
            ?? $_SERVER['SERVER_NAME']
            ?? 'localhost';
    }

    /**
     * Build query string from array or string
     */
    public static function buildQuery(string|array|null $query): string
    {
        if (empty($query)) {
            return '';
        }

        // Convert associative array to query string
        if (is_array($query)) {
            return '?' . http_build_query($query);
        }

        // Ensure string query starts without "?"
        return '?' . ltrim($query, '?');
    }

    /**
     * Detect base path of the application
     * (handles apps inside subfolders)
     */
    protected static function scriptBasePath(): string
    {
        if (self::isCli()) {
            return '';
        }

        return rtrim(dirname($_SERVER['SCRIPT_NAME']), '/');
    }

    /* ===============================
     * Public API
     * =============================== */

    /**
     * Base URL of incoming request (includes app subfolder)
     */
    public static function incomingBase(string|array|null $query = null): string
    {
        return self::scheme()
            . '://'
            . self::host()
            . self::scriptBasePath()
            . self::buildQuery($query);
    }

    /**
     * Full incoming URL (path + query)
     */
    public static function incomingFull(string|array|null $query = null): string
    {
        if (self::isCli()) {
            return self::serverBase($query);
        }

        // Remove query from REQUEST_URI
        $path = strtok($_SERVER['REQUEST_URI'], '?');

        return self::scheme()
            . '://'
            . self::host()
            . $path
            . self::buildQuery($query);
    }

    /**
     * Incoming path only (no scheme, no host)
     */
    public static function incomingPath(string|array|null $query = null): string
    {
        if (self::isCli()) {
            return '';
        }

        $path = strtok($_SERVER['REQUEST_URI'], '?');

        return $path . self::buildQuery($query);
    }

    /**
     * Server base URL (domain only)
     */
    public static function serverBase(string|array|null $query = null): string
    {
        return self::scheme()
            . '://'
            . self::host()
            . self::buildQuery($query);
    }

    /**
     * Redirect to any URL
     */
    public static function redirect(
        string $url,
        string|array|null $query = null,
        int $status = 302
    ): never {
        // Redirects do not make sense in CLI
        if (self::isCli()) {
            throw new RuntimeException('Redirects are not supported in CLI environment.');
        }

        // Send HTTP redirect header
        header('Location: ' . $url . self::buildQuery($query), true, $status);
        exit;
    }

    /**
     * Redirect back to the previous page
     *
     * Uses HTTP_REFERER if available.
     * Falls back to the application base URL.
     */
    public static function redirectBack(
        string|array|null $query = null,
        int $status = 302
    ): never {
        if (self::isCli()) {
            throw new RuntimeException('Redirect back is not supported in CLI environment.');
        }

        // HTTP_REFERER contains the previous page URL (sent by the browser)
        $referer = $_SERVER['HTTP_REFERER'] ?? null;

        // If referer is missing, fallback to app base
        $target = $referer ?: self::incomingBase();

        header('Location: ' . $target . self::buildQuery($query), true, $status);
        exit;
    }
}
