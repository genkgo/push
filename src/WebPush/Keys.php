<?php

declare(strict_types=1);

namespace Genkgo\Push\WebPush;

use Lcobucci\JWT\Encoding\JoseEncoder;

final class Keys
{
    /**
     * @return array{privateKey: string, publicKey: string}
     */
    public static function newKeys(): array
    {
        $privateKey = \openssl_pkey_new([
            'private_key_type' => \OPENSSL_KEYTYPE_EC,
            'curve_name' => 'prime256v1',
        ]);

        if ($privateKey === false) {
            throw new \UnexpectedValueException('Failed to generate private key');
        }

        $privateKeyDetails = \openssl_pkey_get_details($privateKey);
        if ($privateKeyDetails === false) {
            throw new \UnexpectedValueException('Cannot get details of the public key');
        }

        if (!isset($privateKeyDetails['key']) || !\is_string($privateKeyDetails['key'])) {
            throw new \UnexpectedValueException('Cannot get public key details');
        }

        $exported = \openssl_pkey_export($privateKey, $privateKeyPem);
        if ($exported === false) {
            throw new \UnexpectedValueException('Failed to export private key to pem');
        }

        /** @var string $privateKeyPem */
        return [
            'privateKey' => $privateKeyPem,
            'publicKey' => $privateKeyDetails['key'],
        ];
    }

    public static function convertPublicKeyToSec1(string $publicKey): string
    {
        $publicKey = \openssl_pkey_get_public($publicKey);
        if ($publicKey === false) {
            throw new \UnexpectedValueException('Failed to initialize public key');
        }

        /** @var false|array{ec: array{x: string, y: string}} $details */
        $details = \openssl_pkey_get_details($publicKey);
        if ($details === false) {
            throw new \UnexpectedValueException('Cannot get details of the public key');
        }

        $x = \str_pad($details['ec']['x'], 32, "\0", STR_PAD_LEFT);
        $y = \str_pad($details['ec']['y'], 32, "\0", STR_PAD_LEFT);

        return "\x04" . $x . $y;
    }

    public static function convertPublicKeyToApplicationServerKey(string $publicKey): string
    {
        $joseEncoder = new JoseEncoder();
        return $joseEncoder->base64UrlEncode(self::convertPublicKeyToSec1($publicKey));
    }

    public static function p256dhToPublicKey(string $base64Url): string
    {
        $joseEncoder = new JoseEncoder();
        $raw = $joseEncoder->base64UrlDecode($base64Url);

        if (\strlen($raw) !== 65 || $raw[0] !== "\x04") {
            throw new \InvalidArgumentException('Invalid p256dh key');
        }

        return self::sec1ToPem($raw);
    }

    private static function sec1ToPem(string $sec1): string
    {
        $der = "\x30" . "\x59" . "\x30\x13\x06\x07\x2a\x86\x48\xce\x3d\x02\x01"
            . "\x06\x08\x2a\x86\x48\xce\x3d\x03\x01\x07\x03\x42\x00"
            . $sec1;

        return "-----BEGIN PUBLIC KEY-----\n"
            . \chunk_split(\base64_encode($der), 64, "\n")
            . "-----END PUBLIC KEY-----\n";
    }
}
