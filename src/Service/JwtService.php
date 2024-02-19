<?php

namespace src\Service;

use Firebase\JWT\JWT;
use Firebase\JWT\Key;
use Laminas\Stdlib\DateTime;

class JwtService
{
    public static String $secretKey = "toto";

    public static function createToken(array $datas): String
    {
        $issueAt = new \DateTime();
        $expire = new \DateTime();
        $expire->modify("+6 minutes");
        $serverName = "cesi.local";
        $data = [
            "iat" => $issueAt->getTimestamp(),
            "iss" => $serverName,
            "nbf" => $issueAt->getTimestamp(),
            "exp" => $expire->getTimestamp(),
            "datas" => CryptService::encrypt(json_encode($datas))
        ];

        $jwt = JWT::encode($data,self::$secretKey,"HS512");

        return $jwt;
    }

    public static function checkToken()
    {
        if (! preg_match('/Bearer\s(\S+)/', $_SERVER['HTTP_AUTHORIZATION'], $matches)) {
            $result = [
                "code" => 1,
                "body" => "Token non trouvé dans la requête"
            ];
            return $result;
        }

        $jwt = $matches[1];
        if (! $jwt) {
            $result = [
                "code" => 1,
                "body" => "Aucun jeton n'a pu être extrait de l'en-tête d'autorisation."
            ];
            return $result;
        }

        try{
            //ça remonte une exception dès qu'il trouve une erreur on on veut catch l'erreur pour la donner en JSON
            $token = JWT::decode($jwt, new Key(self::$secretKey, 'HS512'));
        }catch (\Exception$e){
            $result = [
                "code" => 1,
                "body" => "Les données du jeton ne sont pas compatibles : {$e->getMessage()}"
            ];
            return $result;
        }

        $now = new \DateTimeImmutable();
        $serverName = "cesi.local";

        if ($token->iss !== $serverName ||
            $token->nbf > $now->getTimestamp() ||
            $token->exp < $now->getTimestamp())
        {
            $result = [
                "code" => 1,
                "body" => "Les données du jeton ne sont pas compatibles"
            ];
            return $result;
        }

        $result = [
            "code" => 0,
            "body" => "Token OK",
            "data" => json_decode(CryptService::decrypt($token->datas))
        ];
        return $result;

    }
}