<?php
namespace AppBundle\Command;

use AppBundle\Service\ApiOperationService;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

class CurrencyComparisonCommand extends Command
{
    private $apiCall;

    // constructor
    public function __construct(ApiOperationService $apiCall)
    {
        $this->apiCall = $apiCall;

        parent::__construct();
    }

    protected function configure()
    {
        $this
            // the name of the command (the part after "bin/console")
            ->setName('app:currency-comparison')

            // the short description shown while running "php bin/console list"
            ->setDescription('Calls given APIs, compares the values and saves minimum into database.')

            // the full command description shown when running the command with the "--help" option
            ->setHelp('This command calls the APIs that were specified, retrieves the currency values from them, compares each of its kind and finally saves the minimum value among those to database')
        ;
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        // the function to be executed on command execution
        $this->apiCall->performOperation();
        $output->writeln('Comparison is completed!');
    }
}