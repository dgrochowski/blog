<?php

declare(strict_types=1);

namespace App\Command;

use App\Bus\Bus;
use App\Bus\Command\DeleteCommand;
use App\Entity\Setting;
use App\Repository\SettingRepository;
use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

#[AsCommand(
    name: 'app:delete-setting',
    description: 'Deletes a new setting | app:delete-setting <slug>',
    hidden: false,
)]
class DeleteSettingCommand extends Command
{
    private Bus $bus;
    private SettingRepository $settingRepository;

    public function __construct(
        Bus $bus,
        SettingRepository $settingRepository,
    ) {
        parent::__construct();

        $this->bus = $bus;
        $this->settingRepository = $settingRepository;
    }

    protected function configure(): void
    {
        $this
            ->addArgument('slug', InputArgument::REQUIRED, 'Setting slug')
        ;
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $output->writeln([
            'Deleting setting',
            '============',
            '',
        ]);

        $slug = $input->getArgument('slug');
        $setting = $this->settingRepository->findOneBySlug($slug);
        if (null !== $setting) {
            $this->bus->command(new DeleteCommand(
                className: Setting::class,
                id: $setting->getId(),
            ));
            $output->writeln("<fg=green>Setting with slug:$slug deleted!</>");

            return Command::SUCCESS;
        }

        $output->writeln("<fg=yellow>Setting with slug:$slug not found</>");

        return Command::FAILURE;
    }
}
