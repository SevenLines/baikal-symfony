<?php
/**
 * Created by PhpStorm.
 * User: m
 * Date: 06.03.17
 * Time: 21:55
 */

namespace AppBundle\Form;


use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\EmailType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;

class BasketType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder->add("full_name", TextType::class, [
            'label' => "ФИО",
            'attr' => ['placeholder' => "Иванов Иван Иванович"]
        ])->add("email", EmailType::class, [
            'label' => "Электронный адрес",
            'attr' => ['placeholder' => "xxxxx@xxxxx.xxx"]
        ])->add("phone", TextType::class, [
            'required' => false,
            'label' => "Телефон",
            'attr' => ['placeholder' => "+7 XXX XXX XX XX"]
        ])->add("comment", TextareaType::class, [
            'required' => false,
            'label' => "Комментарий",
            "attr" => ['cols' => "30", 'rows' => "10"]
        ]);
    }
}