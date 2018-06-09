<?php

class AutoLoader
{
    public static function init()
    {
        spl_autoload_register(function ($class)
        {
            $filename = __DIR__ . '/../' . str_replace('\\', '/', $class) . ".php";

            if (file_exists($filename)) {
                include ($filename);

                if (class_exists($class)) {
                    return true;
                }
            }
            return false;
        });
    }
}
