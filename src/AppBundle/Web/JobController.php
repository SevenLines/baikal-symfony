<?php
/**
 * Created by PhpStorm.
 * User: m
 * Date: 10.02.17
 * Time: 21:05
 */

namespace AppBundle\Web;


use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\ResponseHeaderBag;

class JobController extends Controller
{
    /**
     * @Route("j/{job_id}/{title}", name="job_description")
     * @Route("j/{job_id}", name="job_description_without_name")
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function job($job_id)
    {
        $repo = $this->getDoctrine()->getRepository("AppBundle:Job");
        $job = $repo->createQueryBuilder("j")
            ->leftJoin("j.productCategories", "c")
            ->leftJoin("c.products", "p")
            ->setParameter("job_id", $job_id)
            ->where("j.id = :job_id")
            ->addSelect("c", "p")
            ->orderBy("p.title");

        if(!$this->isGranted("ROLE_ADMIN")) {
            $job = $job->andWhere("c.visible = TRUE");
        }

        $job = $job
            ->getQuery()->getOneOrNullResult();

        return $this->render("web/job.html.twig", [
            "job" => $job,
        ]);
    }


    /**
     * @Route("products.xsl", name="products_xsl")
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function products_list()
    {
        $phpExcelObject = $this->get('phpexcel')->createPHPExcelObject();
        $repo = $this->getDoctrine()->getRepository("AppBundle:Job");
        $jobs = $repo->createQueryBuilder("j")
            ->leftJoin("j.productCategories", "c")
            ->leftJoin("c.products", "p")
            ->addSelect("c", "p")
            ->Where("c.visible = TRUE")
            ->orderBy("p.title")
            ->getQuery()->getResult();

        foreach ($jobs as $job) {
            $sheet = $phpExcelObject->createSheet();
            $sheet->setTitle($job->getTitle());
            $row = 1;
            foreach ($job->getProductCategories() as $category) {
                $sheet->mergeCells("A$row:E$row");
                $sheet->setCellValue("A$row", $category->getTitle());
                $row++;
                foreach ($category->getProducts() as $product) {
                    $sheet->setCellValue("A$row", $product->getId());
                    $cell = $sheet->getCell("B$row");
                    $cell->setValue($product->getTitle());

                    $sheet->getColumnDimension("B")->setWidth(75);
                    $sheet->getRowDimension($row)->setRowHeight(-1);
//                    $sheet->getStyle("B$row")->getAlignment()->setWrapText(true);

                    $sheet->setCellValue("C$row", $product->getPriceMin());
                    $sheet->setCellValue("D$row", $product->getPriceMax());
                    $sheet->setCellValue("E$row", $product->getUnit());
                    $row++;
                }
            }
        }

        $phpExcelObject->removeSheetByIndex();

        // create the writer
        $writer = $this->get('phpexcel')->createWriter($phpExcelObject);
        // create the response
        $response = $this->get('phpexcel')->createStreamedResponse($writer);
        // adding headers
        $dispositionHeader = $response->headers->makeDisposition(
            ResponseHeaderBag::DISPOSITION_ATTACHMENT,
            'price_list.xls'
        );
        $response->headers->set('Content-Type', 'text/vnd.ms-excel; charset=utf-8');
        $response->headers->set('Pragma', 'public');
        $response->headers->set('Cache-Control', 'maxage=1');
        $response->headers->set('Content-Disposition', $dispositionHeader);

        return $response;
    }
}