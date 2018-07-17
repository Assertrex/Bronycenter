<?php

namespace BronyCenter\Model;

use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity @ORM\Table(name="log_logins")
 **/

class LogLogin
{
    /** @ORM\Id @ORM\Column(type="integer", nullable=false, options={"unsigned":true}) @ORM\GeneratedValue **/
    private $id;

    /**
     * @ORM\ManyToOne(targetEntity="User")
     * @ORM\JoinColumn(name="user_id", referencedColumnName="id")
     */
    private $user;

    /** @ORM\Column(type="string", nullable=false, length=45) **/
    private $ip;

    /** @ORM\Column(type="datetime", nullable=false) **/
    private $datetime;

    /** @ORM\Column(type="string", nullable=true, length=255) **/
    private $agent;

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

    public function getIp()
    {
        return $this->ip;
    }

    public function setIp($ip): void
    {
        $this->ip = $ip;
    }

    public function getDatetime()
    {
        return $this->datetime;
    }

    public function setDatetime($datetime): void
    {
        $this->datetime = $datetime;
    }

    public function getAgent()
    {
        return $this->agent;
    }

    public function setAgent($agent): void
    {
        $this->agent = $agent;
    }
}
