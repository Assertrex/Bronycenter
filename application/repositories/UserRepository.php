<?php

namespace BronyCenter\Repository;

use BronyCenter\Model\User;
use DateTime;
use Doctrine\ORM\EntityManager;

class UserRepository
{
    private $em;

    public function __construct(EntityManager $em)
    {
        $this->em = $em;
    }

    public function createUser(array $array): User
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

        $this->em->persist($user);
        $this->em->flush();

        return $user;
    }
}
