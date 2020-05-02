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

    public function findById(string $id)
    {
        return $this->em->getRepository(Asset::class)->find($id);
    }

    /**
     * @param int $id
     * @param string $currency
     * @return array
     * @throws ValidProviderNotFound
     */
    public function getUserCurrencyValues(int $id, string $currency)
    {
        $result =[];
        $total =0;
        $values = $this->em->getRepository(Asset::class)->getAllValuesGroupedByCurrencies($id);
        foreach ($values as $key => $value){
            $result[$value['currency']]['value'] = $value['value'];
            $usdRate = $this->exchangeService->getExchangeRate($value['currency'],$currency);
            $result[$value['currency']]['USD Rate'] = round($usdRate,2);

            $totalUsd =round($value['value'] * $usdRate ,2);
            $result[$value['currency']]['USD'] = $totalUsd;
            $total += $totalUsd;
        }
        $result['Total USD'] = $total;
        return $result;
    }
}