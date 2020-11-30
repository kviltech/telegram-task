<?php


namespace App\Entities;


use ReflectionClass;
use ReflectionProperty;

/**
 * Class AbstractEntity
 * @package App\Entities
 */
abstract class AbstractEntity implements EntityInterface
{
    /**
     * @return array
     * @throws \ReflectionException
     */
    public function convertToArray(): array
    {
        $data = [];

        $reflection = new ReflectionClass($this);
        $vars = $reflection->getProperties(ReflectionProperty::IS_PRIVATE);

        foreach ($vars as $var) {
            if (substr($var->getName(), 0, 1) != '_') {
                $methodName = 'get' . $var->getName();

                if (method_exists($this, $methodName)) {
                    $data[$var->getName()] = $this->$methodName();
                }
            }
        }

        return $data;
    }
}
