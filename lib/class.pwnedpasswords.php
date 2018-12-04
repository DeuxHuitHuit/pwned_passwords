<?php

class PwnedPasswords
{
    public function isPasswordPwned($password)
    {
        $hash = strtoupper(sha1($password));
        $prefix = substr($hash, 0, 5);
        $suffix = substr($hash, 5);
        $ch = new Gateway;
        $ch->init("https://api.pwnedpasswords.com/range/$prefix");
        $ch->setopt('HTTPHEADER', array(
            'User-Agent' => 'Symphony CMS pwned_passwords extension',
        ));
        $result = @$ch->exec();
        if ($result === false) {
            return false;
        }
        return strpos($result, "$suffix:") !== false;
    }
}
