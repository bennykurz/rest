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

namespace N86io\Rest\Persistence;

/**
 * @author Viktor Firus <v@n86.io>
 * @since  0.1.0
 */
class Limit implements LimitInterface
{
    /**
     * @var int
     */
    protected $offset;

    /**
     * @var int
     */
    protected $rowCount;

    /**
     * @param int $offset
     * @param int $rowCount
     */
    public function __construct(int $offset, int $rowCount)
    {
        $this->offset = $offset;
        $this->rowCount = $rowCount;
    }

    /**
     * @return int
     */
    public function getOffset(): int
    {
        return $this->offset;
    }

    /**
     * @return int
     */
    public function getRowCount(): int
    {
        return $this->rowCount;
    }
}
