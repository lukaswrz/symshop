<?php

declare(strict_types=1);

namespace App\Command;

use App\Entity\Product;
use App\Entity\User;
use Doctrine\ORM\EntityManagerInterface;
use Faker\Factory as FakerFactory;
use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;

#[AsCommand(
    name: 'app:example',
    description: 'Generate example data',
)]
class ExampleCommand extends Command
{
    public function __construct(
        private readonly EntityManagerInterface $entityManager
    ) {
        parent::__construct();
    }

    protected function configure(): void
    {
        $this
            ->addArgument('entity', InputArgument::REQUIRED, 'Entity name')
            ->addOption('amount', 'a', InputOption::VALUE_OPTIONAL, 'Amount', 1)
        ;
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $io = new SymfonyStyle($input, $output);

        $entityName = $input->getArgument('entity');

        $amount = $input->getOption('amount');

        if (!is_numeric($amount)) {
            $io->error('Amount should be numeric.');

            return Command::FAILURE;
        }

        if ($amount < 1) {
            $io->error('Amount should be greater than 1.');

            return Command::FAILURE;
        }

        $faker = FakerFactory::create();

        for ($i = 0; $i < $amount; ++$i) {
            switch ($entityName) {
                case 'user':
                    $user = new User();
                    $user->setFirstName($faker->firstName());
                    $user->setLastName($faker->lastName());
                    $user->setEmail(
                        mb_strtolower($user->getFirstName()).'.'.mb_strtolower($user->getLastName()).'@'.$faker->freeEmailDomain()
                    );

                    $this->entityManager->persist($user);
                    $this->entityManager->flush();

                    $io->info([
                        'ID: '.$user->getId(),
                        'Email: '.$user->getEmail(),
                        'First name: '.$user->getFirstName(),
                        'Last name: '.$user->getLastName(),
                    ]);
                    break;
                case 'product':
                    $product = new Product();
                    $product->setName(mb_convert_case($faker->words(3, asText: true), MB_CASE_TITLE));
                    $product->setPrice($faker->numberBetween(1, 100) * 1000 - 1);
                    $product->setStock($faker->randomNumber(2));

                    $this->entityManager->persist($product);
                    $this->entityManager->flush();

                    $io->info([
                        'ID: '.$product->getId(),
                        'Name: '.$product->getName(),
                        'Price: '.$product->getPrice() / 100,
                        'Stock: '.$product->getStock(),
                    ]);
                    break;
                default:
                    $io->error('Unrecognized entity name \''.$entityName.'\'/');

                    return Command::FAILURE;
            }
        }

        return Command::SUCCESS;
    }
}
