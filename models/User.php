<?php

namespace models;

use DateTime;

class User{
    private $id;
    private string $surname;
    private string $name;
    private string $patronymic;
    private string $email;
    private string $phone;
    private $total_spent_amount;
    private ?string $hash;
    private ?string $role;
    private ?string $avatar;

    /**
     * @param $id
     * @param string $surname
     * @param string $name
     * @param string $patronymic
     * @param string $email
     * @param string $phone
     * @param string $hash
     * @param string|null $role
     */
    public function __construct($id, string $surname = null, string $name = null, string $patronymic = null, string $email = null, string $phone = null, string $hash = null, ?string $role = null, ?string $spent_amount = null)
    {
        $this->id = $id;
        $this->surname = $surname;
        $this->name = $name;
        $this->patronymic = $patronymic;
        $this->email = $email;
        $this->phone = $phone;
        $this->hash = $hash;
        $this->role = $role;
        $this->avatar = findFileByName(base_path('users-avatars'), "IMG-$id") ? "/users-avatars".DIRECTORY_SEPARATOR.findFileByName(base_path('users-avatars'), "IMG-$id") : null;
        $this->total_spent_amount = $spent_amount;
    }

    public function surname(): string
    {
        return $this->surname;
    }

    public function setSurname(string $surname): void
    {
        $this->surname = $surname;
    }

    public function name(): string
    {
        return $this->name;
    }

    public function setName(string $name): void
    {
        $this->name = $name;
    }

    public function patronymic(): string
    {
        return $this->patronymic;
    }

    public function setPatronymic(string $patronymic): void
    {
        $this->patronymic = $patronymic;
    }

    public function phone(): string
    {
        return $this->phone;
    }

    public function setPhone(string $phone): void
    {
        $this->phone = $phone;
    }

    public function avatar(): ?string
    {

        return $this->avatar;
    }

    public function setAvatar(?string $avatar): void
    {
        $this->avatar = $avatar;
    }

    public function id()
    {
        return $this->id;
    }

    public function setId($id)
    {
        $this->id = $id;
    }

    public function email()
    {
        return $this->email;
    }

    public function setEmail($email)
    {
        $this->email = $email;
    }

    public function role(): string
    {
        return $this->role;
    }

    public function setRole($role): void
    {
        $this->role = $role;
    }

    public function hash(): string
    {
        return $this->hash;
    }

    public function setHash(string $hash): void
    {
        $this->hash = $hash;
    }

    public function __serialize(): array
    {
        return [
            'id'=>$this->id,
            'phone'=>$this->phone,
            'email'=>$this->email,
            'surname'=>$this->surname,
            'name'=>$this->name,
            'middlename'=>$this->patronymic,
            'role'=>$this->role,
            'hash'=>$this->hash,
            'total_spent_amount'=>$this->total_spent_amount,
            'avatar'=>$this->avatar()
        ];
    }

    /**
     * @return mixed
     */
    public function totalSpentAmount()
    {
        return $this->total_spent_amount;
    }

    /**
     * @param mixed $total_spent_amount
     */
    public function setTotalSpentAmount($total_spent_amount): void
    {
        $this->total_spent_amount = $total_spent_amount;
    }
}