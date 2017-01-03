<?php
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

use Lcobucci\JWT\Builder;
use Lcobucci\JWT\Token;
use N86io\Di\ContainerInterface;
use N86io\Di\Singleton;
use Webmozart\Assert\Assert;

/**
 * Class TokenGenerator
 *
 * @author Viktor Firus <v@n86.io>
 */
class TokenGenerator implements Singleton
{
    /**
     * @inject
     * @var ContainerInterface
     */
    protected $container;

    /**
     * @inject
     * @var Configuration
     */
    protected $authConf;

    /**
     * @param int $uid
     * @return Token
     */
    public function create($uid)
    {
        Assert::integer($uid);
        return $this->container->get(Builder::class)
            ->setIssuedAt(time())
            ->set('uid', $uid)
            ->sign($this->authConf->getSigner(), $this->authConf->getSignKey())
            ->getToken();
    }
}
