<?php
/**
 * Created by PhpStorm.
 * User: m
 * Date: 13.04.17
 * Time: 2:59
 */

namespace AppBundle\Form;


use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\NumberType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;

class ProductType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder->add("title", TextType::class)
            ->add("priceMin", NumberType::class)
            ->add("priceMax", NumberType::class);
    }

}