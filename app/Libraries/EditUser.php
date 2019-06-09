<?php

namespace App\Libraries;

//use Illuminate\Support\Arr;
/*
 *
 * Edit User class.
 *
 * Author : Trim Camaj
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
		$dom = new \DOMDocument('1.0', 'UTF-8');
		$dom->preserveWhiteSpace = false;
		$dom->formatOutput = true;
		$dom->load(storage_path('app/' . User::_XML_USER_FILE_PATH));
		$xpath = new \DOMXpath($dom);
		$targets = $xpath->query('/users/user[@email="' . $user_email . '"]');
		if ($targets && $targets->length > 0) {
			$target = $targets->item(0);
			$target->setAttribute('email', $new_user_email);
        }
        $dom->save(storage_path('app/' . User::_XML_USER_FILE_PATH));

		return $new_user_email;
    }

    /**
	 * Changes user's password in the provided XML file
	 *
	 * @param 	string		$user_email			    User's e-mail
	 * @param 	string		$new_password			User's new password
	 * @return 	string								Returns the user's new password
	 **/
	public static function changeUserPassword($user_email, $new_password)
	{
		$dom = new \DOMDocument('1.0', 'UTF-8');
		$dom->preserveWhiteSpace = false;
		$dom->formatOutput = true;
		$dom->load(storage_path('app/' . User::_XML_USER_FILE_PATH));
		$xpath = new \DOMXpath($dom);
		$targets = $xpath->query('/users/user[@email="' . $user_email . '"]');
		if ($targets && $targets->length > 0) {
			$target = $targets->item(0);
			$target->setAttribute('password', bcrypt($new_password));
        }
        $dom->save(storage_path('app/' . User::_XML_USER_FILE_PATH));

		return true;
	}
}
