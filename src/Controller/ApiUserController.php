<?php

namespace src\Controller;

use src\Model\User;

class ApiUserController extends AbstractController
{
    public function login()
    {
        $json = file_get_contents('php://input');
        $data = json_decode($json, true);
        
        if(!isset($data["mail"], $data["password"]))
        {
            throw new \Exception("Aucun user avec ce mail en base"); 
        }
        $user = User::SqlGetByMail($data["mail"]);
        if($user == null)
        {
            throw new \Exception("Adresse email invalide {$data["mail"]}");
        }
        if(!password_verify($data["password"], $user->getPassword()))
        {
            throw new \Exception("Mot de passe incorrect pour {$data["password"]}");
        }
    }
}