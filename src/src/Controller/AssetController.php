<?php

namespace App\Controller;

use App\Entity\Asset;
use App\Entity\User;
use App\Form\AssetType;
use App\Model\AssetModel;
use App\Service\Exceptions\ValidProviderNotFound;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;

/**
 * @Route("/api")
 */
class AssetController extends AbstractController
{
    /** @var AssetModel */
    private $model;

    public function __construct(AssetModel $assetModel)
    {
        $this->model = $assetModel;
    }


    /**
     * @Route("/create", name="create", methods={"POST"})
     * @IsGranted("ROLE_USER")
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

            $this->model->saveAsset($asset);

            $message = 'User [%s] added asset: %s';
            $data = [
                'message' => sprintf($message, $user->getUsername(), $asset->getLabel())
            ];
            return new JsonResponse([$data]);

        }
        $errors = [];
        foreach ($form->getErrors(true, true) as $error) {
            $errors[] = $error->getMessage();
        }

        $data = [
            'type' => 'validation_error',
            'title' => 'There was a validation error',
            'errors' => $errors
        ];
        return new JsonResponse($data, 400);
    }

    /**
     * @Route("/update/{id}", name="update", methods={"POST"})
     * @IsGranted("ROLE_USER")
     */
    public function update(Request $request, $id)
    {
        $data = json_decode($request->getContent(), true);
        $asset = $this->model->findById($id);
        $user = $this->getUser();

        $form = $this->createForm(AssetType::class, $asset);

        if ($asset instanceof Asset) {
            if($user->getId() !== $asset->getUser()->getId()){
                $data = [
                    'status' => 'error',
                    'message' => 'Cant delete, asset create by different user',
                ];
                return new JsonResponse($data, 400);
            }
            $form->submit($data);
            if ($form->isValid() && $form->isSubmitted()) {
                /** @var User $user */
                $asset->setUser($user);

                $this->model->saveAsset($asset);

                $message = 'User [%s] updated asset: %s';
                $data = [
                    'status' => 'success',
                    'message' => sprintf($message, $user->getUsername(), $asset->getLabel())
                ];
                return new JsonResponse([$data]);

            }
            $errors = [];
            foreach ($form->getErrors() as $error) {
                $errors[] = $error->getMessage();
            }

            $data = [
                'type' => 'validation_error',
                'title' => 'There was a validation error',
                'errors' => $errors
            ];
            return new JsonResponse($data, 400);
        }

        $data = [
            'error',
            'message' => 'Asset not found',
        ];
        return new JsonResponse($data, 400);
    }


    /**
     * @Route("/delete/{id}", name="delete", methods={"GET"})
     * @IsGranted("ROLE_USER")
     */
    public function delete(Request $request, $id)
    {
       $asset = $this->model->findById($id);
        if ($asset instanceof Asset) {
            $label = $asset->getLabel();

            $user = $this->getUser();

            if($user->getId() !== $asset->getUser()->getId()){
                $data = [
                    'status' => 'error',
                    'message' => 'Cant delete, asset created by different user',
                ];
                return new JsonResponse($data, 400);
            }

            $this->model->delete($asset);

            $message = 'Asset [%s] was delete';
            $data = [
                'status' => 'success',
                'message' => sprintf($message, $label)
            ];
            return new JsonResponse([$data]);

        }

        $data = [
            'status' => 'error',
            'message' => 'Asset not found',
        ];
        return new JsonResponse($data, 400);
    }
    /**
     * @Route("/get_values/{currency}", name="get_values", methods={"GET"})
     * @IsGranted("ROLE_USER")
     */
    public function getValues(Request $request, $currency)
    {
        try {
            $values =$this->model->getUserCurrencyValues($this->getUser()->getId(),$currency);
        }catch (ValidProviderNotFound $e) {
            $data = [
                'status' => 'error',
                'message' => $e->getMessage(),
            ];
            return new JsonResponse($data, 400);
        }

        return new JsonResponse($values);
    }

}
