<?php

/**
 * UserIdentity represents the data needed to identity a user.
 * It contains the authentication method that checks if the provided
 * data can identity the user.
 */
class UserIdentity extends CUserIdentity
{
	public $user;
	public $errorCode;
    public $username;
    public $password;

    public function UserIdentity($username, $password)
    {
        $this->username = $username;
        $this->password = $password;

    }

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
		$this->errorCode=self::LOGIN_ERROR;

		if($user != null)
		{
            if($user->passtype == self::USER_PASSTYPE_AUTO)
            {
                //imie密码登陆
                if($user->password == null || $user->password =='')
                {
                    //密码为空则更新密码.
                    if($user->imie = $this->password)
                    {
                        $user->password = md5($this->password);
                        $user->lastlogin_time = time();
                        $user->save();

                        $this->errorCode=self::AUTO_PASSWORD_PASSED;
                    }

                }
                elseif($user->password === md5($this->password))
                {
                    //登录成功
                    $user->lastlogin_time = time();
                    $user->save();

                    $this->user = $user;

                    $this->errorCode=self::AUTO_PASSWORD_PASSED;
                }
                else
                {
                    $this->errorCode=self::AUTO_PASSWORD_FAILED;
                }

            }
            elseif($user->passtype == 2)
            {
                //独立密码登陆
                $this->errorCode=self::PASSWORD_NEEDED;

            }
            else
            {
                $this->errorCode=self::LOGIN_ERROR;
            }
        }
        else
        {
            $this->errorCode=self::USER_NOTFOUND;

        }

        return $this->errorCode;
        
	}

	public function getUser()
    {
        return $this->user;
    }
}