<?php
/**
 * Created by PhpStorm.
 * User: m
 * Date: 23.03.17
 * Time: 6:36
 */

namespace AppBundle\Form;


use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\HiddenType;
use Symfony\Component\Form\Extension\Core\Type\NumberType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;

class PortfolioSetType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder->add("longitude", HiddenType::class)
            ->add("latitude", HiddenType::class)
            ->add("title", TextType::class, [
                'label' => 'Название набора фотографий'
            ])
            ->add("job", EntityType::class, [
                'class' => 'AppBundle\Entity\Job',
                'label' => false,
                'attr' => array(
                    'class' => 'hidden'
                )
            ]);
    }

}