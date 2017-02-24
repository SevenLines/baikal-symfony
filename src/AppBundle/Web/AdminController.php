<?php
/**
 * Created by PhpStorm.
 * User: m
 * Date: 24.02.17
 * Time: 19:21
 */

namespace AppBundle\Web;


use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;

class AdminController extends Controller
{
    /**
     * @Route("admin/options_edit", name="options_edit")
     */
    public function optionsEditAction()
    {
        $options = $this->get("options_service")->getOptions();

        // redirect to the 'edit' view of the given entity item
        return $this->redirectToRoute('easyadmin', array(
            'action' => 'edit',
            'id' => $options->getId(),
            'entity' => 'Options',
        ));
    }
}