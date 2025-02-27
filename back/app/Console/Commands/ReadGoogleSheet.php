<?php

namespace App\Console\Commands;

use App\Services\EntriesGSService;
use Illuminate\Console\Command;
use Symfony\Component\Console\Helper\ProgressBar;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\ConsoleOutput;

class ReadGoogleSheet extends Command
{
    public function __construct(
        protected EntriesGSService $service
    )
    {
        parent::__construct();
    }

    protected function configure()
    {
        $this
            ->setName('app:read-google-sheet')
            ->setDescription('Read and display data from Google Sheet')
            ->addOption('count', null, InputOption::VALUE_OPTIONAL, 'Limit records', 10);
    }

    public function handle()
    {
        $entriesGs = $this->service->fromSetting();

        $userCountLimit = $this->option('count') ?? PHP_INT_MAX;
        $values = $entriesGs->getRows(0, $userCountLimit);

        $isWeb = php_sapi_name() !== 'cli';

        if (empty($values)) {
            $this->warn('Google таблица пуста.');
            return;
        }

        $total = min($userCountLimit, count($values));
        $this->info("Выводим первые $total строк из Google Sheets:");

        if ($isWeb) {
            $stepFn = function ($msg) {
                $this->output->writeln($msg);
            };
        } else {
            $output = new ConsoleOutput();
            $section1 = $output->section();
            $section2 = $output->section();
            $section2->setMaxHeight(30);

            $progress = new ProgressBar($section1, $total);
            $progress->start();

            $stepFn = function ($msg) use ($progress, $section2) {
                $progress->advance();
                $section2->writeln($msg);
            };
            $progress->finish();
        }

        foreach ($values as $row) {
            $stepFn($this->rowMessage($row));
        }

        $this->info('Готово.');
    }

    protected function rowMessage($row)
    {
        $id = $row[0] ?? 'N/A';
        $comment = $row[4] ?? 'Нет комментария';
        return "ID: $id | Комментарий: $comment";
    }
}
