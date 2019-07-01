<?php

namespace App\Libraries;

use Tcja\DOMDXMLParser\DOMDXMLParser;

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
	 *  @var string $XML_PAGE_FOLDER_PATH Default pages folder location
	 * */
	const XML_USER_FILE_PATH = 'private/users/users.xml';

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
	/* private function fetchUser()
	{

	} */
	/**
	 * Fetches the admin password
	 *
	 * @return	string	Returns the password
	 **/
	private function fetchAdminPassword()
	{
        $xml = new DOMDXMLParser(storage_path('app/' . self::XML_USER_FILE_PATH));

        return $xml->pickNode('user')->getAttr('password');
    }
    /**
	 * Fetches the admin e-mail
	 *
	 * @return	string	Returns the e-mail
	 **/
	private function fetchAdminEmail()
	{
        $xml = new DOMDXMLParser(storage_path('app/' . self::XML_USER_FILE_PATH));

        return $xml->pickNode('user')->getAttr('email');
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
