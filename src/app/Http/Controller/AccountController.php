<?php

declare(strict_types=1);

namespace App\Http\Controller;

use App\Domain\Service\UserService;
use App\Http\Controller\AbstractController;
use App\Http\Input\Account\CreateAvatarInput;
use App\Http\Input\Account\EditAccountInput;
use App\Http\SupportService\FileUploader\FileUploader;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\RequestStack;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class AccountController extends AbstractController
{
    public function __construct(
        private UserService $userService,
        private FileUploader $fileUploader,
        RequestStack $requestStack
    ) {
        parent::__construct($requestStack);
    }

    #[Route('/account/edit', methods: ['GET', 'HEAD'], name: 'pages.account.edit')]
    public function showEdit(): Response
    {
        return $this->render('pages/account/edit.twig');
    }

    #[Route('/account/edit', methods: ['POST'], name: 'actions.account.edit')]
    public function edit(EditAccountInput $input): RedirectResponse
    {
        if (!$this->validateInput($input)) {
            return $this->redirectBack();
        }

        $firstName = $input->getFirstNameParam();
        $lastName = $input->getLastNameParam();
        $bio = $input->getBioParamOrNull();

        $user = $this->getUser();

        $user->setFirstName($firstName);
        $user->setLastName($lastName);
        $user->setBio($bio);

        $this->userService->save($user);

        return $this->redirectBack();
    }

    #[Route('/account/add-avatar', methods: ['POST'], name: 'actions.account.create-avatar')]
    public function createAvatar(CreateAvatarInput $input): RedirectResponse
    {
        if (!$this->validateInput($input)) {
            return $this->redirectBack('/account/edit');
        }

        $avatar = $input->getAvatarParam();

        $imagesFolder = $this->getStringParameter('public.images.posts');
        $user = $this->getUser();

        $avatarUri = $this->fileUploader->uploadAndReturnFilename($avatar, $imagesFolder)->getFullUri();
        $this->userService->createAndSetAvatar($user, $avatarUri);

        return $this->redirectBack('/account/edit');
    }
}
