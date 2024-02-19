<?php

namespace src\Controller;

use Mpdf\Mpdf;
use Mpdf\Output\Destination;
use src\Model\BDD;
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
        return $this->twig->render("Telephone/show.html.twig", [
            "telephone" => $telephone
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
        // Exécute une requête qui vide la table (truncate table telephones)
        $requete = BDD::getInstance()->prepare("TRUNCATE TABLE telephones")->execute();
        // Créer 2 array PHP « jeu de donnée »
        // - Un array PHP qui contient 6 Marques de téléphone différents
        $arrayMarque = ["Apple", "Samsung", "Google", "Huawei", "Xiaomi"];
        // - Un array PHP qui contient 6 Modèles de téléphone différents
        $arrayModele = ["iPhone 13", "Galaxy S21", "Pixel 6", "P40", "Mi 11"];
        // Créer une variable Datetime (date du jour)
        $dateDuJour = new \DateTime();

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
                ->setStatut("En stock");
            Telephone::SqlAdd($telephone);
        }

        header("Location:/AdminTelephone/list");
    }
}
