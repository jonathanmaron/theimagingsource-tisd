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

namespace Tisd\Sdk;

/**
 * Trait ContextsTrait
 *
 * @package Tisd\Sdk
 */
trait ContextsTrait
{
    /**
     * Get the array of consolidated data
     *
     * @return array
     */
    abstract protected function getConsolidated();

    /**
     * Get the array of contexts
     *
     * @return array
     */
    public function getContexts()
    {
        $consolidated = $this->getConsolidated();

        return $consolidated['contexts'] ?? [];
    }
}
