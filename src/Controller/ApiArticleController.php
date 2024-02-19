<?php

namespace src\Controller;

use src\Model\Article;
use src\Service\JwtService;

class ApiArticleController
{
    public function __construct()
    {
        header("Content-Type: application/json; charset=utf-8");
    }

    public function getAll()
    {
        if($_SERVER["REQUEST_METHOD"] != "GET"){
            header("HTTP/1.1 405 Method Not Allowed");
            return json_encode([
                "code" => 1,
                "Message" => "Get Attendu"
            ]);
        }
        $result = JwtService::checkToken();
        if($result["code"] == 1){
            return json_encode($result);
        }
        //var_dump($result);
        if(!in_array("Administrateur",$result["data"]->roles)){
            return json_encode("Vous n'avez pa le bon role");
        }

        $article = Article::SqlGetAll();
        return json_encode($article);

    }

    public function add()
    {
        if($_SERVER["REQUEST_METHOD"] != "POST"){
            header("HTTP/1.1 405 Method Not Allowed");
            return json_encode([
                "code" => 1,
                "Message" => "POST Attendu"
            ]);
        }
        //Récupération du body en String
        $data = file_get_contents("php://input");
        //Conversion du string en JSON
        $json = json_decode($data);

        if(empty($json)){
            header("HTTP/1.1 403 Forbiden");
            return json_encode([
                "code" => 1,
                "Message" => "Il faut des données"
            ]);
        }

        if(!isset($json->Titre) || !isset($json->Description)){
            header("HTTP/1.1 403 Forbiden");
            return json_encode([
                "code" => 1,
                "Message" => "Il faut des données"
            ]);
        }

        //Upload de l'image
        $sqlRepository = null;
        $nomImage = null;

        if(isset($json->Image)){
            // Renommer le fichier image (normalement le client envoi aussi le nom de l'image dans un autre champ pour tester les extension etc.)
            $nomImage = uniqid().".jpg";

            // Fabriquer répertoire d'accueil
            $dateNow = new \DateTime();
            $sqlRepository = $dateNow->format("Y/m");
            $repository = "{$_SERVER["DOCUMENT_ROOT"]}/uploads/images/{$sqlRepository}";
            if(!is_dir($repository)){
                mkdir($repository,0777,true);
            }


            $ifp = fopen("{$repository}/{$nomImage}", "wb");
            fwrite($ifp, base64_decode($json->Image));
            fclose($ifp);

        }


        $article = new Article();
        $article->setTitre($json->Titre)
            ->setDescription($json->Description)
            ->setDatePublication(new \DateTime($json->DatePublication))
            ->setImageRepository($sqlRepository)
            ->setImageFileName($nomImage)
            ->setAuteur($json->Auteur);
        $id = Article::SqlAdd($article);
        return json_encode([
            "code" => 0,
            "Message" => "Article ajouté avec succès",
            "Id" => $id
        ]);
    }

    public function search()
    {
        if($_SERVER["REQUEST_METHOD"] != "POST"){
            header("HTTP/1.1 405 Method Not Allowed");
            return json_encode([
                "code" => 1,
                "Message" => "Post Attendu"
            ]);
        }



        //Récupération du body en String
        $data = file_get_contents("php://input");
        //Conversion du string en JSON
        $json = json_decode($data);

        if(!isset($json->keyword)){
            header("HTTP/1.1 403 Forbiden");
            return json_encode([
                "code" => 1,
                "Message" => "GET keyword manquant"
            ]);
        }


        $articles = Article::SqlSearch($json->keyword);
        return json_encode($articles);
    }

}