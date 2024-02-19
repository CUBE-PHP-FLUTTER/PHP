<?php
session_start();
require_once "../vendor/autoload.php";
// Autoload de classe qui nous évite de faire des require à toutes les sauces
function chargerClasse($class)
{
    $ds = DIRECTORY_SEPARATOR;
    $dir = $_SERVER["DOCUMENT_ROOT"]."$ds..";
    //Remplacement des séparateurs Namespace
    $className = str_replace("\\",$ds,$class);
    $file = "{$dir}{$ds}{$className}.php";
    if(is_readable($file)){
        require_once $file;
    }
}
spl_autoload_register("chargerClasse");
//Routeur
$url = explode("/",$_GET["url"]);
$controller = (isset($url[0])) ? $url[0] : '';
$action = (isset($url[1])) ? $url[1] : '';
$param = (isset($url[2])) ? $url[2] : '';

if($controller != ''){
    try{
        $class = "src\Controller\\".$controller."Controller";
        //src\Controller\ArticleController
        if(class_exists($class)){
            $controller = new $class;
            if(method_exists($class,$action)){
                echo $controller->$action($param);
            }else{
                throw new Exception("Méthode $action inexistante dans $class", 1028);
            }
        }else{
            throw new Exception("Controller $class inexistant", 1032);
        }
    }catch (Exception $e){
        // On verra plus tard pour faire un ErrorController
        $controller = new \src\Controller\ErrorController();
        echo $controller->showMessage($e);
    }
}else{
    $controller = new \src\Controller\ArticleController();
    echo $controller->index();
}

