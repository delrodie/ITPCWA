<?php

namespace App\Services;

use App\Entity\User;
use App\Repository\UserRepository;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;

class GestionUser
{
    const email = "delrodieamoikon@gmail.com";
    const password = "ITPCWA";
    const role = ['ROLE_SUPER_ADMIN'];

    public function __construct(
        private UserPasswordHasherInterface $passwordHasher, private UserRepository $userRepository
    )
    {
    }

    /**
     * Initialisation du premier utilisateur
     *
     * @return bool
     */
    public function initialisation(): bool
    {
        $verif = $this->userRepository->findOneBy(['email' => self::email]); //dd($verif);
        if ($verif) return false;

        $user = new User();
        $user->setEmail(self::email);
        $user->setPassword(self::password);
        $user->setRoles(self::role);
        $user->setPassword(
            $this->passwordHasher->hashPassword($user, self::password)
        );

        $this->userRepository->save($user, true);

        return true;
    }
}