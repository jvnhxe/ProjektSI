<?php
namespace App\Controller;

use App\Entity\Tag;
use App\Form\Type\TagType;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Http\Attribute\IsGranted;

#[Route('/tag')]
#[IsGranted('ROLE_ADMIN')]
class TagController extends AbstractController
{
    #[Route('/', name: 'tag_index', methods: ['GET'])]
    public function index(EntityManagerInterface $em): Response
    {
        $tags = $em->getRepository(Tag::class)->findBy([], ['name' => 'ASC']);
        return $this->render('tag/index.html.twig', ['tags' => $tags]);
    }

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
