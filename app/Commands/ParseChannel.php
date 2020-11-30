<?php


namespace App\Commands;


use App\Commands\Process\AuthProcess;
use App\Commands\Process\ParseProcess;
use App\Services\TelegramApi\TelegramApiInterface;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;

/**
 * Class ParseChannel
 * @package App\Commands
 */
class ParseChannel extends Command
{
    private const ARGUMENT_CHANNEL = 'channel';

    /**
     * @var string
     */
    protected static $defaultName = 'parse-channel';

    /**
     * @var TelegramApiInterface
     */
    private $telegramApi;

    /**
     * @var SymfonyStyle
     */
    private $io;

    /**
     * ParseChannel constructor.
     * @param TelegramApiInterface $telegramApi
     */
    public function __construct(TelegramApiInterface $telegramApi)
    {
        $this->telegramApi = $telegramApi;

        parent::__construct();
    }

    public function configure()
    {
        $this
            ->setName('parse-channel')
            ->setDescription('Parse telegram channel')
            ->addArgument(
                self::ARGUMENT_CHANNEL,
                InputArgument::REQUIRED,
                'Telegram channel name or link'
            )
        ;
    }

    /**
     * @param InputInterface $input
     * @param OutputInterface $output
     * @return int
     */
    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $io =  new SymfonyStyle($input, $output);

        $authProcess = new AuthProcess($this->telegramApi, $io);

        $parseProcess = new ParseProcess(
            $this->telegramApi,
            $io,
            $input->getArgument(self::ARGUMENT_CHANNEL)
        );

        $io->text('Running processes');

        if (!$authProcess->run()) {
            $io->error('Auth error');
            return Command::FAILURE;
        }

        $io->text('Auth process is done');

        if (!$parseProcess->run()) {
            $io->error('Parse error');
            return Command::FAILURE;
        }

        $io->text('Parse process is done');

        return Command::SUCCESS;
    }

}
