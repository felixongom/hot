<?php
namespace Hot;

use Exception;

class View {
    protected static $viewPath = __DIR__ . '/views';

    public static function setPath($path) {
        self::$viewPath = rtrim($path, '/');
    }

    // Auto-echo render
    public static function render($file, $data = []) {
        $filePath = self::$viewPath . '/' . $file . '.php';
        if (!file_exists($filePath)) {
            throw new Exception("View file not found: $filePath");
        }

        extract($data); // make variables available
        ob_start();
        $content = file_get_contents($filePath);

        // Layout directives (@layout or {{ layout() }})
        $content = preg_replace_callback(
            '/(?:@|{{\s*)layout\((.*?)\)(?:\s*}})?/',
            function($matches) use ($data) {
                $args = self::parseArgs($matches[1], $data);
                $layoutFile = array_shift($args);
                return self::render($layoutFile, $args);
            },
            $content
        );

        // Include directives (@include or {{ include() }})
        $content = preg_replace_callback(
            '/(?:@|{{\s*)include\((.*?)\)(?:\s*}})?/',
            function($matches) use ($data) {
                $args = self::parseArgs($matches[1], $data);
                $includeFile = array_shift($args);
                return self::render($includeFile, $args);
            },
            $content
        );

        // Replace {{ variable }} or {{ nested.variable }}
        $content = preg_replace_callback('/{{\s*(.*?)\s*}}/', function($matches) use ($data) {
            return self::resolveVariable($matches[1], $data);
        }, $content);

        eval('?>' . $content);

        // Auto-echo the rendered content
        ob_end_flush();
    }

    protected static function parseArgs($string, $parentData = []) {
        $string = trim($string);
        $args = [];
        if (strpos($string, ',') !== false) {
            $parts = explode(',', $string, 2);
            $file = trim($parts[0], "'\" ");
            $vars = trim($parts[1]);
            $array = [];
            eval('$array = ' . $vars . ';');
            $args[] = $file;
            $args[] = array_merge($parentData, $array);
        } else {
            $args[] = trim($string, "'\" ");
            $args[] = $parentData;
        }
        return $args;
    }

    protected static function resolveVariable($key, $data) {
        $keys = explode('.', $key);
        $value = $data;
        foreach ($keys as $k) {
            if (is_array($value) && isset($value[$k])) {
                $value = $value[$k];
            } else {
                return '';
            }
        }
        return $value;
    }
}


