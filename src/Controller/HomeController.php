<?php

namespace App\Controller;

use App\Entity\User;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;
use Symfony\Component\Routing\Attribute\Route;

final class HomeController extends AbstractController
{
    #[Route("/", name: "home")]
    function index(Request $request, EntityManagerInterface $em, UserPasswordHasherInterface $hasher): Response
    {
        //dd($request);
        //return new Response('Hello ' . $request->query->get('name', 'Inconnu'));
        return $this->render('home/index.html.twig');
    }
}
