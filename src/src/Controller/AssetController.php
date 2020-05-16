<?php

declare(strict_types=1);

namespace App\Controller;

use App\Entity\Asset;
use App\Entity\User;
use App\Exception\ApiException;
use App\Exception\AssetException;
use App\Exception\AssetNotFoundException;
use App\Exception\FormValidationException;
use App\Form\AssetType;
use App\Service\Exceptions\ValidProviderNotFound;
use App\Service\ExchangeService;
use Doctrine\ORM\EntityManagerInterface;
use http\Exception\UnexpectedValueException;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Form\FormInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;

/**
 * @Route("/api")
 * @IsGranted("ROLE_USER")
 */
class AssetController extends AbstractController
{
    /** @var ExchangeService */
    private $service;

    /** @var EntityManagerInterface */
    private $em;

    /**
     * AssetController constructor.
     * @param ExchangeService $service
     * @param EntityManagerInterface $em
     */
    public function __construct(ExchangeService $service, EntityManagerInterface $em)
    {
        $this->service = $service;
        $this->em = $em;
    }

    /**
     * @Route("/create", name="create", methods={"POST"})
     */
    public function create(Request $request)
    {
        $data = json_decode($request->getContent(), true);
        $asset = new Asset();
        $form = $this->createForm(AssetType::class, $asset);
        $form->submit($data);
        if ($form->isValid() && $form->isSubmitted()) {
            /** @var User $user */
            $user = $this->getUser();
            $asset->setUser($user);
            $this->saveAsset($asset);
            $message = 'User [%s] added asset: %s';
            return $this->json(['message' => sprintf($message, $user->getUsername(), $asset->getLabel())]);

        }
        throw new FormValidationException($this->getErrors($form));
    }

    /**
     * @Route("/update/{id}", name="update", methods={"PUT"})
     */
    public function update(Request $request, $id)
    {
        $data = json_decode($request->getContent(), true);
        $asset = $this->em->getRepository(Asset::class)->find($id);
        $user = $this->getUser();

        $form = $this->createForm(AssetType::class, $asset);

        if ($asset instanceof Asset) {
            if ($user->getId() !== $asset->getUser()->getId()) {
                throw new AssetException(ApiException::ASSET_NOT_USERS);
            }
            $form->submit($data);
            if ($form->isValid() && $form->isSubmitted()) {
                /** @var User $user */
                $asset->setUser($user);
                $this->saveAsset($asset);

                $message = 'User [%s] updated asset: %s';
                $data = [
                    'status' => 'success',
                    'message' => sprintf($message, $user->getUsername(), $asset->getLabel())
                ];
                return $this->json([$data]);

            }
            throw new FormValidationException($this->getErrors($form));
        }
        throw new AssetException(ApiException::ASSET_NOT_FOUND);
    }

    /**
     * @Route("/delete/{id}", name="delete", methods={"DELETE"})
     * @IsGranted("ROLE_USER")
     */
    public function delete(Request $request, $id)
    {
        $asset = $this->enti->findById($id);
        if ($asset instanceof Asset) {
            $label = $asset->getLabel();
            $user = $this->getUser();
            if ($user->getId() !== $asset->getUser()->getId()) {
                throw new AssetException(ApiException::ASSET_NOT_USERS);
            }

            $this->deleteAsset($asset);

            $message = 'Asset [%s] was delete';
            $data = [
                'status' => 'success',
                'message' => sprintf($message, $label)
            ];
            return $this->json($data);
        }
        throw new AssetException(ApiException::ASSET_NOT_FOUND);
    }

    /**
     * @Route("/get_values/{currency}", name="get_values", methods={"GET"})
     */
    public function getValues(Request $request, $currency)
    {
        try {
            $values = $this->service->getUserCurrencyValues($this->getUser()->getId(), $currency);
        } catch (ValidProviderNotFound $e) {
            return $this->json([$e->getMessage()], 400);
        }

        return $this->json([$values]);
    }

    /**
     * @param Asset $asset
     */
    private function saveAsset(Asset $asset)
    {
        $this->em->persist($asset);
        $this->em->flush();
    }

    /**
     * @param Asset $asset
     */
    private function deleteAsset(Asset $asset)
    {
        $this->em->remove($asset);
        $this->em->flush();
    }

    /**
     * @param FormInterface $form
     * @return array
     */
    public function getErrors(FormInterface $form): array
    {
        $errors = [];
        foreach ($form->getErrors() as $error) {
            $errors[] = $error->getMessage();
        }
        foreach ($form->all() as $childForm) {
            if ($childForm instanceof FormInterface) {
                if ($childErrors = $this->getErrors($childForm)) {
                    $errors[$childForm->getName()] = $childErrors;
                }
            }
        }
        return $errors;
    }

}
