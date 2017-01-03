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

use N86io\Di\ContainerInterface;
use N86io\Rest\DomainObject\EntityInfo\EntityInfoInterface;
use N86io\Rest\DomainObject\EntityInfo\EntityInfoStorage;
use N86io\Rest\Exception\BadRequestException;
use N86io\Rest\Exception\InternalServerErrorException;
use N86io\Rest\Exception\MethodNotAllowedException;
use N86io\Rest\Exception\RequestNotFoundException;
use N86io\Rest\Http\RequestInterface;
use N86io\Rest\Http\ResponseFactory;
use N86io\Rest\Persistence\Constraint\ConstraintFactory;
use N86io\Rest\Persistence\Constraint\ConstraintUtility;
use N86io\Rest\Persistence\LimitInterface;
use N86io\Rest\Persistence\RepositoryInterface;
use N86io\Rest\Service\Configuration;
use Psr\Http\Message\ResponseInterface;

/**
 * Class Controller
 *
 * @author Viktor Firus <v@n86.io>
 */
class Controller implements ControllerInterface
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
    protected $configuration;

    /**
     * @inject
     * @var ResponseFactory
     */
    protected $responseFactory;

    /**
     * @inject
     * @var EntityInfoStorage
     */
    protected $entityInfoStorage;

    /**
     * @inject
     * @var ConstraintUtility
     */
    protected $constraintUtility;

    /**
     * @inject
     * @var ConstraintFactory
     */
    protected $constraintFactory;

    /**
     * @var array
     */
    protected $resultParser;

    /**
     * @var RequestInterface
     */
    protected $request;

    /**
     * @var array
     */
    protected $repositoryResult;

    /**
     * @var array
     */
    protected $settings;

    /**
     * @param RequestInterface $request
     * @return ResponseInterface
     * @throws BadRequestException
     * @throws InternalServerErrorException
     * @throws MethodNotAllowedException
     * @throws RequestNotFoundException
     */
    final public function process(RequestInterface $request)
    {
        $this->request = $request;
        $this->settings = $this->configuration->getApiControllerSettings($request->getApiIdentifier());

        if ($this->request->getMode() === RequestInterface::REQUEST_MODE_CREATE) {
            throw new MethodNotAllowedException;
        }

        if ($this->request->getMode() === RequestInterface::REQUEST_MODE_UPDATE ||
            $this->request->getMode() === RequestInterface::REQUEST_MODE_PATCH
        ) {
            if ($this->isListRequest()) {
                throw new BadRequestException;
            }
            throw new MethodNotAllowedException;
        }

        if ($this->request->getMode() === RequestInterface::REQUEST_MODE_DELETE) {
            throw new MethodNotAllowedException;
        }

        if ($this->request->getMode() === RequestInterface::REQUEST_MODE_READ) {
            $result = $this->read();
            if (count($result) === 0) {
                throw new RequestNotFoundException;
            }
            return $this->responseFactory->createResponse(
                200,
                $result,
                $this->request->getOutputLevel()
            );
        }

        throw new InternalServerErrorException;
    }

    /**
     * @return array
     */
    private function read()
    {
        $this->call('preRead');
        $this->repositoryResult = $this->readFromConnector();
        $this->call('afterRead');
        return $this->repositoryResult;
    }

    /**
     * @param string $method
     */
    private function call($method)
    {
        if (method_exists($this, $method)) {
            call_user_func([$this, $method]);
        }
    }

    /**
     * @return bool
     */
    private function isListRequest()
    {
        return count($this->request->getResourceIds()) !== 1;
    }

    /**
     * @return array
     */
    private function readFromConnector()
    {
        $entityInfo = $this->entityInfoStorage->get($this->request->getModelClassName());
        $repository = $entityInfo->createRepositoryInstance();
        $this->setConstraints($repository, $entityInfo);
        $this->setOrdering($repository);
        $this->setLimit($repository);
        return $repository->read();
    }

    /**
     * @param RepositoryInterface $repository
     */
    protected function setLimit(RepositoryInterface $repository)
    {
        if (($limit = $this->request->getLimit()) !== null) {
            $repository->setLimit($limit);
            return;
        }
        $rowCount = $this->settings['defaultRowCount'] ? $this->settings['defaultRowCount'] : 10;
        $limit = $this->container->get(LimitInterface::class, 0, $rowCount);
        $repository->setLimit($limit);
    }

    /**
     * @param RepositoryInterface $repository
     */
    protected function setOrdering(RepositoryInterface $repository)
    {
        if ($this->request->getOrdering()) {
            $repository->setOrdering($this->request->getOrdering());
        }
    }

    /**
     * @param RepositoryInterface $repository
     * @param EntityInfoInterface $entityInfo
     */
    protected function setConstraints(RepositoryInterface $repository, EntityInfoInterface $entityInfo)
    {
        $constraints = $this->getDefaultConstraints($entityInfo);
        if (!empty($constraints)) {
            $constraints = $this->constraintFactory->logicalAnd($constraints);
            $repository->setConstraints($constraints);
        }
    }

    /**
     * @param EntityInfoInterface $entityInfo
     * @return array
     */
    protected function getDefaultConstraints(EntityInfoInterface $entityInfo)
    {
        $constraints = [];
        if (!empty($requestConstraints = $this->request->getConstraints())) {
            $constraints[] = $this->constraintFactory->logicalAnd($requestConstraints);
        }
        if (!empty($this->request->getResourceIds())) {
            $constraints[] = $this->constraintUtility->createResourceIdsConstraints(
                $entityInfo->getResourceIdPropertyInfo(),
                $this->request->getResourceIds()
            );
        }
        return $constraints;
    }
}
