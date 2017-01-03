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

namespace N86io\Rest\ContentConverter;

use N86io\Di\ContainerInterface;
use N86io\Di\Singleton;
use Webmozart\Assert\Assert;

/**
 * Class ConverterFactory
 *
 * @author Viktor Firus <v@n86.io>
 */
class ConverterFactory implements Singleton
{
    /**
     * @inject
     * @var ContainerInterface
     */
    protected $container;

    protected $renderer = [
        JsonConverter::class
    ];

    /**
     * TODO: determine proper converter from accept with package willdurand/negotiation
     * @param string $accept
     * @return ConverterInterface
     */
    public function createFromAccept($accept)
    {
        Assert::string($accept);
        $defaultRenderer = null;
        foreach ($this->renderer as $item) {
            /** @var ConverterInterface $rendererInstance */
            $rendererInstance = $this->container->get($item);
            if (!$defaultRenderer) {
                $defaultRenderer = $rendererInstance;
            }
            if ($rendererInstance->getContentType() === $accept) {
                return $rendererInstance;
            }
        }
        return $defaultRenderer;
    }
}
