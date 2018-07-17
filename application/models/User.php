<?php

namespace BronyCenter\Model;

use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity @ORM\Table(name="users")
 **/

class User
{
    /** @ORM\Id @ORM\Column(type="integer", nullable=false, options={"unsigned":true}) @ORM\GeneratedValue **/
    private $id;

    /** @ORM\Column(type="string", nullable=false, length=32, unique=true) **/
    private $display_name;

    /** @ORM\Column(type="datetime", nullable=true) **/
    private $display_name_datetime;

    /** @ORM\Column(type="smallint", nullable=false, options={"unsigned":true, "default":0}) **/
    private $display_name_changes;

    /** @ORM\Column(type="string", nullable=true, length=255) **/
    private $display_name_recent;

    /** @ORM\Column(type="string", nullable=false, length=24, unique=true) **/
    private $username;

    /** @ORM\Column(type="string", nullable=true, length=64, unique=true) **/
    private $email;

    /** @ORM\Column(type="string", nullable=false, length=255) **/
    private $password;

    /** @ORM\Column(type="string", nullable=false, length=45) **/
    private $registration_ip;

    /** @ORM\Column(type="datetime", nullable=false) **/
    private $registration_datetime;

    /** @ORM\Column(type="string", nullable=true, length=45) **/
    private $login_ip;

    /** @ORM\Column(type="datetime", nullable=true) **/
    private $login_datetime;

    /** @ORM\Column(type="datetime", nullable=true) **/
    private $seen_datetime;

    /** @ORM\Column(type="datetime", nullable=true) **/
    private $activity_datetime;

    /** @ORM\Column(type="string", nullable=true, length=2, options={"fixed":true}) **/
    private $country_code;

    /** @ORM\Column(type="string", nullable=true, length=64) **/
    private $timezone;

    /** @ORM\Column(type="string", nullable=true, length=32) **/
    private $city;

    /** @ORM\Column(type="string", nullable=true, length=16, unique=true, options={"fixed":true}) **/
    private $avatar;

    /** @ORM\Column(type="smallint", nullable=false, options={"unsigned":true, "default":0}) **/
    private $account_type;

    /** @ORM\Column(type="smallint", nullable=false, options={"unsigned":true, "default":0}) **/
    private $account_standing;

    /** @ORM\Column(type="integer", nullable=false, options={"unsigned":true, "default":0}) **/
    private $activity_points;

    /** @ORM\Column(type="integer", nullable=false, options={"unsigned":true, "default":0}) **/
    private $development_points;

    /** @ORM\Column(type="date", nullable=true) **/
    private $birthdate;

    /** @ORM\Column(type="smallint", nullable=false, options={"unsigned":true, "default":0}) **/
    private $gender;

    /** @ORM\Column(type="string", nullable=true, length=255) **/
    private $short_description;

    public function getId()
    {
        return $this->id;
    }

    public function setId($id): void
    {
        $this->id = $id;
    }

    public function getDisplayName()
    {
        return $this->display_name;
    }

    public function setDisplayName($display_name): void
    {
        $this->display_name = $display_name;
    }

    public function getDisplayNameDatetime()
    {
        return $this->display_name_datetime;
    }

    public function setDisplayNameDatetime($display_name_datetime): void
    {
        $this->display_name_datetime = $display_name_datetime;
    }

    public function getDisplayNameChanges()
    {
        return $this->display_name_changes;
    }

    public function setDisplayNameChanges($display_name_changes): void
    {
        $this->display_name_changes = $display_name_changes;
    }

    public function getDisplayNameRecent()
    {
        return $this->display_name_recent;
    }

    public function setDisplayNameRecent($display_name_recent): void
    {
        $this->display_name_recent = $display_name_recent;
    }

    public function getUsername()
    {
        return $this->username;
    }

    public function setUsername($username): void
    {
        $this->username = $username;
    }

    public function getEmail()
    {
        return $this->email;
    }

    public function setEmail($email): void
    {
        $this->email = $email;
    }

    public function getPassword()
    {
        return $this->password;
    }

    public function setPassword($password): void
    {
        $this->password = $password;
    }

    public function getRegistrationIp()
    {
        return $this->registration_ip;
    }

    public function setRegistrationIp($registration_ip): void
    {
        $this->registration_ip = $registration_ip;
    }

    public function getRegistrationDatetime()
    {
        return $this->registration_datetime;
    }

    public function setRegistrationDatetime($registration_datetime): void
    {
        $this->registration_datetime = $registration_datetime;
    }

    public function getLoginIp()
    {
        return $this->login_ip;
    }

    public function setLoginIp($login_ip): void
    {
        $this->login_ip = $login_ip;
    }

    public function getLoginDatetime()
    {
        return $this->login_datetime;
    }

    public function setLoginDatetime($login_datetime): void
    {
        $this->login_datetime = $login_datetime;
    }

    public function getSeenDatetime()
    {
        return $this->seen_datetime;
    }

    public function setSeenDatetime($seen_datetime): void
    {
        $this->seen_datetime = $seen_datetime;
    }

    public function getActivityDatetime()
    {
        return $this->activity_datetime;
    }

    public function setActivityDatetime($activity_datetime): void
    {
        $this->activity_datetime = $activity_datetime;
    }

    public function getCountryCode()
    {
        return $this->country_code;
    }

    public function setCountryCode($country_code): void
    {
        $this->country_code = $country_code;
    }

    public function getTimezone()
    {
        return $this->timezone;
    }

    public function setTimezone($timezone): void
    {
        $this->timezone = $timezone;
    }

    public function getCity()
    {
        return $this->city;
    }

    public function setCity($city): void
    {
        $this->city = $city;
    }

    public function getAvatar()
    {
        return $this->avatar;
    }

    public function setAvatar($avatar): void
    {
        $this->avatar = $avatar;
    }

    public function getAccountType()
    {
        return $this->account_type;
    }

    public function setAccountType($account_type): void
    {
        $this->account_type = $account_type;
    }

    public function getAccountStanding()
    {
        return $this->account_standing;
    }

    public function setAccountStanding($account_standing): void
    {
        $this->account_standing = $account_standing;
    }

    public function getActivityPoints()
    {
        return $this->activity_points;
    }

    public function setActivityPoints($activity_points): void
    {
        $this->activity_points = $activity_points;
    }

    public function getDevelopmentPoints()
    {
        return $this->development_points;
    }

    public function setDevelopmentPoints($development_points): void
    {
        $this->development_points = $development_points;
    }

    public function getBirthdate()
    {
        return $this->birthdate;
    }

    public function setBirthdate($birthdate): void
    {
        $this->birthdate = $birthdate;
    }

    public function getGender()
    {
        return $this->gender;
    }

    public function setGender($gender): void
    {
        $this->gender = $gender;
    }

    public function getShortDescription()
    {
        return $this->short_description;
    }

    public function setShortDescription($short_description): void
    {
        $this->short_description = $short_description;
    }
}
