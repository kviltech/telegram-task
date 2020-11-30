<?php


namespace App\Commands\Process;


use App\Services\TelegramApi\TelegramApiInterface;
use Symfony\Component\Console\Style\StyleInterface;

/**
 * Class AuthProcess
 * @package App\Commands\Process
 */
class AuthProcess implements ProcessInterface
{
    /**
     * @var StyleInterface
     */
    private $io;

    /**
     * @var TelegramApiInterface
     */
    private $telegramApi;

    /**
     * AuthProcess constructor.
     * @param TelegramApiInterface $telegramApi
     * @param StyleInterface $io
     */
    public function __construct(
        TelegramApiInterface $telegramApi,
        StyleInterface $io
    ) {
        $this->telegramApi = $telegramApi;
        $this->io = $io;
    }

    /**
     * @return bool
     */
    public function run(): bool
    {
        return $this->handlePhoneInput();
    }

    /**
     * @param int $code
     * @return bool
     */
    private function handleTelegramStatus(int $code)
    {
        switch ($code) {
            case TelegramApiInterface::CODE_AUTH_SENT:
                return $this->handleConfirmInput();

            case TelegramApiInterface::CODE_AUTH_WAIT_TWO_FACTORY_CONFIRM:
                return $this->handleTwoFactoryAuth();

            case TelegramApiInterface::CODE_AUTH_SUCCESS:
                return true;

            case TelegramApiInterface::CODE_ERROR:
                $this->io->error('Auth error');
                return false;

            case TelegramApiInterface::CODE_INCORRECT_TWO_FACTORY:
                $this->io->error('Incorrect password');
                return false;

            case TelegramApiInterface::CODE_AUTH_INCORRECT_CONFIRM:
                $this->io->error('Incorrect code');
                return false;

            case TelegramApiInterface::CODE_INVALID_NUMBER:
                $this->io->error('Invalid number');
                return false;

            case TelegramApiInterface::CODE_NUMBER_NOT_REGISTERED:
                $this->io->error('Unregistered number');
                return false;

            case TelegramApiInterface::CODE_UNDEFINED_STATUS:
            case null:
            default:
                $this->io->error('Undefined error');
                return false;
                break;
        }
    }

    /**
     * @return bool
     */
    private function handlePhoneInput(): bool
    {
        $number = $this->io->ask(
            'Enter telegram account number',
            null,
            function ($number) {
                str_replace(['+', '-', ' ', '(', ')'], '', $number);

                if (!is_numeric($number))
                    throw new \RuntimeException('Incorrect number');

                return $number;
            }
        );
        $this->io->text('Sending data...');
        $status = $this->telegramApi->loginByPhone($number);

        return $this->handleTelegramStatus($status);
    }

    /**
     * @return bool
     */
    private function handleConfirmInput(): bool
    {
        $confirmCode = (int)$this->io->ask('Enter confirm code');
        $this->io->text('Sending data...');
        $status = $this->telegramApi->confirmLoginByPhone($confirmCode);

        return $this->handleTelegramStatus($status);
    }

    /**
     * @return bool
     */
    private function handleTwoFactoryAuth(): bool
    {
        $password = $this->io->ask('Enter your password');
        $this->io->text('Sending data...');
        $status = $this->telegramApi->confirmTwoFactoryAuth($password);

        return $this->handleTelegramStatus($status);
    }
}
