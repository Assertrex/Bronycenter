<?php

namespace BronyCenter\Model;

use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity @ORM\Table(name="email_keys")
 **/

class EmailKey
{
    /** @ORM\Id @ORM\Column(type="integer", nullable=false, options={"unsigned":true}) @ORM\GeneratedValue **/
    private $id;

    /**
     * @ORM\ManyToOne(targetEntity="User")
     * @ORM\JoinColumn(name="user_id", referencedColumnName="id")
     */
    private $user;

    /** @ORM\Column(type="string", nullable=false, length=16, options={"fixed":true}) **/
    private $hash;

    /** @ORM\Column(type="string", nullable=false, length=64) **/
    private $email;

    /** @ORM\Column(type="datetime", nullable=false) **/
    private $datetime;

    /** @ORM\Column(type="string", nullable=true, length=45) **/
    private $used_ip;

    /** @ORM\Column(type="datetime", nullable=true) **/
    private $used_datetime;

    public function getId()
    {
        return $this->id;
    }

    public function getUser()
    {
        return $this->user;
    }

    public function setUser($user): void
    {
        $this->user = $user;
    }

    public function getHash()
    {
        return $this->hash;
    }

    public function setHash($hash): void
    {
        $this->hash = $hash;
    }

    public function getEmail()
    {
        return $this->email;
    }

    public function setEmail($email): void
    {
        $this->email = $email;
    }

    public function getDatetime()
    {
        return $this->datetime;
    }

    public function setDatetime($datetime): void
    {
        $this->datetime = $datetime;
    }

    public function getUsedIp()
    {
        return $this->used_ip;
    }

    public function setUsedIp($used_ip): void
    {
        $this->used_ip = $used_ip;
    }

    public function getUsedDatetime()
    {
        return $this->used_datetime;
    }

    public function setUsedDatetime($used_datetime): void
    {
        $this->used_datetime = $used_datetime;
    }
}
