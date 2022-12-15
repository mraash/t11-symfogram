<?php

declare(strict_types=1);

namespace App\Http\Controller\User;

use App\Domain\Repository\UserRepository;
use App\Http\Controller\AbstractController;
use App\Http\Input\User\Account\EditAccountInput;
use App\Http\SupportService\FileUploader\FileUploader;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\RequestStack;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class AccountController extends AbstractController
{
    public function __construct(
        private UserRepository $userRepository,
        private FileUploader $fileUploader, /** @phpstan-ignore-line */
        RequestStack $requestStack
    ) {
        parent::__construct($requestStack);
    }

    #[Route('account/edit', methods: ['HEAD', 'GET'], name: 'pages.account.edit')]
    public function showEdit(): Response
    {
        return $this->render('pages/account/edit.twig');
    }

    #[Route('account/edit', methods: ['POST'], name: 'actions.account.edit')]
    public function edit(EditAccountInput $input): RedirectResponse
    {
        if (!$this->validateInput($input)) {
            return $this->redirectBack();
        }

        $firstName = $input->getFirstNameParam();
        $lastName = $input->getLastNameParam();
        $bio = $input->getBioParam() ?: null;

        $user = $this->getUser();

        $user
            ->setFirstName($firstName)
            ->setLastName($lastName)
            ->setBio($bio)
        ;

        $this->userRepository->save($user);
        $this->userRepository->flush();

        return $this->redirectBack();
    }

    #[Route('account/add-avatar', methods: ['POST'], name: 'actions.account.create-avatar')]
    public function createAvatar(): RedirectResponse
    {
        $this->addErrorFlash('Method is empty');

        return $this->redirectBack();
    }
}
