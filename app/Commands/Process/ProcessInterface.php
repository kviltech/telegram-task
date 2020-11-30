<?php


namespace App\Commands\Process;


/**
 * Interface ProcessInterface
 * @package App\Commands\Process
 */
interface ProcessInterface
{
    /**
     * @return bool
     */
    public function run(): bool;
}
