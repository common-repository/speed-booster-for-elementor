<?php

// autoload_static.php @generated by Composer

namespace Composer\Autoload;

class ComposerStaticInite746b7e91717afea8e09aa5829f4f475
{
    public static $prefixLengthsPsr4 = array (
        'S' => 
        array (
            'SpeedBoosterforElementor\\' => 25,
        ),
    );

    public static $prefixDirsPsr4 = array (
        'SpeedBoosterforElementor\\' => 
        array (
            0 => __DIR__ . '/../..' . '/app',
        ),
    );

    public static $classMap = array (
        'Composer\\InstalledVersions' => __DIR__ . '/..' . '/composer/InstalledVersions.php',
        'SpeedBoosterforElementor\\Blacklist\\Store' => __DIR__ . '/../..' . '/app/Blacklist/Store.php',
        'SpeedBoosterforElementor\\Blacklist\\WooCommerce' => __DIR__ . '/../..' . '/app/Blacklist/WooCommerce.php',
        'SpeedBoosterforElementor\\Common\\Admin' => __DIR__ . '/../..' . '/app/Common/Admin.php',
        'SpeedBoosterforElementor\\Common\\Api' => __DIR__ . '/../..' . '/app/Common/Api.php',
        'SpeedBoosterforElementor\\Common\\Helpers' => __DIR__ . '/../..' . '/app/Common/Helpers.php',
        'SpeedBoosterforElementor\\WPPOOLSBE' => __DIR__ . '/../..' . '/app/WPPOOLSBE.php',
    );

    public static function getInitializer(ClassLoader $loader)
    {
        return \Closure::bind(function () use ($loader) {
            $loader->prefixLengthsPsr4 = ComposerStaticInite746b7e91717afea8e09aa5829f4f475::$prefixLengthsPsr4;
            $loader->prefixDirsPsr4 = ComposerStaticInite746b7e91717afea8e09aa5829f4f475::$prefixDirsPsr4;
            $loader->classMap = ComposerStaticInite746b7e91717afea8e09aa5829f4f475::$classMap;

        }, null, ClassLoader::class);
    }
}
