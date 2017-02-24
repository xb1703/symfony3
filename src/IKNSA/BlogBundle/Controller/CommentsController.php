<?php

namespace IKNSA\BlogBundle\Controller;

use IKNSA\BlogBundle\Entity\Comments;
use IKNSA\BlogBundle\Entity\Post;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;

/**
 * Comment controller.
 *
 */
class CommentsController extends Controller
{
    /**
     * Lists all comment entities.
     *
     */
    public function indexAction()
    {
        $em = $this->getDoctrine()->getManager();

        $comments = $em->getRepository('IKNSABlogBundle:Comments')->findAll();

        return $this->render('IKNSABlogBundle:Comments:index.html.twig', array(
            'comments' => $comments,
        ));
    }

    /**
     * Creates a new comment entity.
     *
     */
    public function newAction(Request $request)
    {
        $comment = new Comments();
        $form = $this->createForm('IKNSA\BlogBundle\Form\CommentsType', $comment);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $em = $this->getDoctrine()->getManager();
            $comment->setUser($this->getUser());
            $comment->setPost($this->getPost());
            $em->persist($comment);
            $em->flush($comment);

            return $this->redirectToRoute('comments_show', array('id' => $comment->getId()));
        }

        return $this->render('IKNSABlogBundle:Comments:new.html.twig', array(
            'comment' => $comment,
            'form' => $form->createView(),
        ));
    }

    /**
     * Finds and displays a comment entity.
     *
     */
    public function showAction(Comments $comment)
    {
        $deleteForm = $this->createDeleteForm($comment);

        return $this->render('IKNSABlogBundle:Comments:show.html.twig', array(
            'comment' => $comment,
            'delete_form' => $deleteForm->createView(),
        ));
    }

    /**
     * Displays a form to edit an existing comment entity.
     *
     */
    public function editAction(Request $request, Comments $comment)
    {
        $deleteForm = $this->createDeleteForm($comment);
        $editForm = $this->createForm('IKNSA\BlogBundle\Form\CommentsType', $comment);
        $editForm->handleRequest($request);

        if ($editForm->isSubmitted() && $editForm->isValid()) {
            $this->getDoctrine()->getManager()->flush();

            return $this->redirectToRoute('comments_edit', array('id' => $comment->getId()));
        }

        return $this->render('IKNSABlogBundle:Comments:edit.html.twig', array(
            'comment' => $comment,
            'edit_form' => $editForm->createView(),
            'delete_form' => $deleteForm->createView(),
        ));
    }

    /**
     * Deletes a comment entity.
     *
     */
    public function deleteAction(Request $request, Comments $comment)
    {
        $form = $this->createDeleteForm($comment);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $em = $this->getDoctrine()->getManager();
            $em->remove($comment);
            $em->flush($comment);
        }

        return $this->redirectToRoute('comments_index');
    }

    /**
     * Creates a form to delete a comment entity.
     *
     * @param Comments $comment The comment entity
     *
     * @return \Symfony\Component\Form\Form The form
     */
    private function createDeleteForm(Comments $comment)
    {
        return $this->createFormBuilder()
            ->setAction($this->generateUrl('comments_delete', array('id' => $comment->getId())))
            ->setMethod('DELETE')
            ->getForm()
        ;
    }
}
