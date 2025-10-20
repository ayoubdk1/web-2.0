<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use App\Repository\BookRepository;
use Symfony\Component\HttpFoundation\Request;
use App\Entity\Book;
use App\Form\BookType;
use App\Entity\Author;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Doctrine\ORM\EntityManagerInterface;

final class BookController extends AbstractController
{
    #[Route('/book', name: 'app_book')]
    public function index(): Response
    {
        return $this->render('book/index.html.twig', [
            'controller_name' => 'BookController',
        ]);
    }
     #[Route('/Affiche', name: 'app_AfficheBook')]
public function Affiche (BookRepository $repository, EntityManagerInterface $em)
{
    $publishedBooks = $em->getRepository(Book::class)->findBy(['published' => true]);
    $numPublishedBooks = count($publishedBooks);
    $numUnPublishedBooks = count($em->getRepository(Book::class)->findBy(['published' => false]));

    // Remove the conditional below:
    // if ($numPublishedBooks > 0) { ... } else { ... }
    // Always render the same template and let Twig handle the display

    return $this->render('book/Affiche.html.twig', [
        'publishedBooks' => $publishedBooks,
        'numPublishedBooks' => $numPublishedBooks,
        'numUnPublishedBooks' => $numUnPublishedBooks
    ]);
}






    #[Route('/AddBook', name: 'app_AddBook')]
public function Add(Request $request, EntityManagerInterface $em)
{
    $book = new Book();
    $form = $this->createForm(BookType::class, $book);
    $form->add('Ajouter', SubmitType::class);
    $form->handleRequest($request);

    if ($form->isSubmitted() && $form->isValid()) {
        $author = $book->getAuthor();
        if ($author instanceof Author) {
            $author->setNbBooks($author->getNbBooks() + 1);
            $em->persist($book);
            $em->flush();
            return $this->redirectToRoute('app_AfficheBook');
        }
        // Optionally: handle  case where author is missing/wrong type
        // return a message, or redirect, or show an error page
        return $this->render('book/Add.html.twig', [
            'f' => $form->createView(),
            'error' => 'Author is invalid or missing.',
        ]);
    }

    // Always render form if the above conditions are not met
    return $this->render('book/Add.html.twig', ['f' => $form->createView()]);
}
#[Route('/editbook/{ref}', name: 'app_editBook')]
    public function edit(BookRepository $repository, $ref, Request $request, EntityManagerInterface $em)
    {
        $author = $repository->find($ref);
        $form = $this->createForm(BookType::class, $author);
        $form->add('Edit', SubmitType::class);

        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            // Use the injected EntityManagerInterface
            $em->flush(); // Correction : Utilisez la méthode flush() sur l'EntityManager pour enregistrer les modifications en base de données.
            return $this->redirectToRoute("app_AfficheBook");
        }

        return $this->render('book/edit.html.twig', [
            'f' => $form->createView(),
        ]);
    }
    #[Route('/deletebook/{ref}', name: 'app_deleteBook')]
    public function delete($ref, BookRepository $repository, EntityManagerInterface $em)
    {
        $book = $repository->find($ref);

        $em->remove($book);
        $em->flush();

        return $this->redirectToRoute('app_AfficheBook');
    }
 #[Route('/showbook/{ref}', name: 'app_showBook')]
public function show($ref, BookRepository $repository)
{
    $book = $repository->find($ref);
    return $this->render('book/show.html.twig', [
        'book' => $book,
    ]);
}

}