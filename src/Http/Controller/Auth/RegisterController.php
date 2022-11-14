<?php

declare(strict_types=1);

namespace App\Http\Controller\Auth;

use App\Domain\Repository\EmailVerificationTokenRepository;
use App\Domain\Repository\UserRepository;
use App\Extension\Http\Controller\AbstractController;
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

        $this->addInfoFlash('Please check your email.');

        return $this->redirectBack();
    }

    #[Route('/register/verify-email', methods: ['HEAD', 'GET'], name: 'actions.register.verify-email')]
    public function verifyEmail(Request $request): RedirectResponse
    {
        try {
            $this->emailVerifier->verifyEmailByRequest($request);
        }
        catch (TokenNotProvidedException) {
            dd('invalid request');
        }
        catch (TokenNotFoundException) {
            dd('token not found');
        }
        catch (EmailIsAlreadyVerifiedException) {
            dd('email is already verified');
        }
        catch (Exception) {
            dd('something went wrong');
        }

        return $this->redirectToRoute('pages.register.create-profile');
    }

    #[Route('/register/create-profile', methods: ['HEAD', 'GET'], name: 'pages.register.create-profile')]
    public function showProfileCreationsForm(): Response
    {
        return new Response('create profile');
    }

    #[Route('/register/create-profile', methods: ['POST'], name: 'actions.register.create-profile')]
    public function createProfile(): RedirectResponse
    {
        return $this->redirectToRoute('pages.home');
    }
}
