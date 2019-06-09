<?php

namespace App\Libraries;

//use Illuminate\Support\Arr;
/*
 *
 * User class.
 *
 * Author : Trim Camaj
 *
 * Description : Class used to retrieve informations about users
 *
 */
class User
{
	/**
	 *
	 *
	 * @var string $content user's info
	 * */
    protected $admin_password;
    /**
	 *
	 *
	 * @var string $content user's info
	 * */
	protected $admin_email;


	/**
	 *
	 *  @var string $_XML_PAGE_FOLDER_PATH Default pages folder location
	 * */
	const _XML_USER_FILE_PATH = 'private/users/users.xml';

	/**
	 *
	 * @return	void
	 **/
 	public function __construct()
	{
		$this->admin_password = $this->fetchAdminPassword();
		$this->admin_email = $this->fetchAdminEmail();
	}
	/**
	 * Fetches the user
	 *
	 *
	 * @return	array	Returns an array of the user
	 **/
	private function fetchUser()
	{

	}
	/**
	 * Fetches the admin password
	 *
	 * @return	string	Returns the password
	 **/
	private function fetchAdminPassword()
	{
		$doc = new \DOMDocument('1.0', 'UTF-8');
		$password = null;
		$doc->load(storage_path('app/' . self::_XML_USER_FILE_PATH));
		$datas = $doc->getElementsByTagName('user');
		foreach ($datas as $data) {
            $password = $data->getAttribute('password');
        }
		return $password;
    }
    /**
	 * Fetches the admin e-mail
	 *
	 * @return	string	Returns the e-mail
	 **/
	private function fetchAdminEmail()
	{
		$doc = new \DOMDocument('1.0', 'UTF-8');
		$email = null;
		$doc->load(storage_path('app/' . self::_XML_USER_FILE_PATH));
		$datas = $doc->getElementsByTagName('user');
		foreach ($datas as $data) {
            $email = $data->getAttribute('email');
        }
		return $email;
	}
	/**
	 * Gets admin's password
	 *
	 * @return	string	Returns the password
	 **/
	public function getAdminPassword()
	{
		return $this->admin_password;
    }
    /**
	 * Gets admin's Email
	 *
	 * @return	string	Returns the Email
	 **/
	public function getAdminEmail()
	{
		return $this->admin_email;
	}
}
