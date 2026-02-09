<?php

namespace Hot;

class SecureId
{
    private static string $cipher = 'AES-256-CBC';

    /**
     * Encode ID to URL-safe constant-length string
     */
    public static function encode(int|string $id, string $secret, int $length = 32): string
    {
        $key = hash('sha256', $secret, true);

        // generate random IV
        $iv = random_bytes(openssl_cipher_iv_length(self::$cipher));

        $encrypted = openssl_encrypt((string)$id, self::$cipher, $key, OPENSSL_RAW_DATA, $iv);

        // store IV + encrypted together (required for decoding)
        $payload = $iv . $encrypted;

        // base64 URL safe
        $token = rtrim(strtr(base64_encode($payload), '+/', '-_'), '=');

        // enforce fixed length WITHOUT cutting encryption
        if (strlen($token) < $length) {
            $token = str_pad($token, $length, 'A');
        }

        return $token;
    }

    /**
     * Decode back to original ID
     */
    public static function decode(string $token, string $secret): string|false
    {
        $key = hash('sha256', $secret, true);

        // remove padding added during encode
        $token = rtrim($token, 'A');

        // restore base64
        $data = base64_decode(strtr($token, '-_', '+/'));

        if (!$data) {
            return false;
        }

        $ivLength = openssl_cipher_iv_length(self::$cipher);

        $iv = substr($data, 0, $ivLength);
        $encrypted = substr($data, $ivLength);

        return openssl_decrypt($encrypted, self::$cipher, $key, OPENSSL_RAW_DATA, $iv);
    }
}

