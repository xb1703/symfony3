<?php

namespace IKNSA\BlogBundle\Controller;

use IKNSA\BlogBundle\Entity\Post;
use IKNSA\BlogBundle\Entity\Comments;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;


/**
 * Post controller.
 *
 */
class PostController extends Controller
{
    /**
     * Lists all post entities.
     *
     */
    public function indexAction()
    {
        $em = $this->getDoctrine()->getManager();
        // TODO : Afficher les 3 premiers posts
        $posts = $em->getRepository('IKNSABlogBundle:Post')->findAll();
        
        return $this->render('IKNSABlogBundle:Post:index.html.twig', array(
            'posts' => $posts, 'user' => $this->getUser()
        ));
    }

    // TODO : retourner un objet json
    public function apiAction()
    {
        $em = $this->getDoctrine()->getManager();
        //$posts = $em->getRepository('IKNSABlogBundle:Post')->fetch();

        $qb = $em->createQueryBuilder();
        $qb->select('p')
            ->from('IKNSA\BlogBundle\Entity\Post', 'p')
            //->orderBy('p.created_at')
            ->setMaxResults(3);

        $posts = $qb->getQuery()->getResult();
        $json_posts = array();
        array_push($json_posts, $posts);

        return $this->render('IKNSABlogBundle:Post:json.html.twig', array(
            'posts' => json_encode($json_posts),
        ));

    }


    /**
     * Creates a new post entity.
     *
     */
    public function newAction(Request $request)
    {
        $post = new Post();
        $form = $this->createForm('IKNSA\BlogBundle\Form\PostType', $post);
        $form->handleRequest($request);

        if(!$this->getUser()) {
          $this->addFlash('notice', 'You must be identified to access this section');
          return $this->redirectToRoute('post_index');
        }

        if ($form->isSubmitted() && $form->isValid()) {
            $em = $this->getDoctrine()->getManager();
            $post->setUser($this->getUser());
            $em->persist($post);
            $em->flush($post);

            return $this->redirectToRoute('post_show', array('id' => $post->getId()));
        }

        return $this->render('IKNSABlogBundle:Post:new.html.twig', array(
            'post' => $post,
            'form' => $form->createView(),
        ));
    }

    /**
     * Finds and displays a post entity.
     *
     */
    public function showAction(Post $post, Request $request)
    {

          $em = $this->getDoctrine()->getManager();
          // liste des comments du post
          $comments = $em->getRepository('IKNSABlogBundle:Comments')->findByPost($post->getId());
          // formulaire commentaire new
          $comment = new Comments();
          $form = $this->createForm('IKNSA\BlogBundle\Form\CommentsType', $comment);
          $form->handleRequest($request);

          if ($form->isSubmitted() && $form->isValid()) {
              $em = $this->getDoctrine()->getManager();
              $comment->setUser($this->getUser());
              $comment->setPost($post);
              $comment->setCreatedAt(new \Datetime('NOW'));
              $em->persist($comment);
              $em->flush($comment);

              return $this->redirectToRoute('comments_show', array('id' => $comment->getId()));
          }

          $deleteForm = $this->createDeleteForm($post);

          return $this->render('IKNSABlogBundle:Post:show.html.twig', array(
              'post' => $post,
              'delete_form' => $deleteForm->createView(),
              'comments' => $comments,
              'form' => $form->createView(),
          ));
    }

    /**
     * Displays a form to edit an existing post entity.
     *
     */
    public function editAction(Request $request, Post $post)
    {
        if($this->getUser() === $post->getUser()){

        $deleteForm = $this->createDeleteForm($post);
        $editForm = $this->createForm('IKNSA\BlogBundle\Form\PostType', $post);
        $editForm->handleRequest($request);

        if ($editForm->isSubmitted() && $editForm->isValid()) {
            $this->getDoctrine()->getManager()->flush();

            return $this->redirectToRoute('post_edit', array('id' => $post->getId()));
        }

        return $this->render('IKNSABlogBundle:Post:edit.html.twig', array(
            'post' => $post,
            'edit_form' => $editForm->createView(),
            'delete_form' => $deleteForm->createView(),
        ));
      } else {
          $this->addFlash('notice', 'You must be the author of the post to access this section');
          return $this->redirectToRoute('post_index');
      }
    }

    /**
     * Deletes a post entity.
     *
     */
    public function deleteAction(Request $request, Post $post)
    {
        $form = $this->createDeleteForm($post);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $em = $this->getDoctrine()->getManager();
            $em->remove($post);
            $em->flush($post);
        }

        return $this->redirectToRoute('post_index');
    }

    /**
     * Creates a form to delete a post entity.
     *
     * @param Post $post The post entity
     *
     * @return \Symfony\Component\Form\Form The form
     */
    private function createDeleteForm(Post $post)
    {
        return $this->createFormBuilder()
            ->setAction($this->generateUrl('post_delete', array('id' => $post->getId())))
            ->setMethod('DELETE')
            ->getForm()
        ;
    }
}
