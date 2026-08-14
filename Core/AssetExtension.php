<?php

namespace Core;

use Twig\Extension\AbstractExtension;
use Twig\TwigFunction;

class AssetExtension extends AbstractExtension
{
    private $manifest;
    private $dev;

    public function __construct($manifestPath, $dev = false)
    {
        $this->dev = $dev;
        if (file_exists($manifestPath)) {
            $this->manifest = json_decode(file_get_contents($manifestPath), true);
        }
    }

    public function getFunctions()
    {
        return [
            new TwigFunction('asset', [$this, 'getAssetPath']),
        ];
    }
    // returns the correct asset path, unhashed in dev mode and manifest with hashed name in production
    // public function getAssetPath($file)
    // {
    //     //If your files are physically in /public/js/ but your VHost root is the project root:
    //     $basePath = "/public/";

    //     //If your VHost already points INSIDE the public folder:
    //     $basePath = "/";

    //     if ($this->dev) {
    //         return $basePath . $file;
    //     }
    //     return $basePath . ($this->manifest[$file] ?? $file);
    // }
    public function getAssetPath($file)
    {
      if ($this->dev) {
            return "/" . $file; // no hash in dev
        }
        return "/" . ($this->manifest[$file] ?? $file);
    }
}
