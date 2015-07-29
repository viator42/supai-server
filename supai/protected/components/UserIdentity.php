<?php

/**
 * UserIdentity represents the data needed to identity a user.
 * It contains the authentication method that checks if the provided
 * data can identity the user.
 */
class UserIdentity extends CUserIdentity
{
	public $user;
	public $errcode;
	/**
	 * Authenticates a user.
	 * The example implementation makes sure if the username and password
	 * are both 'demo'.
	 * In practical applications, this should be changed to authenticate
	 * against some persistent user identity storage (e.g. database).
	 * @return boolean whether authentication succeeds.
	 */
	public function authenticate()
	{
		$user = User::model()->findByAttributes(array('tel'=>$this->username));
		$this->errorCode=self::ERROR_NONE;

		if($user)
		{
			if($user->password == null || $user->password =='')
			{
				//密码为空则更新密码.
				if($user->imie = $this->password)
				{
					$user->password = md5($this->password);
					$user->lastlogin_time = time();
					$user->save();
				}
				else
				{
					$this->errorCode=self::ERROR_PASSWORD_INVALID;

				}
				
			}
            elseif($user->password === md5($this->password))
            {
            	$user->lastlogin_time = time();
				$user->save();

                $this->user = $user;
            }
            else
            {
                $this->errorCode=self::ERROR_PASSWORD_INVALID;
            }
        }
        else
        {
            $this->errorCode=self::ERROR_USERNAME_INVALID;
        }

        return $this->errorCode;
        
	}

	public function getUser()
    {
        return $this->user;
    }
}