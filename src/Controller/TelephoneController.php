<?php

namespace src\Controller;

use Mpdf\Mpdf;
use Mpdf\Output\Destination;
use src\Model\Telephone;

class TelephoneController extends AbstractController
{
    public function index()
    {
        $telephones = Telephone::SqlGetLast(20);
        return $this->twig->render("Telephone/index.html.twig", [
            "telephones" => $telephones
        ]);
    }

    public function show(int $id)
    {
        $telephone = Telephone::SqlGetById($id);
        return $this->twig->render("Telephone/details.html.twig", [
            "produit" => $telephone
        ]);
    }

    public function pdf(int $id)
    {
        $telephone = Telephone::SqlGetById($id);
        $mpdf = new Mpdf([
            "tempDir" => $_SERVER["DOCUMENT_ROOT"] . "/../var/cache/pdf/"
        ]);

        $mpdf->WriteHTML($this->twig->render("Telephone/pdf.html.twig", [
            "telephone" => $telephone
        ]));

        $mpdf->Output("{$_SERVER["DOCUMENT_ROOT"]}/uploads/pdf/Telephone-{$telephone->getIDTelephone()}.pdf", Destination::FILE);
        header("Location:/Telephone/show/{$telephone->getIDTelephone()}");
    }

    public function fixtures()
    {
        UserController::haveGoodRole(["Administrateur"]);

        // Supprimer tous les enregistrements existants dans la table des téléphones
        Telephone::SqlTruncateTable();

        // Jeu de données pour les marques et modèles de téléphone
        $arrayMarque = ["Apple", "Samsung", "Google", "Huawei", "Xiaomi"];
        $arrayModele = ["iPhone 13", "Galaxy S21", "Pixel 6", "P40", "Mi 11"];

        // Date actuelle
        $dateDuJour = new \DateTime();

        // Insertion de 200 entrées de données de téléphone avec des valeurs aléatoires
        for ($i = 1; $i <= 200; $i++) {
            $dateDuJour->modify("+1 day");
            shuffle($arrayMarque);
            shuffle($arrayModele);
            $telephone = new Telephone();
            $telephone->setMarque($arrayMarque[0])
                ->setModele($arrayModele[0])
                ->setCaracteristiques("Lorem ipsum dolor sit amet, consectetur adipiscing elit.")
                ->setPrix(999.99)
                ->setQuantite(100)
                ->setIDVendeur(1)
                ->setDatePublication($dateDuJour)
                ->setStatut("En stock")
                ->setLongitude(mt_rand(-180, 180)) // Ajout de la longitude aléatoire
                ->setLatitude(mt_rand(-90, 90));   // Ajout de la latitude aléatoire
            Telephone::SqlAdd($telephone);
        }

        header("Location:/AdminTelephone/list");
    }
}
