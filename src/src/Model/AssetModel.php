<?php


namespace App\Model;


use App\Entity\Asset;
use App\Service\ExchangeService;
use Doctrine\ORM\EntityManagerInterface;
use App\Service\Exceptions\ValidProviderNotFound;

class AssetModel
{

    /** @var EntityManagerInterface */
    private $em;

    /** @var ExchangeService */
    private $exchangeService;

    public function __construct(EntityManagerInterface $em, ExchangeService $exchangeService)
    {
        $this->em = $em;
        $this->exchangeService = $exchangeService;

    }

    public function saveAsset(Asset $asset)
    {
        $this->em->persist($asset);
        $this->em->flush();
    }

    public function delete(Asset $asset)
    {
        $this->em->remove($asset);
        $this->em->flush();
    }


}