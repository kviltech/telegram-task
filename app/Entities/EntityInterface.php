<?php


namespace App\Entities;


/**
 * Interface EntityInterface
 * @package App\Entities
 */
interface EntityInterface
{
    /**
     * @return array
     */
    public function convertToArray(): array;
}
