<?php

namespace BronyCenter\Repository;

use BronyCenter\Model\EmailKey;
use BronyCenter\Model\User;
use DateTime;
use Doctrine\ORM\EntityManager;

class EmailKeyRepository
{
    private $em;

    public function __construct(EntityManager $em)
    {
        $this->em = $em;
    }

    public function createKey(array $array): EmailKey
    {
        $currentDatetime = new DateTime();
        $array['user'] = $this->em->getRepository('BronyCenter\Model\User')->find($array['user_id']);

        $key = new EmailKey();
        $key->setUser($array['user']);
        $key->setHash($array['hash']);
        $key->setEmail($array['email']);
        $key->setDatetime($currentDatetime);

        $this->em->persist($key);
        $this->em->flush();

        return $key;
    }
}
