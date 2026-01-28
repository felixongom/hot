<?php
namespace Hot;

use Exception;

class View
{
    protected static string $viewPath      = __DIR__ . '/views';
    protected static string $layoutPath    = __DIR__ . '/layouts';
    protected static string $componentPath = __DIR__ . '/components';
    protected static string $cachePath     = __DIR__ . '/cache';

    /* ======================================================
       PATHS
    ====================================================== */

    public static function setViewPath(string $path): void
    {
        self::$viewPath = rtrim($path, DIRECTORY_SEPARATOR);
        self::ensureDirectory(self::$viewPath);
    }

    public static function setLayoutPath(string $path): void
    {
        self::$layoutPath = rtrim($path, DIRECTORY_SEPARATOR);
        self::ensureDirectory(self::$layoutPath);
    }

    public static function setComponentPath(string $path): void
    {
        self::$componentPath = rtrim($path, DIRECTORY_SEPARATOR);
        self::ensureDirectory(self::$componentPath);
    }

    public static function setCachePath(string $path): void
    {
        self::$cachePath = rtrim($path, DIRECTORY_SEPARATOR);
        self::ensureDirectory(self::$cachePath);
    }

    protected static function ensureDirectory(string $path): void
    {
        if (!is_dir($path)) mkdir($path, 0777, true);
    }

    /* ======================================================
       ESCAPE HELPERS
    ====================================================== */

    public static function out(mixed $value): string
    {
        return htmlspecialchars((string)$value, ENT_QUOTES, 'UTF-8');
    }

    public static function raw(mixed $value): string
    {
        return (string)$value;
    }

    /* ======================================================
       RENDER
    ====================================================== */

    public static function render(string $view, array $data = [], ?string $layout = null): void
    {
        echo self::fetch($view, $data, $layout);
    }

    public static function fetch(string $view, array $data = [], ?string $layout = null): string
    {
        $content = self::evaluate(self::$viewPath, $view, $data);

        if ($layout) {
            $data['slot'] = $content; // raw slot
            return self::evaluate(self::$layoutPath, $layout, $data);
        }

        return $content;
    }

    protected static function evaluate(string $base, string $viewName, array $data): string
    {
        $file = "$base/$viewName.php";

        if (!file_exists($file)) {
            throw new Exception("View [$viewName] not found");
        }

        extract($data, EXTR_SKIP);

        ob_start();
        require self::compile($file);
        return ob_get_clean();
    }

    /* ======================================================
       COMPILER
    ====================================================== */

    protected static function compile(string $file): string
    {
        if (!is_dir(self::$cachePath)) {
            mkdir(self::$cachePath, 0777, true);
        }

        $cache = self::$cachePath . '/' . md5($file) . '.php';

        if (!file_exists($cache) || filemtime($cache) < filemtime($file)) {
            $code = file_get_contents($file);

            // Raw HTML: $$var → raw()
            $code = preg_replace_callback(
                '/\$\$(\w+)/',
                fn($m) => ' self::raw(' . '$' . $m[1] . ') ',
                $code
            );

            // Escaped: $var → out()
            $code = preg_replace_callback(
                '/<\?=\s*(\$\w+)\s*\?>/',
                fn($m) => '<?= self::out(' . $m[1] . ') ?>',
                $code
            );

            // php echo $var  → escaped
            $code = preg_replace_callback(
                '/<\?php\s+echo\s+(\$\w+)\s*;?\s*\?>/',
                fn($m) => '<?= self::out(' . $m[1] . ') ?>',
                $code
            );

            // Components
            $code = self::compileComponents($code);

            file_put_contents($cache, $code);
        }

        return $cache;
    }

    /* ======================================================
       COMPONENTS
    ====================================================== */

    protected static function compileComponents(string $html): string
    {
        // <x-name>...</x-name>
        $html = preg_replace_callback(
            '/<x-([\w\-]+)([^>]*)>(.*?)<\/x-\1>/s',
            fn($m) => self::componentCall($m[1], $m[2], $m[3]),
            $html
        );

        // <x-name ... /> self-closing
        $html = preg_replace_callback(
            '/<x-([\w\-]+)([^\/>]*)\/>/',
            fn($m) => self::componentCall($m[1], $m[2], ''),
            $html
        );

        return $html;
    }

    protected static function componentCall(string $name, string $attrString, string $slot): string
    {
        $props = [];

        preg_match_all('/([\w\-]+)="([^"]*)"/', $attrString, $matches, PREG_SET_ORDER);

        foreach ($matches as [, $key, $value]) {
            if (str_starts_with($value, '$')) {
                $props[$key] = $value;
            } else {
                $props[$key] = var_export($value, true);
            }
        }

        $propsCode = '[' . implode(',', array_map(
            fn($k, $v) => "'$k'=>$v",
            array_keys($props),
            array_values($props)
        )) . ']';

        return "<?= self::renderComponent('$name', $propsCode, " . var_export($slot, true) . ") ?>";
    }

    public static function renderComponent(string $component_name, array $props, string $slot): string
    {
        $file = self::$componentPath . "/$component_name.php";

        if (!file_exists($file)) {
            throw new Exception("Component [$component_name] not found");
        }

        $props['slot'] = $slot;

        extract($props, EXTR_SKIP);

        ob_start();
        require self::compile($file);
        return ob_get_clean();
    }
}
