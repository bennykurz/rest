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

namespace N86io\Rest\Authorization;

use N86io\Di\Singleton;
use Webmozart\Assert\Assert;

/**
 * Class Configuration
 *
 * @author Viktor Firus <v@n86.io>
 */
class Configuration implements Singleton
{
    /**
     * @var array
     */
    protected $accessConf = [];

    /**
     * @return array
     */
    public function getAccessConf()
    {
        return $this->accessConf;
    }

    /**
     * @param string|array $content
     */
    public function setAccessConf($content)
    {
        if (is_array($content)) {
            $this->accessConf = $content;
            return;
        }
        Assert::string($content, 'Access conf should be array, valid json-string or file with valid json.');
        $content = $this->readFile($content);
        $content = json_decode($content, true);
        Assert::notNull($content, 'Access conf should be array, valid json-string or file with valid json.');
        $this->accessConf = $content;
    }

    /**
     * @param string $content
     * @return string
     */
    protected function readFile($content)
    {
        if (strpos($content, 'file://') === 0) {
            Assert::readable($content);
            $content = file_get_contents($content);
        }
        return $content;
    }
}
