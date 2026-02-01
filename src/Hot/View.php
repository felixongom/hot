<?php
namespace Hot;

use Exception;

class View
{
    protected static string $viewPath      = __DIR__ . '/views';
    protected static string $layoutPath    = __DIR__ . '/layouts';
    protected static string $componentPath = __DIR__ . '/components';
    protected static string $cachePath     = __DIR__ . '/cache';
    protected static string $view_extension = '.php';
    protected static array $gloabal_variables = [];
    protected static string $current_layout = '';
    protected static string $current_page = '';

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

    public static function setViewExtension(string $extension): void
    {
        self::$view_extension = $extension;
    }
    public static function setGlobalVariables(array $gloabal_variables): void
    {
        self::$gloabal_variables = [...self::$gloabal_variables, ...$gloabal_variables];
    }

    protected static function ensureDirectory(string $path): void
    {
        if (!is_dir($path)) {
            mkdir($path, 0777, true);
        }
    }

    /* ======================================================
       ESCAPING
    ====================================================== */

    public static function out(mixed $value): string
    {
        return htmlspecialchars((string) $value, ENT_QUOTES, 'UTF-8');
    }

    public static function raw(mixed $value): string
    {
        return (string) $value;
    }

    /* ======================================================
       RENDERING
    ====================================================== */

    public static function render(string $view, array $data = [], ?string $layout = null): void
    {
        echo self::fetch($view, $data, $layout);
    }

    public static function fetch(string $view, array $data = [], ?string $layout = null): string
    { 
        // 
        self::$gloabal_variables = [
            ...self::$gloabal_variables,
            'current_layout'=>$layout,
            'pathname'=>Url::incomingPath()
        ];
        foreach ($data as $key =>$value) {
            if(str_starts_with($key, 'global')){
                self::$gloabal_variables = [
                    ...self::$gloabal_variables, 
                    $key=>$value
                ];
            }
        }
        // 

        if (self::isInlineTemplate($view)) {
            $content = self::evaluateString($view, [...self::$gloabal_variables,...$data, ]);
        } else {
            $page_dotted_path = explode('.', $view);
            // 
            self::$gloabal_variables = [
                ...self::$gloabal_variables,
                'current_page'=>$page_dotted_path,
                'current_page_name'=>end($page_dotted_path)
                ];
            $content = self::evaluate(self::$viewPath, $view, [...self::$gloabal_variables,...$data]);
        }

        if ($layout) {
            $data['slot'] = $content;
            return self::evaluate(self::$layoutPath, $layout, [...self::$gloabal_variables,...$data]);
        }

        return $content;
    }

    protected static function isInlineTemplate(string $view): bool
    {
        return (
            str_contains($view, '<') &&
            str_contains($view, '>')
        ) || str_contains(trim($view), ' ');
    }

    /* ======================================================
       FILE EVALUATION
    ====================================================== */

    protected static function evaluate(string $base, string $view, array $data): string
    {
       
        $file = self::resolvePath($base, $view);
        if (!file_exists($file)) {
            throw new Exception("View [$view] not found");
        }

        extract($data, EXTR_SKIP);

        ob_start();
        require self::compile($file);
        return ob_get_clean();
    }

    protected static function resolvePath(string $base, string $name): string
    {
        $path = str_replace('.', DIRECTORY_SEPARATOR, $name);
        $path = trim($path, DIRECTORY_SEPARATOR);
        return $base . DIRECTORY_SEPARATOR . $path . self::$view_extension ;
    }

    /* ======================================================
       INLINE TEMPLATE EVALUATION
    ====================================================== */

    protected static function evaluateString(string $template, array $data): string
    {
        // $$var → raw output
            $code = preg_replace_callback(
                '/\$\$(\w+)/',
                fn($m) => 'self::raw($' . $m[1] . ')',
                $template
            );

            //  $var  → escaped
            $code = preg_replace_callback(
                '/<\?=\s*(\$\w+)\s*\?>/',
                fn($m) => '<?= self::out(' . $m[1] . ') ?>',
                $template
            );

            // php echo $var  → escaped
            $code = preg_replace_callback(
                '/<\?php\s+echo\s+(\$\w+)\s*;?\s*\?>/',
                fn($m) => '<?= self::out(' . $m[1] . ') ?>',
                $template
            );

        extract($data, EXTR_SKIP);

        ob_start();
        eval('?>' . $template);
        return ob_get_clean();
    }

    /* ======================================================
       COMPILER
    ====================================================== */

    protected static function compile(string $file): string
    {
        self::ensureDirectory(self::$cachePath);

        $cache = self::$cachePath . '/' . md5($file) . '.php' ;

        if (!file_exists($cache) || filemtime($cache) < filemtime($file)) {
            $code = file_get_contents($file);

            // $$var → raw output
            $code = preg_replace_callback(
                '/\$\$(\w+)/',
                fn($m) => 'self::raw($' . $m[1] . ')',
                $code
            );

            //  $var  → escaped
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
        do {
            $old = $html;

            // Paired components
            $html = preg_replace_callback(
                '/<x-([\w\.\-]+)([^>]*)>(.*?)<\/x-\1>/s',
                fn($m) => self::componentCall($m[1], $m[2], $m[3]),
                $html
            );

            // Self-closing components
            $html = preg_replace_callback(
                '/<x-([\w\.\-]+)([^>]*)\/>/',
                fn($m) => self::componentCall($m[1], $m[2], ''),
                $html
            );

        } while ($html !== $old); // 🔥 keep compiling until stable

        return $html;
    }


    protected static function componentCall(string $name, string $attrString, string $slot): string
    {
        $props = [];

        preg_match_all('/([\w\-]+)="([^"]*)"/', $attrString, $matches, PREG_SET_ORDER);

        foreach ($matches as [, $key, $value]) {
            $props[$key] = str_starts_with($value, '$')
                ? $value
                : var_export($value, true);
        }

        $propsCode = '[' . implode(',', array_map(
            fn($k, $v) => "'$k'=>$v",
            array_keys($props),
            array_values($props)
        )) . ']';

        // unique slot variable
        $slotVar = '__slot_' . uniqid();

        return <<<PHP
    <?php ob_start(); ?>
    $slot
    <?php \$$slotVar = ob_get_clean(); ?>
    <?= self::renderComponent('$name', $propsCode, \$$slotVar) ?>
    PHP;
    }




    public static function renderComponent(string $current_component, array $props, string $slot): string
    {
        $path = str_replace('.', DIRECTORY_SEPARATOR, $current_component);
        $file = self::$componentPath . DIRECTORY_SEPARATOR . $path . self::$view_extension;
        $_component_name = explode('.', $current_component);
        $current_component_name = end($_component_name);
        // 
        if (!file_exists($file)) {
            throw new Exception("Component [$current_component] not found");
        }

        // 🔥 render slot FIRST (so nested components work)
        $props['slot'] = self::evaluateString(
            self::compileComponents($slot),
            [...self::$gloabal_variables, ...$props]
        );

        extract([...self::$gloabal_variables, ...$props], EXTR_SKIP);

        ob_start();
        require self::compile($file);
        return ob_get_clean();
    }

}
