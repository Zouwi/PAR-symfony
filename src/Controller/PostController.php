<?php

namespace App\Controller;

use App\Entity\Comment;
use App\Entity\Like;
use App\Entity\Post;
use App\Entity\Poste;
use App\Form\CommentType;
use App\Repository\CommentRepository;
use App\Repository\LikeRepository;
use App\Repository\PostRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
//use Symfony\Bundle\SecurityBundle\Security;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Core\Security;

class PostController extends AbstractController
{
    #[Route('/post', name: 'app_post')]
    public function index(PostRepository $postRepository): Response
    {
        return $this->render('post/index.html.twig', [
            'posts' => $postRepository->findAll(),
        ]);
    }

    #[Route('/post/{id}', name: 'app_post_detail', methods: ['GET', 'POST'])]
    public function show(Post $post, $id, Request $request, EntityManagerInterface $entityManager, LikeRepository $likeRepository): Response
    {
        $comment = new Comment();
        $form = $this->createForm(CommentType::class, $comment);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $comment->setAuthor($this->getUser())
                ->setPost($post);
            $entityManager->persist($post);
            $entityManager->flush();
            return $this->redirectToRoute('app_post_detail',[
                'id' => $id,
            ]);
        }

        return $this->render('post/post_detail.html.twig', [
            'posts' => $post,
            'form' =>$form->createView(),
            'comment' => $comment,
            'id' => $id,
        ]);
    }

    #[Route('/post/post_new', name: 'app_post_new')]
    public function create(PostRepository $postRepository): Response
    {
        return $this->render('post/create.html.twig', [
            'posts' => $postRepository->findAll(),
        ]);
    }

    #[Route('/post/{id}/like/{postId}', name: 'app_post_like_comment', methods: ['GET'])]
    public function likeComment(PostRepository $postRepository, EntityManagerInterface $entityManager, Security $security, int $postId, Post $post, LikeRepository $likeRepository): Response
    {
        //$this->denyAccessUnlessGranted('IS_AUTHENTICATED');
        $like = new Like();
        $user = $security->getUser();
        $currentComment = $postRepository->find($postId);
        if (!($likeRepository->findBy(
            ['user' => $user,
                'post' => $currentComment]
        ))) {
            $like->setUser($user);
            $like->setPost($currentComment);
            $like->setType(false);

            $entityManager->persist($like);
            $entityManager->flush();
        } elseif ($likeRepository->findBy(
            ['user' => $user,
                'post' => $currentComment,
                'type' => true]
        )) {
            $currentLike = $likeRepository->findOneBy(
                ['user' => $user,
                    'post' => $currentComment,
                    'type' => true]
            );

            $entityManager->remove($currentLike);
            $entityManager->flush();

            $like->setUser($user);
            $like->setPost($currentComment);
            $like->setType(false);

            $entityManager->persist($like);
            $entityManager->flush();
        } else {
            $currentLike = $likeRepository->findOneBy(
                ['user' => $user,
                    'post' => $currentComment]
            );

            $entityManager->remove($currentLike);
            $entityManager->flush();
        }
        return $this->redirectToRoute("app_post_detail", ["id" => $post->getId()]);
    }

    #[Route('/post/{id}/dislike/{postId}', name: 'app_post_dislike_comment', methods: ['GET'])]
    public function dislikeComment(PostRepository $postRepository, EntityManagerInterface $entityManager, Security $security, int $postId, Post $post, LikeRepository $likeRepository): Response
    {
        //$this->denyAccessUnlessGranted('IS_AUTHENTICATED');
        $like = new Like();
        $user = $security->getUser();
        $currentComment = $postRepository->find($postId);
        if (!($likeRepository->findBy(
            ['user' => $user,
                'post' => $currentComment]
        ))) {
            $like->setUser($user);
            $like->setPost($currentComment);
            $like->setType(true);

            $entityManager->persist($like);
            $entityManager->flush();
        } elseif ($likeRepository->findBy(
            ['user' => $user,
                'post' => $currentComment,
                'type' => false]
        )) {
            $currentLike = $likeRepository->findOneBy(
                ['user' => $user,
                    'post' => $currentComment,
                    'type' => false]
            );

            $entityManager->remove($currentLike);
            $entityManager->flush();

            $like->setUser($user);
            $like->setPost($currentComment);
            $like->setType(true);

            $entityManager->persist($like);
            $entityManager->flush();
        } else {
            $currentLike = $likeRepository->findOneBy(
                ['user' => $user,
                    'post' => $currentComment]
            );

            $entityManager->remove($currentLike);
            $entityManager->flush();
        }


        return $this->redirectToRoute("app_post_detail", ["id" => $post->getId()]);
    }
}
