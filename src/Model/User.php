<?php

namespace src\Model;

class User
{
    private ?int $Id = null;
    private String $Mail;
    private String $Password;
    private array $Roles;

    public function getId(): ?int
    {
        return $this->Id;
    }

    public function setId(?int $Id): User
    {
        $this->Id = $Id;
        return $this;
    }

    public function getMail(): string
    {
        return $this->Mail;
    }

    public function setMail(string $Mail): User
    {
        $this->Mail = $Mail;
        return $this;
    }

    public function getPassword(): string
    {
        return $this->Password;
    }

    public function setPassword(string $Password): User
    {
        $this->Password = $Password;
        return $this;
    }

    public function getRoles(): array
    {
        return $this->Roles;
    }

    public function setRoles(array $Roles): User
    {
        $this->Roles = $Roles;
        return $this;
    }

    public static function SqlAdd(User $user) :int
    {
        $requete = BDD::getInstance()->prepare("INSERT INTO users (Email, Password, NomPrenom, Roles) VALUES(:Email, :Password, :NomPrenom, :Roles)");

        $requete->execute([
            "Email" => $user->getMail(),
            "Password" => $user->getPassword(),
            "NomPrenom" => "Olivier Carglass", //Prévoir un champ dans le formulaire pour ça à l'avenir
            "Roles" => json_encode($user->getRoles())
        ]);

        return BDD::getInstance()->lastInsertId();
    }

    public static function SqlGetByMail(string $mail): ?User
    {
        $requete = BDD::getInstance()->prepare("SELECT * FROM users WHERE Email=:mail");
        $requete->execute([
            "mail" => $mail
        ]);
        $datas = $requete->fetch(\PDO::FETCH_ASSOC);
        if($datas != false){
            $user = new User();
            $user->setId($datas["Id"])
                ->setMail($datas["Email"])
                ->setPassword($datas["Password"])
                ->setRoles(json_decode($datas["Roles"]));
            return $user;
        }
        return null;
    }
}