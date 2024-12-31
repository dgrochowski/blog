<?php

declare(strict_types=1);

namespace App\Command;

use App\Bus\Bus;
use App\Bus\Command\CreateSettingCommand as BusCreateSettingCommand;
use App\Entity\Setting;
use App\Service\SlugService;
use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

#[AsCommand(
    name: 'app:create-setting',
    description: 'Creates a new setting | app:create-setting <name> <value>',
    hidden: false,
)]
class CreateSettingCommand extends Command
{
    private Bus $bus;
    private SlugService $slugService;

    public function __construct(
        Bus $bus,
        SlugService $slugService,
    ) {
        parent::__construct();

        $this->bus = $bus;
        $this->slugService = $slugService;
    }

    protected function configure(): void
    {
        $this
            ->addArgument('name', InputArgument::REQUIRED, 'Setting name')
            ->addArgument('value', InputArgument::REQUIRED, 'Setting value')
        ;
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $output->writeln([
            'Setting Creator',
            '============',
            '',
        ]);

        $name = $input->getArgument('name');
        $value = $input->getArgument('value');
        $uniqueSlug = $this->slugService->unique(Setting::class, $name);

        $this->bus->command(new BusCreateSettingCommand(
            name: $name,
            value: $value,
            slug: $uniqueSlug,
        ));
        $output->writeln("<fg=green>Setting $name<$value> created! Slug:$uniqueSlug</>");

        return Command::SUCCESS;
    }
}
