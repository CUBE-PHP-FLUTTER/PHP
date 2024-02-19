<?php

namespace src\Controller;

class ErrorController extends AbstractController
{
    public function showMessage(\Exception $e) :string
    {
        return $this->twig->render("error.html.twig",[
            "code" => $e->getCode(),
            "message" => $e->getMessage()
        ]);
    }

}