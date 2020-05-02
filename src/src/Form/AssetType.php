<?php
namespace App\Form;


use App\Entity\Asset;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\IntegerType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\Exception\ExceptionInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class AssetType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('label', TextType::class)
            ->add('currency', ChoiceType::class, [
                'choices' => [
                    'BTC' => 'BTC',
                    'ETH' => 'ETH',
                    'I0TA' => 'I0TA',
                ]
            ])
            ->add('value', IntegerType::class)
        ;
    }
    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults(array(
            'data_class' => Asset::class,
            'csrf_protection' => false,
        ));
    }
    public function getName()
    {
        return 'asset';
    }
}