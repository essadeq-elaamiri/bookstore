<?php

namespace App\Controller;

use App\Entity\Livre;
use App\Form\LivreType;
use App\Repository\AuteurRepository;
use App\Repository\GenreRepository;
use App\Repository\LivreRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

#[Route('/')]
class LivreController extends AbstractController
{

    #[Route('/', name: 'livre_home', methods: ['GET', 'POST'])]
    public function home(Request $request, LivreRepository $livreRepository, GenreRepository $genreRepository, AuteurRepository $auteurRepository): Response
    {
        $livres = $livreRepository->findAll();
        if($request->isMethod('POST')){
            $livres = $this->handelFilters($request, $livreRepository, $genreRepository, $auteurRepository);
        }

        return $this->render('livre/home.html.twig', [
            'livres' => $livres,
            'auteurs' => $auteurRepository->findAll(),
            'genres' => $genreRepository->findAll(),
        ]);
    }

    private function handelFilters(Request $request, LivreRepository $livreRepository, GenreRepository $genreRepository, AuteurRepository $auteurRepository){

        if($request->get('keywords')){
            return $livreRepository->findByTitreField($request->get('keywords'));
        }
        else if($request->get('note')){
            return $livreRepository->findByNoteField($request->get('note'));
        }
        else if($request->get('date1') && $request->get('date2') ){
            return $livreRepository->findByDateField($request->get('date1'), $request->get('date2'));
        }
        else if($request->get('author')){
            $id = $request->get("author");
            $author = $auteurRepository->find($id);
            return $author->getLivres();
        }
        else if($request->get('category')){
            $id = $request->get("category");
            $genre = $genreRepository->find($id);
            return $genre->getLivres();
        }
        else{
            return $livreRepository->findAll();
        }

    }
/*
    private function redirectAnonymousUsersToLogin(Request $request){
        //$this->get('security.context')
        if(!$request->get('security.context')->isGranted('ROLE_ADMIN') && !$request->get('security.context')->isGranted('ROLE_ADMIN') ){
            return new RedirectResponse($this->urlGenerator->genetare('app_login'));
        }
    }
*/
    #[Route('/livre', name: 'livre_index', methods: ['GET'])]
    public function index(Request $request, LivreRepository $livreRepository): Response
    {

        return $this->render('livre/index.html.twig', [
            'livres' => $livreRepository->findAll(),
        ]);
    }

    #[Route('/livre/new', name: 'livre_new', methods: ['GET', 'POST'])]
    public function new(Request $request, EntityManagerInterface $entityManager): Response
    {
        $livre = new Livre();
        $form = $this->createForm(LivreType::class, $livre);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager->persist($livre);
            $entityManager->flush();

            return $this->redirectToRoute('livre_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->renderForm('livre/new.html.twig', [
            'livre' => $livre,
            'form' => $form,
        ]);
    }

    #[Route('/livre/{id}', name: 'livre_show', methods: ['GET'])]
    public function show(Livre $livre): Response
    {
        return $this->render('livre/show.html.twig', [
            'livre' => $livre,
        ]);
    }

    #[Route('/livre/{id}/edit', name: 'livre_edit', methods: ['GET', 'POST'])]
    #[ParamConverter('id', class: 'Livre', options:['id'=>'id'])]
    public function edit(Request $request, Livre $livre, EntityManagerInterface $entityManager): Response
    {
        $form = $this->createForm(LivreType::class, $livre);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager->flush();

            return $this->redirectToRoute('livre_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->renderForm('livre/edit.html.twig', [
            'livre' => $livre,
            'form' => $form,
        ]);
    }

    #[Route('/livre/{id}/delete', name: 'livre_delete', methods: ['POST'])]
    public function delete(Request $request, Livre $livre, EntityManagerInterface $entityManager): Response
    {
        if ($this->isCsrfTokenValid('delete'.$livre->getId(), $request->request->get('_token'))) {
            $entityManager->remove($livre);
            $entityManager->flush();
        }

        return $this->redirectToRoute('livre_index', [], Response::HTTP_SEE_OTHER);
    }
}
