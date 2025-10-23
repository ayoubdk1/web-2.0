<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use App\Repository\AuthorRepository;
use Doctrine\ORM\EntityManagerInterface;
use App\Entity\Author;
use Symfony\Component\HttpFoundation\Request;
use App\Form\AuthorType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Security\Core\Role\Role;

final class AuthorController extends AbstractController
{
    #[Route('/author', name: 'app_author')]
    public function index(): Response
    {
        return $this->render('author/index.html.twig', [
            'controller_name' => 'AuthorController',
        ]);
    }
     #[Route('/showAuthor/{name}', name: 'app_showAuthor')]
     public function showAuthor ($name){
         return $this->render('author/show.html.twig', ['n' => $name ]);    

     }
        #[Route('/showlist', name: 'app_showlist')] 
     public function list(){
        $authors = array(
array('id' => 1, 'picture' => '/images/Victor-Hugo.jpg','username' => 'Victor Hugo', 'email' =>
'victor.hugo@gmail.com ', 'nb_books' => 100),
array('id' => 2, 'picture' => '/images/william-shakespeare.jpg','username' => ' William Shakespeare', 'email' =>
' william.shakespeare@gmail.com', 'nb_books' => 200 ),
array('id' => 3, 'picture' => '/images/Taha_Hussein.jpg','username' => 'Taha Hussein', 'email' =>
'taha.hussein@gmail.com', 'nb_books' => 300),
);
        return $this->render('author/list.html.twig', ['authors' => $authors]);     
     }
     #[Route('/auhtorDetails/{id}', name: 'app_authorDetails')]

    public function auhtorDetails»($id)
    {
        $author = [
            'id' => $id,
            'picture' => '~images',
            'username' => 'Author',
            'email' => 'author.email',
            'nb_books' => 10,
        ];

        return $this->render("author/showAuthor.html.twig",['author'=>$author]);
        }
     

        #[Route('/affiche', name: 'app_affiche')]
public function Affiche(Request $request, AuthorRepository $repository)
{
    $minBooks = $request->query->get('min_books');
    $maxBooks = $request->query->get('max_books');
    
    if ($minBooks !== null && $maxBooks !== null) {
        $authors = $repository->findByBookCountRange((int)$minBooks, (int)$maxBooks);
    } else {
        $authors = $repository->findAll();
    }
    
    return $this->render('author/Affiche.html.twig', [
        'author' => $authors,
        'minBooks' => $minBooks,
        'maxBooks' => $maxBooks
    ]);
}

        
#[Route('/addStatique', name: 'app_addStatique')]
public function addStatique(EntityManagerInterface $entityManager): Response
{
    $author1 = new Author();
    $author1->setUsername('ayoub');
    $author1->setEmail('ayoub@gmail.com');

    $entityManager->persist($author1); // prépare la sauvegarde
    $entityManager->flush();           // effectue la sauvegarde

    return $this->redirectToRoute('app_affiche');
}
#[Route('/add', name: 'app_add')]
public function Add (Request $request, EntityManagerInterface $entityManager)
{
$author=new Author();
$form=$this->createForm(AuthorType::class,$author);
$form->add('enter', SubmitType::class);
$form->handleRequest($request);
if ($form->isSubmitted() && $form->isValid()){
    $entityManager->persist($author);
    $entityManager->flush();
    return $this->redirectToRoute('app_affiche');
}
return $this->render('author/Add.html.twig',['form'=>$form->createView()]);
}
    #[Route('/edit/{id}', name: 'app_edit')]
        public function edit(AuthorRepository $repository, $id, Request $request, EntityManagerInterface $entityManager) 
   {
    $author = $repository->find($id);
    $form = $this->createForm(AuthorType::class, $author);
    $form->add('enter', SubmitType::class);
    $form->handleRequest($request);
    if ($form->isSubmitted() && $form->isValid()) {
        $entityManager->flush();
        return $this->redirectToRoute('app_affiche');
    }
    return $this->render('author/edit.html.twig', ['f' => $form->createView()]);
   } 
    #[Route('/delete/{id}', name: 'app_delete')]
    public function delete($id, AuthorRepository $repository, EntityManagerInterface $entityManager)
    {
        $author = $repository->find($id);

        if (!$author) {
            throw $this->createNotFoundException('Auteur non trouvé');
        }

        $entityManager->remove($author);
        $entityManager->flush();

        return $this->redirectToRoute('app_affiche');
    }
    #[Route('/delete-authors-no-books', name: 'app_delete_authors_no_books')]
public function deleteAuthorsWithNoBooks(AuthorRepository $repository, EntityManagerInterface $entityManager): Response
{
    $authors = $repository->findBy(['nb_books' => 0]);
    
    $count = count($authors);
    foreach ($authors as $author) {
        $entityManager->remove($author);
    }
    
    $entityManager->flush();
    
    $this->addFlash('success', "$count author(s) with 0 books deleted successfully.");
    
    return $this->redirectToRoute('app_affiche');
}
}