<?php

namespace AppBundle\Web;

use AppBundle\Entity\PortfolioImage;
use AppBundle\Entity\ProductCategory;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;

class PortfolioController extends Controller
{

    /**
     * @Route("portfolio/{job_id}/{title}", name="portfolio")
     * @Route("portfolio/{job_id}", name="portfolio", defaults={"job_id": null})
     * @param $job_id
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function indexAction($job_id)
    {
        $doctrine = $this->getDoctrine();
        $categories = $doctrine->getRepository("AppBundle:ProductCategory")->createQueryBuilder("c")
            ->select("c.title as text, c.id as id")
            ->innerJoin("c.job", "j");

        $job = $doctrine->getRepository("AppBundle:Job")->find($job_id);

        $images = $doctrine->getRepository("AppBundle:PortfolioImage")->createQueryBuilder("i")
            ->select("i, c")
            ->leftJoin("i.categories", 'c')
            ->leftJoin("c.job", 'j')
            ->orderBy("i.updatedAt");

        if (!is_null($job_id)) {
            $images = $images->where("j.id = :job_id OR i.job = :job_id")
                ->setParameter("job_id", $job_id);
            $categories = $categories->where("j.id = :job_id")->setParameter("job_id", $job_id);
        }

        $portfolio_service = $this->get("portfolio_service");

        if ($this->get("security.authorization_checker")->isGranted("ROLE_PORTFOLIO_EDIT")) {
            $categories = $categories->getQuery()->getArrayResult();
        }

        $images = $images->getQuery()->getResult();
        $images = array_values(array_map(function (PortfolioImage $image) use ($portfolio_service) {
            return $portfolio_service->toDict($image);
        }, array_filter($images, function (PortfolioImage $image) {
            return !is_null($image->getImageName());
        })));

        $form = $this->createForm('AppBundle\Form\PortfolioImageType', null, [
            'job' => $job
        ]);
        $setForm = $this->createForm('AppBundle\Form\PortfolioSetType');
        $setForm->get("job")->setData($job);

        return $this->render('web/portfolio/portfolio_index.html.twig', [
            'categories' => $categories,
            'images' => $images,
            'job' => $job,
            'form' => $form->createView(),
            'setForm' => $setForm->createView(),
            'form_name' => $form->getName(),
        ]);
    }

    /**
     * @Route("portfolio/full/", name="all_portfolio")
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function allAction() {
        $doctrine = $this->getDoctrine();
        $categories = $doctrine->getRepository("AppBundle:ProductCategory")->createQueryBuilder("c")
            ->select("c.title as text, c.id as id")
            ->innerJoin("c.job", "j");

        $images = $doctrine->getRepository("AppBundle:PortfolioImage")->createQueryBuilder("i")
            ->select("i, c")
            ->leftJoin("i.categories", 'c')
            ->leftJoin("c.job", 'j')
            ->orderBy("i.updatedAt");

        $portfolio_service = $this->get("portfolio_service");

        if ($this->get("security.authorization_checker")->isGranted("ROLE_PORTFOLIO_EDIT")) {
            $categories = $categories->getQuery()->getArrayResult();
        }

        $images = $images->getQuery()->getResult();
        $images = array_values(array_map(function (PortfolioImage $image) use ($portfolio_service) {
            return $portfolio_service->toDict($image);
        }, array_filter($images, function (PortfolioImage $image) {
            return !is_null($image->getImageName());
        })));

        return $this->render('web/portfolio/portfolio_index.html.twig', [
            'categories' => $categories,
            'images' => $images,
        ]);
    }
}
