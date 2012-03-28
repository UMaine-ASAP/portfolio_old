<?php

class UserController
{
	static function createUser($username, $pass, $first, $middle, $last, $email, $email_priv, $addn_contact, $bio, $user_pic, $major, $minor, $grad_year, $type_id)
	{
		$user = Model::factory('User')->create();

		$user->username = $username;
		$user->pass = $pass;
		$user->first = $first;
		$user->middle = $middle;
		$user->last = $last;
		$user->email = $email;
		$user->email_priv = $email_priv;
		$user->addn_contact = $addn_contact;
		$user->bio = $bio;
		$user->user_pic = $user_pic;
		$user->major = $major;
		$user->minor = $minor;
		$user->grad_year = $grad_year;
		$user->grad_year = $type_id;

		if(!$user->save())
		{
			return false;
		}

		return $user;
	}

	static function getUser($userID)
	{
		return Model::factory('User')->find_one($userID);
	}

	static function editUser($userID, $username, $pass, $first, $middle, $last, $email, $email_priv, $addn_contact, $bio, $user_pic, $majidor, $minor, $grad_year, $type_id)
	{
		$user = self::getUser($userID);

		if (!$user)
		{
			return false;
		}

		$user->username = $username;
		$user->pass = $pass;
		$user->first = $first;
		$user->middle = $middle;
		$user->last = $last;
		$user->email = $email;
		$user->email_priv = $email_priv;
		$user->addn_contact = $addn_contact;
		$user->bio = $bio;
		$user->user_pic = $user_pic;
		$user->major = $major;
		$user->minor = $minor;
		$user->grad_year = $grad_year;
		$user->grad_year = $type_id;

		if(!$user->save())
		{
			return false;
		}

		return true;
	}

	static function deleteUser($userID)
	{
		$user = self::getUser($userID);

		if (!$user)
		{
			return false;
		}

		$user->delete();
		return true;
	}
}
