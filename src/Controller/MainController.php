<?php

namespace App\Controller;

use DateTime;
use Exception;
use App\Entity\User;
use App\Entity\Favori;
use App\Entity\Annonce;
use App\Entity\Comment;
use App\Entity\Demande;
use App\Entity\Commande;
use Square\SquareClient;
use Stripe\StripeClient;
use App\Form\RefusFormType;
use Square\Api\PaymentsApi;
use App\Form\AnnonceFormType;
use App\Form\ConfirmFormType;
use App\Form\DeclineFormType;
use App\Form\DemandeFormType;
use SquareConnect\Model\Money;
use App\Entity\PropositionJeux;
use App\Form\AddArticleFormType;
use App\Form\AddCommentFormType;
use App\Form\EditProfilFormType;
use Square\Models\CreatePaymentRequest;
use Doctrine\ORM\EntityManagerInterface;
use Doctrine\Persistence\ManagerRegistry;
use Knp\Component\Pager\PaginatorInterface;
use Symfony\Bridge\Twig\Mime\TemplatedEmail;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Mailer\MailerInterface;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\Security\Http\Attribute\IsGranted;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\ParamConverter;

class MainController extends AbstractController
{

    #[Route('/', name: 'home')]
    public function home(ManagerRegistry $doctrine): Response
    {
        $repository = $doctrine->getRepository(Annonce::class);

        $annonces = $repository->findAll();

        $repository2 = $doctrine->getRepository(Commande::class);

        $my_games = $repository2->findBy(['user' => $this->getUser()]);

        if ($this->getUser()) {

            $my_games = $repository2->createQueryBuilder('c')
                ->select('c')
                ->where('c.user = :id')
                ->setParameter('id', $this->getUser()->getId())
                ->getQuery()
                ->execute();
        }

        $repository3 = $doctrine->getRepository(Favori::class);

        $favoris = $repository3->findBy(['user' => $this->getUser()]);

        if ($this->getUser()) {

            $favoris = $repository3->createQueryBuilder('f')
                ->select('f')
                ->where('f.user = :id')
                ->setParameter('id', $this->getUser()->getId())
                ->getQuery()
                ->execute();
        }

        return $this->render('main/home.html.twig', [
            'my_games' => $my_games,
            'favoris' => $favoris,
            'annonces' => $annonces,
        ]);
    }

    #[Route('/success', name: 'success')]
    public function success()
    {
        return $this->render('main/success.html.twig');
    }

    #[Route('/error', name: 'error')]
    public function error()
    {
        return $this->render('main/error.html.twig');
    }

    #[Route('/create-stripe-session/{project}', name: 'pay')]
    public function stripeCheckout(EntityManagerInterface $em, $project)
    {
        $project = $em->getRepository(Demande::class)->findOneBy(['id' => $project]);

        if (!$project) {
            $this->addFlash('error', 'La commande n\'a pas été trouvé.');
            return $this->redirectToRoute('home');
        }

        $paymentStripe[] = [
            'price_data' => [
                'currency' => 'eur',
                'product_data' => [
                    'name' => 'Project',
                ],
                'unit_amount' => $project->getPrice() * 100,
            ],
            'quantity' => 1,
        ];

        $stripePrivateKey = new StripeClient($_ENV['STRIPE_SECRET_KEY']);
        // Stripe::setApiKey($stripePrivateKey);

        $checkout_session = $stripePrivateKey->checkout->sessions->create([
            'line_items' => $paymentStripe,
            'mode' => 'payment',
           'success_url' => $this->generateUrl('success', [], UrlGeneratorInterface::ABSOLUTE_URL),
            'cancel_url' => $this->generateUrl('error', [], UrlGeneratorInterface::ABSOLUTE_URL),
        ]);

        return new RedirectResponse($checkout_session->url);
    }

    #[Route('/create-stripe-session-commande/{project}', name: 'pay')]
    public function stripeCommandeCheckout(EntityManagerInterface $em, $project)
    {
        $project = $em->getRepository(Commande::class)->findOneBy(['id' => $project]);

        if (!$project) {
            $this->addFlash('error', 'La commande n\'a pas été trouvé.');
            return $this->redirectToRoute('home');
        }

        $paymentStripe[] = [
            'price_data' => [
                'currency' => 'eur',
                'product_data' => [
                    'name' => 'Project',
                ],
                'unit_amount' => $project->getPrice() * 100,
            ],
            'quantity' => 1,
        ];

        $stripePrivateKey = new StripeClient($_ENV['STRIPE_SECRET_KEY']);
        // Stripe::setApiKey($stripePrivateKey);

        $checkout_session = $stripePrivateKey->checkout->sessions->create([
            'line_items' => $paymentStripe,
            'mode' => 'payment',
           'success_url' => $this->generateUrl('success', [], UrlGeneratorInterface::ABSOLUTE_URL),
            'cancel_url' => $this->generateUrl('error', [], UrlGeneratorInterface::ABSOLUTE_URL),
        ]);

        return new RedirectResponse($checkout_session->url);
    }

    #[Route('/mentions-legales/', name: 'mentions_legales')]
    public function mentionsLegales(ManagerRegistry $doctrine): Response
    {

        $repository2 = $doctrine->getRepository(Commande::class);

        $my_games = $repository2->findBy(['user' => $this->getUser()]);

        if ($this->getUser()) {

            $my_games = $repository2->createQueryBuilder('c')
                ->select('c')
                ->where('c.user = :id')
                ->setParameter('id', $this->getUser()->getId())
                ->getQuery()
                ->execute();
        }

        $repository3 = $doctrine->getRepository(Favori::class);

        $favoris = $repository3->findBy(['user' => $this->getUser()]);

        if ($this->getUser()) {

            $favoris = $repository3->createQueryBuilder('f')
                ->select('f')
                ->where('f.user = :id')
                ->setParameter('id', $this->getUser()->getId())
                ->getQuery()
                ->execute();
        }

        return $this->render('main/mentions_legales.html.twig', [
            'my_games' => $my_games,
            'favoris' => $favoris,
        ]);
    }

    #[Route('/liens/', name: 'links')]
    public function links(ManagerRegistry $doctrine): Response
    {

        $repository2 = $doctrine->getRepository(Commande::class);

        $my_games = $repository2->findBy(['user' => $this->getUser()]);

        if ($this->getUser()) {

            $my_games = $repository2->createQueryBuilder('c')
                ->select('c')
                ->where('c.user = :id')
                ->setParameter('id', $this->getUser()->getId())
                ->getQuery()
                ->execute();
        }

        $repository3 = $doctrine->getRepository(Favori::class);

        $favoris = $repository3->findBy(['user' => $this->getUser()]);

        if ($this->getUser()) {

            $favoris = $repository3->createQueryBuilder('f')
                ->select('f')
                ->where('f.user = :id')
                ->setParameter('id', $this->getUser()->getId())
                ->getQuery()
                ->execute();
        }

        return $this->render('main/links.html.twig', [
            'my_games' => $my_games,
            'favoris' => $favoris,
        ]);
    }

    #[Route('/mes-commandes/', name: 'mes_commandes')]
    public function mesCommandes(ManagerRegistry $doctrine): Response
    {

        if (!$this->isGranted('ROLE_USER')) {

            $this->addFlash('error', 'Vous devez être connecté pour accéder à cette page !');

            return $this->redirectToRoute('app_login');
        } else {

            $repository2 = $doctrine->getRepository(Commande::class);

            $my_games = $repository2->findBy(['user' => $this->getUser()]);

            $repository3 = $doctrine->getRepository(Favori::class);

            $favoris = $repository3->findBy(['user' => $this->getUser()]);

            if ($this->getUser()) {

                $my_games = $repository2->createQueryBuilder('c')
                    ->select('c')
                    ->where('c.user = :id')
                    ->setParameter('id', $this->getUser()->getId())
                    ->getQuery()
                    ->execute();

                $favoris = $repository3->createQueryBuilder('f')
                    ->select('f')
                    ->where('f.user = :id')
                    ->setParameter('id', $this->getUser()->getId())
                    ->getQuery()
                    ->execute();
            }

            return $this->render('main/mes_commandes.html.twig', [
                'my_games' => $my_games,
                'favoris' => $favoris,
            ]);
        }
    }

    #[Route('/favoris/', name: 'favoris')]
    public function favoris(ManagerRegistry $doctrine): Response
    {

        if (!$this->isGranted('ROLE_USER')) {

            $this->addFlash('error', 'Vous devez être connecté pour accéder à cette page !');

            return $this->redirectToRoute('app_login');
        } else {

            $repository = $doctrine->getRepository(Favori::class);

            $favoris = $repository->findBy(['user' => $this->getUser()]);

            $repository2 = $doctrine->getRepository(Commande::class);

            $my_games = $repository2->findBy(['user' => $this->getUser()]);

            if ($this->getUser()) {

                $my_games = $repository2->createQueryBuilder('c')
                    ->select('c')
                    ->where('c.user = :id')
                    ->setParameter('id', $this->getUser()->getId())
                    ->getQuery()
                    ->execute();

                $favoris = $repository->createQueryBuilder('f')
                    ->select('f')
                    ->where('f.user = :id')
                    ->setParameter('id', $this->getUser()->getId())
                    ->getQuery()
                    ->execute();
            }

            return $this->render('main/favoris.html.twig', [
                'my_games' => $my_games,
                'favoris' => $favoris,
            ]);
        }
    }

    #[Route('/commandes/', name: 'commandes')]
    public function gamesPropositionsList(ManagerRegistry $doctrine): Response
    {

        $repository = $doctrine->getRepository(Commande::class);

        $propositions_jeux = $repository->findAll();

        $repository2 = $doctrine->getRepository(Commande::class);

        $my_games = $repository2->findBy(['user' => $this->getUser()]);

        if ($this->getUser()) {

            $my_games = $repository2->createQueryBuilder('c')
                ->select('c')
                ->where('c.user = :id')
                ->setParameter('id', $this->getUser()->getId())
                ->getQuery()
                ->execute();
        }

        $repository3 = $doctrine->getRepository(Favori::class);

        $favoris = $repository3->findBy(['user' => $this->getUser()]);

        if ($this->getUser()) {

            $favoris = $repository3->createQueryBuilder('f')
                ->select('f')
                ->where('f.user = :id')
                ->setParameter('id', $this->getUser()->getId())
                ->getQuery()
                ->execute();
        }

        return $this->render('main/commandes.html.twig', [
            'propositions_jeux' => $propositions_jeux,
            'my_games' => $my_games,
            'favoris' => $favoris,
        ]);
    }

    #[Route('/article/{id}/', name: 'article_view')]
    #[ParamConverter('propositionJeux', options: ['mapping' => ['id' => 'id']])]
    public function articleView(PropositionJeux $article, Request $request, ManagerRegistry $doctrine): Response
    {

        $repository2 = $doctrine->getRepository(Commande::class);

        $my_games = $repository2->findBy(['user' => $this->getUser()]);

        if ($this->getUser()) {

            $my_games = $repository2->createQueryBuilder('c')
                ->select('c')
                ->where('c.user = :id')
                ->setParameter('id', $this->getUser()->getId())
                ->getQuery()
                ->execute();
        }

        $repository3 = $doctrine->getRepository(Favori::class);

        $favoris = $repository3->findBy(['user' => $this->getUser()]);

        if ($this->getUser()) {

            $favoris = $repository3->createQueryBuilder('f')
                ->select('f')
                ->where('f.user = :id')
                ->setParameter('id', $this->getUser()->getId())
                ->getQuery()
                ->execute();
        }

        if (!$this->getUser()) {
            return $this->render('main/article_view.html.twig', [
                'article' => $article,
                'my_games' => $my_games,
                'favoris' => $favoris,
            ]);
        }

        $comment = new Comment();

        $commentForm = $this->createForm(AddCommentFormType::class, $comment);

        $commentForm->handleRequest($request);

        if ($commentForm->isSubmitted() && $commentForm->isValid()) {

            $comment
                ->setPublicationDate(new DateTime())
                ->setAuthor($this->getUser())
                ->setPropositionJeux($article);

            try {

                $em = $doctrine->getManager();
                $em->persist($comment);
                $em->flush();

                $this->addFlash('success', 'Commentaire publié avec succès !');
            } catch (Exception $exception) {

                $this->addFlash('error', 'Désolé, un problème est survenu !');
            }

            unset($comment);
            unset($commentForm);

            $comment = new Comment();
            $commentForm = $this->createForm(AddCommentFormType::class, $comment);
        }

        return $this->render('main/article_view.html.twig', [
            'article' => $article,
            'commentForm' => $commentForm->createView(),
            'my_games' => $my_games,
            'favoris' => $favoris,
        ]);
    }

    #[Route('/coques/', name: 'coque')]
    public function coque(ManagerRegistry $doctrine, Request $request, PaginatorInterface $paginator): Response
    {

        $repository2 = $doctrine->getRepository(Commande::class);

        $my_games = $repository2->findBy(['user' => $this->getUser()]);

        if ($this->getUser()) {

            $my_games = $repository2->createQueryBuilder('c')
                ->select('c')
                ->where('c.user = :id')
                ->setParameter('id', $this->getUser()->getId())
                ->getQuery()
                ->execute();
        }

        $repository3 = $doctrine->getRepository(Favori::class);

        $favoris = $repository3->findBy(['user' => $this->getUser()]);

        if ($this->getUser()) {

            $favoris = $repository3->createQueryBuilder('f')
                ->select('f')
                ->where('f.user = :id')
                ->setParameter('id', $this->getUser()->getId())
                ->getQuery()
                ->execute();
        }

        $requestedPage = $request->query->getInt('page', 1);

        if ($requestedPage < 1) {
            throw new NotFoundHttpException();
        }

        $em = $doctrine->getManager();

        $query = $em->createQuery('SELECT p FROM App\Entity\PropositionJeux p');

        $articles = $paginator->paginate(
            $query,
            $requestedPage,
            5000,
        );

        return $this->render('main/coque.html.twig', [
            'articles' => $articles,
            'my_games' => $my_games,
            'favoris' => $favoris,
        ]);
    }

    #[Route('/minimalistes/', name: 'minimaliste')]
    public function minimaliste(ManagerRegistry $doctrine, Request $request, PaginatorInterface $paginator): Response
    {

        $repository2 = $doctrine->getRepository(Commande::class);

        $my_games = $repository2->findBy(['user' => $this->getUser()]);

        if ($this->getUser()) {

            $my_games = $repository2->createQueryBuilder('c')
                ->select('c')
                ->where('c.user = :id')
                ->setParameter('id', $this->getUser()->getId())
                ->getQuery()
                ->execute();
        }

        $repository3 = $doctrine->getRepository(Favori::class);

        $favoris = $repository3->findBy(['user' => $this->getUser()]);

        if ($this->getUser()) {

            $favoris = $repository3->createQueryBuilder('f')
                ->select('f')
                ->where('f.user = :id')
                ->setParameter('id', $this->getUser()->getId())
                ->getQuery()
                ->execute();
        }

        $requestedPage = $request->query->getInt('page', 1);

        if ($requestedPage < 1) {
            throw new NotFoundHttpException();
        }

        $em = $doctrine->getManager();

        $query = $em->createQuery('SELECT p FROM App\Entity\PropositionJeux p');

        $articles = $paginator->paginate(
            $query,
            $requestedPage,
            5000,
        );

        return $this->render('main/minimaliste.html.twig', [
            'articles' => $articles,
            'my_games' => $my_games,
            'favoris' => $favoris,
        ]);
    }

    #[Route('/logos/', name: 'logo')]
    public function logo(ManagerRegistry $doctrine, Request $request, PaginatorInterface $paginator): Response
    {

        $repository2 = $doctrine->getRepository(Commande::class);

        $my_games = $repository2->findBy(['user' => $this->getUser()]);

        if ($this->getUser()) {

            $my_games = $repository2->createQueryBuilder('c')
                ->select('c')
                ->where('c.user = :id')
                ->setParameter('id', $this->getUser()->getId())
                ->getQuery()
                ->execute();
        }

        $repository3 = $doctrine->getRepository(Favori::class);

        $favoris = $repository3->findBy(['user' => $this->getUser()]);

        if ($this->getUser()) {

            $favoris = $repository3->createQueryBuilder('f')
                ->select('f')
                ->where('f.user = :id')
                ->setParameter('id', $this->getUser()->getId())
                ->getQuery()
                ->execute();
        }

        $requestedPage = $request->query->getInt('page', 1);

        if ($requestedPage < 1) {
            throw new NotFoundHttpException();
        }

        $em = $doctrine->getManager();

        $query = $em->createQuery('SELECT p FROM App\Entity\PropositionJeux p');

        $articles = $paginator->paginate(
            $query,
            $requestedPage,
            5000,
        );

        return $this->render('main/logo.html.twig', [
            'articles' => $articles,
            'my_games' => $my_games,
            'favoris' => $favoris,
        ]);
    }

    #[Route('/calligraphies/', name: 'calligraphie')]
    public function calligraphie(ManagerRegistry $doctrine, Request $request, PaginatorInterface $paginator): Response
    {

        $repository2 = $doctrine->getRepository(Commande::class);

        $my_games = $repository2->findBy(['user' => $this->getUser()]);

        if ($this->getUser()) {

            $my_games = $repository2->createQueryBuilder('c')
                ->select('c')
                ->where('c.user = :id')
                ->setParameter('id', $this->getUser()->getId())
                ->getQuery()
                ->execute();
        }

        $repository3 = $doctrine->getRepository(Favori::class);

        $favoris = $repository3->findBy(['user' => $this->getUser()]);

        if ($this->getUser()) {

            $favoris = $repository3->createQueryBuilder('f')
                ->select('f')
                ->where('f.user = :id')
                ->setParameter('id', $this->getUser()->getId())
                ->getQuery()
                ->execute();
        }

        $requestedPage = $request->query->getInt('page', 1);

        if ($requestedPage < 1) {
            throw new NotFoundHttpException();
        }

        $em = $doctrine->getManager();

        $query = $em->createQuery('SELECT p FROM App\Entity\PropositionJeux p');

        $articles = $paginator->paginate(
            $query,
            $requestedPage,
            5000,
        );

        return $this->render('main/calligraphie.html.twig', [
            'articles' => $articles,
            'my_games' => $my_games,
            'favoris' => $favoris,
        ]);
    }

    #[Route('/abstraits/', name: 'abstrait')]
    public function abstrait(ManagerRegistry $doctrine, Request $request, PaginatorInterface $paginator): Response
    {

        $repository2 = $doctrine->getRepository(Commande::class);

        $my_games = $repository2->findBy(['user' => $this->getUser()]);

        if ($this->getUser()) {

            $my_games = $repository2->createQueryBuilder('c')
                ->select('c')
                ->where('c.user = :id')
                ->setParameter('id', $this->getUser()->getId())
                ->getQuery()
                ->execute();
        }

        $repository3 = $doctrine->getRepository(Favori::class);

        $favoris = $repository3->findBy(['user' => $this->getUser()]);

        if ($this->getUser()) {

            $favoris = $repository3->createQueryBuilder('f')
                ->select('f')
                ->where('f.user = :id')
                ->setParameter('id', $this->getUser()->getId())
                ->getQuery()
                ->execute();
        }

        $requestedPage = $request->query->getInt('page', 1);

        if ($requestedPage < 1) {
            throw new NotFoundHttpException();
        }

        $em = $doctrine->getManager();

        $query = $em->createQuery('SELECT p FROM App\Entity\PropositionJeux p');

        $articles = $paginator->paginate(
            $query,
            $requestedPage,
            5000,
        );

        return $this->render('main/abstrait.html.twig', [
            'articles' => $articles,
            'my_games' => $my_games,
            'favoris' => $favoris,
        ]);
    }

    #[Route('/resine/', name: 'resine')]
    public function resine(ManagerRegistry $doctrine, Request $request, PaginatorInterface $paginator): Response
    {

        $repository2 = $doctrine->getRepository(Commande::class);

        $my_games = $repository2->findBy(['user' => $this->getUser()]);

        if ($this->getUser()) {

            $my_games = $repository2->createQueryBuilder('c')
                ->select('c')
                ->where('c.user = :id')
                ->setParameter('id', $this->getUser()->getId())
                ->getQuery()
                ->execute();
        }

        $repository3 = $doctrine->getRepository(Favori::class);

        $favoris = $repository3->findBy(['user' => $this->getUser()]);

        if ($this->getUser()) {

            $favoris = $repository3->createQueryBuilder('f')
                ->select('f')
                ->where('f.user = :id')
                ->setParameter('id', $this->getUser()->getId())
                ->getQuery()
                ->execute();
        }

        $requestedPage = $request->query->getInt('page', 1);

        if ($requestedPage < 1) {
            throw new NotFoundHttpException();
        }

        $em = $doctrine->getManager();

        $query = $em->createQuery('SELECT p FROM App\Entity\PropositionJeux p');

        $articles = $paginator->paginate(
            $query,
            $requestedPage,
            5000,
        );

        return $this->render('main/resine.html.twig', [
            'articles' => $articles,
            'my_games' => $my_games,
            'favoris' => $favoris,
        ]);
    }

    #[Route('/paysages/', name: 'paysage')]
    public function paysage(ManagerRegistry $doctrine, Request $request, PaginatorInterface $paginator): Response
    {

        $repository2 = $doctrine->getRepository(Commande::class);

        $my_games = $repository2->findBy(['user' => $this->getUser()]);

        if ($this->getUser()) {

            $my_games = $repository2->createQueryBuilder('c')
                ->select('c')
                ->where('c.user = :id')
                ->setParameter('id', $this->getUser()->getId())
                ->getQuery()
                ->execute();
        }

        $repository3 = $doctrine->getRepository(Favori::class);

        $favoris = $repository3->findBy(['user' => $this->getUser()]);

        if ($this->getUser()) {

            $favoris = $repository3->createQueryBuilder('f')
                ->select('f')
                ->where('f.user = :id')
                ->setParameter('id', $this->getUser()->getId())
                ->getQuery()
                ->execute();
        }

        $requestedPage = $request->query->getInt('page', 1);

        if ($requestedPage < 1) {
            throw new NotFoundHttpException();
        }

        $em = $doctrine->getManager();

        $query = $em->createQuery('SELECT p FROM App\Entity\PropositionJeux p');

        $articles = $paginator->paginate(
            $query,
            $requestedPage,
            5000,
        );

        return $this->render('main/paysage.html.twig', [
            'articles' => $articles,
            'my_games' => $my_games,
            'favoris' => $favoris,
        ]);
    }

    #[Route('/prenoms/', name: 'prenom')]
    public function prenom(ManagerRegistry $doctrine, Request $request, PaginatorInterface $paginator): Response
    {

        $repository2 = $doctrine->getRepository(Commande::class);

        $my_games = $repository2->findBy(['user' => $this->getUser()]);

        if ($this->getUser()) {

            $my_games = $repository2->createQueryBuilder('c')
                ->select('c')
                ->where('c.user = :id')
                ->setParameter('id', $this->getUser()->getId())
                ->getQuery()
                ->execute();
        }

        $repository3 = $doctrine->getRepository(Favori::class);

        $favoris = $repository3->findBy(['user' => $this->getUser()]);

        if ($this->getUser()) {

            $favoris = $repository3->createQueryBuilder('f')
                ->select('f')
                ->where('f.user = :id')
                ->setParameter('id', $this->getUser()->getId())
                ->getQuery()
                ->execute();
        }

        $requestedPage = $request->query->getInt('page', 1);

        if ($requestedPage < 1) {
            throw new NotFoundHttpException();
        }

        $em = $doctrine->getManager();

        $query = $em->createQuery('SELECT p FROM App\Entity\PropositionJeux p');

        $articles = $paginator->paginate(
            $query,
            $requestedPage,
            5000,
        );

        return $this->render('main/prenom.html.twig', [
            'articles' => $articles,
            'my_games' => $my_games,
            'favoris' => $favoris,
        ]);
    }

    #[Route('/enfants/', name: 'enfant')]
    public function enfant(ManagerRegistry $doctrine, Request $request, PaginatorInterface $paginator): Response
    {

        $repository2 = $doctrine->getRepository(Commande::class);

        $my_games = $repository2->findBy(['user' => $this->getUser()]);

        if ($this->getUser()) {

            $my_games = $repository2->createQueryBuilder('c')
                ->select('c')
                ->where('c.user = :id')
                ->setParameter('id', $this->getUser()->getId())
                ->getQuery()
                ->execute();
        }

        $repository3 = $doctrine->getRepository(Favori::class);

        $favoris = $repository3->findBy(['user' => $this->getUser()]);

        if ($this->getUser()) {

            $favoris = $repository3->createQueryBuilder('f')
                ->select('f')
                ->where('f.user = :id')
                ->setParameter('id', $this->getUser()->getId())
                ->getQuery()
                ->execute();
        }

        $requestedPage = $request->query->getInt('page', 1);

        if ($requestedPage < 1) {
            throw new NotFoundHttpException();
        }

        $em = $doctrine->getManager();

        $query = $em->createQuery('SELECT p FROM App\Entity\PropositionJeux p');

        $articles = $paginator->paginate(
            $query,
            $requestedPage,
            5000,
        );

        return $this->render('main/enfant.html.twig', [
            'articles' => $articles,
            'my_games' => $my_games,
            'favoris' => $favoris,
        ]);
    }

    #[Route('/suppression-annonce/{id}/', name: 'delete_annonce', priority: 10)]
    #[ParamConverter('annonce', options: ['mapping' => ['id' => 'id']])]
    #[IsGranted('ROLE_ADMIN')]
    public function deleteAnnonce(Annonce $annonce, Request $request, ManagerRegistry $doctrine): Response
    {

        if (!$this->isCsrfTokenValid('delete_annonce_' . $annonce->getId(), $request->query->get('csrf_token'))) {

            $this->addFlash('error', 'Token sécurité invalide, veuillez réessayer.');
        } else {

            $em = $doctrine->getManager();
            $em->remove($annonce);
            $em->flush();

            $this->addFlash('success', 'L\'annonce a été supprimée avec succès!');
        }

        return $this->redirectToRoute('home');
    }

    #[Route('/ajouter-un-article/', name: 'add_article')]
    public function addArticle(Request $request, ManagerRegistry $doctrine): Response
    {

        $repository2 = $doctrine->getRepository(Commande::class);

        $my_games = $repository2->findBy(['user' => $this->getUser()]);

        if ($this->getUser()) {

            $my_games = $repository2->createQueryBuilder('c')
                ->select('c')
                ->where('c.user = :id')
                ->setParameter('id', $this->getUser()->getId())
                ->getQuery()
                ->execute();
        }

        $repository3 = $doctrine->getRepository(Favori::class);

        $favoris = $repository3->findBy(['user' => $this->getUser()]);

        if ($this->getUser()) {

            $favoris = $repository3->createQueryBuilder('f')
                ->select('f')
                ->where('f.user = :id')
                ->setParameter('id', $this->getUser()->getId())
                ->getQuery()
                ->execute();
        }

        if (!$this->isGranted('ROLE_ADMIN')) {

            $this->addFlash('error', 'Vous n\'êtes pas autorisé à accéder à cette page !');

            return $this->redirectToRoute('home');
        } else {

            $article = new PropositionJeux();

            $form = $this->createForm(AddArticleFormType::class, $article);

            $form->handleRequest($request);

            if ($form->isSubmitted() && $form->isValid()) {

                $em = $doctrine->getManager();

                if (!$form->get('images')->getData() == null) {

                    $images = $form->get('images')->getData();

                    $newFileNames = [];

                    foreach ($images as $image) {

                        $newFileName = md5(time() . rand() . uniqid()) . '.' . $image->guessExtension();

                        $image->move(
                            $this->getParameter('app.user.image1.directory'),
                            $newFileName
                        );

                        $newFileNames[] = $newFileName;
                        $article->setImages($newFileNames);
                    }
                }

                $em->persist($article);

                $em->flush();

                $this->addFlash('success', 'Article publié avec succès !');

                return $this->redirectToRoute('add_article');
            }

            return $this->render('main/add_article.html.twig', [
                'addarticle_form' => $form->createView(),
                'my_games' => $my_games,
                'favoris' => $favoris,
            ]);
        }
    }

    #[Route('/ajouter-une-annonce/', name: 'add_annonce')]
    public function addAnnonce(Request $request, ManagerRegistry $doctrine): Response
    {

        $repository2 = $doctrine->getRepository(Commande::class);

        $my_games = $repository2->findBy(['user' => $this->getUser()]);

        if ($this->getUser()) {

            $my_games = $repository2->createQueryBuilder('c')
                ->select('c')
                ->where('c.user = :id')
                ->setParameter('id', $this->getUser()->getId())
                ->getQuery()
                ->execute();
        }

        $repository3 = $doctrine->getRepository(Favori::class);

        $favoris = $repository3->findBy(['user' => $this->getUser()]);

        if ($this->getUser()) {

            $favoris = $repository3->createQueryBuilder('f')
                ->select('f')
                ->where('f.user = :id')
                ->setParameter('id', $this->getUser()->getId())
                ->getQuery()
                ->execute();
        }

        if (!$this->isGranted('ROLE_ADMIN')) {

            $this->addFlash('error', 'Vous n\'êtes pas autorisé à accéder à cette page!');

            return $this->redirectToRoute('home');
        } else {

            $annonce = new Annonce();

            $form = $this->createForm(AnnonceFormType::class, $annonce);

            $form->handleRequest($request);

            if ($form->isSubmitted() && $form->isValid()) {

                if (!$form->get('image')->getData() == null) {

                    $image = $form->get('image')->getData();

                    $newFileName = md5(time() . rand() . uniqid()) . '.' . $image->guessExtension();

                    $annonce
                        ->setImage($newFileName);

                    $image->move(
                        $this->getParameter('app.user.image.directory'),
                        $newFileName
                    );
                }

                $em = $doctrine->getManager();

                $em->persist($annonce);

                $em->flush();

                $this->addFlash('success', 'Annonce publiée avec succès!');

                return $this->redirectToRoute('home');
            }

            return $this->render('main/annonces.html.twig', [
                'add_annonce_form' => $form->createView(),
                'my_games' => $my_games,
                'favoris' => $favoris,
            ]);
        }
    }

    #[Route('/utilisateurs/', name: 'users')]
    public function users(ManagerRegistry $doctrine): Response
    {

        $repository2 = $doctrine->getRepository(Commande::class);

        $my_games = $repository2->findBy(['user' => $this->getUser()]);

        if ($this->getUser()) {

            $my_games = $repository2->createQueryBuilder('c')
                ->select('c')
                ->where('c.user = :id')
                ->setParameter('id', $this->getUser()->getId())
                ->getQuery()
                ->execute();
        }

        $repository3 = $doctrine->getRepository(Favori::class);

        $favoris = $repository3->findBy(['user' => $this->getUser()]);

        if ($this->getUser()) {

            $favoris = $repository3->createQueryBuilder('f')
                ->select('f')
                ->where('f.user = :id')
                ->setParameter('id', $this->getUser()->getId())
                ->getQuery()
                ->execute();
        }

        if (!$this->isGranted('ROLE_ADMIN')) {

            $this->addFlash('error', 'Vous n\'êtes pas autorisé à accéder à cette page!');

            return $this->redirectToRoute('home');
        } else {

            $repository = $doctrine->getRepository(User::class);

            $users = $repository->findAll();

            return $this->render('main/users.html.twig', [
                'users' => $users,
                'my_games' => $my_games,
                'favoris' => $favoris,
            ]);
        }
    }

    #[Route('/supprimer-utilisateur/{id}/', name: 'delete_user', priority: 10)]
    #[ParamConverter('user', options: ['mapping' => ['id' => 'id']])]
    #[IsGranted('ROLE_ADMIN')]
    public function deleteUser(User $user, Request $request, ManagerRegistry $doctrine): Response
    {

        if (!$this->isCsrfTokenValid('delete_user_' . $user->getId(), $request->query->get('csrf_token'))) {

            $this->addFlash('error', 'Jeton de sécurité non valide, veuillez réessayer.');
        } else {

            $em = $doctrine->getManager();
            $em->remove($user);
            $em->flush();

            $this->addFlash('success', 'L\'utilisateur a été supprimé avec succès!');

            return $this->redirectToRoute('users');
        }

        return $this->redirectToRoute('users');
    }

    #[Route('/profil/', name: 'profil')]
    public function profil(ManagerRegistry $doctrine): Response
    {

        $repository2 = $doctrine->getRepository(Commande::class);

        $my_games = $repository2->findBy(['user' => $this->getUser()]);

        if ($this->getUser()) {

            $my_games = $repository2->createQueryBuilder('c')
                ->select('c')
                ->where('c.user = :id')
                ->setParameter('id', $this->getUser()->getId())
                ->getQuery()
                ->execute();
        }

        $repository3 = $doctrine->getRepository(Favori::class);

        $favoris = $repository3->findBy(['user' => $this->getUser()]);

        if ($this->getUser()) {

            $favoris = $repository3->createQueryBuilder('f')
                ->select('f')
                ->where('f.user = :id')
                ->setParameter('id', $this->getUser()->getId())
                ->getQuery()
                ->execute();
        }

        if (!$this->isGranted('ROLE_USER')) {

            $this->addFlash('error', 'Vous n\'êtes pas connecté!');

            return $this->redirectToRoute('app_login');
        } else {

            $user = $this->getUser();

            return $this->render('main/profil.html.twig', [
                'user' => $user,
                'my_games' => $my_games,
                'favoris' => $favoris,
            ]);
        }
    }

    #[Route('/edit-profile/{id}/', name: 'edit_profil')]
    #[ParamConverter('user', options: ['mapping' => ['id' => 'id']])]
    public function editProfil(User $user, ManagerRegistry $doctrine, Request $request): Response
    {

        $repository2 = $doctrine->getRepository(Commande::class);

        $my_games = $repository2->findBy(['user' => $this->getUser()]);

        if ($this->getUser()) {

            $my_games = $repository2->createQueryBuilder('c')
                ->select('c')
                ->where('c.user = :id')
                ->setParameter('id', $this->getUser()->getId())
                ->getQuery()
                ->execute();
        }

        $repository3 = $doctrine->getRepository(Favori::class);

        $favoris = $repository3->findBy(['user' => $this->getUser()]);

        if ($this->getUser()) {

            $favoris = $repository3->createQueryBuilder('f')
                ->select('f')
                ->where('f.user = :id')
                ->setParameter('id', $this->getUser()->getId())
                ->getQuery()
                ->execute();
        }

        if (!$this->isGranted('ROLE_USER')) {

            $this->addFlash('error', 'Vous devez être connecté pour accéder à cette page!');

            return $this->redirectToRoute('app_login');
        } else {

            $form = $this->createForm(EditProfilFormType::class, $user);

            $form->handleRequest($request);

            if ($form->isSubmitted() && $form->isValid()) {

                $em = $doctrine->getManager();
                $em->flush();

                $this->addFlash('success', 'Le profil a été modifié avec succès!');

                return $this->redirectToRoute('profil');
            }

            return $this->render('main/edit_profil.html.twig', [
                'edit_profil_form' => $form->createView(),
                'my_games' => $my_games,
                'favoris' => $favoris,
            ]);
        }
    }

    #[Route('/modification-annonce/{id}/', name: 'edit_annonce', priority: 10)]
    #[ParamConverter('annonce', options: ['mapping' => ['id' => 'id']])]
    #[IsGranted('ROLE_ADMIN')]
    public function annonceEdit(Annonce $annonce, Request $request, ManagerRegistry $doctrine): Response
    {

        $repository2 = $doctrine->getRepository(Commande::class);

        $my_games = $repository2->findBy(['user' => $this->getUser()]);

        if ($this->getUser()) {

            $my_games = $repository2->createQueryBuilder('c')
                ->select('c')
                ->where('c.user = :id')
                ->setParameter('id', $this->getUser()->getId())
                ->getQuery()
                ->execute();
        }

        $repository3 = $doctrine->getRepository(Favori::class);

        $favoris = $repository3->findBy(['user' => $this->getUser()]);

        if ($this->getUser()) {

            $favoris = $repository3->createQueryBuilder('f')
                ->select('f')
                ->where('f.user = :id')
                ->setParameter('id', $this->getUser()->getId())
                ->getQuery()
                ->execute();
        }

        $form = $this->createForm(AnnonceFormType::class, $annonce);

        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {

            if (!$form->get('image')->getData() == null) {

                $image = $form->get('image')->getData();

                $newFileName = md5(time() . rand() . uniqid()) . '.' . $image->guessExtension();

                $annonce
                    ->setImage($newFileName);

                $image->move(
                    $this->getParameter('app.user.image.directory'),
                    $newFileName
                );
            }

            $em = $doctrine->getManager();
            $em->flush();

            $this->addFlash('success', 'Annonce modifiée avec succès !');

            return $this->redirectToRoute('home');
        }

        return $this->render('main/annonces.html.twig', [
            'add_annonce_form' => $form->createView(),
            'my_games' => $my_games,
            'favoris' => $favoris,
        ]);
    }

    #[Route('/recherche/', name: 'article_search')]
    public function search(ManagerRegistry $doctrine, Request $request, PaginatorInterface $paginator): Response
    {

        $repository2 = $doctrine->getRepository(Commande::class);

        $my_games = $repository2->findBy(['user' => $this->getUser()]);

        if ($this->getUser()) {

            $my_games = $repository2->createQueryBuilder('c')
                ->select('c')
                ->where('c.user = :id')
                ->setParameter('id', $this->getUser()->getId())
                ->getQuery()
                ->execute();
        }

        $repository3 = $doctrine->getRepository(Favori::class);

        $favoris = $repository3->findBy(['user' => $this->getUser()]);

        if ($this->getUser()) {

            $favoris = $repository3->createQueryBuilder('f')
                ->select('f')
                ->where('f.user = :id')
                ->setParameter('id', $this->getUser()->getId())
                ->getQuery()
                ->execute();
        }

        // Récupération de $_GET['page'], 1 si elle n'existe pas
        $requestedPage = $request->query->getInt('page', 1);

        // Vérification que le nombre est positif

        if ($requestedPage < 1) {
            throw new NotFoundHttpException();
        }

        // On récupère la recherche de l'utilisateur depuis l'URL ( $_GET['search'] )
        $search = $request->query->get('search', '');

        $em = $doctrine->getManager();

        //Création de la requête de recherche
        $query = $em
            ->createQuery('SELECT p FROM App\Entity\PropositionJeux p WHERE p.title LIKE :search OR p.price LIKE :search')
            ->setParameters([
                'search' => '%' . $search . '%'
            ]);

        $articles = $paginator->paginate(
            $query,     // Requête créée juste avant
            $requestedPage,     // Page qu'on souhaite voir
            5000,     // Nombre d'article à afficher par page
        );

        return $this->render('main/article_search.html.twig', [
            'articles' => $articles,
            'my_games' => $my_games,
            'favoris' => $favoris,
        ]);
    }

    #[Route('/modification-article/{id}/', name: 'edit_article', priority: 10)]
    #[ParamConverter('propositionJeux', options: ['mapping' => ['id' => 'id']])]
    #[IsGranted('ROLE_ADMIN')]
    public function publicationEdit(PropositionJeux $article, Request $request, ManagerRegistry $doctrine): Response
    {

        $repository2 = $doctrine->getRepository(Commande::class);

        $my_games = $repository2->findBy(['user' => $this->getUser()]);

        if ($this->getUser()) {

            $my_games = $repository2->createQueryBuilder('c')
                ->select('c')
                ->where('c.user = :id')
                ->setParameter('id', $this->getUser()->getId())
                ->getQuery()
                ->execute();
        }

        $repository3 = $doctrine->getRepository(Favori::class);

        $favoris = $repository3->findBy(['user' => $this->getUser()]);

        if ($this->getUser()) {

            $favoris = $repository3->createQueryBuilder('f')
                ->select('f')
                ->where('f.user = :id')
                ->setParameter('id', $this->getUser()->getId())
                ->getQuery()
                ->execute();
        }

        $form = $this->createForm(AddArticleFormType::class, $article);

        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {

            if (!$form->get('images')->getData() == null) {

                $images = $form->get('images')->getData();

                $newFileNames = [];

                foreach ($images as $image) {

                    $newFileName = md5(time() . rand() . uniqid()) . '.' . $image->guessExtension();

                    $image->move(
                        $this->getParameter('app.user.image1.directory'),
                        $newFileName
                    );

                    $newFileNames[] = $newFileName;
                    $article->setImages($newFileNames);
                }
            }

            $em = $doctrine->getManager();
            $em->flush();

            $this->addFlash('success', 'Article modifié avec succès !');

            return $this->redirectToRoute('propositions');
        }

        return $this->render('main/article_edit.html.twig', [
            'article_edit_form' => $form->createView(),
            'my_games' => $my_games,
            'favoris' => $favoris,
        ]);
    }

    #[Route('/suppression-article/{id}/', name: 'delete_article', priority: 10)]
    #[ParamConverter('propositionJeux', options: ['mapping' => ['id' => 'id']])]
    #[IsGranted('ROLE_ADMIN')]
    public function deleteArticle(PropositionJeux $article, Request $request, ManagerRegistry $doctrine): Response
    {

        if (!$this->isCsrfTokenValid('delete_article_' . $article->getId(), $request->query->get('csrf_token'))) {

            $this->addFlash('error', 'Token sécurité invalide, veuillez réessayer.');
        } else {

            $em = $doctrine->getManager();
            $em->remove($article);
            $em->flush();

            $this->addFlash('success', 'L\'article a été supprimé avec succès !');
        }

        return $this->redirectToRoute('propositions');
    }

    #[Route('/annulation-commande/{id}/', name: 'cancelCommande')]
    #[ParamConverter('commande', options: ['mapping' => ['id' => 'id']])]
    public function cancelReservation(Commande $commande, MailerInterface $mailer, ManagerRegistry $doctrine): Response
    {

        $email = (new TemplatedEmail())
            ->from('support@lhannz.fr')
            ->to('support@lhannz.fr', 'anishamouche@gmail.com')
            ->subject('Votre commande chez Lhannz !')
            ->textTemplate('emails/cancel_reserve.txt.twig')
            ->htmlTemplate('emails/cancel_reserve.html.twig')
            ->context([
                'user_firstname' => $commande->getUser()->getFirstname(),
                'user_lastname' => $commande->getUser()->getLastname(),
                'user_email' => $commande->getUser()->getEmail(),
                'commande_id' => $commande->getId(),
                'commande_price' => $commande->getArticle()->getPrice(),
                'commande_title' => $commande->getArticle()->getTitle(),
            ]);

        $mailer->send($email);

        $entityManager = $doctrine->getManager();

        $entityManager->remove($commande);

        $entityManager->flush();

        $this->addFlash('success', 'Commande annulée avec succès !');

        return $this->redirectToRoute('mes_commandes');
    }

    #[Route('/commande/{id}/', name: 'commande')]
    #[ParamConverter('propositionJeux', options: ['mapping' => ['id' => 'id']])]
    public function commande(PropositionJeux $article, ManagerRegistry $doctrine): Response
    {

        $repository2 = $doctrine->getRepository(Commande::class);

        $my_games = $repository2->findBy(['user' => $this->getUser()]);

        if ($this->getUser()) {

            $my_games = $repository2->createQueryBuilder('c')
                ->select('c')
                ->where('c.user = :id')
                ->setParameter('id', $this->getUser()->getId())
                ->getQuery()
                ->execute();
        }

        $repository3 = $doctrine->getRepository(Favori::class);

        $favoris = $repository3->findBy(['user' => $this->getUser()]);

        if ($this->getUser()) {

            $favoris = $repository3->createQueryBuilder('f')
                ->select('f')
                ->where('f.user = :id')
                ->setParameter('id', $this->getUser()->getId())
                ->getQuery()
                ->execute();
        }

        if (!$this->isGranted('ROLE_USER')) {

            $this->addFlash('error', 'Vous devez être connecté pour commander un article !');

            return $this->redirectToRoute('app_login');
        }

        return $this->render('main/commande.html.twig', [
            'article' => $article,
            'user' => $this->getUser(),
            'my_games' => $my_games,
            'favoris' => $favoris,
        ]);
    }

    #[Route('/commander/{id}/{userId}/', name: 'commandeExec')]
    #[ParamConverter('commande', options: ['mapping' => ['id' => 'id']])]
    #[ParamConverter('user', options: ['mapping' => ['userId' => 'id']])]
    public function commandeExec(User $user, PropositionJeux $article, MailerInterface $mailer, ManagerRegistry $doctrine): Response
    {

        if (!$this->isGranted('ROLE_USER')) {

            $this->addFlash('error', 'Vous devez être connecté pour commander !');

            return $this->redirectToRoute('app_login');
        } else {

            $this->addFlash('success', 'Votre article a été ajouté dans votre panier!');

            $entityManager = $doctrine->getManager();

            $commande = new Commande();

            $commande->setUser($user);

            $commande->setArticle($article);

            $commande->setStatus('Status: En attente de votre paiement.');

            $entityManager->persist($commande);

            $entityManager->flush();

            $email = (new TemplatedEmail())
                ->from('support@lhannz.fr')
                ->to('support@lhannz.fr', 'anishamouche@gmail.com')
                ->subject('Votre commande chez Lhannz !')
                ->textTemplate('emails/reserve.txt.twig')
                ->htmlTemplate('emails/reserve.html.twig')
                ->context([
                    'user_firstname' => $commande->getUser()->getFirstname(),
                    'user_lastname' => $commande->getUser()->getLastname(),
                    'user_email' => $commande->getUser()->getEmail(),
                    'commande_id' => $commande->getId(),
                    'commande_price' => $commande->getArticle()->getPrice(),
                    'commande_title' => $commande->getArticle()->getTitle(),
                ]);

            $mailer->send($email);

            return $this->redirectToRoute('mes_commandes');
        }
    }

    #[Route('/confirmer-envoi/{id}/', name: 'confirm')]
    #[ParamConverter('commande', options: ['mapping' => ['id' => 'id']])]
    public function confirm(Commande $commande, Request $request, ManagerRegistry $doctrine): Response
    {
        $estimatedBudget = $request->query->get('estimatedBudget');

        if (!$estimatedBudget) {
            $this->addFlash('error', 'Le prix estimé est requis!');
            return $this->redirectToRoute('commandes');
        }

        $em = $doctrine->getManager();

        $commande->setPrice((int)$estimatedBudget);
        $commande->setStatus('Status: Payé.');

        $em->persist($commande);
        $em->flush();

        $this->addFlash('success', 'L\'envoi a été confirmé avec succès!');

        return $this->redirectToRoute('commandes');
    }

    #[Route('/manquement/{id}/', name: 'manquement')]
    #[ParamConverter('commande', options: ['mapping' => ['id' => 'id']])]
    public function manquement(Commande $commande, Request $request, ManagerRegistry $doctrine): Response
    {

        if (!$this->isCsrfTokenValid('manquement_' . $commande->getId(), $request->query->get("csrf_token"))) {

            $this->addFlash('error', 'Token sécurité invalide, veuillez réessayer!');
        } else {


            $em = $doctrine->getManager();

            $commande->setStatus('Status: Paiement incomplet.');

            $em->persist($commande);

            $em->flush();

            $this->addFlash('success', 'L\'information a été communiquée avec succès!');

            return $this->redirectToRoute('commandes');
        }

        return $this->redirectToRoute('commandes');
    }

    #[Route('/annulation/{id}/', name: 'annulation')]
    #[ParamConverter('commande', options: ['mapping' => ['id' => 'id']])]
    public function annulation(Commande $commande, Request $request, ManagerRegistry $doctrine): Response
    {

        if (!$this->isCsrfTokenValid('annulation_' . $commande->getId(), $request->query->get("csrf_token"))) {

            $this->addFlash('error', 'Token sécurité invalide, veuillez réessayer!');
        } else {


            $em = $doctrine->getManager();

            $commande->setStatus('Status: Annulé.');

            $em->persist($commande);

            $em->flush();

            $this->addFlash('success', 'La commande a été annulée avec succès!');

            return $this->redirectToRoute('commandes');
        }

        return $this->redirectToRoute('commandes');
    }

    #[Route('/confirmation/{id}/', name: 'confirmation')]
    #[ParamConverter('commande', options: ['mapping' => ['id' => 'id']])]
    public function confirmation(Commande $commande, ManagerRegistry $doctrine): Response
    {

        $em = $doctrine->getManager();

        $commande->setStatus('Status: Reçu.');

        $em->persist($commande);

        $em->flush();

        $this->addFlash('success', 'La réception a été confirmée avec succès!');

        return $this->redirectToRoute('mes_commandes');
    }

    #[Route('/confirmation-demande/{id}/', name: 'confirmation2')]
    #[ParamConverter('demande', options: ['mapping' => ['id' => 'id']])]
    public function confirmation2(Demande $demande, ManagerRegistry $doctrine): Response
    {

        $em = $doctrine->getManager();

        $demande->setStatus('Status: Reçu.');

        $em->persist($demande);

        $em->flush();

        $this->addFlash('success', 'La réception a été confirmée avec succès!');

        return $this->redirectToRoute('mes_demandes');
    }

    #[Route('/faire-une-demande/', name: 'add_request')]
    public function addRequest(Request $request, ManagerRegistry $doctrine): Response
    {

        $repository2 = $doctrine->getRepository(Commande::class);

        $my_games = $repository2->findBy(['user' => $this->getUser()]);

        if ($this->getUser()) {

            $my_games = $repository2->createQueryBuilder('c')
                ->select('c')
                ->where('c.user = :id')
                ->setParameter('id', $this->getUser()->getId())
                ->getQuery()
                ->execute();
        }

        $repository3 = $doctrine->getRepository(Favori::class);

        $favoris = $repository3->findBy(['user' => $this->getUser()]);

        if ($this->getUser()) {

            $favoris = $repository3->createQueryBuilder('f')
                ->select('f')
                ->where('f.user = :id')
                ->setParameter('id', $this->getUser()->getId())
                ->getQuery()
                ->execute();
        }

        if (!$this->isGranted('ROLE_USER')) {

            $this->addFlash('error', 'Vous devez être connecté pour accéder à cette page !');

            return $this->redirectToRoute('app_login');
        } else {

            $demande = new Demande();

            $form = $this->createForm(DemandeFormType::class, $demande);

            $form->handleRequest($request);

            if ($form->isSubmitted() && $form->isValid()) {

                $em = $doctrine->getManager();

                if (!$form->get('images')->getData() == null) {

                    $images = $form->get('images')->getData();

                    $newFileNames = [];

                    foreach ($images as $image) {

                        $newFileName = md5(time() . rand() . uniqid()) . '.' . $image->guessExtension();

                        $image->move(
                            $this->getParameter('app.user.image3.directory'),
                            $newFileName
                        );

                        $newFileNames[] = $newFileName;
                        $demande->setImages($newFileNames);
                    }
                }

                $demande
                    ->setAuthor($this->getUser())
                    ->setStatus('Status: en cours de création.')
                ;

                $em->persist($demande);

                $em->flush();

                $this->addFlash('success', 'Votre demande a été publiée avec succès!');

                return $this->redirectToRoute('mes_demandes');
            }
        }

        return $this->render('main/demandes.html.twig', [
            'add_demande_form' => $form->createView(),
            'my_games' => $my_games,
            'favoris' => $favoris,
        ]);
    }

    #[Route('/mes-demandes/', name: 'mes_demandes')]
    public function mesDemandes(ManagerRegistry $doctrine): Response
    {

        $repository2 = $doctrine->getRepository(Commande::class);

        $my_games = $repository2->findBy(['user' => $this->getUser()]);

        if ($this->getUser()) {

            $my_games = $repository2->createQueryBuilder('c')
                ->select('c')
                ->where('c.user = :id')
                ->setParameter('id', $this->getUser()->getId())
                ->getQuery()
                ->execute();
        }

        $repository3 = $doctrine->getRepository(Favori::class);

        $favoris = $repository3->findBy(['user' => $this->getUser()]);

        if ($this->getUser()) {

            $favoris = $repository3->createQueryBuilder('f')
                ->select('f')
                ->where('f.user = :id')
                ->setParameter('id', $this->getUser()->getId())
                ->getQuery()
                ->execute();
        }

        if (!$this->isGranted('ROLE_USER')) {

            $this->addFlash('error', 'Vous devez être connecté pour accéder à cette page !');

            return $this->redirectToRoute('app_login');
        } else {

            $repository = $doctrine->getRepository(Demande::class);

            $my_games2 = $repository->createQueryBuilder('d')
                ->select('d')
                ->where('d.author = :id')
                ->setParameter('id', $this->getUser()->getId())
                ->getQuery()
                ->execute();

            return $this->render('main/mes_demandes.html.twig', [
                'my_games2' => $my_games2,
                'my_games' => $my_games,
                'favoris' => $favoris,
            ]);
        }
    }

    #[Route('/demandes/', name: 'demandes')]
    public function demandes(ManagerRegistry $doctrine): Response
    {

        $repository2 = $doctrine->getRepository(Commande::class);

        $my_games = $repository2->findBy(['user' => $this->getUser()]);

        if ($this->getUser()) {

            $my_games = $repository2->createQueryBuilder('c')
                ->select('c')
                ->where('c.user = :id')
                ->setParameter('id', $this->getUser()->getId())
                ->getQuery()
                ->execute();
        }

        $repository3 = $doctrine->getRepository(Favori::class);

        $favoris = $repository3->findBy(['user' => $this->getUser()]);

        if ($this->getUser()) {

            $favoris = $repository3->createQueryBuilder('f')
                ->select('f')
                ->where('f.user = :id')
                ->setParameter('id', $this->getUser()->getId())
                ->getQuery()
                ->execute();
        }

        $repository = $doctrine->getRepository(Demande::class);

        $propositions_jeux = $repository->findAll();

        return $this->render('main/demandesList.html.twig', [
            'propositions_jeux' => $propositions_jeux,
            'my_games' => $my_games,
            'favoris' => $favoris,
        ]);
    }

    #[Route('/annulation-demande/{id}/', name: 'cancelDemande')]
    #[ParamConverter('demande', options: ['mapping' => ['id' => 'id']])]
    public function cancelDemande(Demande $demande, ManagerRegistry $doctrine): Response
    {

        $entityManager = $doctrine->getManager();

        $entityManager->remove($demande);

        $entityManager->flush();

        $this->addFlash('success', 'Demande annulée avec succès !');

        return $this->redirectToRoute('mes_demandes');
    }

    #[Route('/terminer-demande/{id}/', name: 'done_demande')]
    #[ParamConverter('demande', options: ['mapping' => ['id' => 'id']])]
    public function doneDemande(Demande $demande, ManagerRegistry $doctrine): Response
    {

        $entityManager = $doctrine->getManager();

        $entityManager->remove($demande);

        $entityManager->flush();

        return $this->redirectToRoute('mes_demandes');
    }

    #[Route('/ajouter-favori/{id}/{userId}/', name: 'add_fav')]
    #[ParamConverter('article', options: ['mapping' => ['id' => 'id']])]
    #[ParamConverter('user', options: ['mapping' => ['userId' => 'id']])]
    public function addFav(User $user, PropositionJeux $article, ManagerRegistry $doctrine): Response
    {

        $favoris = new Favori();

        $em = $doctrine->getManager();

        $favoris
            ->setUser($user)
            ->setArticle($article);

        $em->persist($favoris);

        $em->flush();

        $this->addFlash('success', 'Favori ajouté avec succès !');

        return $this->redirectToRoute('favoris');
    }

    #[Route('/supprimer-favori/{id}/', name: 'del_fav')]
    #[ParamConverter('favoris', options: ['mapping' => ['id' => 'id']])]
    public function delFav(Favori $favoris, ManagerRegistry $doctrine): Response
    {

        $em = $doctrine->getManager();

        $em->remove($favoris);

        $em->flush();

        $this->addFlash('success', 'Favori retiré avec succès !');

        return $this->redirectToRoute('favoris');
    }

    #[Route('/confirmer-commande/{id}/', name: 'confirm_command')]
    #[ParamConverter('commande', options: ['mapping' => ['id' => 'id']])]
    public function confirmCommand(Commande $commande, Request $request, ManagerRegistry $doctrine): Response
    {

        $repository2 = $doctrine->getRepository(Commande::class);

        $my_games = $repository2->findBy(['user' => $this->getUser()]);

        if ($this->getUser()) {

            $my_games = $repository2->createQueryBuilder('c')
                ->select('c')
                ->where('c.user = :id')
                ->setParameter('id', $this->getUser()->getId())
                ->getQuery()
                ->execute();
        }

        $repository3 = $doctrine->getRepository(Favori::class);

        $favoris = $repository3->findBy(['user' => $this->getUser()]);

        if ($this->getUser()) {

            $favoris = $repository3->createQueryBuilder('f')
                ->select('f')
                ->where('f.user = :id')
                ->setParameter('id', $this->getUser()->getId())
                ->getQuery()
                ->execute();
        }

        $form = $this->createForm(ConfirmFormType::class);

        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {

            $em = $doctrine->getManager();

            $commande
                ->setStatus('Status: prêt, en attente de votre paiement.')
                ->setPrice($form->get('price')->getData());

            $em->persist($commande);

            $em->flush();

            $this->addFlash('success', 'La commande a été confirmée avec succès!');

            return $this->redirectToRoute('home');
        }

        return $this->render('main/confirm_demande.html.twig', [
            'confirm_form' => $form->createView(),
            'my_games' => $my_games,
            'favoris' => $favoris,
        ]);
    }

    #[Route('/confirmer-demande/{id}/', name: 'confirm_demande')]
    #[ParamConverter('demande', options: ['mapping' => ['id' => 'id']])]
    public function confirmDemande(Demande $demande, Request $request, ManagerRegistry $doctrine): Response
    {

        $repository2 = $doctrine->getRepository(Commande::class);

        $my_games = $repository2->findBy(['user' => $this->getUser()]);

        if ($this->getUser()) {

            $my_games = $repository2->createQueryBuilder('c')
                ->select('c')
                ->where('c.user = :id')
                ->setParameter('id', $this->getUser()->getId())
                ->getQuery()
                ->execute();
        }

        $repository3 = $doctrine->getRepository(Favori::class);

        $favoris = $repository3->findBy(['user' => $this->getUser()]);

        if ($this->getUser()) {

            $favoris = $repository3->createQueryBuilder('f')
                ->select('f')
                ->where('f.user = :id')
                ->setParameter('id', $this->getUser()->getId())
                ->getQuery()
                ->execute();
        }

        $form = $this->createForm(ConfirmFormType::class);

        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {

            $em = $doctrine->getManager();

            $demande
                ->setStatus('Status: prêt, en attente de votre paiement.')
                ->setPrice($form->get('price')->getData());

            if (!$form->get('images')->getData() == null) {

                $images = $form->get('images')->getData();

                $newFileNames = [];

                foreach ($images as $image) {

                    $newFileName = md5(time() . rand() . uniqid()) . '.' . $image->guessExtension();

                    $image->move(
                        $this->getParameter('app.user.image2.directory'),
                        $newFileName
                    );

                    $newFileNames[] = $newFileName;
                    $demande->setImages($newFileNames);
                }
            }

            $em->persist($demande);

            $em->flush();

            $this->addFlash('success', 'La demande a été confirmée avec succès!');

            return $this->redirectToRoute('demandes');
        }

        return $this->render('main/confirm_demande.html.twig', [
            'confirm_form' => $form->createView(),
            'my_games' => $my_games,
            'favoris' => $favoris,
        ]);
    }

    #[Route('/refuser/{id}/', name: 'refus')]
    #[ParamConverter('demande', options: ['mapping' => ['id' => 'id']])]
    public function refus(Demande $demande, Request $request, ManagerRegistry $doctrine): Response
    {

        $repository2 = $doctrine->getRepository(Commande::class);

        $my_games = $repository2->findBy(['user' => $this->getUser()]);

        if ($this->getUser()) {

            $my_games = $repository2->createQueryBuilder('c')
                ->select('c')
                ->where('c.user = :id')
                ->setParameter('id', $this->getUser()->getId())
                ->getQuery()
                ->execute();
        }

        $repository3 = $doctrine->getRepository(Favori::class);

        $favoris = $repository3->findBy(['user' => $this->getUser()]);

        if ($this->getUser()) {

            $favoris = $repository3->createQueryBuilder('f')
                ->select('f')
                ->where('f.user = :id')
                ->setParameter('id', $this->getUser()->getId())
                ->getQuery()
                ->execute();
        }

        $form = $this->createForm(RefusFormType::class);

        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {

            $em = $doctrine->getManager();

            $demande->setStatus('Status de la demande: refusée, recréation en cours.');

            $reason = $form->get('reason')->getData();

            $demande->setReason($reason);

            $em->persist($demande);

            $em->flush();

            $this->addFlash('success', 'Votre nouvelle demande a été envoyée avec succès!');

            return $this->redirectToRoute('mes_demandes');
        }

        return $this->render('main/refus_contact.html.twig', [
            'refus_form' => $form->createView(),
            'my_games' => $my_games,
            'favoris' => $favoris,
        ]);
    }

    #[Route('/confirmer-envoi-demande/{id}/', name: 'confirm2')]
    #[ParamConverter('demande', options: ['mapping' => ['id' => 'id']])]
    public function confirm2(Demande $demande, Request $request, ManagerRegistry $doctrine): Response
    {

        if (!$this->isCsrfTokenValid('confirm_demande_' . $demande->getId(), $request->query->get("csrf_token"))) {

            $this->addFlash('error', 'Token sécurité invalide, veuillez réessayer!');
        } else {


            $em = $doctrine->getManager();

            $demande->setStatus('Status: Payé.');

            $em->persist($demande);

            $em->flush();

            $this->addFlash('success', 'L\'envoi a été confirmé avec succès!');

            return $this->redirectToRoute('demandes');
        }

        return $this->redirectToRoute('demandes');
    }

    #[Route('/manquement-demande/{id}/', name: 'manquement2')]
    #[ParamConverter('demande', options: ['mapping' => ['id' => 'id']])]
    public function manquement2(Demande $demande, Request $request, ManagerRegistry $doctrine): Response
    {

        if (!$this->isCsrfTokenValid('manquement_demande_' . $demande->getId(), $request->query->get("csrf_token"))) {

            $this->addFlash('error', 'Token sécurité invalide, veuillez réessayer!');
        } else {


            $em = $doctrine->getManager();

            $demande->setStatus('Status: Paiement incomplet.');

            $em->persist($demande);

            $em->flush();

            $this->addFlash('success', 'L\'information a été communiquée avec succès!');

            return $this->redirectToRoute('demandes');
        }

        return $this->redirectToRoute('demandes');
    }

    #[Route('/annulation-commande-demande/{id}/', name: 'annulation2')]
    #[ParamConverter('commande', options: ['mapping' => ['id' => 'id']])]
    public function annulation2(Demande $demande, Request $request, ManagerRegistry $doctrine): Response
    {

        if (!$this->isCsrfTokenValid('annulation_demande_' . $demande->getId(), $request->query->get("csrf_token"))) {

            $this->addFlash('error', 'Token sécurité invalide, veuillez réessayer!');
        } else {

            $em = $doctrine->getManager();

            $demande->setStatus('Status: Annulé.');

            $em->persist($demande);

            $em->flush();

            $this->addFlash('success', 'La commande a été annulée avec succès!');

            return $this->redirectToRoute('demandes');
        }

        return $this->redirectToRoute('demandes');
    }
}
