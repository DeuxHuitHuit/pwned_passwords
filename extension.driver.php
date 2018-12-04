<?php
/**
 * Copyright: Deux Huit Huit 2018
 * License: MIT, see the LICENSE file
 */

if (!defined("__IN_SYMPHONY__")) die("<h2>Error</h2><p>You cannot directly access this file</p>");

require_once(EXTENSIONS . '/pwned_passwords/lib/class.pwnedpasswords.php');

class extension_pwned_passwords extends Extension
{
    /**
     * Name of the extension
     * @var string
     */
    const EXT_NAME = 'Pwned Passwords';

    /* ********* INSTALL/UPDATE/UNINSTALL ******* */

    /**
     * Creates the table needed for the settings of the field
     */
    public function install()
    {
        return true;
    }

    /**
     * Creates the table needed for the settings of the field
     */
    public function update($previousVersion = false)
    {
        $ret = true;
        return $ret;
    }

    /**
     *
     * Drops the table needed for the settings of the field
     */
    public function uninstall()
    {
        return true;
    }

    /*------------------------------------------------------------------------------------------------*/
    /*  Delegates  */
    /*------------------------------------------------------------------------------------------------*/

    public function getSubscribedDelegates()
    {
        return array(
            array(
                'page'     => '/system/authors/',
                'delegate' => 'AuthorPreCreate',
                'callback' => 'checkPassword',
            ),
            array(
                'page' => '/system/authors/',
                'delegate' => 'AuthorPreEdit',
                'callback' => 'checkPassword',
            ),
        );
    }

    public function checkPassword(array $context)
    {
        // Check that we have enough data
        if (!isset($context['author'], $context['field'])) {
            return;
        }
        // Check that a password was supplied by the user
        if (empty($context['field']['password'])) {
            return;
        }
        // Make sure the password does not have any errors
        if (isset($context['errors']['password'])) {
            return;
        }
        // Make sure password matches confirmation
        if ($context['errors']['password'] !== $context['errors']['confirm-password']) {
            return;
        }
        // At least ask for 5 chars
        if (General::strlen($context['field']['password']) < 5) {
            $context['errors']['password'] = __('Your password is too short.') .
                ' ' . __('Please use at least 5 characters');
            return;
        }
        // Check for pwnage
        $pp = new PwnedPasswords;
        if ($pp->isPasswordPwned($context['field']['password'])) {
            $context['errors']['password'] = __('Your password appears in breached password lists.') .
                ' ' . __ ('Please choose another password.');
        }
    }
}
