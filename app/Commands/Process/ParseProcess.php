<?php


namespace App\Commands\Process;


use App\Entities\Post;
use App\Services\ServiceContainer;
use App\Services\TelegramApi\TelegramApiInterface;
use Symfony\Component\Console\Style\StyleInterface;

/**
 * Class ParseProcess
 * @package App\Commands\Process
 */
class ParseProcess implements ProcessInterface
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
     * @var string
     */
    private $source;

    /**
     * ParseProcess constructor.
     * @param TelegramApiInterface $telegramApi
     * @param StyleInterface $io
     * @param string $source
     */
    public function __construct(
        TelegramApiInterface $telegramApi,
        StyleInterface $io,
        string $source
    ) {
        $this->telegramApi = $telegramApi;
        $this->io = $io;
        $this->source = $source;
    }

    /**
     * @return bool
     * @throws \ReflectionException
     */
    public function run(): bool
    {
        $offset = 0;
        $limit = ServiceContainer::getInstance()->getConfig()
            ->getConfig()['parse_chunk'];

        $filePath = realpath(__DIR__ . '/../../../data/');

        $fileName = $this->generateFileName();
        while(file_exists($filePath . $fileName))
            $fileName = $this->generateFileName();

        $fp = fopen($filePath . $fileName, 'w');
        $isFirstRecord = true;

        // Записываем частями, чтобы не забрать много оперативки
        fwrite($fp, '[');

        while (
            !empty($posts = $this->telegramApi->getPosts($this->source, $limit, $offset))
        ) {
            $this->io->text('Start parsing chunk...');

            foreach ($posts as $post) {
                /** @var Post $post */
                fwrite(
                    $fp,
                    ($isFirstRecord ? '' : ',')
                        . json_encode($post->convertToArray())
                );

                $isFirstRecord = false;
            }

            $this->io->text('Parsed: ' . count($posts));

            $offset += $limit;
        }

        fwrite($fp, ']');
        fclose($fp);

        if ($posts === null) {
            unlink($filePath . $fileName);
            $this->io->error('Parsing error');
            return false;
        }

        $this->io->success('Data has been saved, path: ' . $filePath . $fileName);

        return true;
    }

    /**
     * @return string
     */
    private function generateFileName(): string
    {
        return date('YmdHis_') . md5(rand(0, 5000)) . '.json';
    }
}
