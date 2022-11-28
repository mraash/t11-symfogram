<?php

declare(strict_types=1);

namespace App\Http\Controller\Auth;

use App\Domain\Entity\User;
use App\Domain\Repository\UserRepository;
use App\Domain\Service\UserService;
use SymfonyExtension\Http\Controller\AbstractController;
use App\Http\Authenticator\LoginFormAuthenticator;
use App\Http\Input\Auth\Register\CreateProfileInput;
use App\Http\Input\Auth\Register\RegisterInput;
use App\Http\SupportService\EmailVerifier\EmailVerifier;
use App\Http\SupportService\EmailVerifier\Exceptions\TokenNotFoundException;
use App\Http\SupportService\EmailVerifier\Exceptions\TokenNotProvidedException;
use App\Http\SupportService\FileUploader\FileUploader;
use Symfony\Bridge\Twig\Mime\TemplatedEmail;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\RequestStack;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Core\Authentication\Token\Storage\TokenStorageInterface;
use Symfony\Component\Security\Core\Authentication\Token\UsernamePasswordToken;
use Symfony\Component\Security\Http\Authentication\UserAuthenticatorInterface;

class RegisterController extends AbstractController
{
    public function __construct(
        private UserRepository $userRepository,
        private UserService $userService,
        private EmailVerifier $emailVerifier,
        private FileUploader $fileUploader,
        RequestStack $requestStack,
    ) {
        parent::__construct($requestStack);
    }

    #[Route('/register', methods: ['HEAD', 'GET'], name: 'pages.register')]
    public function showRegister(): Response
    {
        return $this->render('pages/auth/register.twig');
    }

    #[Route('/register', methods: ['POST'], name: 'actions.register')]
    public function register(RegisterInput $input): RedirectResponse
    {
        if (!$this->validateInput($input)) {
            return $this->redirectBack();
        }

        $email = $input->getEmailParam();
        $password = $input->getPasswordParam();

        $user = $this->userRepository->create($email, $password);
        $this->userRepository->flush();

        $message = (new TemplatedEmail())
            ->to($user->getEmail())
            ->subject('Please verify your email.')
            ->htmlTemplate('emails/verify-email.twig')
        ;

        $this->emailVerifier->createTokenAndSendEmail($user, $message);

        $this->addSuccessFlash('Please check your email.');

        return $this->redirectBack();
    }

    #[Route('/register/verify-email', methods: ['HEAD', 'GET'], name: 'actions.register.verify-email')]
    public function verifyEmail(
        Request $request,
        UserAuthenticatorInterface $userAuthenticator,
        LoginFormAuthenticator $authenticator
    ): RedirectResponse {
        /** @var User */
        $user = null;

        try {
            $user = $this->emailVerifier->verifyEmailByRequest($request);
        }
        catch (TokenNotProvidedException | TokenNotFoundException) {
            $this->addErrorFlash('Email verification request was invalid.');
            return $this->redirectToRoute('pages.register');
        }

        if (!$user->hasVerifiedRole()) {
            $this->addErrorFlash('Something went wrong. Your email is not verified.');
            return $this->redirectToRoute('pages.register');
        }

        $userAuthenticator->authenticateUser($user, $authenticator, $request);

        if ($user->hasBasedRole()) {
            $this->addInfoFlash('You alerady have verified account.');
            return $this->redirectToRoute('pages.home');
        }

        return $this->redirectToRoute('pages.register.create-profile');
    }

    #[Route('/register/create-profile', methods: ['HEAD', 'GET'], name: 'pages.register.create-profile')]
    public function showProfileCreationsForm(): Response
    {
        return $this->render('pages/auth/create-profile.twig');
    }

    #[Route('/register/create-profile', methods: ['POST'], name: 'actions.register.create-profile')]
    public function createProfile(CreateProfileInput $input, TokenStorageInterface $tokenStorage): RedirectResponse
    {
        if (!$this->validateInput($input)) {
            return $this->redirectBack();
        }

        $firstName = $input->getFirstNameParam();
        $lastName = $input->getLastNameParam();
        $bio = $input->getBioParam();
        $avatar = $input->getAvatarParam();

        /** @var User */
        $user = $this->getUser();

        $user
            ->setFirstName($firstName)
            ->setLastName($lastName)
            ->setBio($bio)
        ;

        if ($avatar !== null) {
            $avatarFolder = $this->getParameter('public.images.posts');
            $avatarPublicFilename = $this->fileUploader->createFilename($avatar, $avatarFolder);
            $avatarUri = $avatarPublicFilename->getFullUri();

            $this->fileUploader->upload($avatar, $avatarPublicFilename);
            $this->userService->createAndSetAvatar($user, $avatarUri);
        }

        $user->addBasedRole();

        $this->userRepository->save($user);
        $this->userRepository->flush();

        // Create new token because user has new role
        $newToken = new UsernamePasswordToken($user, 'main', $user->getRoles());
        $tokenStorage->setToken($newToken);

        return $this->redirectToRoute('pages.home');
    }
}
