<?php

namespace BronyCenter\Repository;

use BronyCenter\Model\User;
use DateTime;
use Doctrine\ORM\EntityManager;

class UserRepository
{
    private $entityManager;

    public function __construct(EntityManager $entityManager)
    {
        $this->entityManager = $entityManager;
    }

    public function createUser(array $array) : User
    {
        $array['password'] = password_hash($array['password'], PASSWORD_ARGON2I, [
            'memory_cost' => 8192,
            'time_cost' => 36,
            'threads' => 2
        ]);
        $array['registration_datetime'] = new DateTime();

        $user = new User();
        $user->setDisplayName($array['display_name']);
        $user->setUsername($array['username']);
        $user->setPassword($array['password']);
        $user->setRegistrationIp($array['registration_ip']);
        $user->setRegistrationDatetime($array['registration_datetime']);

        $user->setDisplayNameChanges(0);
        $user->setAccountType(0);
        $user->setAccountStanding(0);
        $user->setActivityPoints(0);
        $user->setDevelopmentPoints(0);
        $user->setGender(0);

        $this->entityManager->persist($user);
        $this->entityManager->flush();

        return $user;
    }

    public function findByUsername(string $username) : ?User
    {
        if (!filter_var($username, FILTER_VALIDATE_EMAIL)) {
            $user = $this->entityManager->getRepository('BronyCenter\Model\User')->findBy(['username' => $username]);
        } else {
            $user = $this->entityManager->getRepository('BronyCenter\Model\User')->findBy(['email' => $username]);
        }

        if (count($user) > 0) {
            return $user[0];
        } else {
            return null;
        }
    }
}
