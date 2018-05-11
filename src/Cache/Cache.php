<?php

/**
 * The Imaging Source Download System PHP Wrapper
 *
 * PHP wrapper for The Imaging Source Download System Web API. Authored and supported by The Imaging Source Europe GmbH.
 *
 * @link      http://dl-gui.theimagingsource.com to learn more about The Imaging Source Download System
 * @link      https://github.com/jonathanmaron/theimagingsource-tisd-sdk for the canonical source repository
 * @license   https://github.com/jonathanmaron/theimagingsource-tisd-sdk/blob/master/LICENSE.md
 * @copyright © 2018 The Imaging Source Europe GmbH
 */

namespace Tisd\Sdk\Cache;

/**
 * Class Cache
 *
 * @package Tisd\Sdk\Cache
 */
class Cache extends AbstractCache
{
    /**
     * Write data to the cache file
     *
     * @param string $cacheId
     * @param string $data
     *
     * @return bool|int
     */
    public function write($cacheId, $data)
    {
        $filename = $this->getFilename($cacheId);

        if (is_file($filename)) {
            unlink($filename);
        }

        $buffer = sprintf("<?php\n\nreturn %s;\n", var_export($data, true));

        $ret = file_put_contents($filename, $buffer);

        return $ret;
    }

    /**
     * Read data from the cache file
     *
     * @param $cacheId
     *
     * @return bool|array
     */
    public function read($cacheId)
    {
        $ret = false;

        $filename = $this->getFilename($cacheId);

        if (is_readable($filename)) {
            if (filemtime($filename) + $this->getTtl() > time()) {
                $ret = include $filename;
            }
        }

        return $ret;
    }

    /**
     * Purge the cache file
     *
     * @param string $user
     *
     * @return bool|null
     */
    public function purge($user = null)
    {
        $ret = null;

        if (null === $user) {
            $user = $this->getUser();
        }

        foreach (glob($this->getFilename('*', $user)) as $filename) {
            $ret = unlink($filename);
        }

        return $ret;
    }
}
