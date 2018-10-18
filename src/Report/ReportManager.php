<?php

/*
 * This file is part of PHP CS Fixer.
 *
 * (c) Fabien Potencier <fabien@symfony.com>
 *     Dariusz Rumiński <dariusz.ruminski@gmail.com>
 *
 * This source file is subject to the MIT license that is bundled
 * with this source code in the file LICENSE.
 */

namespace ConferenceTools\Tickets\Report;

use Zend\ServiceManager\AbstractPluginManager;
use Zend\ServiceManager\Exception;

class ReportManager extends AbstractPluginManager
{
    /**
     * Validate the plugin.
     *
     * Checks that the filter loaded is either a valid callback or an instance
     * of FilterInterface.
     *
     * @param mixed $plugin
     *
     * @throws Exception\RuntimeException if invalid
     */
    public function validatePlugin($plugin)
    {
        if ($plugin instanceof ReportInterface) {
            return;
        }

        throw new Exception\RuntimeException(
            sprintf('Report %s doesn\'t implement report interface', \get_class($plugin))
        );
    }
}
