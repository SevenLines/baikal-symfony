<?php

namespace AppBundle\Form;

use Doctrine\ORM\EntityRepository;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\FileType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class PortfolioImageType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder->add("imageFile", FileType::class)
            ->add("categories", EntityType::class, [
                'query_builder' => function(EntityRepository $er) use ($options) {
                    if (!is_null($options['job_id'])) {
                        return $er->createQueryBuilder('c')
                            ->select("c")
                            ->where("c.job = :job_id")
                            ->setParameter("job_id", $options['job_id']);
                    }
                    return $er->createQueryBuilder('c')->select("c");
                },
                'choice_label' => 'title',
                'multiple' => true,
                'class' => 'AppBundle\Entity\ProductCategory'
            ]);
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'csrf_token_id' => 'csrf_portfolio_image',
            'job_id' => null,
        ]);
    }



    public function getBlockPrefix()
    {
        return 'portfolio_image';
    }
}
