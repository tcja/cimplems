<?php

namespace App\Libraries;

use Tcja\DOMDXMLParser;

/*
 *
 * Edit User class.
 *
 * Author: tcja
 *
 * Description : Class used to manipulate users informations
 *
 */
class EditUser extends User
{
 	public function __construct()
	{
    }

    /**
	 * Changes user's e-mail in the provided XML file
	 *
	 * @param 	string		$user_email			    User's old e-mail
	 * @param 	string		$new_user_email			User's new e-mail
	 * @return 	string								Returns the user's new e-mail
	 **/
	public static function changeUserEmail($user_email, $new_user_email)
	{
        $xml = new DOMDXMLParser(storage_path('app/' . User::XML_USER_FILE_PATH));

        $xml->pickNode('email', $user_email)->changeData('email', $new_user_email);

		return $new_user_email;
    }

    /**
	 * Changes user's password in the provided XML file
	 *
	 * @param 	string		$user_email			    User's e-mail
	 * @param 	string		$new_password			User's new password
	 * @return 	string								Returns true on change success
	 **/
	public static function changeUserPassword($user_email, $new_password)
	{
        $xml = new DOMDXMLParser(storage_path('app/' . User::XML_USER_FILE_PATH));

        $xml->pickNode('email', $user_email)->changeData('password', bcrypt($new_password));

		return true;
	}
}
