<?php
namespace Hot;

use Exception;

class Template {
    protected static string $viewPath   = __DIR__ . '/views';
    protected static string $layoutPath = __DIR__ . '/layouts';

    public static function setViewPath(string $path) {
        self::$viewPath = rtrim($path, '/');
    }

    public static function setLayoutPath(string $path) {
        self::$layoutPath = rtrim($path, '/');
    }

    /**
     * Auto-echo the output (string, number, or template).
     */
    public static function render(string|int $viewOrContent, array $data = [], ?string $layout = null): void {
        echo self::fetch($viewOrContent, $data, $layout);
    }

    /**
     * Return output as string (string, number, or template).
     */
    public static function fetch(string|int $viewOrContent, array $data = [], ?string $layout = null): string {
        // Numbers → just return
        if (is_numeric($viewOrContent)) {
            return (string) $viewOrContent;
        }

        // Raw string template
        if (strpos($viewOrContent, '<') !== false || strpos($viewOrContent, '{{') !== false) {
            $compiled = self::compile($viewOrContent);
            extract($data, EXTR_SKIP);
            ob_start();
            eval("?>$compiled<?php ");
            $content = ob_get_clean();
        } else {
            // Template file
            $file = self::$viewPath . '/' . $viewOrContent . '.php';
            if (!file_exists($file)) {
                throw new Exception("View [$viewOrContent] not found at " . $file);
            }
            $compiled = self::compile(file_get_contents($file));
            extract($data, EXTR_SKIP);
            ob_start();
            eval("?>$compiled<?php ");
            $content = ob_get_clean();
        }

        // Apply layout if provided
        if ($layout) {
            $layoutFile = self::$layoutPath . '/' . $layout . '.php';
            if (!file_exists($layoutFile)) {
                throw new Exception("Layout [$layout] not found at " . $layoutFile);
            }

            // ✅ FIX: inject $slot into data so layout can access it
            $data['slot'] = $content;

            $compiledLayout = self::compile(file_get_contents($layoutFile));
            extract($data, EXTR_SKIP);
            ob_start();
            eval("?>$compiledLayout<?php ");
            return ob_get_clean();
        }

        return $content;
    }

    /**
     * Compile template syntax.
     */
    public static function compile(string $content): string {
        // {{ var }} → echo
        $content = preg_replace_callback('/\{\{\s*(.+?)\s*\}\}/', function ($m) {
            $expr = trim($m[1]);

            // {{ include(...) }}
            if (preg_match('/^include\((.+)\)$/i', $expr, $inc)) {
                return "<?= View::includePartial({$inc[1]}) ?>";
            }

            // remove optional $ prefix
            $expr = ltrim($expr, '$');

            // dot syntax
            if (strpos($expr, '.') !== false) {
                $parts = explode('.', $expr);
                $var = '$' . array_shift($parts);
                foreach ($parts as $p) {
                    $var .= "['$p']";
                }
                return "<?= $var ?>";
            }

            // arrow syntax
            if (strpos($expr, '->') !== false) {
                $parts = explode('->', $expr);
                $var = '$' . array_shift($parts);
                foreach ($parts as $p) {
                    $var .= "->{$p}";
                }
                return "<?= $var ?>";
            }

            return "<?= \$$expr ?>";
        }, $content);

        // @include(...)
        $content = preg_replace_callback('/@include\((.+)\)/', function ($m) {
            return "<?= View::includePartial({$m[1]}) ?>";
        }, $content);

        // @layout(...)
        $content = preg_replace_callback('/@layout\([\'"](.+?)[\'"]\)/', function ($m) {
            return "<?php echo View::extendLayout('{$m[1]}', get_defined_vars()); ?>";
        }, $content);

        return $content;
    }

    /**
     * Include partial with data.
     */
    public static function includePartial(string $view, array $data = []): string {
        $file = self::$viewPath . '/' . $view . '.php';
        if (!file_exists($file)) {
            throw new Exception("Partial [$view] not found at " . $file);
        }
        $compiled = self::compile(file_get_contents($file));
        extract($data, EXTR_SKIP);
        ob_start();
        eval("?>$compiled<?php ");
        return ob_get_clean();
    }

    /**
     * Extend another layout from inside a layout.
     */
    public static function extendLayout(string $layout, array $data): string {
        $file = self::$layoutPath . '/' . $layout . '.php';
        if (!file_exists($file)) {
            throw new Exception("Layout [$layout] not found at " . $file);
        }
        $compiled = self::compile(file_get_contents($file));
        extract($data, EXTR_SKIP);
        ob_start();
        eval("?>$compiled<?php ");
        return ob_get_clean();
    }
}





