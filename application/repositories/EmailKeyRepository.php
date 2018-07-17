<?php

namespace BronyCenter\Repository;

use BronyCenter\Model\EmailKey;
use BronyCenter\Model\User;
use DateTime;
use Doctrine\ORM\EntityManager;

class EmailKeyRepository
{
    private $entityManager;

    public function __construct(EntityManager $entityManager)
    {
        $this->entityManager = $entityManager;
    }

    public function createKey(array $array): EmailKey
    {
        $currentDatetime = new DateTime();
        $array['user'] = $this->entityManager->getRepository('BronyCenter\Model\User')->find($array['user_id']);

        $key = new EmailKey();
        $key->setUser($array['user']);
        $key->setHash($array['hash']);
        $key->setEmail($array['email']);
        $key->setDatetime($currentDatetime);

        $this->entityManager->persist($key);
        $this->entityManager->flush();

        return $key;
    }

    public function checkIfKeyExists(string $hash) : bool
    {
        $keyFound = $this->entityManager->getRepository('BronyCenter\Model\EmailKey')->count(['hash' => $hash]);

        return boolval($keyFound);
    }
}
