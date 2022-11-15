<?php

declare(strict_types=1);

namespace App\Http\Controller\Auth;

use App\Domain\Entity\User;
use App\Domain\Repository\EmailVerificationTokenRepository;
use App\Domain\Repository\UserRepository;
use App\Extension\Http\Controller\AbstractController;
use App\Http\Authenticator\LoginFormAuthenticator;
use App\Http\Input\Auth\Register\CreateProfileInput;
use App\Http\Input\Auth\Register\RegisterInput;
use App\Http\SupportService\EmailVerifier\EmailVerifier;
use App\Http\SupportService\EmailVerifier\Exceptions\EmailIsAlreadyVerifiedException;
use App\Http\SupportService\EmailVerifier\Exceptions\TokenNotFoundException;
use App\Http\SupportService\EmailVerifier\Exceptions\TokenNotProvidedException;
use Exception;
use Symfony\Bridge\Twig\Mime\TemplatedEmail;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\RequestStack;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Http\Authentication\UserAuthenticatorInterface;

class RegisterController extends AbstractController
{
    private EmailVerifier $emailVerifier;
    private UserRepository $userRepository;
    private EmailVerificationTokenRepository $emailVerificationTokenRepository;

    public function __construct(
        RequestStack $requestStack,
        EmailVerifier $emailVerifier,
        UserRepository $userRepository,
        EmailVerificationTokenRepository $emailVerificationTokenRepository
    ) {
        parent::__construct($requestStack);
        $this->emailVerifier = $emailVerifier;
        $this->userRepository = $userRepository;
        $this->emailVerificationTokenRepository = $emailVerificationTokenRepository;
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

        $email = $input->getEmailInput();
        $password = $input->getPasswordInput();

        $user = $this->userRepository->create($email, $password);
        $this->userRepository->flush();

        $message = (new TemplatedEmail())
            ->from('manager@t11.symfogram.my')
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
        try {
            $user = $this->emailVerifier->verifyEmailByRequest($request);
        }
        catch (TokenNotProvidedException) {
            $this->addErrorFlash('Request is invalid.');
            $this->redirectToRoute('pages.register');
        }
        catch (TokenNotFoundException) {
            $this->addErrorFlash('Request is invalid.');
            $this->redirectToRoute('pages.register');
        }
        catch (EmailIsAlreadyVerifiedException) {
            $this->addInfoFlash('The email has already been confirmed.');
            $this->redirectToRoute('pages.register');
        }

        if (!$user->isVerified()) {
            $this->addErrorFlash('Something went wrong. Your email is not verified.');
            $this->redirectToRoute('pages.register');
        }

        $userAuthenticator->authenticateUser($user, $authenticator, $request);

        return $this->redirectToRoute('pages.register.create-profile');
    }

    #[Route('/register/create-profile', methods: ['HEAD', 'GET'], name: 'pages.register.create-profile')]
    public function showProfileCreationsForm(): Response
    {
        return $this->render('pages/auth/create-profile.twig');
    }

    #[Route('/register/create-profile', methods: ['POST'], name: 'actions.register.create-profile')]
    public function createProfile(CreateProfileInput $input): RedirectResponse
    {
        if (!$this->validateInput($input)) {
            return $this->redirectBack();
        }

        $firstName = $input->getFirstNameInput();
        $lastName = $input->getLastNameInput();
        $bio = $input->getBioInput();

        /** @var User */
        $user = $this->getUser();

        $user->setFirstName($firstName);
        $user->setLastName($lastName);
        $user->setBio($bio);
        $user->setIsBased(true);

        $this->userRepository->save($user);
        $this->userRepository->flush();

        return $this->redirectToRoute('pages.home');
    }
}
