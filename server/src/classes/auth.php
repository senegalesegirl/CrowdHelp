<?php
use \Firebase\JWT\JWT;

class Auth {

    private static $config = array(
        "token_expire" => 3600,
        "server_name" => "localhost",
        "token_secret" => "mToXURjHMF/sBbeKasW1Bg=="

    );

    public static function Validate($token){

        $decoded = self::Decode($token);
        
        if ($decoded === false) return false;

        if ($decoded->exp < time()){
            return false;
        }else{
            return $decoded->data;
        }
    }

    /**
     * Génère et retourn un token crypté
     * Il est possible d'encoder des données passées en paramètres variable, tableau, objet
     * @param array $data (Optionnel)
     * @return string
     */
    public static function GenerateToken($data = array()){

        $tokenId    = base64_encode(openssl_random_pseudo_bytes(32));
        $issuedAt   = time();
        $notBefore  = $issuedAt;
        $expire     = $notBefore + self::$config['token_expire'];           // Adding 60 seconds
        $serverName = self::$config['server_name'];                         // Retrieve the server name from config file
        $secretKey  = "";
        /*
        * Create the token as an array
        */
        $token = [
            'iat'  => $issuedAt,         // Issued at: time when the token was generated
            'jti'  => $tokenId,          // Json Token Id: an unique identifier for the token
            'iss'  => $serverName,       // Issuer
            'nbf'  => $notBefore,        // Not before
            'exp'  => $expire,           // Expire
            'data' => $data
        ];

        /**
         * IMPORTANT:
         * You must specify supported algorithms for your application. See
         * https://tools.ietf.org/html/draft-ietf-jose-json-web-algorithms-40
         * for a list of spec-compliant algorithms.
         */
        $jwt = JWT::encode($token, self::$config['token_secret'], 'HS512');


        return $jwt;
    }

    /**
     * Decode un token et le retourne sous forme d'objet.
     * Retourne FALSE si le token est invalide (expiré par exemple)
     * @param bool $jwt
     * @return bool|object
     */
    private static function Decode($jwt = false){

        if ($jwt) {
            try {
                /*
                * decode the jwt using the key from config
                */
                $secretKey = self::$config['token_secret'];

                $token = JWT::decode($jwt, $secretKey, array('HS512'));

                if($token->exp < time()) {
                    return false;
                }else{
                    return $token;
                }

            } catch (Exception $e) {
                /*
                * the token was not able to be decoded.
                * this is likely because the signature was not able to be verified (tampered token)
                */
                //die($e->getMessage());
                return false;
            }
        } else {
            /*
            * No token was able to be extracted from the authorization header
            */
            return false;
        }
    }


}
