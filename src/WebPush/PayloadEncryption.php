<?php

declare(strict_types=1);

namespace Genkgo\Push\WebPush;

use Genkgo\Push\Recipient\WebRecipient;
use Lcobucci\JWT\Encoding\JoseEncoder;

final class PayloadEncryption
{
    /**
     * @param array<string, mixed> $payload
     */
    public function encrypt(array $payload, WebRecipient $recipient): string
    {
        $json = \json_encode($payload, \JSON_THROW_ON_ERROR);

        $joseEncoder = new JoseEncoder();
        $auth = $joseEncoder->base64UrlDecode($recipient->get('keys.auth'));

        $ephemeral = Keys::newKeys();

        $subscriberPublicKeyBase64 = Keys::p256dhToPublicKey($recipient->get('keys.p256dh'));
        $subscriberKey = \openssl_pkey_get_public($subscriberPublicKeyBase64);
        if ($subscriberKey === false) {
            throw new \UnexpectedValueException('Cannot initialize subscriber public key');
        }

        $ephemeralPrivateKey = \openssl_pkey_get_private($ephemeral['privateKey']);
        if ($ephemeralPrivateKey === false) {
            throw new \UnexpectedValueException('Cannot initialize ephemeral private key');
        }

        $sharedSecret = \openssl_pkey_derive($subscriberKey, $ephemeralPrivateKey);
        if ($sharedSecret === false) {
            throw new \UnexpectedValueException('Cannot derive shared secret from subscriber public key and ephemeral private key');
        }

        $ephemeralPublicKeySec1 = Keys::convertPublicKeyToSec1($ephemeral['publicKey']);
        $subscriberPublicKeySec1 = Keys::convertPublicKeyToSec1($subscriberPublicKeyBase64);
        $infoIKM = 'WebPush: info' . \chr(0) . $subscriberPublicKeySec1 . $ephemeralPublicKeySec1;
        $ikm = \hash_hkdf('sha256', $sharedSecret, 32, $infoIKM, $auth);

        $salt = \random_bytes(16);
        $key = \hash_hkdf('sha256', $ikm, 16, 'Content-Encoding: aes128gcm' . \chr(0), $salt);
        $nonce = \hash_hkdf('sha256', $ikm, 12, 'Content-Encoding: nonce' . \chr(0), $salt);

        $ciphertext = \openssl_encrypt($json . \chr(2), 'aes-128-gcm', $key, OPENSSL_RAW_DATA, $nonce, $tag);
        $header = $salt . \pack('N', 4096) . \chr(65) . $ephemeralPublicKeySec1;
        return $header . $ciphertext . $tag;
    }
}
