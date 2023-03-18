<?php

namespace App\DependencyInjection;

use App\Helper\CommissionCalculator;
use App\Helper\CountryChecker;
use App\Provider\BankIdentificationNumber\BankIdentificationNumberApiProvider;
use App\Provider\BankIdentificationNumber\BankIdentificationNumberProviderInterface;
use App\Provider\Rate\RateApiProvider;
use App\Provider\Rate\RateProviderInterface;
use App\Provider\Transaction\TransactionFileProvider;
use App\Provider\Transaction\TransactionProviderInterface;
use Symfony\Component\DependencyInjection\ContainerBuilder;

class Container extends ContainerBuilder
{
    public function __construct()
    {
        parent::__construct();

        $this->registerServices();
        $this->registerParameters();

        $this->compile();
    }

    private function registerServices()
    {
        $this->register(TransactionProviderInterface::class, TransactionFileProvider::class)
            ->setPublic(true)
            ->setAutowired(true)
            ->setArgument('$fileName', '%transactionsFileName%');

        $this->register(BankIdentificationNumberProviderInterface::class, BankIdentificationNumberApiProvider::class)
            ->setPublic(true)
            ->setAutowired(true);

        $this->register(RateProviderInterface::class, RateApiProvider::class)
            ->setPublic(true)
            ->setAutowired(true);

        $this->register(CountryChecker::class, CountryChecker::class)
            ->setPublic(true)
            ->setAutowired(true);

        $this->register(CommissionCalculator::class, CommissionCalculator::class)
            ->setPublic(true)
            ->setAutowired(true)
            ->setShared(false);
    }

    private function registerParameters()
    {
        global $argv;

        $this->setParameter('transactionsFileName', $argv[1]);
    }
}
