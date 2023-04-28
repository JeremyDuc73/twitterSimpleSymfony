<?php

namespace App\Controller;

use App\Entity\Comment;
use App\Entity\Post;
use App\Form\CommentType;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

#[Route('/comment')]
class CommentController extends AbstractController
{
    #[Route('/', name: 'app_comment')]
    public function index(): Response
    {
        return $this->render('comment/index.html.twig', [
            'controller_name' => 'CommentController',
        ]);
    }

    #[Route('/create/{id}', name:'create_comment')]
    public function create(Request $request, EntityManagerInterface $manager, Post $post):Response
    {
        $comment = new Comment();
        $commentForm = $this->createForm(CommentType::class,$comment);
        $commentForm->handleRequest($request);
        if($commentForm->isSubmitted() && $commentForm->isValid())
        {
            $comment->setPost($post);
            $comment->setAuthor($this->getUser());
            $comment->setCreatedAt(new \DateTime());
            $manager->persist($comment);
            $manager->flush();
        }
        return $this->redirectToRoute('show_post', ['id'=>$comment->getPost()->getId()]);
    }

    #[Route('/delete/{id}', name: 'delete_comment', priority: 2)]
    public function delete(Comment $comment, EntityManagerInterface $manager): Response
    {
        $manager->remove($comment);
        $manager->flush();
        return $this->redirectToRoute('show_post', ['id'=>$comment->getPost()->getId()]);
    }

    #[Route('/edit/{id}', name: 'edit_comment', priority: 2)]
    public function edit(Comment $comment, Request $request, EntityManagerInterface $manager): Response
    {
        $editCommentForm = $this->createForm(CommentType::class, $comment);
        $editCommentForm->handleRequest($request);
        if($editCommentForm->isSubmitted() && $editCommentForm->isValid())
        {
            $manager->persist($comment);
            $manager->flush();
            return $this->redirectToRoute('show_post', ['id'=>$comment->getPost()->getId()]);
        }
        return $this->renderForm('comment/edit.html.twig',[
            'editCommentForm'=>$editCommentForm
        ]);
    }
}