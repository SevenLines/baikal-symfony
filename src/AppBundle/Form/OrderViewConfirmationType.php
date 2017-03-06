<?php
/**
 * Created by PhpStorm.
 * User: m
 * Date: 06.03.17
 * Time: 22:20
 */

namespace AppBundle\Form;


use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;

class OrderViewConfirmationType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder->add("email", TextType::class, [
            'label' => "Введите email указанный при оформлении заявки",
            'attr' => [
                'placeholder' => "xxxxx@xxxxx.xxx",
                'class' => "input-lg"
            ]
        ])->add("Отправить", SubmitType::class, [
            'attr' => [
                'class' => 'btn-primary btn-lg'
            ]
        ]);
    }

}