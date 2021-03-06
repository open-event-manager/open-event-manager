<?php
// src/Twig/AppExtension.php
namespace App\Twig;

use App\Entity\Checklist;
use App\Entity\MyUser;
use App\Entity\Standort;
use App\Service\LicenseService;
use App\Service\MessageService;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\DependencyInjection\ParameterBag\ParameterBagInterface;
use Symfony\Component\Security\Core\Authentication\Token\Storage\TokenStorageInterface;
use Twig\Extension\AbstractExtension;
use Twig\TwigFilter;
use Twig\TwigFunction;
use function GuzzleHttp\Psr7\str;

class License extends AbstractExtension
{


    private $licenseService;

    public function __construct(LicenseService $licenseService, TokenStorageInterface $tokenStorage, EntityManagerInterface $em)
    {
        $this->licenseService = $licenseService;

    }

    public function getFilters()
    {
        return [
            new TwigFilter('validateLicense', [$this, 'validateLicense']),
            new TwigFilter('validateUntilLicense', [$this, 'validateUntilLicense']),
        ];
    }

    public function validateLicense(Standort $server):bool
    {
         return $this->licenseService->verify($server);

    }
    public function validateUntilLicense(Standort $server):\DateTime
    {
        return $this->licenseService->validUntil($server);
    }
}