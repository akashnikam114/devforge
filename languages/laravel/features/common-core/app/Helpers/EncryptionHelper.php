<?php

namespace App\Helpers;

use Illuminate\Support\Facades\Auth;
use App\Helpers\BusinessSettingHelper;
use RuntimeException;
use Exception;

class EncryptionHelper
{
    private static ? string $encryptionKey = null;
    private const CIPHER = 'AES-256-CBC';
    private const HASH_ALGO = 'sha256';

    private static function initialize(): void
    {
        if (self::$encryptionKey === null) {
            $key = BusinessSettingHelper::getBusinessInfo('encryption_key');
            if (empty($key)) {
                throw new RuntimeException('Encryption key not configured');
            }
            self::$encryptionKey = base64_decode($key);
        }
    }

    public static function encryptData(string $data): string
    {
        self::initialize();
        
        $iv = random_bytes(openssl_cipher_iv_length(self::CIPHER));
        $key = hash(self::HASH_ALGO, self::$encryptionKey, true);
        
        $encrypted = openssl_encrypt(
            $data,
            self::CIPHER,
            $key,
            OPENSSL_RAW_DATA,
            $iv
        );
        
        return base64_encode($iv . $encrypted);
    }

    public static function decryptData(string $data): string
    {
        try {
            self::initialize();
            
            $raw = base64_decode($data, true);
            if ($raw === false) {
                return $data;
            }
            
            $ivLength = openssl_cipher_iv_length(self::CIPHER);
            if (strlen($raw) < $ivLength) {
                return $data;
            }
            
            $iv = substr($raw, 0, $ivLength);
            $encryptedData = substr($raw, $ivLength);
            
            $key = hash(self::HASH_ALGO, self::$encryptionKey, true);
            $decrypted = openssl_decrypt(
                $encryptedData,
                self::CIPHER,
                $key,
                OPENSSL_RAW_DATA,
                $iv
            );
            
            if ($decrypted === false) {
                return $data;
            }
            
            return $decrypted;
        } catch (Exception $e) {
            return $data;
        }
    }
}
