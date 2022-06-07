<?php

// autoload_static.php @generated by Composer

namespace Composer\Autoload;

class ComposerStaticInitc8cef8a203ce15a78b642a5ebe3f26b9
{
    public static $prefixLengthsPsr4 = array (
        'H' => 
        array (
            'Hyperpay\\ConnectIn\\' => 19,
        ),
    );

    public static $prefixDirsPsr4 = array (
        'Hyperpay\\ConnectIn\\' => 
        array (
            0 => __DIR__ . '/../..' . '/src',
        ),
    );

    public static $classMap = array (
        'Composer\\InstalledVersions' => __DIR__ . '/..' . '/composer/InstalledVersions.php',
        'Hyperpay\\ConnectIn\\Http\\Controllers\\ConnectInController' => __DIR__ . '/../..' . '/src/Http/Controllers/ConnectInController.php',
    );

    public static function getInitializer(ClassLoader $loader)
    {
        return \Closure::bind(function () use ($loader) {
            $loader->prefixLengthsPsr4 = ComposerStaticInitc8cef8a203ce15a78b642a5ebe3f26b9::$prefixLengthsPsr4;
            $loader->prefixDirsPsr4 = ComposerStaticInitc8cef8a203ce15a78b642a5ebe3f26b9::$prefixDirsPsr4;
            $loader->classMap = ComposerStaticInitc8cef8a203ce15a78b642a5ebe3f26b9::$classMap;

        }, null, ClassLoader::class);
    }
}