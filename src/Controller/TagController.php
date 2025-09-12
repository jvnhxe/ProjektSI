<?php
/*
 * This file is part of the YourProject package.
 *
 * (c) Your Name <your-email@example.com>
 *
 * @license MIT
 */

declare(strict_types=1);

namespace App\Controller;

use App\Entity\Tag;
use App\Form\Type\TagType;
use App\Service\TagServiceInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Http\Attribute\IsGranted;

/**
 * Class TagController.
 *
 * Admin CRUD controller for managing tags via service layer.
 */
#[Route('/tag')]
#[IsGranted('ROLE_ADMIN')]
class TagController extends AbstractController
{
    /**
     * Lists all tags ordered by name.
     *
     * @param TagServiceInterface $tagService Tag domain service.
     *
     * @return Response Rendered tags index view.
     */
    #[Route('/', name: 'tag_index', methods: ['GET'])]
    public function index(TagServiceInterface $tagService): Response
    {
        $tags = $tagService->getAllOrdered();

        return $this->render('tag/index.html.twig', ['tags' => $tags]);
    }

    /**
     * Creates a new tag.
     *
     * @param Request             $request    HTTP request with form data.
     * @param TagServiceInterface $tagService Tag domain service.
     *
     * @return Response Rendered form or redirect after creation.
     */
    #[Route('/new', name: 'tag_new', methods: ['GET', 'POST'])]
    public function new(Request $request, TagServiceInterface $tagService): Response
    {
        $tag = new Tag();
        $form = $this->createForm(TagType::class, $tag);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $tagService->save($tag);
            $this->addFlash('success', 'Tag utworzony.');

            return $this->redirectToRoute('tag_index');
        }

        return $this->render('tag/form.html.twig', [
            'form'  => $form->createView(),
            'title' => 'Nowy tag',
        ]);
    }

    /**
     * Edits an existing tag.
     *
     * @param Tag                 $tag        Tag to edit (resolved from {id}).
     * @param Request             $request    HTTP request with form data.
     * @param TagServiceInterface $tagService Tag domain service.
     *
     * @return Response Rendered form or redirect after save.
     */
    #[Route('/{id}/edit', name: 'tag_edit', methods: ['GET', 'POST'])]
    public function edit(Tag $tag, Request $request, TagServiceInterface $tagService): Response
    {
        $form = $this->createForm(TagType::class, $tag);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $tagService->save($tag);
            $this->addFlash('success', 'Tag zapisany.');

            return $this->redirectToRoute('tag_index');
        }

        return $this->render('tag/form.html.twig', [
            'form'  => $form->createView(),
            'title' => 'Edycja tagu',
        ]);
    }

    /**
     * Deletes a tag (CSRF protected).
     *
     * @param Tag                 $tag        Tag to delete (resolved from {id}).
     * @param Request             $request    HTTP request containing CSRF token.
     * @param TagServiceInterface $tagService Tag domain service.
     *
     * @return Response Redirect to tags index.
     */
    #[Route('/{id}', name: 'tag_delete', methods: ['POST'])]
    public function delete(Tag $tag, Request $request, TagServiceInterface $tagService): Response
    {
        if ($this->isCsrfTokenValid('delete_tag_'.$tag->getId(), $request->request->get('_token'))) {
            $tagService->delete($tag);
            $this->addFlash('success', 'Tag usunięty.');
        }

        return $this->redirectToRoute('tag_index');
    }
}
