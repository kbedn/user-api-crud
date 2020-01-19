<?php namespace App\Controller;

use App\Entity\User;
use App\Form\RegistrationType;
use App\Form\UserType;
use App\Security\LoginAuthenticator;
use Doctrine\ORM\OptimisticLockException;
use Doctrine\ORM\ORMException;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Security\Guard\GuardAuthenticatorHandler;
use Symfony\Component\Routing\Annotation\Route;
use App\Security\UserManager;

class RegistrationController extends AbstractController
{
    /** @var UserManager */
    protected $userManager;

    /**
     * @param UserManager $userManager
     */
    public function __construct(UserManager $userManager)
    {
        $this->userManager = $userManager;
    }

    /**
     * @param LoginAuthenticator $authenticator
     * @param GuardAuthenticatorHandler $guardHandler
     * @param Request $request
     * @return RedirectResponse|Response|null
     */
    public function register(
        LoginAuthenticator $authenticator,
        GuardAuthenticatorHandler $guardHandler,
        Request $request
    ) {
        $user = new User();
        $form = $this->createForm(UserType::class, $user);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $em = $this->getDoctrine()->getManager();
            $em->persist($user);
            $em->flush();

            return $this->redirectToRoute('homepage');
        }

        return $guardHandler->authenticateUserAndHandleSuccess(
            $user,
            $request,
            $authenticator,
            'main'
        );
    }

    /**
     * @Route("/register", name="register")
     * @param Request $request
     * @return Response
     * @throws ORMException
     * @throws OptimisticLockException
     */
    public function renderFormAction(Request $request): Response
    {
        $user = new User();
        $form = $this->createForm(RegistrationType::class, $user);

        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            $userData = $form->getData();
            $this->userManager->createUserFromArray($userData->jsonSerialize());

            return $this->redirectToRoute('login');
        }

        return $this->render('Registration/register.html.twig', [
            'form' => $form->createView(),
        ]);
    }
}
