<?php
namespace Hot;

use Exception;

class Template {
    protected static string $viewPath      = __DIR__ . '/views';
    protected static string $layoutPath    = __DIR__ . '/layouts';
    protected static string $componentPath = __DIR__ . '/views/components';
    protected static string $cachePath     = __DIR__ . '/storage/views';

    /* ================= PATHS ================= */

    public static function setViewPath(string $path) {
        self::$viewPath = rtrim($path, '/');
    }

    public static function setLayoutPath(string $path) {
        self::$layoutPath = rtrim($path, '/');
    }

    public static function setComponentPath(string $path) {
        self::$componentPath = rtrim($path, '/');
    }

    public static function setCachePath(string $path) {
        self::$cachePath = rtrim($path, '/');
    }

    /* ================= RENDER ================= */

    public static function render(string|int $viewOrContent, array $data = [], ?string $layout = null): void {
        echo self::fetch($viewOrContent, $data, $layout);
    }

    public static function fetch(string|int $viewOrContent, array $data = [], ?string $layout = null): string {
        if (is_numeric($viewOrContent)) {
            return (string) $viewOrContent;
        }

        $content = self::renderSource($viewOrContent, $data);

        if ($layout) {
            $layoutFile = self::$layoutPath . '/' . $layout . '.php';
            if (!file_exists($layoutFile)) {
                throw new Exception("Layout [$layout] not found");
            }

            $data['slot'] = $content;
            return self::renderFile($layoutFile, $data);
        }

        return $content;
    }

    /* ================= CORE ================= */

    protected static function renderSource(string $source, array $data): string {
        if (str_contains($source, '<') || str_contains($source, '{{')) {
            $cache = self::getCachedString($source);
            extract($data, EXTR_SKIP);
            ob_start();
            require $cache;
            return ob_get_clean();
        }

        $file = self::$viewPath . '/' . $source . '.php';
        if (!file_exists($file)) {
            throw new Exception("View [$source] not found");
        }

        return self::renderFile($file, $data);
    }

    protected static function renderFile(string $file, array $data): string {
        $cache = self::getCachedFile($file);
        extract($data, EXTR_SKIP);
        ob_start();
        require $cache;
        return ob_get_clean();
    }

    /* ================= ESCAPING ================= */

    protected static function e($value): string {
        return htmlspecialchars((string)$value, ENT_QUOTES | ENT_SUBSTITUTE, 'UTF-8', false);
    }

    protected static function raw($value): string {
        return (string) $value;
    }

    /* ================= COMPILER ================= */

    public static function compile(string $content): string {

        /* COMPONENTS */

        // <x-name />
        $content = preg_replace_callback(
            '/<x-([\w\-]+)\s*([^>]*)\/>/',
            function ($m) {
                return "<?= View::renderComponent('{$m[1]}', " .
                    self::compileProps($m[2]) . ") ?>";
            },
            $content
        );

        // <x-name>...</x-name>
        $content = preg_replace_callback(
            '/<x-([\w\-]+)\s*([^>]*)>(.*?)<\/x-\1>/s',
            function ($m) {
                return "<?= View::renderComponent('{$m[1]}', " .
                    self::compileProps($m[2]) . ", " .
                    var_export($m[3], true) . ") ?>";
            },
            $content
        );

        /* RAW */
        $content = preg_replace_callback(
            '/\{!!\s*(.+?)\s*!!\}/s',
            fn($m) => "<?= View::raw(" . self::normalizeExpression($m[1]) . ") ?>",
            $content
        );

        /* ESCAPED */
        $content = preg_replace_callback(
            '/\{\{\s*(.+?)\s*\}\}/s',
            fn($m) => "<?= View::e(" . self::normalizeExpression($m[1]) . ") ?>",
            $content
        );

        /* @include */
        $content = preg_replace_callback(
            '/@include\((.+)\)/',
            fn($m) => "<?= View::includePartial({$m[1]}) ?>",
            $content
        );

        /* @layout */
        $content = preg_replace_callback(
            '/@layout\([\'"](.+?)[\'"]\)/',
            fn($m) => "<?php echo View::extendLayout('{$m[1]}', get_defined_vars()); ?>",
            $content
        );

        return $content;
    }

    /* ================= EXPRESSIONS ================= */

    protected static function normalizeExpression(string $expr): string {
        $expr = trim($expr);

        if (str_starts_with($expr, '$')) {
            return $expr;
        }

        if (str_contains($expr, '.')) {
            $parts = explode('.', $expr);
            $var = '$' . array_shift($parts);
            foreach ($parts as $p) {
                $var .= "['$p']";
            }
            return $var;
        }

        if (str_contains($expr, '->')) {
            return '$' . $expr;
        }

        return '$' . $expr;
    }

    /* ================= COMPONENT PROPS ================= */

    protected static function compileProps(string $propString): string {
        $props = [];

        preg_match_all(
            '/([\w\-]+)\s*=\s*(["\'])(.*?)\2/',
            $propString,
            $matches,
            PREG_SET_ORDER
        );

        foreach ($matches as $m) {
            $key = $m[1];
            $val = trim($m[3]);

            if (str_starts_with($val, '$')) {
                // dynamic PHP value
                $props[] = "'$key' => $val";
            } else {
                // string value
                $props[] = "'$key' => " . var_export($val, true);
            }
        }

        return '[' . implode(', ', $props) . ']';
    }

    protected static function renderComponent(string $name, array $props = [], string $slot = ''): string {
        $file = self::$componentPath . '/' . $name . '.php';
        if (!file_exists($file)) {
            throw new Exception("Component [$name] not found");
        }

        $props['slot'] = $slot;
        return self::renderFile($file, $props);
    }

    /* ================= PARTIALS & LAYOUTS ================= */

    public static function includePartial(string $view, array $data = []): string {
        $file = self::$viewPath . '/' . $view . '.php';
        if (!file_exists($file)) {
            throw new Exception("Partial [$view] not found");
        }
        return self::renderFile($file, $data);
    }

    public static function extendLayout(string $layout, array $data): string {
        $file = self::$layoutPath . '/' . $layout . '.php';
        if (!file_exists($file)) {
            throw new Exception("Layout [$layout] not found");
        }
        return self::renderFile($file, $data);
    }

    /* ================= CACHING ================= */

    protected static function getCachedFile(string $file): string {
        $key = md5($file . filemtime($file));
        $cache = self::$cachePath . '/' . $key . '.php';

        if (!file_exists($cache)) {
            if (!is_dir(self::$cachePath)) {
                mkdir(self::$cachePath, 0777, true);
            }
            file_put_contents($cache, self::compile(file_get_contents($file)));
        }

        return $cache;
    }

    protected static function getCachedString(string $content): string {
        $key = md5($content);
        $cache = self::$cachePath . '/str_' . $key . '.php';

        if (!file_exists($cache)) {
            if (!is_dir(self::$cachePath)) {
                mkdir(self::$cachePath, 0777, true);
            }
            file_put_contents($cache, self::compile($content));
        }

        return $cache;
    }
}
