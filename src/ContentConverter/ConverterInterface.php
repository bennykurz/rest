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

/**
 * Interface ConverterInterface
 * @package N86io\Rest\ContentConverter
 */
interface ConverterInterface
{
    /**
     * @param array $array
     * @return string
     */
    public function render(array $array);

    /**
     * @param string $string
     * @return array
     */
    public function parse($string);

    /**
     * @return string
     */
    public function getContentType();
}
