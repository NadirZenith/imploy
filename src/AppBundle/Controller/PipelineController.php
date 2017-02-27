<?php

namespace AppBundle\Controller;

use AppBundle\Entity\Pipeline;
use AppBundle\Form\PipelineType;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Component\HttpFoundation\Request;

/**
 * Pipeline controller.
 *
 * @Route("/pipeline")
 */
class PipelineController extends Controller
{
    /**
     * Lists all pipeline entities.
     *
     * @Route("/")
     * @Method("GET")
     */
    public function listAction()
    {
        $em = $this->getDoctrine()->getManager();

        $pipelines = $em->getRepository(Pipeline::class)->findAll();

        return $this->render('pipeline/list.html.twig', array(
            'pipelines' => $pipelines,
        ));
    }

    /**
     * Creates a new pipeline entity.
     *
     * @Route("/create")
     * @Method({"GET", "POST"})
     */
    public function createAction(Request $request)
    {
        $form = $this->createForm(PipelineType::class, new Pipeline());
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $em = $this->getDoctrine()->getManager();
            $em->persist($form->getData());
            $em->flush();

            return $this->redirectToRoute('app_pipeline_read', array('id' => $form->getData()->getId()));
        }

        return $this->render('pipeline/form.html.twig', array(
            'action' => 'create',
            'form'   => $form->createView(),
        ));
    }

    /**
     * Finds and displays a pipeline entity.
     *
     * @Route("/{id}")
     * @Method("GET")
     * @param Pipeline $pipeline
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function readAction(Pipeline $pipeline)
    {

        return $this->render('pipeline/read.html.twig', array(
            'action'   => 'read',
            'pipeline' => $pipeline,));
    }

    /**
     * Displays a form to edit an existing pipeline entity.
     *
     * @Route("/{id}/update")
     * @Method({"GET", "POST"})
     * @param Request $request
     * @param Pipeline $pipeline
     * @return \Symfony\Component\HttpFoundation\RedirectResponse|\Symfony\Component\HttpFoundation\Response
     */
    public function updateAction(Request $request, Pipeline $pipeline)
    {
        $form = $this->createForm(PipelineType::class, $pipeline);
        $form->handleRequest($request);

        // delete
        if ($form->get('delete')->isClicked()) {
            return $this->deleteAction($request, $pipeline);

        } // update
        elseif ($form->isSubmitted() && $form->isValid()) {
            $this->getDoctrine()->getManager()->flush();

            return $this->redirectToRoute('app_pipeline_update', array('id' => $pipeline->getId()));
        }

        return $this->render('pipeline/form.html.twig', array(
            'action' => 'update',
            'form'   => $form->createView()
        ));
    }

    /**
     * Deletes a pipeline entity.
     *
     * @Route("/{id}/delete")
     * @param Request $request
     * @param Pipeline $pipeline
     * @return \Symfony\Component\HttpFoundation\RedirectResponse
     */
    public function deleteAction(Request $request, Pipeline $pipeline)
    {
        $em = $this->getDoctrine()->getManager();
        $em->remove($pipeline);
        $em->flush($pipeline);
        return $this->redirectToRoute('app_pipeline_list');
    }

    /**
     * Creates a form to delete a pipeline entity.
     *
     * @param Pipeline $pipeline The pipeline entity
     *
     * @return \Symfony\Component\Form\Form The form
     */
    private function createDeleteForm(Pipeline $pipeline)
    {
        return $this->createFormBuilder()
            ->setAction($this->generateUrl('app_pipeline_delete', array('id' => $pipeline->getId())))
            ->setMethod('DELETE')
            ->getForm();
    }
}
