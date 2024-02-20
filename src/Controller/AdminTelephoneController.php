<?php

namespace src\Controller;

use src\Model\Telephone;

class AdminTelephoneController extends AbstractController
{
    public function list()
    {
        UserController::haveGoodRole(["Administrateur"]);
        $telephones = Telephone::SqlGetAll();
        $token = bin2hex(random_bytes(32));
        $_SESSION["token"] = $token;
        return $this->twig->render("Admin/Telephone/list.html.twig", [
            "telephones" => $telephones,
            "token" => $token
        ]);
    }

    public function delete()
    {
        if ($_SESSION["token"] == $_POST["token"]) {
            Telephone::SqlDelete($_POST["id"]);
        }
        header("Location:/AdminTelephone/list");
    }

    public function add()
    {
        if ($_SERVER["REQUEST_METHOD"] === "POST") {
            $telephone = new Telephone();
            $datePublication = new \DateTime($_POST["DatePublication"]);
            $telephone->setMarque($_POST["Marque"])
                ->setModele($_POST["Modele"])
                ->setCaracteristiques($_POST["Caracteristiques"])
                ->setPrix(floatval($_POST["Prix"]))
                ->setQuantite(intval($_POST["Quantite"]))
                ->setIDVendeur(intval($_POST["ID_Vendeur"]))
                ->setDatePublication($datePublication)
                ->setStatut($_POST["Statut"])
                ->setLongitude($_POST["Longitude"])
                ->setLatitude($_POST["Latitude"]);
    
            // Gérer le téléchargement de l'image
            $imageFileName = null;
            $sqlRepository = null;
            if (isset($_FILES["ImageFileName"]["name"])) {
                $extensionsAutorisee = ["jpg", "jpeg", "png"];
                $extension = pathinfo($_FILES["ImageFileName"]["name"], PATHINFO_EXTENSION);
                if (in_array($extension, $extensionsAutorisee)) {
                    // Créer répertoire date "2023/12"
                    $dateNow = new \DateTime();
                    $sqlRepository = $dateNow->format("Y/m");
                    $repository = "./uploads/images/{$sqlRepository}";
                    if (!is_dir($repository)) {
                        mkdir($repository, 0777, true);
                    }
                    // Renommer le fichier image
                    $imageFileName = uniqid() . "." . $extension;
    
                    // Envoyer le fichier dans le bon répertoire
                    move_uploaded_file($_FILES["ImageFileName"]["tmp_name"], $repository . "/" . $imageFileName);
                }
            }
            $telephone->setImageRepository($sqlRepository)
                ->setImageFileName($imageFileName);
    
            // Enregistrer le téléphone dans la base de données
            $id = Telephone::SqlAdd($telephone);
    
            // Rediriger vers une autre page après l'ajout
            header("Location: /Telephone/show/{$id}");
            exit();
        } else {
            return $this->twig->render("Admin/Telephone/add.html.twig");
        }
    }

    
    public function update(int $id)
    {
    $telephone = Telephone::SqlGetById($id);
    if(isset($_POST["Marque"])){
        $sqlRepository = (isset($_POST["ImageRepository"])) ? $_POST["ImageRepository"] : null;
        $imageFileName = (isset($_POST["ImageFileName"])) ? $_POST["ImageFileName"] : null;

        if(isset($_FILES["ImageFileName"]["name"])){
            $extensionsAutorisee = ["jpg", "jpeg", "png"];
            $extension = pathinfo($_FILES["ImageFileName"]["name"], PATHINFO_EXTENSION);
            if(in_array($extension, $extensionsAutorisee)){
                // Créer répertoire date "2023/12"
                $dateNow = new \DateTime();
                $sqlRepository = $dateNow->format("Y/m");
                $repository = "./uploads/images/{$sqlRepository}";
                if(!is_dir($repository)){
                    mkdir($repository, 0777, true);
                }
                // Renommer le fichier image
                $imageFileName = uniqid() . "." . $extension;

                // Envoyer le fichier dans le bon répertoire
                move_uploaded_file($_FILES["ImageFileName"]["tmp_name"], $repository . "/" . $imageFileName);
            }
            // Si une image était déjà en place, la supprimer
            if(isset($_POST["ImageFileName"]) && $_POST["ImageFileName"] != '' && file_exists("{$_SERVER["DOCUMENT_ROOT"]}/uploads/images/{$_POST["ImageRepository"]}/{$_POST["ImageFileName"]}")){
                unlink("{$_SERVER["DOCUMENT_ROOT"]}/uploads/images/{$_POST["ImageRepository"]}/{$_POST["ImageFileName"]}");
            }
        }

        $datePublication = new \DateTime($_POST["DatePublication"]);
        $telephone->setMarque($_POST["Marque"])
            ->setModele($_POST["Modele"])
            ->setCaracteristiques($_POST["Caracteristiques"])
            ->setPrix(floatval($_POST["Prix"]))
            ->setQuantite(intval($_POST["Quantite"]))
            ->setIDVendeur(intval($_POST["ID_Vendeur"]))
            ->setDatePublication($datePublication)
            ->setStatut($_POST["Statut"])
            ->setLongitude($_POST["Longitude"])
            ->setLatitude($_POST["Latitude"])
            ->setImageRepository($sqlRepository)
            ->setImageFileName($imageFileName);
        
        Telephone::SqlUpdate($telephone);
        header("Location:/Telephone/show/{$id}");
        exit();
    } else {
        return $this->twig->render("Admin/Telephone/update.html.twig", [
            "telephone" => $telephone
        ]);
    }
}
}
