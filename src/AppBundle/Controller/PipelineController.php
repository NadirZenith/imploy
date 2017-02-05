<?php

namespace AppBundle\Controller;

use AppBundle\Entity\Pipeline;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;use Symfony\Component\HttpFoundation\Request;

/**
 * Pipeline controller.
 *
 * @Route("pipeline")
 */
class PipelineController extends Controller
{
    /**
     * Lists all pipeline entities.
     *
     * @Route("/", name="pipeline_index")
     * @Method("GET")
     */
    public function indexAction()
    {
        $em = $this->getDoctrine()->getManager();

        $pipelines = $em->getRepository('AppBundle:Pipeline')->findAll();

        return $this->render('pipeline/index.html.twig', array(
            'pipelines' => $pipelines,
        ));
    }

    /**
     * Creates a new pipeline entity.
     *
     * @Route("/new", name="pipeline_new")
     * @Method({"GET", "POST"})
     */
    public function newAction(Request $request)
    {
        $pipeline = new Pipeline();
        $form = $this->createForm('AppBundle\Form\PipelineType', $pipeline);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $em = $this->getDoctrine()->getManager();
            $em->persist($pipeline);
            $em->flush($pipeline);

            return $this->redirectToRoute('pipeline_show', array('id' => $pipeline->getId()));
        }

        return $this->render('pipeline/new.html.twig', array(
            'pipeline' => $pipeline,
            'form' => $form->createView(),
        ));
    }

    /**
     * Finds and displays a pipeline entity.
     *
     * @Route("/{id}", name="pipeline_show")
     * @Method("GET")
     */
    public function showAction(Pipeline $pipeline)
    {
        $deleteForm = $this->createDeleteForm($pipeline);

        return $this->render('pipeline/show.html.twig', array(
            'pipeline' => $pipeline,
            'delete_form' => $deleteForm->createView(),
        ));
    }

    /**
     * Displays a form to edit an existing pipeline entity.
     *
     * @Route("/{id}/edit", name="pipeline_edit")
     * @Method({"GET", "POST"})
     */
    public function editAction(Request $request, Pipeline $pipeline)
    {
        $deleteForm = $this->createDeleteForm($pipeline);
        $editForm = $this->createForm('AppBundle\Form\PipelineType', $pipeline);
        $editForm->handleRequest($request);

        if ($editForm->isSubmitted() && $editForm->isValid()) {
            $this->getDoctrine()->getManager()->flush();

            return $this->redirectToRoute('pipeline_edit', array('id' => $pipeline->getId()));
        }

        return $this->render('pipeline/edit.html.twig', array(
            'pipeline' => $pipeline,
            'edit_form' => $editForm->createView(),
            'delete_form' => $deleteForm->createView(),
        ));
    }

    /**
     * Deletes a pipeline entity.
     *
     * @Route("/{id}", name="pipeline_delete")
     * @Method("DELETE")
     */
    public function deleteAction(Request $request, Pipeline $pipeline)
    {
        $form = $this->createDeleteForm($pipeline);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $em = $this->getDoctrine()->getManager();
            $em->remove($pipeline);
            $em->flush($pipeline);
        }

        return $this->redirectToRoute('pipeline_index');
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
            ->setAction($this->generateUrl('pipeline_delete', array('id' => $pipeline->getId())))
            ->setMethod('DELETE')
            ->getForm()
        ;
    }
}
