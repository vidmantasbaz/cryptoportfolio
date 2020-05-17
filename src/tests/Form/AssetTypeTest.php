<?php


namespace App\Tests\Form;

use App\Controller\AssetController;
use App\Entity\Asset;
use App\Form\AssetType;
use Symfony\Component\Form\FormInterface;
use Symfony\Component\Form\Test\TypeTestCase;

class AssetTypeTest extends TypeTestCase
{

    public function testSubmitValidData()
    {
        $formData = [
            'label' => 'test',
            'currency' => 'BTC',
            'value' => 10,
        ];

        $assetTest = new Asset();
        $form = $this->factory->create(AssetType::class, $assetTest);

        $asset = (new Asset())
            ->setCurrency('BTC')
            ->setLabel('test')
            ->setValue(10);
        $form->submit($formData);

        $this->assertTrue($form->isSynchronized());
        $this->assertTrue($form->isValid());

        // check that $objectToCompare was modified as expected when the form was submitted
        $this->assertEquals($asset, $asset);

        $view = $form->createView();
        $children = $view->children;

        foreach (array_keys($formData) as $key) {
            $this->assertArrayHasKey($key, $children);
        }
    }

    public function testSubmitInvalidData()
    {
        $formData = [
            'label' => null,
            'currency' => 'asds',
            'value' => -10,
        ];

        $assetTest = new Asset();
        $form = $this->factory->create(AssetType::class, $assetTest);

        $form->submit($formData);

        $this->assertTrue($form->isSynchronized());
        $this->assertFalse($form->isValid());
    }

}