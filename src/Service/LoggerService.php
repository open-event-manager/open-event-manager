<?php


namespace App\Service;


use Psr\Log\LoggerInterface;
use Symfony\Component\DependencyInjection\ParameterBag\ParameterBagInterface;

class LoggerService
{
    private $logger;
    private $parameterBag;

    public function __construct(LoggerInterface $logger, ParameterBagInterface $parameterBag)
    {
        $this->logger = $logger;
        $this->parameterBag = $parameterBag;
    }
    public function log($message,$value){
        $this->logger->info($message,$value);
    }
}