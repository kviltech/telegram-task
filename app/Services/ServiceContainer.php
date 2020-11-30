<?php


namespace App\Services;


use App\Services\Config\ConfigInterface;
use App\Services\Config\YamlConfig;
use App\Services\TelegramApi\MadelineProtoApi;
use App\Services\TelegramApi\TelegramApiInterface;
use danog\MadelineProto\API;

/**
 * Class ServiceContainer
 * @package App\Services
 */
class ServiceContainer
{
    /**
     * @var self
     */
    private static $instance;

    /**
     * @var ConfigInterface
     */
    private $config;

    /**
     * @var TelegramApiInterface
     */
    private $telegramApi;

    /**
     * ServiceContainer constructor.
     */
    private function __construct()
    {
    }

    /**
     * @return ServiceContainer
     */
    public static function getInstance(): self
    {
        if (!self::$instance) self::$instance = new self();

        return self::$instance;
    }

    /**
     * @return ConfigInterface
     */
    public function getConfig(): ConfigInterface
    {
        if (!$this->config) $this->config = new YamlConfig();

        return $this->config;
    }

    /**
     * @return TelegramApiInterface
     */
    public function getTelegramApi(): TelegramApiInterface
    {
        if (!$this->telegramApi) {
            /*
             * Удаляем перед созданием новой сессии, т.к. если поместить в
             * деструктор и завершить процесс через ctrl+c, детстурктор не сработает
             */
            MadelineProtoApi::clearSessionFiles();

            try {
                $api = new API(
                    MadelineProtoApi::getSessionPath(),
                    $this->getConfig()->getConfig()['madeline_proto_api']
                );
            } catch (\Exception $e) {

            }

            $this->telegramApi = new MadelineProtoApi($api);
        }

        return $this->telegramApi;
    }
}
