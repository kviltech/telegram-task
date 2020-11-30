<?php


namespace App\Services\Config;


/**
 * Interface ConfigInterface
 * @package App\Services\Config
 */
interface ConfigInterface
{
    /**
     * @return array
     */
    public function getConfig(): array;
}
