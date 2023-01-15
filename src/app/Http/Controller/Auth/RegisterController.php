<?php

declare(strict_types=1);

namespace App\Http\Controller\Auth;

use App\Domain\Entity\User;
use App\Domain\Service\UserService;
use App\Http\Authenticator\LoginFormAuthenticator;
use App\Http\Controller\AbstractController;
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
        private UserService $userService,
        private EmailVerifier $emailVerifier,
        private FileUploader $fileUploader,
        RequestStack $requestStack,
    ) {
        parent::__construct($requestStack);
    }

    #[Route('/register', methods: ['GET', 'HEAD'], name: 'pages.register')]
    public function showRegister(): Response
    {
        if ($this->getUserOrNull()?->hasBasedRole()) {
            return $this->redirectToRoute('pages.index');
        }

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
        $passwordRepeat = $input->getPasswordRepeatParam();

        if ($password !== $passwordRepeat) {
            $this->addErrorFlash('Wrong password repetition.');
            return $this->redirectBack();
        }

        $user = $this->userService->createAccount($email, $password);

        $this->emailVerifier->createTokenAndSendEmail($user);

        $this->addSuccessFlash('Please check your email.');

        return $this->redirectBack();
    }

    #[Route('/register/verify-email', methods: ['GET', 'HEAD'], name: 'pactions.register.verify-email')]
    public function verifyEmail(
        Request $request,
        UserAuthenticatorInterface $userAuthenticator,
        LoginFormAuthenticator $authenticator
    ): RedirectResponse {
        /** @var User */
        $user = null;

        try {
            $user = $this->emailVerifier->verifyTokenAndReturnUser($request);
        }
        catch (TokenNotProvidedException | TokenNotFoundException) {
            $this->addErrorFlash('Email verification request was invalid.');
            return $this->redirectToRoute('pages.register');
        }

        $userAuthenticator->authenticateUser($user, $authenticator, $request);

        return $this->redirectToRoute('pages.register.create-profile');
    }

    #[Route('/register/create-profile', methods: ['GET', 'HEAD'], name: 'pages.register.create-profile')]
    public function showProfileCreationsForm(): Response
    {
        if ($this->getUserOrNull()?->hasBasedRole()) {
            return $this->redirectToRoute('pages.index');
        }

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
        $bio = $input->getBioParamOrNull();
        $avatar = $input->getAvatarParamOrNull();

        $avatarUri = null;

        $user = $this->getUser();

        if ($avatar !== null) {
            $avatarFolder = $this->getStringParameter('public.images.posts');

            $avatarUri = $this->fileUploader->uploadAndReturnFilename($avatar, $avatarFolder)->getFullUri();
        }

        $this->userService->activateProfile($user, $firstName, $lastName, $bio, $avatarUri);

        // Create new token because user has new role
        $authToken = new UsernamePasswordToken($user, 'main', $user->getRoles());
        $tokenStorage->setToken($authToken);

        return $this->redirectToRoute('pages.index');
    }
}
