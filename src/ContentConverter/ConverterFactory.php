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

namespace N86io\Rest\ContentConverter;

use N86io\Di\ContainerInterface;
use N86io\Di\Singleton;
use Webmozart\Assert\Assert;

/**
 * @author Viktor Firus <v@n86.io>
 * @since  0.1.0
 */
class ConverterFactory implements Singleton
{
    /**
     * @inject
     * @var ContainerInterface
     */
    protected $container;

    /**
     * Class-register with converter.
     *
     * @var array
     */
    protected $converter = [
        JsonConverter::class
    ];

    /**
     * Create a converter from html header accept.
     *
     * TODO: determine proper converter from accept with package willdurand/negotiation
     *
     * @param string $accept
     *
     * @return ConverterInterface
     */
    public function createFromAccept(string $accept): ConverterInterface
    {
        $defaultConverter = null;
        foreach ($this->converter as $item) {
            /** @var ConverterInterface $converterInstance */
            $converterInstance = $this->container->get($item);
            if (!$defaultConverter) {
                $defaultConverter = $converterInstance;
            }
            if ($converterInstance->getContentType() === $accept) {
                return $converterInstance;
            }
        }

        return $defaultConverter;
    }
}
