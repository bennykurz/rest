<?php declare(strict_types = 1);
/**
 * This file is part of N86io/Rest.
 *
 * N86io/Rest is free software: you can redistribute it and/or modify
 * it under the terms of the GNU General Public License as published by
 * the Free Software Foundation, either version 3 of the License, or
 * any later version.
 *
 * N86io/Rest is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE. See the
 * GNU General Public License for more details.
 *
 * You should have received a copy of the GNU General Public License
 * along with N86io/Rest or see <http://www.gnu.org/licenses/>.
 */

namespace N86io\Rest\Authentication;

use Lcobucci\JWT\Signer;
use N86io\Di\Singleton;
use Webmozart\Assert\Assert;

/**
 * @author Viktor Firus <v@n86.io>
 * @since  0.1.0
 */
class Configuration implements Singleton
{
    const HMAC = 1;
    const RSA = 2;
    const ECDSA = 4;

    const SHA256 = 8;
    const SHA384 = 16;
    const SHA512 = 32;

    /**
     * @var Signer\Key
     */
    protected $signKey;

    /**
     * @var Signer\Key
     */
    protected $verifyKey;

    /**
     * @var Signer
     */
    protected $signer;

    /**
     * @param int    $alg
     * @param string $signKey
     * @param string $verifyKey
     */
    public function initialize(
        int $alg,
        string $signKey,
        string $verifyKey = ''
    ) {
        $this->setKey($alg, $signKey, $verifyKey);
    }

    /**
     * @return Signer\Key
     */
    public function getSignKey(): Signer\Key
    {
        return $this->signKey;
    }

    /**
     * @return Signer\Key
     */
    public function getVerifyKey(): Signer\Key
    {
        return $this->verifyKey;
    }

    /**
     * @return Signer
     */
    public function getSigner(): Signer
    {
        return $this->signer;
    }

    /**
     * @param int    $alg
     * @param string $signKey
     * @param string $verifyKey
     */
    protected function setKey(int $alg, string $signKey, string $verifyKey)
    {
        Assert::oneOf(
            $alg,
            [
                self::HMAC | self::SHA256,
                self::HMAC | self::SHA384,
                self::HMAC | self::SHA512,
                self::RSA | self::SHA256,
                self::RSA | self::SHA384,
                self::RSA | self::SHA512,
                self::ECDSA | self::SHA256,
                self::ECDSA | self::SHA384,
                self::ECDSA | self::SHA512
            ],
            'Wrong algorithm selected.'
        );

        if (($alg & self::HMAC) !== 0) {
            $this->createHmacSigner($signKey, $alg - self::HMAC);

            return;
        }
        if (($alg & self::RSA) !== 0) {
            $this->createRsaSigner($signKey, $verifyKey, $alg - self::RSA);

            return;
        }
        $this->createEcdsaSigner($signKey, $verifyKey, $alg - self::ECDSA);
    }

    /**
     * @param string $signKey
     * @param string $verifyKey
     * @param int    $hash
     */
    protected function createEcdsaSigner(string $signKey, string $verifyKey, int $hash)
    {
        switch ($hash) {
            case self::SHA256:
                $this->signer = new Signer\Ecdsa\Sha256;
                break;
            case self::SHA384:
                $this->signer = new Signer\Ecdsa\Sha384;
                break;
            default:
                $this->signer = new Signer\Ecdsa\Sha512;
        }
        $this->signKey = new Signer\Key($signKey);
        $this->verifyKey = new Signer\Key($verifyKey);
    }

    /**
     * @param string $signKey
     * @param string $verifyKey
     * @param int    $hash
     */
    protected function createRsaSigner(string $signKey, string $verifyKey, int $hash)
    {
        switch ($hash) {
            case self::SHA256:
                $this->signer = new Signer\Rsa\Sha256;
                break;
            case self::SHA384:
                $this->signer = new Signer\Rsa\Sha384;
                break;
            default:
                $this->signer = new Signer\Rsa\Sha512;
        }
        $this->signKey = new Signer\Key($signKey);
        $this->verifyKey = new Signer\Key($verifyKey);
    }

    /**
     * @param string $key
     * @param int    $hash
     */
    protected function createHmacSigner(string $key, int $hash)
    {
        switch ($hash) {
            case self::SHA256:
                $this->signer = new Signer\Hmac\Sha256;
                break;
            case self::SHA384:
                $this->signer = new Signer\Hmac\Sha384;
                break;
            default:
                $this->signer = new Signer\Hmac\Sha512;
        }
        $this->signKey = new Signer\Key($key);
        $this->verifyKey = $this->signKey;
    }
}
