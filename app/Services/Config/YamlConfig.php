<?php


namespace App\Services\Config;


use Symfony\Component\Yaml\Yaml;

/**
 * Class YamlConfig
 * @package App\Services\Config
 */
class YamlConfig implements ConfigInterface
{
    /**
     * @var array
     */
    private $config = [];

    /**
     * YamlConfig constructor.
     */
    public function __construct()
    {
        $configPath = realpath(__DIR__ . '/../../../');

        $this->config = Yaml::parseFile($configPath . '/' . 'config.yaml');

        if (file_exists($configPath . '/' . 'config.local.yaml')) {
            $this->config = array_merge(
                $this->config,
                Yaml::parseFile($configPath . '/' . 'config.local.yaml') ?? []
            );
        }
    }

    /**
     * @return array
     */
    public function getConfig(): array
    {
        return $this->config;
    }
}
