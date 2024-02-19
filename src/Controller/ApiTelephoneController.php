<?php

namespace src\Controller;

use src\Model\Telephone;
use src\Service\JwtService;

class ApiTelephoneController
{
    public function __construct()
    {
        header("Content-Type: application/json; charset=utf-8");
    }

    public function getAll()
    {
        if ($_SERVER["REQUEST_METHOD"] != "GET") {
            header("HTTP/1.1 405 Method Not Allowed");
            return json_encode([
                "code" => 1,
                "Message" => "Get Attendu"
            ]);
        }
        $result = JwtService::checkToken();
        if ($result["code"] == 1) {
            return json_encode($result);
        }
        if (!in_array("Administrateur", $result["data"]->roles)) {
            return json_encode("Vous n'avez pas le bon rôle");
        }

        $telephones = Telephone::SqlGetAll();
        return json_encode($telephones);
    }

    public function add()
    {
        if ($_SERVER["REQUEST_METHOD"] != "POST") {
            header("HTTP/1.1 405 Method Not Allowed");
            return json_encode([
                "code" => 1,
                "Message" => "POST Attendu"
            ]);
        }
        // Récupération du corps de la requête en chaîne
        $data = file_get_contents("php://input");
        // Conversion de la chaîne en JSON
        $json = json_decode($data);

        if (empty($json)) {
            header("HTTP/1.1 403 Forbidden");
            return json_encode([
                "code" => 1,
                "Message" => "Il faut des données"
            ]);
        }

        // Upload de l'image
        $sqlRepository = null;
        $nomImage = null;

        if (isset($json->Image)) {
            // Renommer le fichier image (normalement le client envoie aussi le nom de l'image dans un autre champ pour tester les extensions, etc.)
            $nomImage = uniqid() . ".jpg";

            // Fabriquer le répertoire d'accueil
            $dateNow = new \DateTime();
            $sqlRepository = $dateNow->format("Y/m");
            $repository = "{$_SERVER["DOCUMENT_ROOT"]}/uploads/images/{$sqlRepository}";
            if (!is_dir($repository)) {
                mkdir($repository, 0777, true);
            }

            $ifp = fopen("{$repository}/{$nomImage}", "wb");
            fwrite($ifp, base64_decode($json->Image));
            fclose($ifp);
        }

        $telephone = new Telephone();
        $telephone->setMarque($json->Marque)
            ->setModele($json->Modele)
            ->setCaracteristiques($json->Caracteristiques)
            ->setPrix($json->Prix)
            ->setQuantite($json->Quantite)
            ->setIDVendeur($json->ID_Vendeur)
            ->setDatePublication(new \DateTime($json->DatePublication))
            ->setStatut($json->Statut)
            ->setImageFileName($nomImage);

        $id = Telephone::SqlAdd($telephone);
        return json_encode([
            "code" => 0,
            "Message" => "Téléphone ajouté avec succès",
            "Id" => $id
        ]);
    }

    public function search()
    {
        if ($_SERVER["REQUEST_METHOD"] != "POST") {
            header("HTTP/1.1 405 Method Not Allowed");
            return json_encode([
                "code" => 1,
                "Message" => "POST Attendu"
            ]);
        }

        // Récupération du corps de la requête en chaîne
        $data = file_get_contents("php://input");
        // Conversion de la chaîne en JSON
        $json = json_decode($data);

        if (!isset($json->keyword)) {
            header("HTTP/1.1 403 Forbidden");
            return json_encode([
                "code" => 1,
                "Message" => "GET keyword manquant"
            ]);
        }

        $telephones = Telephone::SqlSearch($json->keyword);
        return json_encode($telephones);
    }
}
