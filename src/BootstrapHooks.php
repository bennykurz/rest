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

namespace N86io\Rest;

/**
 * Class BootstrapHooks
 *
 * @author Viktor Firus <v@n86.io>
 */
class BootstrapHooks
{
    /**
     * @var callable
     */
    protected $firstRun;

    /**
     * @var callable
     */
    protected $aftInitCont;

    /**
     * @var callable
     */
    protected $aftInitRequest;

    /**
     * @var callable
     */
    protected $aftInitAuthent;

    /**
     * @var callable
     */
    protected $aftCheckAuthori;

    /**
     * @param callable $firstRun
     * @return BootstrapHooks
     */
    public function setFirstRun(callable $firstRun)
    {
        $this->firstRun = $firstRun;
        return $this;
    }

    /**
     * @param Bootstrap $bootstrap
     */
    public function runFirstRun(Bootstrap $bootstrap)
    {
        $this->run($this->firstRun, $bootstrap);
    }

    /**
     * @param callable $aftInitCont
     * @return BootstrapHooks
     */
    public function setAfterInitializeContainer(callable $aftInitCont)
    {
        $this->aftInitCont = $aftInitCont;
        return $this;
    }

    /**
     * @param Bootstrap $bootstrap
     */
    public function runAfterInitializeContainer(Bootstrap $bootstrap)
    {
        $this->run($this->aftInitCont, $bootstrap);
    }

    /**
     * @param callable $aftInitRequest
     * @return BootstrapHooks
     */
    public function setAfterInitializeRequest(callable $aftInitRequest)
    {
        $this->aftInitRequest = $aftInitRequest;
        return $this;
    }

    /**
     * @param Bootstrap $bootstrap
     */
    public function runAfterInitializeRequest(Bootstrap $bootstrap)
    {
        $this->run($this->aftInitRequest, $bootstrap);
    }

    /**
     * @param callable $aftInitAuthent
     * @return BootstrapHooks
     */
    public function setAfterInitializeAuthentication(callable $aftInitAuthent)
    {
        $this->aftInitAuthent = $aftInitAuthent;
        return $this;
    }

    /**
     * @param Bootstrap $bootstrap
     */
    public function runAfterInitializeAuthentication(Bootstrap $bootstrap)
    {
        $this->run($this->aftInitAuthent, $bootstrap);
    }

    /**
     * @param callable $aftCheckAuthori
     * @return BootstrapHooks
     */
    public function setAfterCheckAuthorization(callable $aftCheckAuthori)
    {
        $this->aftCheckAuthori = $aftCheckAuthori;
        return $this;
    }

    /**
     * @param Bootstrap $bootstrap
     */
    public function runAfterCheckAuthorization(Bootstrap $bootstrap)
    {
        $this->run($this->aftCheckAuthori, $bootstrap);
    }

    /**
     * @param callable $callable
     * @param Bootstrap $bootstrap
     */
    protected function run($callable, Bootstrap $bootstrap)
    {
        if (is_callable($callable)) {
            call_user_func($callable, $bootstrap);
        }
    }
}
