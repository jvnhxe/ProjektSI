<?php
/**
 * Post controller.
 */

namespace App\Controller;

use App\Dto\PostListInputFiltersDto;
use App\Entity\Post;
use App\Entity\User;
use App\Form\Type\PostType;
use App\Repository\CommentRepository;
use App\Repository\PostRepository;
use App\Resolver\PostListInputFiltersDtoResolver;
use App\Service\PostServiceInterface;
use Knp\Component\Pager\PaginatorInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Form\Extension\Core\Type\FormType;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Attribute\MapQueryParameter;
use Symfony\Component\HttpKernel\Attribute\MapQueryString;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Security\Http\Attribute\IsGranted;
use Symfony\Contracts\Translation\TranslatorInterface;
use Symfony\Component\HttpKernel\Exception\AccessDeniedHttpException;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Security;

/**
 * Class PostController.
 */
#[Route('/post')]
class PostController extends AbstractController
{
    /**
     * Constructor.
     *
     * @param PostServiceInterface $postService Post service
     * @param TranslatorInterface  $translator  Translator
     */
    public function __construct(private readonly PostServiceInterface $postService, private readonly TranslatorInterface $translator)
    {
    }

    /**
     * Index action.
     *
     * @param PostListInputFiltersDto $filters Input filters
     * @param int                     $page    Page number
     *
     * @return Response HTTP response
     */
    #[Route(
        name: 'post_index',
        methods: 'GET'
    )]
    public function index(#[MapQueryString(resolver: PostListInputFiltersDtoResolver::class)] PostListInputFiltersDto $filters, #[MapQueryParameter] int $page = 1): Response
    {
        /** @var User $user */
        $user = $this->getUser();
        $pagination = $this->postService->getPaginatedList(
            $page,
            $user,
            $filters
        );

        return $this->render('post/index.html.twig', ['pagination' => $pagination]);
    }

    /**
     * Show action.
     *
     * @param Post               $post              Post entity
     * @param CommentRepository  $commentRepository Comment repository
     * @param PaginatorInterface $paginator         Paginator
     * @param Request            $request           HTTP request
     *
     * @return Response HTTP response
     */
    #[Route('/post/{id}', name: 'post_show', methods: ['GET'])]
    public function show(Post $post, CommentRepository $commentRepository, PaginatorInterface $paginator, Request $request): Response
    {

// Hide drafts from the public; allow author or admin to view
        if (method_exists($post, 'getStatus') && $post->getStatus() !== 'published') {
            $user = $this->getUser();
            $isAuthor = method_exists($post, 'getAuthor') && $user && $post->getAuthor() === $user;
            if (!$this->isGranted('ROLE_ADMIN') && !$isAuthor) {
                throw $this->createNotFoundException();
            }
        }

        $queryBuilder = $commentRepository->queryAllByPost($post);
        $page = $request->query->getInt('page', 1);

        $commentPagination = $paginator->paginate(
            $queryBuilder,
            $page,
            10 // number of comments per page
        );

        return $this->render('post/show.html.twig', [
            'post' => $post,
            'commentPagination' => $commentPagination,
        ]);
    }

    /**
     * Create action.
     *
     * @param Request $request HTTP request
     *
     * @return Response HTTP response
     */
    #[Route('/create', name: 'post_create', methods: 'GET|POST')]
    public function create(Request $request): Response
    {
        /** @var User $user */
        $user = $this->getUser();
        $post = new Post();
        $post->setAuthor($user);
        $post->setCreatedAt(new \DateTimeImmutable());

        $form = $this->createForm(
            PostType::class,
            $post,
            ['action' => $this->generateUrl('post_create')]
        );
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            // VichUploaderBundle will handle the file upload automatically
            // through its lifecycle events when the entity is persisted
            $this->postService->save($post);

            $this->addFlash(
                'success',
                $this->translator->trans('message.created_successfully')
            );

            return $this->redirectToRoute('post_show', ['id' => $post->getId()]);
        }

        return $this->render(
            'post/create.html.twig',
            ['form' => $form->createView(), 'post' => $post]
        );
    }

    /**
     * Edit action.
     *
     * @param Request $request HTTP request
     * @param Post    $post    Post entity
     *
     * @return Response HTTP response
     */
    #[Route('/{id}/edit', name: 'post_edit', requirements: ['id' => '[1-9]\d*'], methods: 'GET|PUT')]
    #[IsGranted('EDIT', subject: 'post')]
    public function edit(Request $request, Post $post): Response
    {
        $form = $this->createForm(
            PostType::class,
            $post,
            [
                'method' => 'PUT',
                'action' => $this->generateUrl('post_edit', ['id' => $post->getId()]),
            ]
        );
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            // VichUploaderBundle will handle the file upload automatically
            // through its lifecycle events when the entity is persisted
            $this->postService->save($post);

            $this->addFlash(
                'success',
                $this->translator->trans('message.edited_successfully')
            );

            return $this->redirectToRoute('post_show', ['id' => $post->getId()]);
        }

        return $this->render(
            'post/edit.html.twig',
            [
                'form' => $form->createView(),
                'post' => $post,
            ]
        );
    }

    #[Route('/me/posts', name: 'post_my', methods: ['GET'])]
    #[IsGranted('ROLE_USER')]
    public function my(
        #[MapQueryString(resolver: PostListInputFiltersDtoResolver::class)] PostListInputFiltersDto $filters,
        Request $request
    ): Response {
        /** @var User $user */
        $user = $this->getUser();

        // page z domyślną wartością 1
        $page = max(1, $request->query->getInt('page', 1));

        // ?status=all|draft|published (opcjonalnie)
        $statusParam = $request->query->get('status');
        $status = \in_array($statusParam, ['draft', 'published'], true) ? $statusParam : null;

        $pagination = $this->postService->getPaginatedListForAuthor($page, $user, $filters, $status);

        return $this->render('post/my.html.twig', [
            'pagination' => $pagination,
            'status' => $statusParam ?? 'all',
        ]);
    }

    /**
     * Delete action.
     *
     * @param Request $request HTTP request
     * @param Post    $post    Post entity
     *
     * @return Response HTTP response
     */
    #[Route('/{id}/delete', name: 'post_delete', requirements: ['id' => '[1-9]\d*'], methods: 'GET|DELETE')]
    #[IsGranted('DELETE', subject: 'post')]
    public function delete(Request $request, Post $post): Response
    {
        $form = $this->createForm(
            FormType::class,
            $post,
            [
                'method' => 'DELETE',
                'action' => $this->generateUrl('post_delete', ['id' => $post->getId()]),
            ]
        );
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            // VichUploaderBundle will handle file deletion automatically
            // when the entity is removed
            $this->postService->delete($post);

            $this->addFlash(
                'success',
                $this->translator->trans('message.deleted_successfully')
            );

            return $this->redirectToRoute('post_index');
        }

        return $this->render(
            'post/delete.html.twig',
            [
                'form' => $form->createView(),
                'post' => $post,
            ]
        );
    }

    /**
     * Handles the search request for posts by title.
     *
     * @param Request        $request        the current HTTP request
     * @param PostRepository $postRepository the repository used to search posts
     *
     * @return Response the rendered search results page
     */
    #[Route('/posts/search', name: 'post_search', methods: ['GET'])]
    public function search(Request $request, PostRepository $postRepository): Response
    {
        $query = trim($request->query->get('q', ''));
        $posts = [];

        if ('' !== $query && '0' !== $query) {
            $posts = $postRepository->searchByTitle($query);
        }

        return $this->render('post/search.html.twig', [
            'posts' => $posts,
            'query' => $query,
        ]);
    }
}
