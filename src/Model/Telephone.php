<?php
namespace src\Model;

class Telephone implements \JsonSerializable {
    private ?int $ID_Telephone = null;
    private string $Marque;
    private string $Modele;
    private string $Caracteristiques;
    private float $Prix;
    private int $Quantite;
    private int $ID_Vendeur;
    private \DateTime $DatePublication;
    private string $Statut;
    private ?string $ImageFileName = null;

    // Getters and Setters
    public function getIDTelephone(): ?int {
        return $this->ID_Telephone;
    }

    public function setIDTelephone(?int $ID_Telephone): self {
        $this->ID_Telephone = $ID_Telephone;
        return $this;
    }

    public function getMarque(): string {
        return $this->Marque;
    }

    public function setMarque(string $Marque): self {
        $this->Marque = $Marque;
        return $this;
    }

    public function getModele(): string {
        return $this->Modele;
    }

    public function setModele(string $Modele): self {
        $this->Modele = $Modele;
        return $this;
    }

    public function getCaracteristiques(): string {
        return $this->Caracteristiques;
    }

    public function setCaracteristiques(string $Caracteristiques): self {
        $this->Caracteristiques = $Caracteristiques;
        return $this;
    }

    public function getPrix(): float {
        return $this->Prix;
    }

    public function setPrix(float $Prix): self {
        $this->Prix = $Prix;
        return $this;
    }

    public function getQuantite(): int {
        return $this->Quantite;
    }

    public function setQuantite(int $Quantite): self {
        $this->Quantite = $Quantite;
        return $this;
    }

    public function getIDVendeur(): int {
        return $this->ID_Vendeur;
    }

    public function setIDVendeur(int $ID_Vendeur): self {
        $this->ID_Vendeur = $ID_Vendeur;
        return $this;
    }

    public function getDatePublication(): \DateTime {
        return $this->DatePublication;
    }

    public function setDatePublication(\DateTime $DatePublication): self {
        $this->DatePublication = $DatePublication;
        return $this;
    }

    public function getStatut(): string {
        return $this->Statut;
    }

    public function setStatut(string $Statut): self {
        $this->Statut = $Statut;
        return $this;
    }

    public function getImageFileName(): ?string {
        return $this->ImageFileName;
    }

    public function setImageFileName(?string $ImageFileName): self {
        $this->ImageFileName = $ImageFileName;
        return $this;
    }

    // Serialization
    public function jsonSerialize(): mixed {
        return [
            "ID_Telephone" => $this->getIDTelephone(),
            "Marque" => $this->getMarque(),
            "Modele" => $this->getModele(),
            "Caracteristiques" => $this->getCaracteristiques(),
            "Prix" => $this->getPrix(),
            "Quantite" => $this->getQuantite(),
            "ID_Vendeur" => $this->getIDVendeur(),
            "DatePublication" => $this->getDatePublication()->format("Y-m-d"),
            "Statut" => $this->getStatut(),
            "ImageFileName" => $this->getImageFileName(),
        ];
    }

    // Database Operations
    public static function SqlAdd(Telephone $Telephone):int
    {
        $requete = BDD::getInstance()->prepare("INSERT INTO Telephones (Marque, Modele, Caracteristiques, Prix, Quantite, ID_Vendeur, DatePublication, Statut, ImageFileName) VALUES(:Marque, :Modele, :Caracteristiques, :Prix, :Quantite, :ID_Vendeur, :DatePublication, :Statut, :ImageFileName)");

        $requete->execute([
            "Marque" => $Telephone->getMarque(),
            "Modele" => $Telephone->getModele(),
            "Caracteristiques" => $Telephone->getCaracteristiques(),
            "Prix" => $Telephone->getPrix(),
            "Quantite" => $Telephone->getQuantite(),
            "ID_Vendeur" => $Telephone->getIDVendeur(),
            "DatePublication" => $Telephone->getDatePublication()->format("Y-m-d"),
            "Statut" => $Telephone->getStatut(),
            "ImageFileName" => $Telephone->getImageFileName(),
        ]);

        return BDD::getInstance()->lastInsertId();
    }

    public static function SqlGetLast(int $nb)
    {
        $requete = BDD::getInstance()->prepare('SELECT * FROM Telephones ORDER BY ID_Telephone DESC LIMIT :limit');
        $requete->bindValue("limit", $nb, \PDO::PARAM_INT);
        $requete->execute();

        $TelephonesSql = $requete->fetchAll(\PDO::FETCH_ASSOC);
        $TelephonesObjet = [];
        foreach ($TelephonesSql as $TelephoneSql){
            $Telephone = new Telephone();
            $Telephone->setIDTelephone($TelephoneSql["ID_Telephone"])
                ->setMarque($TelephoneSql["Marque"])
                ->setModele($TelephoneSql["Modele"])
                ->setCaracteristiques($TelephoneSql["Caracteristiques"])
                ->setPrix($TelephoneSql["Prix"])
                ->setQuantite($TelephoneSql["Quantite"])
                ->setIDVendeur($TelephoneSql["ID_Vendeur"])
                ->setDatePublication(new \DateTime($TelephoneSql["DatePublication"]))
                ->setStatut($TelephoneSql["Statut"])
                ->setImageFileName($TelephoneSql["ImageFileName"]);
            $TelephonesObjet[] = $Telephone;
        }
        return $TelephonesObjet;
    }

    public static function SqlGetAll()
    {
        $requete = BDD::getInstance()->prepare('SELECT * FROM Telephones');
        $requete->execute();
        $TelephonesSql = $requete->fetchAll(\PDO::FETCH_ASSOC);
        $TelephonesObjet = [];
        foreach ($TelephonesSql as $TelephoneSql){
            $Telephone = new Telephone();
            $Telephone->setIDTelephone($TelephoneSql["ID_Telephone"])
                ->setMarque($TelephoneSql["Marque"])
                ->setModele($TelephoneSql["Modele"])
                ->setCaracteristiques($TelephoneSql["Caracteristiques"])
                ->setPrix($TelephoneSql["Prix"])
                ->setQuantite($TelephoneSql["Quantite"])
                ->setIDVendeur($TelephoneSql["ID_Vendeur"])
                ->setDatePublication(new \DateTime($TelephoneSql["DatePublication"]))
                ->setStatut($TelephoneSql["Statut"])
                ->setImageFileName($TelephoneSql["ImageFileName"]);
            $TelephonesObjet[] = $Telephone;
        }
        return $TelephonesObjet;
    }

    public static function SqlDelete(int $idTelephone)
    {
        $requete = BDD::getInstance()->prepare("DELETE FROM Telephones WHERE ID_Telephone=:ID_Telephone");
        $requete->execute([
            "ID_Telephone" => $idTelephone
        ]);
    }

    public static function SqlGetById(int $id):Telephone
    {
        $requete = BDD::getInstance()->prepare('SELECT * FROM Telephones WHERE ID_Telephone=:id');
        $requete->bindValue("id", $id, \PDO::PARAM_INT);
        $requete->execute();

        $TelephoneSql = $requete->fetch(\PDO::FETCH_ASSOC);
        $Telephone = new Telephone();
        $Telephone->setIDTelephone($TelephoneSql["ID_Telephone"])
            ->setMarque($TelephoneSql["Marque"])
            ->setModele($TelephoneSql["Modele"])
            ->setCaracteristiques($TelephoneSql["Caracteristiques"])
            ->setPrix($TelephoneSql["Prix"])
            ->setQuantite($TelephoneSql["Quantite"])
            ->setIDVendeur($TelephoneSql["ID_Vendeur"])
            ->setDatePublication(new \DateTime($TelephoneSql["DatePublication"]))
            ->setStatut($TelephoneSql["Statut"])
            ->setImageFileName($TelephoneSql["ImageFileName"]);
        return $Telephone;
    }

    public static function SqlUpdate(Telephone $Telephone)
    {
        $requete = BDD::getInstance()->prepare("UPDATE Telephones SET Marque=:Marque, Modele=:Modele, Caracteristiques=:Caracteristiques, Prix=:Prix, Quantite=:Quantite, ID_Vendeur=:ID_Vendeur, DatePublication=:DatePublication, Statut=:Statut, ImageFileName=:ImageFileName WHERE ID_Telephone=:ID_Telephone");

        $bool = $requete->execute([
            "Marque" => $Telephone->getMarque(),
            "Modele" => $Telephone->getModele(),
            "Caracteristiques" => $Telephone->getCaracteristiques(),
            "Prix" => $Telephone->getPrix(),
            "Quantite" => $Telephone->getQuantite(),
            "ID_Vendeur" => $Telephone->getIDVendeur(),
            "DatePublication" => $Telephone->getDatePublication()->format("Y-m-d"),
            "Statut" => $Telephone->getStatut(),
            "ImageFileName" => $Telephone->getImageFileName(),
            "ID_Telephone" => $Telephone->getIDTelephone()
        ]);
    }

    public static function SqlSearch(string $keyword): array
    {
        $requete = BDD::getInstance()->prepare("SELECT * FROM Telephones WHERE Marque like :Marque OR Modele like :Modele OR Caracteristiques like :Caracteristiques");
        $bool = $requete->execute([
            "Marque" => "%{$keyword}%",
            "Modele" => "%{$keyword}%",
            "Caracteristiques" => "%{$keyword}%"
        ]);
        $TelephonesSql = $requete->fetchAll(\PDO::FETCH_ASSOC);
        $TelephonesObjet = [];
        foreach ($TelephonesSql as $TelephoneSql){
            $Telephone = new Telephone();
            $Telephone->setIDTelephone($TelephoneSql["ID_Telephone"])
                ->setMarque($TelephoneSql["Marque"])
                ->setModele($TelephoneSql["Modele"])
                ->setCaracteristiques($TelephoneSql["Caracteristiques"])
                ->setPrix($TelephoneSql["Prix"])
                ->setQuantite($TelephoneSql["Quantite"])
                ->setIDVendeur($TelephoneSql["ID_Vendeur"])
                ->setDatePublication(new \DateTime($TelephoneSql["DatePublication"]))
                ->setStatut($TelephoneSql["Statut"])
                ->setImageFileName($TelephoneSql["ImageFileName"]);
            $TelephonesObjet[] = $Telephone;
        }
        return $TelephonesObjet;
    }
}
