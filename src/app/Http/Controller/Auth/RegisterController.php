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
        if ($this->getUser()?->hasBasedRole()) {
            return $this->redirectToRoute('pages.index');
        }

        return $this->render('pages/auth/register.twig');
    }

    #[Route('/register', methods: ['POST'], name: 'actions.register')]
    public function register(RegisterInput $input): RedirectResponse
    {
        if (!$this->validateInput($input)) {
            return $this->redirectBack('/register');
        }

        $email = $input->getEmailParam();
        $password = $input->getPasswordParam();
        $passwordRepeat = $input->getPasswordRepeatParam();

        if ($password !== $passwordRepeat) {
            $this->addErrorFlash('Wrong password repetition.');
            return $this->redirectBack('/register');
        }

        $user = $this->userService->create($email, $password);

        $emailMessage = (new TemplatedEmail())
            ->to($user->getEmail())
            ->subject('Please verify your email.')
            ->htmlTemplate('emails/verify-email.twig')
        ;

        $this->emailVerifier->createTokenAndSendEmail($user, $emailMessage);

        $this->addSuccessFlash('Please check your email.');

        return $this->redirectBack('/register');
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

        if (!$user->hasVerifiedRole()) {
            $this->addErrorFlash('Something went wrong. Your email is not verified.');
            return $this->redirectToRoute('pages.register');
        }

        $userAuthenticator->authenticateUser($user, $authenticator, $request);

        if ($user->hasBasedRole()) {
            $this->addInfoFlash('You alerady have verified account.');
            return $this->redirectToRoute('pages.index');
        }

        return $this->redirectToRoute('pages.register.create-profile');
    }

    #[Route('/register/create-profile', methods: ['GET', 'HEAD'], name: 'pages.register.create-profile')]
    public function showProfileCreationsForm(): Response
    {
        if ($this->getUser()?->hasBasedRole()) {
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

        $user = $this->getUser();

        $user
            ->setFirstName($firstName)
            ->setLastName($lastName)
            ->setBio($bio)
        ;

        if ($avatar !== null) {
            $avatarFolder = $this->getStringParameter('public.images.posts');

            $avatarUriFilename = $this->fileUploader->createFilename($avatar, $avatarFolder);
            $avatarUri = $avatarUriFilename->getFullUri();

            $this->fileUploader->upload($avatar, $avatarUriFilename);
            $this->userService->createAndSetAvatar($user, $avatarUri);
        }

        $user->addBasedRole();

        $this->userService->save($user);

        // Create new token because user has new role
        $newToken = new UsernamePasswordToken($user, 'main', $user->getRoles());
        $tokenStorage->setToken($newToken);

        return $this->redirectToRoute('pages.index');
    }
}
