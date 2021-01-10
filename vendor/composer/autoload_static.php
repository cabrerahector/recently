<?php

// autoload_static.php @generated by Composer

namespace Composer\Autoload;

class ComposerStaticInitb2eb25f9aec7c0b3185b4edf09813b72
{
    public static $prefixLengthsPsr4 = array (
        'R' => 
        array (
            'Recently\\' => 9,
        ),
    );

    public static $prefixDirsPsr4 = array (
        'Recently\\' => 
        array (
            0 => __DIR__ . '/../..' . '/src',
        ),
    );

    public static $classMap = array (
        'Composer\\InstalledVersions' => __DIR__ . '/..' . '/composer/InstalledVersions.php',
    );

    public static function getInitializer(ClassLoader $loader)
    {
        return \Closure::bind(function () use ($loader) {
            $loader->prefixLengthsPsr4 = ComposerStaticInitb2eb25f9aec7c0b3185b4edf09813b72::$prefixLengthsPsr4;
            $loader->prefixDirsPsr4 = ComposerStaticInitb2eb25f9aec7c0b3185b4edf09813b72::$prefixDirsPsr4;
            $loader->classMap = ComposerStaticInitb2eb25f9aec7c0b3185b4edf09813b72::$classMap;

        }, null, ClassLoader::class);
    }
}
