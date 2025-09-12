<?php
/*
 * This file is part of the YourProject package.
 *
 * (c) Your Name <your-email@example.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace App\Controller;

use App\Entity\Comment;
use App\Entity\Post;
use App\Form\Type\CommentType;
use App\Service\CommentServiceInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Form\Extension\Core\Type\FormType;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Security\Http\Attribute\IsGranted;
use Symfony\Contracts\Translation\TranslatorInterface;

/**
 * Class CommentController.
 */
#[\Symfony\Component\Routing\Attribute\Route('/comment')]
class CommentController extends AbstractController
{
    /**
     * @var CommentServiceInterface Comment Service Interface
     */
    private readonly CommentServiceInterface $commentService;

    /**
     * Constructor.
     *
     * @param CommentServiceInterface $postService Post service
     * @param TranslatorInterface     $translator  Translator
     */
    public function __construct(private readonly CommentServiceInterface $postService, private readonly TranslatorInterface $translator)
    {
        $this->commentService = $postService;
    }

    /**
     * Create action.
     *
     * @param Request $request HTTP request
     * @param Post    $post    Post
     *
     * @return Response HTTP response
     */
    #[\Symfony\Component\Routing\Attribute\Route('/{id}/create', name: 'comment_create', requirements: ['id' => '[1-9]\d*'], methods: 'GET|POST')]
    public function create(Request $request, Post $post): Response
    {
        $comment = new Comment();
        $comment->setPost($post);
        $user = $this->getUser();
        if ($user && method_exists($user, 'getEmail')) {
            $comment->setEmail($user->getEmail());
        }
        $form = $this->createForm(CommentType::class, $comment, ['action' => $this->generateUrl('comment_create', ['id' => $post->getId()])]);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $this->commentService->save($comment);

            $this->addFlash(
                'success',
                $this->translator->trans('message.created_successfully')
            );

            return $this->redirectToRoute('post_show', ['id' => $post->getId()]);
        }

        return $this->render('comment/create.html.twig', [
            'form' => $form->createView(),
            'post' => $post,
        ]);
    }

    /**
     * Delete action.
     *
     * @param Request $request HTTP request
     * @param Comment $comment Comment entity
     *
     * @return Response HTTP response
     */
    #[\Symfony\Component\Routing\Attribute\Route('/{id}/delete', name: 'comment_delete', requirements: ['id' => '[1-9]\d*'], methods: 'GET|DELETE')]
    #[IsGranted('ROLE_ADMIN')]
    public function delete(Request $request, Comment $comment): Response
    {
        $form = $this->createForm(FormType::class, $comment, [
            'method' => 'DELETE',
            'action' => $this->generateUrl('comment_delete', ['id' => $comment->getId()]),
        ]);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $this->commentService->delete($comment);

            $this->addFlash(
                'success',
                $this->translator->trans('message.deleted_successfully')
            );

            return $this->redirectToRoute('post_index');
        }

        return $this->render('comment/delete.html.twig', [
            'form' => $form->createView(),
            'comment' => $comment,
        ]);
    }
}
