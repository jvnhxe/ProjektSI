<?php
/*
 * This file is part of the YourProject package.
 *
 * (c) Your Name <your-email@example.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

declare(strict_types=1);

namespace App\Controller;

use App\Entity\Tag;
use App\Form\Type\TagType;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Http\Attribute\IsGranted;

/**
 * Class TagController.
 *
 * Admin CRUD controller for managing tags.
 */
#[Route('/tag')]
#[IsGranted('ROLE_ADMIN')]
class TagController extends AbstractController
{
    /**
     * Lists all tags ordered by name.
     *
     * @param EntityManagerInterface $em Doctrine entity manager.
     *
     * @return Response Rendered tags index view.
     */
    #[Route('/', name: 'tag_index', methods: ['GET'])]
    public function index(EntityManagerInterface $em): Response
    {
        $tags = $em->getRepository(Tag::class)->findBy([], ['name' => 'ASC']);
        return $this->render('tag/index.html.twig', ['tags' => $tags]);
    }

    /**
     * Creates a new tag.
     *
     * Displays the form and persists the tag on valid submission.
     *
     * @param Request                 $request HTTP request with form data.
     * @param EntityManagerInterface  $em      Doctrine entity manager.
     *
     * @return Response Rendered form or redirect to tags index after creation.
     */
    #[Route('/new', name: 'tag_new', methods: ['GET','POST'])]
    public function new(Request $request, EntityManagerInterface $em): Response
    {
        $tag = new Tag();
        $form = $this->createForm(TagType::class, $tag);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $em->persist($tag);
            $em->flush();
            $this->addFlash('success', 'Tag utworzony.');
            return $this->redirectToRoute('tag_index');
        }

        return $this->render('tag/form.html.twig', ['form' => $form->createView(), 'title' => 'Nowy tag']);
    }

    /**
     * Edits an existing tag.
     *
     * @param Tag                     $tag     Tag to edit (resolved from {id}).
     * @param Request                 $request HTTP request with form data.
     * @param EntityManagerInterface  $em      Doctrine entity manager.
     *
     * @return Response Rendered form or redirect to tags index after save.
     */
    #[Route('/{id}/edit', name: 'tag_edit', methods: ['GET','POST'])]
    public function edit(Tag $tag, Request $request, EntityManagerInterface $em): Response
    {
        $form = $this->createForm(TagType::class, $tag);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $em->flush();
            $this->addFlash('success', 'Tag zapisany.');
            return $this->redirectToRoute('tag_index');
        }

        return $this->render('tag/form.html.twig', ['form' => $form->createView(), 'title' => 'Edycja tagu']);
    }

    /**
     * Deletes a tag.
     *
     * Validates the CSRF token and removes the tag.
     *
     * @param Tag                     $tag     Tag to delete (resolved from {id}).
     * @param Request                 $request HTTP request containing the CSRF token.
     * @param EntityManagerInterface  $em      Doctrine entity manager.
     *
     * @return Response Redirect to tags index.
     */
    #[Route('/{id}', name: 'tag_delete', methods: ['POST'])]
    public function delete(Tag $tag, Request $request, EntityManagerInterface $em): Response
    {
        if ($this->isCsrfTokenValid('delete_tag_'.$tag->getId(), $request->request->get('_token'))) {
            $em->remove($tag);
            $em->flush();
            $this->addFlash('success', 'Tag usunięty.');
        }
        return $this->redirectToRoute('tag_index');
    }
}
