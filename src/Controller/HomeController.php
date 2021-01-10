<?php
namespace App\Controller;


use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

use App\Entity\Lecteur;
use App\Entity\Compte;
use App\Entity\Bibliotheque;
use App\Entity\Livre;
use App\Entity\Categorie;
class HomeController extends AbstractController
{
    /**
     * @Route("/", name="home")
     */
    public function index()
    {
        $livres = $this->getDoctrine()
                    ->getRepository(Livre::class)
                    ->findAll();
        if(isset($_SESSION['connected']))
        {
            return $this->render('home/index.html.twig', [
                'status' => $_SESSION['connected'],
                'books' => $livres
            ]);
        }
        else
        {
            return $this->render('home/index.html.twig', [
                'status' => false,
                'books' => $livres
            ]);
        }
    }

    /**
     *@Route("/books", name="books")
     */
    public function show()
    {
        $livres = $this->getDoctrine()
                ->getRepository(Livre::class)
                ->findAll();

        if(isset($_SESSION['connected']))
        {
            return $this->render("home/books.html.twig", [
                'status' => $_SESSION['connected'],
                'books' => $livres
            ]);
        }
        else
        {
            return $this->render("home/books.html.twig", [
                'status' => false,
                'books' => $livres
            ]);
        }
    }

    /**
     * @Route("/signin", name="signin")
     */
    public function signin(Request $request)
    {
        
        date_default_timezone_set('Africa/Dakar');
        
        if($request->isMethod("POST")) {
            $donnees = $request->request->all();

            // récupération
            $identifiant = htmlspecialchars($donnees['username']);
            $password = sha1(htmlspecialchars($donnees['password']));

            // créer un lecteur
            $lecteur = new Lecteur();

            $lecteur->setNomlecteur($donnees["last-name"]);
            $lecteur->setPrenomlecteur($donnees["first-name"]);
            $lecteur->setTellecteur($donnees["phone"]);
            $lecteur->setAdresselecteur($donnees["address"]);

            $compte = new Compte();
            $compte->setIdentifiant($identifiant);
            $compte->setPassword($password);
            $date = new \DateTime('NOW');
            $compte->setCreationdate($date);
            $compte->setModifdate($date);
            
            $bibliotheque = new Bibliotheque();
            $bibliotheque->setNombibliotheque("Bibliothèque de ".$donnees['last-name']);
            
            // Inialiser entity manager
            $iden = $this->getDoctrine()
                ->getRepository(Compte::class)
                ->findBy(array("identifiant" => $identifiant));
                
            if($iden != null) {
                return new Response("__USERNAME_EXISTS__");
            }
            $entityManager = $this->getDoctrine()->getManager();

            $entityManager->persist($lecteur);
            $entityManager->flush();

            //récupérer l'id du lecteur
            $lecteurId = $entityManager->createQueryBuilder()
                        ->select('MAX(Lecteur.idlecteur)')
                        ->from('App\Entity\Lecteur', 'Lecteur')
                        ->getQuery()
                        ->getSingleScalarResult();
                        
            $reader = $this->getDoctrine()
                ->getRepository(Lecteur::class)
                ->find($lecteurId);

            if($reader != null)
            {
                $compte->setIdlecteur($reader);
                $bibliotheque->setIdlecteur($reader);
                $entityManager->persist($compte);
                $entityManager->persist($bibliotheque);
                $entityManager->flush();

                return new Response("__SUCCES__");
            }
           

        }
        else
        {
            return new Response("__ERROR_IDENTIFIANT__");   
        }
    }

    /**
     * @Route("/login", name="login")
    */
    public function login(Request $request)
    {
        if($request->isMethod("POST"))
        {
            $donnees = $request->request->all();
            //dd($donnees);
            $iden = htmlspecialchars($donnees['identifiant']);
            $password = sha1(htmlspecialchars($donnees['motDePasse']));

            $account = $this->getDoctrine()
                ->getRepository(Compte::class)
                ->findBy(array("identifiant" => $iden));
            if($account != null)
            {
                $reader = $this->getDoctrine()
                ->getRepository(Compte::class)
                ->findBy(array("password" => $password, "identifiant" => $iden));

                if($reader != null)
                {
                    if(!isset($_SESSION))
                        session_start();
                    $_SESSION['reader'] = serialize($reader);
                    $_SESSION['connected'] = true;
                    
                    return new Response("__SUCCES__");
                }
                else
                {
                    return new Response("__ERROR_PASSWORD__");
                }
            }else {
                return new Response("__IDEN_PASSWORD__");
            }

        }
        return new Response("OK");
    }


    /**
     * @Route("/logout", name="logout")
    */
    public function logout()
    {   if(isset($_SESSION['reader']))
            session_destroy();
        return $this->redirectToRoute("home");
    }
}
