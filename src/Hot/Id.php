<?php 
namespace Hot;
// 
class Id
{
    private static $cipher = 'AES-256-CBC';
    private static $key = 'your-secret-key-123456789'; // change this
    private static $iv = '1234567891011121'; // 16 characters

    public static function encode(int|string $id, ?string $secret=null)
    {
        self::$key = $secret?:self::$key; 
        $encrypted = openssl_encrypt(
            $id,
            self::$cipher,
            self::$key,
            0,
            self::$iv
        );

        // Make URL safe
        return rtrim(strtr(base64_encode($encrypted), '+/', '-_'), '=');
    }

    public static function decode(string $token, ?string $secret=null)
    {
        self::$key = $secret?:self::$key; 
        $data = base64_decode(strtr($token, '-_', '+/'));

        return openssl_decrypt(
            $data,
            self::$cipher,
            self::$key,
            0,
            self::$iv
        );
    }
}
