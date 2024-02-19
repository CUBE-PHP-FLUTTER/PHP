<?php

namespace src\Controller;

use Mpdf\Mpdf;
use Mpdf\Output\Destination;
use src\Model\Article;
use src\Model\BDD;

class ArticleController extends AbstractController
{
    public function index()
    {
        $articles = Article::SqlGetLast(20);
        return $this->twig->render("Article/index.html.twig",[
            "articles" => $articles
        ]);
    }

    public function show(int $id)
    {
        $article = Article::SqlGetById($id);
        return $this->twig->render("Article/show.html.twig",[
            "article" => $article
        ]);
    }

    public function pdf(int $id)
    {
        $article = Article::SqlGetById($id);
        $mpdf = new Mpdf([
            "tempDir" => $_SERVER["DOCUMENT_ROOT"]."/../var/cache/pdf/"
        ]);

        $mpdf->WriteHTML($this->twig->render("Article/pdf.html.twig",[
            "article" => $article
        ]));


        $mpdf->Output("{$_SERVER["DOCUMENT_ROOT"]}/uploads/pdf/Article-{$article->getId()}.pdf", Destination::FILE);
        header("Location:/Article/show/{$article->getId()}");
    }
    public function fixtures()
    {
        UserController::haveGoodRole(["Administrateur"]);
        //Exécuter une requête qui vide la table (truncate table articles)
        $requete = BDD::getInstance()->prepare("TRUNCATE TABLE articles")->execute();
        //Créer 2 array PHP « jeu de donnée »
        // - Un array PHP qui contient 6 Titres d’article différents
        $arrayTitre = ["PHP en force", "React JS qui monte", "C# toujorus au top", "Flutter déchire tout", "Java en baissse"];
        // - Un array PHP qui contient 6 Auteurs (prénom) différents
        $arrayAuteur = ["Enzo", "Lukas", "Rémi", "Bastien", "Loup", "Kylian"];
        //Créer une variable Datetime (date du jour)
        $dateDuJour = new \DateTime();
        //Boucle (For ou While) de 200 itérations
        // - Incrémenter la date +1 jour à chaque tour de boucle
        // - Mélanger les tableaux
        // - Requête Insertion de données à chaque boucle (prendre le premier Index de chaque Tableau pour créer du « random » en BDD)

        for($i=1;$i<=200;$i++) {
            $dateDuJour->modify("+1 day");
            shuffle($arrayAuteur);
            shuffle($arrayTitre);
            $article = new Article();
            $article->setTitre($arrayTitre[0])
                ->setDescription("Zypher est un langage de programmation moderne conçu pour offrir une expérience de développement puissante et flexible. Avec une syntaxe claire et concise, Zypher permet aux développeurs de créer des applications robustes et efficaces dans divers domaines, allant de l'informatique embarquée à la programmation web")
                ->setAuteur($arrayAuteur[0])
                ->setDatePublication($dateDuJour);
            Article::SqlAdd($article);
        }

        header("Location:/AdminArticle/list");
    }

}