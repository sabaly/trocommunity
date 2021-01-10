<?php
namespace App\Controller;

session_start();
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use App\Service\FileUploader;
use Symfony\Component\Form\Forms;
use Symfony\Component\Form\Extension\HttpFoundation\HttpFoundationExtension;
use Symfony\Component\Form\Extension\Core\Type\FileType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\FormType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\Extension\Core\Type\ResetType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;


use App\Entity\Categorie;
use App\Entity\Livre;
use App\Entity\Lecteur;
use App\Entity\Bibliotheque;
use App\Entity\Compte;
use App\Entity\Action;
use App\Entity\Echange;
/**
 *@Route("/reader")
*/
class ReaderController extends AbstractController
{
    /**
     * @Route("/library", name="library")
     */
    public function index(Request $request, FileUploader $fileUploader)
    {
        $livres = $this->getDoctrine()
                    ->getRepository(Livre::class)
                    ->findAll();
        //dd($_SESSION);
        if(isset($_SESSION['connected']))
        {

            $categories = $this->getDoctrine()
                ->getRepository(Categorie::class)
                ->findAll();
            $formFactory = Forms::createFormFactoryBuilder()
                            ->addExtension(new HttpFoundationExtension())
                            ->getFormFactory();
            $form = $formFactory->createBuilder(FormType::class, null, [
                'attr' => array('id' => "addBook-form")
            ])
                ->add('bookName', TextType::class, [
                    'label' => "Nom *",
                ])
                ->add("bookDesc", TextareaType::class, [
                    'label' => "Description",
                    'required' => false
                ])
                ->add("categorie", ChoiceType::class, [
                    'choices' => $categories,
                    'choice_label' => function(?Categorie $category) {
                        return $category ? strtoupper($category->getNomcategorie()) : '';
                    },
                ])
                ->add("image", FileType::class, [
                    'required' => false,
                    'attr' => array('accept' => ".jpg, .jpeg, .png", )
                ])
                ->add("submit", SubmitType::class, [
                    'label' => "Ajouter",
                ])
                ->add("Reset", ResetType::class, [
                    'label' => "A zéro",
                    'attr' => array('class' => "btn btn-danger", )
                ])
                ->getForm();
            
                $form->handleRequest($request);
                if($form->isSubmitted() && $form->isValid())
                {
                    $donnees = $form->getData();

                    $file = $donnees["image"];
                    if($file != null)
                        $fileName = $fileUploader->upload($file);

                    $livre = new Livre();
                    $livre->setNomlivre($donnees['bookName']);
                    $livre->setDesclivre($donnees['bookDesc']);
                    $livre->setIdcategorie($donnees['categorie']);
                    $livre->setImage($fileName);
                    $idBiblio = $this->getDoctrine()
                        ->getRepository(Bibliotheque::class)
                        ->findBy(array(
                            'idlecteur' => unserialize($_SESSION['reader'])[0]->getIdlecteur(), 
                        ));
                    //dd($idBiblio);
                    $livre->setIdbibliotheque($idBiblio[0]);
                    
                    $date = new \DateTime('NOW');
                    $livre->setDateajout($date);
                    $livre->setDatemodif($date);

                    $entityManager = $this->getDoctrine()->getManager();
                    $entityManager->persist($livre);
                    $entityManager->flush();

                    $this->addFlash("Ajout", "Ajouté avec succès");

                }
                $idBiblio = $this->getDoctrine()
                        ->getRepository(Bibliotheque::class)
                        ->findBy(array(
                            'idlecteur' => unserialize($_SESSION['reader'])[0]->getIdlecteur(), 
                        ));
                $livres = $this->getDoctrine()
                        ->getRepository(Livre::class)
                        ->findBy(array(
                            'idbibliotheque' => $idBiblio,
                        ));
                return $this->render('reader/library.html.twig', [
                        'books' => $livres,
                        'user' => unserialize($_SESSION['reader'])[0],
                        'addBook_form' => $form->createView(),
                        'status' => $_SESSION['connected'],
                ]);
        }
        else
        {
            return $this->RedirectToRoute('home', [
                'books' => $livres
            ]);
        }
    }

    /**
     * @Route("/update/{id}", name="update")
     */
    public function update(int $id, Request $request, FileUploader $fileUploader)
    {
        $categories = $this->getDoctrine()
            ->getRepository(Categorie::class)
            ->findAll();

        if(isset($_SESSION['connected']))
        {
            $livre = $this->getDoctrine()
                    ->getRepository(Livre::class)
                    ->find($id);
            $formFactory = Forms::createFormFactoryBuilder()
                            ->addExtension(new HttpFoundationExtension())
                            ->getFormFactory();
            $form = $formFactory->createBuilder(FormType::class, null, [
                'attr' => array(
                    'id' => "addBook-form"
                )
            ])
                ->add('bookName', TextType::class, [
                    'label' => "Nom *",
                    'attr' => array(
                        'value' => $livre->getNomlivre()
                    )
                ])
                ->add("bookDesc", TextareaType::class, [
                    'label' => "Description",
                    'required' => false,
                    'data' => $livre->getDesclivre()
                ])
                ->add("categorie", ChoiceType::class, [
                    'choices' => $categories,
                    'choice_label' => function(?Categorie $category) {
                        return $category ? strtoupper($category->getNomcategorie()) : '';
                    },
                    'attr' => array(
                        'value' => $livre->getIdcategorie()->getIdcategorie()
                    )
                ])
                ->add("image", FileType::class, [
                    'required' => false,
                    'attr' => array(
                        'accept' => ".jpg, .jpeg, .png",
                        'label' => "Nouvelle image"
                     )
                ])
                ->add("submit", SubmitType::class, [
                    'label' => "Modifier",
                ])
                ->getForm();

                $form->handleRequest($request);
                if($form->isSubmitted() && $form->isValid())
                {
                    $donnees = $form->getData();

                    $entityManager = $this->getDoctrine()->getManager();
                    
                    $file = $donnees["image"];
                    if($file != null)
                    {
                        $fileName = $fileUploader->upload($file);
                        $livre->setImage($fileName);
                    }
                    $livre->setNomlivre($donnees["bookName"]);
                    $livre->setDesclivre($donnees["bookDesc"]);
                    $livre->setIdcategorie($donnees["categorie"]);

                    $date = new \DateTime('NOW');
                    $livre->setDatemodif($date);

                    $this->addFlash("updated", "Mis à jours avec succès");
                    $entityManager->flush();
                }
        }
        return $this->render('reader/update.html.twig', [
            'controller_name' => 'ReaderController',
            'user' => unserialize($_SESSION['reader'])[0],
            'status' => $_SESSION['connected'],
            'form' => $form->createView()
        ]);
    }

    /**
     *@Route("/show/{id}", name="show")
     */
    public function show(int $id)
    {
        if(isset($_SESSION['connected']))
        {
            $livre = $this->getDoctrine()
                    ->getRepository(Livre::class)
                    ->find($id);
            $categorie = $this->getDoctrine()
                    ->getRepository(Categorie::class)
                    ->findBy(array(
                        'idcategorie' => $livre->getIdcategorie()
                    ));
            if(sizeof($categorie) > 0) 
            {
                $categorie = $categorie[0];
            }
            return $this->render("reader/show.html.twig", [
                'status' => true,
                'user' => unserialize($_SESSION['reader'])[0],
                'livre' => $livre,
                'categorie' => $categorie
            ]);
        }
        else
        {
            $livres = $this->getDoctrine()
                ->getRepository(Livre::class)
                ->findAll();
            return $this->render("home/index.html.twig", [
                'status' => false,
                'books' => $livres
            ]);
        }
        
    }


    /**
     *@Route("/delete", name="del")
     */
    public function delete(Request $request)
    {
        if(isset($_SESSION['connected']))
        {

            $livre = $this->getDoctrine()
                    ->getRepository(Livre::class)
                    ->find($request->get('id'));
            $entityManager = $this->getDoctrine()->getManager();
            $entityManager->remove($livre);
            $entityManager->flush();
            
            return new Response('OK');
        }
        else
        {

            return new Response('NO');
        }
        
    }

    /**
     *@Route("/addToFavorite", name="addToFavorite")
     */
    public function addToFavorite(Request $request)
    {
        if(isset($_SESSION['connected']))
        {
            //Connexion
            $entityManager = $this->getDoctrine()->getManager();
            $conn = $entityManager->getConnection();

            //Récupérer les données
            $id = $request->get('id');
            $actionType = $request->get('action');

            //Les actions précédentes de l'utilisateur
            $userActions = $this->getDoctrine()
                    ->getRepository(Action::class)
                    ->findBy(array(
                        'idlecteur' => unserialize($_SESSION['reader'])[0]->getIdlecteur(),
                        'typeaction' => $actionType
                    ));
            // Vérification si l'action n'a pas été éffectué avant
            $state = false;
            foreach ($userActions as $userAction) {
                $idaction = $userAction->getIdaction();
                $sql = 'SELECT * FROM subir WHERE idaction = :action and idlivre = :livre';
                $stmt = $conn->prepare($sql);

                $stmt->execute([
                    'action' => $userAction->getIdaction(),
                    'livre' => $id
                ]);

                $result = $stmt->fetchAllAssociative();
                if(sizeof($result) > 0)
                {
                    $state = true;
                    break;
                }

            }
            if($state)
            {
                return new Response("__DEJA_AJOUTE__");
            }

            $action = new Action();

            $action->setTypeaction($request->get('action'));
            $idlecteur = unserialize($_SESSION['reader'])[0]->getIdlecteur();

            //Vérifier si le livre n'est pas au propriétaire
            $livre = $this->getDoctrine()
                        ->getRepository(Livre::class)
                        ->find($id);
            $biblio = $this->getDoctrine()
                        ->getRepository(Bibliotheque::class)
                        ->find($livre->getIdbibliotheque());
            
            if($biblio->getIdlecteur()->getIdlecteur() == $idlecteur->getIdlecteur())
            {
                return new Response('__USER_BOOK__');
            }

            $lecteur = $this->getDoctrine()
                        ->getRepository(Lecteur::class)
                        ->find($idlecteur);
            $action->setIdlecteur($lecteur);
            $entityManager->persist($action);
            $entityManager->flush();

            $actionId = $entityManager->createQueryBuilder()
                        ->select('MAX(Action.idaction)')
                        ->from('App\Entity\Action', 'Action')
                        ->getQuery()
                        ->getSingleScalarResult();
            $sql = 'INSERT INTO subir(idlivre, idaction) VALUES (:livre, :action)';
            $stmt = $conn->prepare($sql);
            
            if($stmt->execute([
                'livre' => $request->get('id'),
                'action' => $actionId
            ]))
            {
                return new Response("__ACTION__");
            }

        }
        else
        {
            return new Response('__NOT_CONNECTED__');
        }
    }

    /**
     * @Route("/preferences", name="preferences")
     */
    public function preferences()
    {
        $livres = $this->getDoctrine()
                    ->getRepository(Livre::class)
                    ->findAll();
        if(isset($_SESSION['connected']))
        { 
            $conn = $this->getDoctrine()->getManager()->getConnection();
            $actions = $this->getDoctrine()
                        ->getRepository(Action::class)
                        ->findBy(array(
                            'idlecteur' => unserialize($_SESSION['reader'])[0]->getIdlecteur(),
                            'typeaction' => 'heart'
                        ));
            $hLivres = null;
            $proprietaires = null;
            foreach ($actions as $action) {
                $sql = 'SELECT idlivre FROM subir WHERE idaction = :action';
                $stmt = $conn->prepare($sql);

                $stmt->execute([
                    'action' => $action->getIdaction(),
                ]);
                $result = $stmt->fetchAllAssociative();
                if(sizeof($result) > 0)
                {
                    $livre = $this->getDoctrine()
                            ->getRepository(Livre::class)
                            ->find($result[0]['idlivre']);
                    $hLivres[] = $livre;
                    $bibliotheque = $this->getDoctrine()
                                    ->getRepository(Bibliotheque::class)
                                    ->findBy(array(
                                        'idbibliotheque' => $livre->getIdbibliotheque()
                                    ));
                    $proprietaires[$result[0]['idlivre']] = $bibliotheque[0]->getNombibliotheque();
                }

                
            }

            //dd($proprietaires);
            return $this->render('reader/preferences.html.twig', [
                'books' => $hLivres,
                'proprietaires' => $proprietaires,
                'controller_name' => 'ReaderController',
                'user' => unserialize($_SESSION['reader'])[0],
                'status' => $_SESSION['connected']
            ]);
        }
        else
        {
            return $this->RedirectToRoute('home', [
                'books' => $livres
            ]);
        }
    }

    
    /**
     *@Route("/details/{id}", name="details")
     */
    public function details(int $id)
    {
        if(isset($_SESSION['connected']))
        {
            $livre = $this->getDoctrine()
                    ->getRepository(Livre::class)
                    ->find($id);
            $categorie = $this->getDoctrine()
                    ->getRepository(Categorie::class)
                    ->findBy(array(
                        'idcategorie' => $livre->getIdcategorie()
                    ));
            if(sizeof($categorie) > 0) 
            {
                $categorie = $categorie[0];
            }
            return $this->render("home/details.html.twig", [
                'status' => $_SESSION['connected'],
                'user' => unserialize($_SESSION['reader'])[0],
                'livre' => $livre,
                'categorie' => $categorie
            ]);
        }
        else
        {
            $livres = $this->getDoctrine()
                ->getRepository(Livre::class)
                ->findAll();
            return $this->render("home/index.html.twig", [
                'status' => false,
                'books' => $livres
            ]);
        }
        
    }

    /**
     *@Route("/preference/remove")
     */
    public function RFP(Request $request) 
    {
        $entityManager = $this->getDoctrine()->getManager();
        //récupère l'action
        
        $conn = $entityManager->getConnection();

        $sql = 'SELECT idaction FROM subir WHERE idlivre = :id';
        $stmt = $conn->prepare($sql);

        $stmt->execute([
            'id' => $request->get('id'),
        ]);

        $result = $stmt->fetchAllAssociative();
        
        if(sizeof($result) > 0)
        {
            $action = $this->getDoctrine()
                        ->getRepository(Action::class)
                        ->find($result[0]['idaction']);

            
            $sql = 'DELETE FROM subir WHERE idlivre = :livre';
            $stmt = $conn->prepare($sql);
            
            if($stmt->execute(['livre' => $request->get('id')]))
            {
                $entityManager->remove($action);
                $entityManager->flush();

                return new Response('__REMOVED__');
            }
            else
            {
                return new Response('__CANNOT_REMOVE__');
            }

        }
        else
        {
            return  new Response('__NO_ACTION__');
        }
    }

    
    /**
     * @Route("/suggestions", name="suggestions")
     */
    public function suggestions()
    {

        $livres = $this->getDoctrine()
                    ->getRepository(Livre::class)
                    ->findAll();
        if(isset($_SESSION['connected']))
        {
            $idBiblio = $this->getDoctrine()
                    ->getRepository(Bibliotheque::class)
                    ->findBy(array(
                        'idlecteur' => unserialize($_SESSION['reader'])[0]->getIdlecteur(), 
                    ));
            $livres = $this->getDoctrine()
                    ->getRepository(Livre::class)
                    ->findBy(array(
                        'idbibliotheque' => $idBiblio,
                    ));

            return $this->render('reader/suggestions.html.twig', [
                'books' => $livres,
                'controller_name' => 'ReaderController',
                'user' => unserialize($_SESSION['reader'])[0],
                'status' => $_SESSION['connected']
            ]);
        }
        else
        {
            return $this->RedirectToRoute('home', ['books' => $livres]);
        }
    }

    
    /**
     * @Route("/propositions", name="propositions")
     */
    public function propositions(Request $request)
    {
        
        if(isset($_SESSION['connected']))
        {
            //Connexion
            $entityManager = $this->getDoctrine()->getManager();
            $conn = $entityManager->getConnection();

            // Récupérer l'id de la bibiliothèque de l'utilisateur
            $idBiblio = $this->getDoctrine()
                    ->getRepository(Bibliotheque::class)
                    ->findBy(array(
                        'idlecteur' => unserialize($_SESSION['reader'])[0]->getIdlecteur(), 
                    ));
            $idBiblio = $idBiblio[0]->getIdbibliotheque();
            
            // Récupérer les livres préférés de l'utilisateur
            $actions = $this->getDoctrine()
                        ->getRepository(Action::class)
                        ->findBy(array(
                            'idlecteur' => unserialize($_SESSION['reader'])[0]->getIdlecteur(),
                            'typeaction' => 'heart'
                        ));
            $hLivres = null;
            foreach ($actions as $userAction) {
                $idaction = $userAction->getIdaction();
                $sql = 'SELECT idlivre FROM subir WHERE idaction = :action';
                $stmt = $conn->prepare($sql);
                $stmt->execute([
                    'action' => $userAction->getIdaction(),
                ]);

                $result = $stmt->fetchAllAssociative();
                
                if(sizeof($result) > 0){
                    $livre = $this->getDoctrine()
                            ->getRepository(Livre::class)
                            ->find($result[0]['idlivre']);
                    $hLivres[] = $livre;
                }
            }
            // Les livres proposés; initialiser à null
            $livres = [];
            $contres = [];
            $proprietaires = [];
            if($hLivres != null)
            {
                foreach ($hLivres as $hLivre) {
                    $biblio = $this->getDoctrine()
                            ->getRepository(Bibliotheque::class)
                            ->find($hLivre->getIdbibliotheque());
                    $proprietaire = $this->getDoctrine()
                            ->getRepository(Lecteur::class)
                            ->find($biblio->getIdlecteur());
                    //Récupérer les préférences du propriétaires
                    $propActions = $this->getDoctrine()
                            ->getRepository(Action::class)
                            ->findBy(array(
                                'idlecteur' => $proprietaire->getIdlecteur(),
                                'typeaction' => 'heart'
                            ));
                    foreach ($propActions as $action) {
                        $idaction = $action->getIdaction();
                        $sql = 'SELECT idlivre FROM subir WHERE idaction = :action';
                        $stmt = $conn->prepare($sql);
                        $stmt->execute([
                            'action' => $action->getIdaction(),
                        ]);

                        $result = $stmt->fetchAllAssociative();
                        if(sizeof($result) > 0)
                        {
                            $livre = $this->getDoctrine()
                                ->getRepository(Livre::class)
                                ->find($result[0]['idlivre']);

                            if($livre->getIdbibliotheque()->getIdbibliotheque() == $idBiblio)
                            {
                                $book = $this->getDoctrine()
                                    ->getRepository(Livre::class)
                                    ->find($hLivre->getIdlivre());
                                
                                $livres[] = $book;
                                $contres[$hLivre->getIdlivre()] = $livre;
                                $bibliotheque = $this->getDoctrine()
                                                ->getRepository(Bibliotheque::class)
                                                ->findBy(array(
                                                    'idbibliotheque' => $book->getIdbibliotheque()
                                                ));
                                $proprietaires[$hLivre->getIdlivre()] = $bibliotheque[0]->getNombibliotheque();

                                if($livre->getIdechange() == null){
                                    // Créer un échange
                                    $exchange = new Echange();
                                    $exchange->setStatus(false);
                                    $entityManager->persist($exchange);
                                    $entityManager->flush();

                                    // Récupérer l'id du lecteur
                                    $exchangeId = $entityManager->createQueryBuilder()
                                                ->select('MAX(Echange.idechange)')
                                                ->from('App\Entity\Echange', 'Echange')
                                                ->getQuery()
                                                ->getSingleScalarResult();
                                    $exchange = $this->getDoctrine()
                                                ->getRepository(Echange::class)
                                                ->find($exchangeId);

                                    $livre->setIdechange($exchange);
                                    $book->setIdechange($exchange);

                                    $entityManager->flush();
                                }
                                
                            }
                        }
                    }
                }
            }
            
            if($request->isXmlHttpRequest()) {
                $Numactions = $this->getDoctrine()
                            ->getRepository(Action::class)
                            ->findBy(array(
                                'typeaction' => 'heart',
                                'idlecteur' => unserialize($_SESSION['reader'])[0]->getIdlecteur()->getIdlecteur()
                            ));
                return new Response(sizeof($Numactions));

            }
            return $this->render('reader/propositions.html.twig', [
                'books' => $livres,
                'againts' => $contres,
                'proprietaires' => $proprietaires,
                'controller_name' => 'ReaderController',
                'user' => unserialize($_SESSION['reader'])[0],
                'status' => $_SESSION['connected']
            ]);
        }
        else
        {
            $livres = $this->getDoctrine()
                    ->getRepository(Livre::class)
                    ->findAll();
            return $this->RedirectToRoute('home', ['books' => $livres]);
        }
    }
    
    /**
     * @Route("/exchanges", name="exchanges")
     */
    public function exchanges(Request $request)
    {
        $livres = $this->getDoctrine()
                    ->getRepository(Livre::class)
                    ->findAll();

        if(isset($_SESSION['connected']))
        {
            $entityManager = $this->getDoctrine()->getManager();
            $conn = $this->getDoctrine()->getConnection();
            
            if($request->isXmlHttpRequest()) {
                // Récupération des données
                $idLecteurBook = $request->get('idLecteurBook');
                $idAgainstBook = $request->get('idAgainstBook');

                // Vérifier si le livre n'est pas concerné par un autre échange
                $userBook = $this->getDoctrine()
                        ->getRepository(Livre::class)
                        ->find($idLecteurBook);

                // Vérifier s'il n'a pas déjà accepter
                $sql = 'SELECT * FROM valider WHERE idlecteur = :lecteur and idechange = :echange';
                $stmt = $conn->prepare($sql);
                $stmt->execute([
                    'lecteur' => unserialize($_SESSION['reader'])[0]->getIdlecteur()->getIdlecteur(),
                    'echange' => $userBook->getIdechange()->getIdechange()
                ]);
                if($stmt->fetchAllAssociative() != []) {
                    return new Response("__DEJA_ACCEPTER__");
                }
                // Insertion dans la table valider
                $sql = 'INSERT INTO valider(idlecteur, idechange) VALUES (:lecteur, :echange)';
                $stmt = $conn->prepare($sql);
                if($stmt->execute([
                    'lecteur' => unserialize($_SESSION['reader'])[0]->getIdlecteur()->getIdlecteur(),
                    'echange' => $userBook->getIdechange()->getIdechange()
                ])) {
                    $sql = 'SELECT COUNT(idechange) as idechange FROM valider WHERE idechange = :echange';
                    $stmt = $conn->prepare($sql);
                    $stmt->execute([
                        'echange' => $userBook->getIdechange()->getIdechange(),
                    ]);
                    $result = $stmt->fetchAllAssociative();

                    if($result[0]['idechange'] > 1)
                    {
                        $exchange = $this->getDoctrine()->getRepository(Echange::class)
                                    ->find($userBook->getIdechange()->getIdechange());
                        $exchange->setStatus(true);
                        $entityManager->flush();
                        return new Response("__ECHANGE_VALIDE__");
                    }
                    else
                    {
                        return new Response("__ECHANGE_EN_COURS__");
                    }
                }
            }
            // Get exchanges
            $sql = "SELECT idechange FROM valider WHERE idlecteur = :lecteur";
            $stmt = $conn->prepare($sql);
            $stmt->execute([
                'lecteur' => unserialize($_SESSION['reader'])[0]->getIdlecteur()->getIdlecteur()
            ]);
            $results = $stmt->fetchAllAssociative();
            $exchanges = [];
            $states = [];
            foreach ($results as $result) {

                $books = $this->getDoctrine()
                        ->getRepository(Livre::class)
                        ->findBy(array(
                            'idechange' => $result['idechange']
                        ));
                if($books != []){
                    $exchanges[$result['idechange']] = $books;
                    $echange = $this->getDoctrine()->getRepository(Echange::class)
                            ->find($result['idechange']);
                    $states[$books[0]->getIdlivre()] = $echange->getStatus();  
                }

            }

            
            return $this->render('reader/exchanges.html.twig', [
                'books' => $exchanges,
                'states' => $states,
                'controller_name' => 'ReaderController',
                'user' => unserialize($_SESSION['reader'])[0],
                'status' => $_SESSION['connected']
            ]);
        }
        else
        {
            return $this->RedirectToRoute('home', ['books' => $livres]);
        }
    }

    
    /**
     * @Route("/settings", name="settings")
     */
    public function settings()
    {
        $livres = $this->getDoctrine()
                    ->getRepository(Livre::class)
                    ->findAll();
        if(isset($_SESSION['connected']))
        {
            return $this->render('reader/settings.html.twig', [
                'books' => $livres,
                'controller_name' => 'ReaderController',
                'user' => unserialize($_SESSION['reader'])[0],
                'status' => $_SESSION['connected']
            ]);
        }
        else
        {
            return $this->RedirectToRoute('home', ['books' => $livres]);
        }
    }
}
