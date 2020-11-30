<?php


namespace App\Services\TelegramApi;


use App\Entities\Post;
use danog\MadelineProto\API;

/**
 * Class MadelineProtoApi
 * @package App\Services\TelegramApi
 */
class MadelineProtoApi implements TelegramApiInterface
{
    private const STATUSES_MATCHING = [
        'auth.sentCode' => TelegramApiInterface::CODE_AUTH_SENT,
        'account.password' => TelegramApiInterface::CODE_AUTH_WAIT_TWO_FACTORY_CONFIRM,
        'auth.authorization' => TelegramApiInterface::CODE_AUTH_SUCCESS
    ];

    /**
     * @var API
     */
    private $madelineProto;

    /**
     * MadelineProtoApi constructor.
     * @param API $api
     */
    public function __construct(API $api)
    {
        $this->madelineProto = $api;
    }

    public function __destruct()
    {
        self::clearSessionFiles();
    }

    /**
     * @param int $phone
     * @return int|null
     */
    public function loginByPhone($phone): ?int
    {
        try {
            $res = $this->madelineProto->phoneLogin((string)$phone);
        } catch (\Exception $e) {
            return TelegramApiInterface::CODE_INVALID_NUMBER;
        }

        return $this->generateRequestReturn($res);
    }

    /**
     * @param int $code
     * @return int|null
     */
    public function confirmLoginByPhone(int $code): ?int
    {
        try {
            $res = $this->madelineProto->completePhoneLogin((string)$code);
        } catch (\Exception $e) {
            return TelegramApiInterface::CODE_AUTH_INCORRECT_CONFIRM;
        }

        return $this->generateRequestReturn($res);
    }

    /**
     * @param string $password
     * @return int|null
     */
    public function confirmTwoFactoryAuth(string $password): ?int
    {
        try {
            $res = $this->madelineProto->complete2faLogin($password);
        } catch (\Exception $e) {
            return TelegramApiInterface::CODE_INCORRECT_TWO_FACTORY;
        }

        return $this->generateRequestReturn($res);
    }

    /**
     * @param string $source
     * @param int $limit
     * @param int $offset
     * @return Post[]|null
     */
    public function getPosts(string $source, int $limit = 0, int $offset = 0): ?array
    {
        $posts = [];

        $this->joinChannel($source);

        try {
            $history = $this->madelineProto->messages->getHistory([
                'peer' => $source,
                'add_offset' => $offset,
                'limit' => $limit,
                'offset_id' => 0,
                'offset_date' => 0,
                'max_id' => 0,
                'min_id' => 0,
            ]);
        } catch (\Exception $e) {
            return null;
        }

        if (isset($history['messages'])) {
            foreach ($history['messages'] as $message) {
                if ($message['_'] == 'message') {
                    $post = new Post();

                    $post
                        ->setId($message['id'])
                        ->setDate($message['date'])
                        ->setMessage($message['message'])
                        ->setViews($message['views'])
                    ;

                    if (isset($message['media'])) {
                        $res = $this
                            ->madelineProto
                            ->downloadToDir($message['media'], __DIR__ . '/../../../media');

                        $post->addMediaPath($res);
                    }

                    $posts[] = $post;
                }
            }
        }

        return $posts;
    }

    /**
     * @param string $source
     * @return bool
     */
    public function joinChannel(string $source): bool
    {
        try {
            $this->madelineProto->channels->joinChannel([
                'channel' => $source
            ]);
        } catch (\Exception $e) {
            return false;
        }

        return true;
    }

    /**
     * @param string $status
     * @return int|false
     */
    private function convertStatus(string $status): int
    {
        return self::STATUSES_MATCHING[$status]
            ?? TelegramApiInterface::CODE_UNDEFINED_STATUS;
    }

    /**
     * @param $res
     * @return int|null
     */
    private function generateRequestReturn(&$res): ?int
    {
        return isset($res['_'])
            ? $this->convertStatus($res['_'])
            : null;
    }

    public static function clearSessionFiles(): void
    {
        $session = self::getSessionPath();

        if (is_file($session)) unlink($session);
        if (is_file($session . '.lock')) unlink($session . '.lock');
    }

    public static function getSessionPath(): string
    {
        return realpath(__DIR__ . '/../../../temp') . '/session.madeline';
    }
}
