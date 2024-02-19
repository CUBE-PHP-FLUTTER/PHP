<?php
namespace src\Model;
class Article implements \JsonSerializable {
    private ?int $Id = null;
    private ?string $Titre = null;
    private ?string $Description = null;
    private ?string $Auteur = null;
    private ?\DateTime $DatePublication = null;
    private ?string $ImageRepository = null;
    private ?string $ImageFileName = null;

    public function getId(): ?int
    {
        return $this->Id;
    }

    public function setId(?int $Id): Article
    {
        $this->Id = $Id;
        return $this;
    }

    public function getTitre(): ?string
    {
        return $this->Titre;
    }

    public function setTitre(?string $Titre): Article
    {
        $this->Titre = $Titre;
        return $this;
    }

    public function getDescription(): ?string
    {
        return $this->Description;
    }

    public function setDescription(?string $Description): Article
    {
        $this->Description = $Description;
        return $this;
    }

    public function getAuteur(): ?string
    {
        return $this->Auteur;
    }

    public function setAuteur(?string $Auteur): Article
    {
        $this->Auteur = $Auteur;
        return $this;
    }

    public function getDatePublication(): ?\DateTime
    {
        return $this->DatePublication;
    }

    public function setDatePublication(?\DateTime $DatePublication): Article
    {
        $this->DatePublication = $DatePublication;
        return $this;
    }

    public function getImageRepository(): ?string
    {
        return $this->ImageRepository;
    }

    public function setImageRepository(?string $ImageRepository): Article
    {
        $this->ImageRepository = $ImageRepository;
        return $this;
    }

    public function getImageFileName(): ?string
    {
        return $this->ImageFileName;
    }

    public function setImageFileName(?string $ImageFileName): Article
    {
        $this->ImageFileName = $ImageFileName;
        return $this;
    }

    public static function SqlAdd(Article $article):int
    {
        $requete = BDD::getInstance()->prepare("INSERT INTO articles (Titre, Description, DatePublication, Auteur, ImageRepository, ImageFilename) VALUES(:Titre, :Description,:DatePublication, :Auteur, :ImageRepository, :ImageFilename)");

        $requete->execute([
            "Titre" => $article->getTitre(),
            "Description" => $article->getDescription(),
            "DatePublication" => $article->getDatePublication()->format("Y-m-d"),
            "Auteur" => $article->getAuteur(),
            "ImageRepository" => $article->getImageRepository(),
            "ImageFilename" => $article->getImageFileName(),
        ]);

        return BDD::getInstance()->lastInsertId();
    }

    public static function SqlGetLast(int $nb)
    {
        $requete = BDD::getInstance()->prepare('SELECT * FROM articles ORDER BY Id DESC LIMIT :limit');
        $requete->bindValue("limit", $nb, \PDO::PARAM_INT);
        $requete->execute();

        $articlesSql = $requete->fetchAll(\PDO::FETCH_ASSOC);
        $articlesObjet = [];
        foreach ($articlesSql as $articleSql){
            $article = new Article();
            $article->setTitre($articleSql["Titre"])
                ->setDescription($articleSql["Description"])
                ->setDatePublication(new \DateTime($articleSql["DatePublication"]))
                ->setAuteur($articleSql["Auteur"])
                ->setImageRepository($articleSql["ImageRepository"])
                ->setImageFileName($articleSql["ImageFileName"]);
            $articlesObjet[] = $article;
        }
        return $articlesObjet;
    }

    public static function SqlGetAll()
    {
        $requete = BDD::getInstance()->prepare('SELECT * FROM articles');
        $requete->execute();
        $articlesSql = $requete->fetchAll(\PDO::FETCH_ASSOC);
        $articlesObjet = [];
        foreach ($articlesSql as $articleSql){
            $article = new Article();
            $article->setTitre($articleSql["Titre"])
                ->setId($articleSql["Id"])
                ->setDescription($articleSql["Description"])
                ->setDatePublication(new \DateTime($articleSql["DatePublication"]))
                ->setAuteur($articleSql["Auteur"])
                ->setImageRepository($articleSql["ImageRepository"])
                ->setImageFileName($articleSql["ImageFileName"]);
            $articlesObjet[] = $article;
        }
        return $articlesObjet;
    }

    public static function SqlDelete(int $idArticle)
    {
        $requete = BDD::getInstance()->prepare("DELETE FROM articles WHERE Id=:Id");
        $requete->execute([
            "Id" => $idArticle
        ]);
    }

    public static function SqlGetById(int $id):Article
    {
        $requete = BDD::getInstance()->prepare('SELECT * FROM articles WHERE Id=:id');
        $requete->bindValue("id", $id, \PDO::PARAM_INT);
        $requete->execute();

        $articleSql = $requete->fetch(\PDO::FETCH_ASSOC);
        $article = new Article();
        $article->setTitre($articleSql["Titre"])
            ->setDescription($articleSql["Description"])
            ->setDatePublication(new \DateTime($articleSql["DatePublication"]))
            ->setAuteur($articleSql["Auteur"])
            ->setImageRepository($articleSql["ImageRepository"])
            ->setId($articleSql["Id"])
            ->setImageFileName($articleSql["ImageFileName"]);
        return $article;
    }

    public static function SqlUpdate(Article $article)
    {
        $requete = BDD::getInstance()->prepare("UPDATE articles SET Titre=:Titre, Description=:Description, DatePublication=:DatePublication, Auteur=:Auteur, ImageRepository=:ImageRepository, ImageFileName=:ImageFileName WHERE Id=:Id");

        $bool = $requete->execute([
            "Titre" => $article->getTitre(),
            "Description" => $article->getDescription(),
            "DatePublication" => $article->getDatePublication()->format("Y-m-d"),
            "Auteur" => $article->getAuteur(),
            "ImageRepository" => $article->getImageRepository(),
            "ImageFileName" => $article->getImageFileName(),
            "Id"=> $article->getId()
        ]);
    }

    public static function SqlSearch(string $keyword): array
    {
        $requete = BDD::getInstance()->prepare("SELECT * FROM articles WHERE Titre like :Titre OR Description like :Description");
        $bool = $requete->execute([
            "Titre" => "%{$keyword}%",
            "Description" => "%{$keyword}%"
        ]);
        $articlesSql = $requete->fetchAll(\PDO::FETCH_ASSOC);
        $articlesObjet = [];
        foreach ($articlesSql as $articleSql){
            $article = new Article();
            $article->setTitre($articleSql["Titre"])
                ->setId($articleSql["Id"])
                ->setDescription($articleSql["Description"])
                ->setDatePublication(new \DateTime($articleSql["DatePublication"]))
                ->setAuteur($articleSql["Auteur"])
                ->setImageRepository($articleSql["ImageRepository"])
                ->setImageFileName($articleSql["ImageFileName"]);
            $articlesObjet[] = $article;
        }
        return $articlesObjet;

    }

    public function jsonSerialize(): mixed
    {
        return [
          "DatePublication" => $this->getDatePublication()->format("Y-m-d"),
          "Id" => $this->getId(),
          "Titre" => $this->getTitre(),
          "Auteur" => $this->getAuteur(),
          "Description" => $this->getDescription(),
          "ImageRepository" => $this->getImageRepository(),
          "ImageFileName" => $this->getImageFileName(),
        ];
    }
}




