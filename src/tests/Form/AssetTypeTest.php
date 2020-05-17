<?php


namespace App\Tests\Form;

use Symfony\Component\Form\Test\TypeTestCase;

class AssetTypeTest extends TypeTestCase
{
    public function testSubmitValidData()
    {
        $formData = [
            'test' => 'test',
            'test2' => 'test2',
        ];

        $objectToCompare = new TestObject();
        // $objectToCompare will retrieve data from the form submission; pass it as the second argument
        $form = $this->factory->create(TestedType::class, $objectToCompare);

        $object = new TestObject();
        // ...populate $object properties with the data stored in $formData

        // submit the data to the form directly
        $form->submit($formData);

        $this->assertTrue($form->isSynchronized());

        // check that $objectToCompare was modified as expected when the form was submitted
        $this->assertEquals($object, $objectToCompare);

        $view = $form->createView();
        $children = $view->children;

        foreach (array_keys($formData) as $key) {
            $this->assertArrayHasKey($key, $children);
        }
    }

}