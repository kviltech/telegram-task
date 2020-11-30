<?php


namespace App\Services\TelegramApi;


use App\Entities\Post;

/**
 * Interface TelegramApiInterface
 * @package App\Services\TelegramApi
 */
interface TelegramApiInterface
{
    const CODE_AUTH_SENT = 1000;
    const CODE_AUTH_WAIT_TWO_FACTORY_CONFIRM = 1001;
    const CODE_AUTH_SUCCESS = 1002;

    const CODE_NUMBER_NOT_REGISTERED = 4000;

    const CODE_ERROR = 5000;
    const CODE_AUTH_INCORRECT_CONFIRM = 5001;
    const CODE_INVALID_NUMBER = 5002;
    const CODE_INCORRECT_TWO_FACTORY = 5003;

    const CODE_UNDEFINED_STATUS = 6000;

    /**
     * @param int $phone
     * @return int|null - request status code
     */
    public function loginByPhone(int $phone);

    /**
     * @param int $code
     * @return int|null - request status code
     */
    public function confirmLoginByPhone(int $code): ?int;

    /**
     * @param string $password
     * @return int|null - request status code
     */
    public function confirmTwoFactoryAuth(string $password): ?int;

    /**
     * @param string $source
     * @param int $limit
     * @param int $offset
     * @return Post[]|null
     */
    public function getPosts(string $source, int $limit = 50, int $offset = 0): ?array;

    /**
     * @param string $source
     * @return bool
     */
    public function joinChannel(string $source): bool;
}
