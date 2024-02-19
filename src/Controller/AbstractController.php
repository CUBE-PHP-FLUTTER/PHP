<?php

namespace src\Controller;

use Twig\Extension\DebugExtension;
use Twig\TwigFunction;

abstract class AbstractController
{
    protected $twig;
    public function __construct()
    {
        $loader = new \Twig\Loader\FilesystemLoader($_SERVER["DOCUMENT_ROOT"]."/../src/View");
        $this->twig = new \Twig\Environment($loader, [
            'cache' => $_SERVER["DOCUMENT_ROOT"]."/../var/cache",
            'debug' => true
        ]);
        $this->twig->addExtension(new DebugExtension());
        $fileExist = new TwigFunction('file_exist',function(string $filepath){
            return file_exists($filepath);
        });
        $this->twig->addFunction($fileExist);
        $this->twig->addGlobal("session", $_SESSION);
    }
}