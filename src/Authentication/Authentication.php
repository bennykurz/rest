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

use Lcobucci\JWT\Parser;
use N86io\Rest\Authorization\AuthorizationInterface;
use N86io\Rest\Object\Container;

/**
 * Class Authentication
 *
 * @author Viktor Firus <v@n86.io>
 */
class Authentication implements AuthenticationInterface
{
    /**
     * @inject
     * @var Configuration
     */
    protected $authConf;

    /**
     * @return void
     */
    public function load()
    {
        $headerAuthorization = getallheaders()['Authorization'];
        if (substr($headerAuthorization, 0, 6) !== 'Bearer') {
            $this->callFailedAuthenticationCallable();
            return;
        }

        $jwtParser = new Parser;
        $token = $jwtParser->parse(substr($headerAuthorization, 7));

        try {
            $verified = $token->verify(
                $this->authConf->getSigner(),
                $this->authConf->getVerifyKey()
            );
            if ($verified === false) {
                $this->callFailedAuthenticationCallable();
                return;
            }
        } catch (\BadMethodCallException $e) {
            $this->callFailedAuthenticationCallable();
            return;
        }

        $userId = $token->getClaim('uid');

        $closure = $this->authConf->getSuccessfulAuthenticationCallable();
        $additionalGroups = $closure($userId);

        if ($additionalGroups === false) {
            return;
        }

        $authorization = Container::makeInstance(AuthorizationInterface::class);
        $authorization->addUserGroup(0);
        $authorization->addUserGroups($additionalGroups);
    }

    protected function callFailedAuthenticationCallable()
    {
        call_user_func($this->authConf->getFailedAuthenticationCallable());
    }
}
