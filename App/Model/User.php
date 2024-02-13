<?php
namespace App\Model;

class User extends Model
{
	protected string $table = 'user';

	private int $id;
	private string $username;
	private string $password;

	private string $role;
	public function getId()
	{
		return $this->id;
	}

	public function getUsername()
	{
		return $this->username;
	}

	public function getRole()
	{
		return $this->role;
	}

	public function getPassword()
	{
		return $this->password;
	}
}
