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
        if (isset($_POST["Marque"])) {
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
                ->setImageFileName($_POST["ImageFileName"])
                ->setLongitude($_POST["longitude"])
                ->setLatitude($_POST["latitude"]);

            $id = Telephone::SqlAdd($telephone);
            header("Location:/Telephone/show/{$id}");
            exit();
        } else {
            return $this->twig->render("Admin/Telephone/add.html.twig");
        }
    }

    public function update(int $id)
    {
        $telephone = Telephone::SqlGetById($id);
        if (isset($_POST["Marque"])) {
            $datePublication = new \DateTime($_POST["DatePublication"]);
            $telephone->setMarque($_POST["Marque"])
                ->setModele($_POST["Modele"])
                ->setCaracteristiques($_POST["Caracteristiques"])
                ->setPrix(floatval($_POST["Prix"]))
                ->setQuantite(intval($_POST["Quantite"]))
                ->setIDVendeur(intval($_POST["ID_Vendeur"]))
                ->setDatePublication($datePublication)
                ->setStatut($_POST["Statut"])
                ->setImageFileName($_POST["ImageFileName"])
                ->setLongitude($_POST["longitude"])
                ->setLatitude($_POST["latitude"]);

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
