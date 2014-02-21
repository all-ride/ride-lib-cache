<?php

namespace ride\library\cache\pool\op;

use ride\library\cache\pool\CachePool;

/**
 * Interface for the opcode cache implementation
 */
interface OpCachePool extends CachePool {

    /**
     * Increases a value in the variable store
     * @param string $key Key of the variable
     * @param integer $step Value to increase with
     * @return mixed New value of the variable
     */
    public function increase($key, $step = 1);

    /**
     * Decreases a value in the variable store
     * @param string $key Key of the variable
     * @param integer $step Value to decrease with
     * @return mixed New value of the variable
     */
    public function decrease($key, $step = 1);

}