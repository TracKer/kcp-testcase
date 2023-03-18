<?php

use App\DependencyInjection\Container;
use App\Helper\CommissionCalculator;
use App\Provider\Rate\RateProviderInterface;
use App\Provider\Transaction\TransactionProviderInterface;
use App\Provider\BankIdentificationNumber\BankIdentificationNumberProviderInterface;

require_once 'vendor/autoload.php';

$container = new Container();

/** @var RateProviderInterface $rateProvider */
$rateProvider = $container->get(RateProviderInterface::class);

/** @var BankIdentificationNumberProviderInterface $binProvider */
$binProvider = $container->get(BankIdentificationNumberProviderInterface::class);

/** @var TransactionProviderInterface $transactionsProvider */
$transactionsProvider = $container->get(TransactionProviderInterface::class);

foreach ($transactionsProvider->getList() as $transactionDto) {
    $countryCode = $binProvider->getCountry($transactionDto->getBin());
    $currency = $transactionDto->getCurrency();
    $amount = $transactionDto->getAmount();
    $rate = $rateProvider->getRate($currency);

    /** @var CommissionCalculator $commissionCalculator */
    $commissionCalculator = $container->get(CommissionCalculator::class);
    $commissionCalculator
        ->setCountryCode($countryCode)
        ->setCurrency($currency)
        ->setRate($rate)
        ->setAmount($amount);

    $commission = $commissionCalculator->calculateCommission();
    print "{$commission}\n";
}
