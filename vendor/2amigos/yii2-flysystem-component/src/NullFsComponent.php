<?php
/**
 * This file is part of the 2amigos/yii2-flysystem-component project.
 *
 * (c) 2amigOS! <http://2amigos.us/>
 *
 * For the full copyright and license information, please view
 * the LICENSE file that was distributed with this source code.
 */
namespace dosamigos\flysystem;

use League\Flysystem\Adapter\NullAdapter;

/**
 * Used mostly for testing. Acts like "/dev/null"
 */
class NullFsComponent extends AbstractFsComponent
{
    /**
     * @return NullAdapter
     */
    protected function initAdapter()
    {
        return new NullAdapter();
    }
}
