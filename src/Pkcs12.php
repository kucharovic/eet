<?php

namespace Ondrejnov\EET;

use Ondrejnov\EET\Exceptions\ClientException;
use Ondrejnov\EET\Exceptions\RequirementsException;

/**
 * Extract private key with its X.509 certificate from PKCS #12 archive
*/
class Pkcs12
{
    /**
     * Private key
     * @var string
     */
    private $privateKey;

    /**
     * X.509 certificate
     * @var string
     */
    private $certificate;

    public function __construct($pkcs12 = false, $password = null, $isFile = true) {

        if (!extension_loaded('openssl')) {
            throw new RequirementsException("Extension 'openssl' is not loaded! Please, enable it in php.ini");
        }

        if (false !== $pkcs12) {
            if ($isFile && !file_exists($pkcs12)) {
                throw new ClientException(sprintf("Certificate file '%s' not exists.", $pkcs12));
            }

            if ($isFile) {
                $pkcs12 = file_get_contents($pkcs12);
            }

            $certs = [];

            if (false === openssl_pkcs12_read($pkcs12, $certs, $password)) {
                throw new ClientException("Could not parse PKCS #12 certificate.");
            }

            $this->privateKey = $certs['pkey'];
            $this->certificate = $certs['cert'];
        }
    }

    public function setPrivateKey($key) {
        $this->privateKey = $key;

        return $this;
    }

    public function getPrivateKey() {
        return $this->privateKey;
    }

    public function setCertificate($cert){
        $this->certificate = $cert;

        return $this;
    }

    public function getCertificate() {
        return $this->certificate;
    }
}