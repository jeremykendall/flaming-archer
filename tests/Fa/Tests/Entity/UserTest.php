<?php

namespace Fa\Tests\Entity;

use Fa\Entity\User;

class UserTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @var User
     */
    private $user;

    /**
     * @var array
     */
    private $userData;

    protected function setUp()
    {
        parent::setUp();
        $this->user = new User();
        $this->userData = array(
            'id' => 12,
            'firstName' => 'Zaphod',
            'lastName' => 'Beeblebrox',
            'email' => 'Test@Example.COM',
            'flickrUsername' => 'trillian',
            'flickrApiKey' => '12342342',
            'externalUrl' => 'http://en.wikipedia.org/wiki/The_Hitchhiker%27s_Guide_to_the_Galaxy_(novel)',
            'passwordHash' => 'fjdkslfjdlksjfkljlksjlsdj',
            'lastLogin' => new \DateTime('now'),
        );
    }

    protected function tearDown()
    {
        $this->user = null;
        parent::tearDown();
    }

    public function testSetUserDataAtCreation()
    {
        $user = new User($this->userData);

        $this->assertEquals($this->userData['id'], $user->getId());
        $this->assertEquals($this->userData['firstName'], $user->getFirstName());
        $this->assertEquals($this->userData['lastName'], $user->getLastName());
        $this->assertEquals($this->userData['firstName'] . ' ' . $this->userData['lastName'], $user->getFullName());
        $this->assertEquals($this->userData['email'], $user->getEmail());
        $this->assertEquals(strtolower($this->userData['email']), $user->getEmailCanonical());
        $this->assertEquals($this->userData['flickrUsername'], $user->getFlickrUsername());
        $this->assertEquals($this->userData['flickrApiKey'], $user->getFlickrApiKey());
        $this->assertEquals($this->userData['externalUrl'], $user->getExternalUrl());
        $this->assertEquals($this->userData['passwordHash'], $user->getPasswordHash());
        $this->assertSame($this->userData['lastLogin'], $user->getLastLogin());
    }

    public function testSetFromArray()
    {
        $this->user->setFromArray($this->userData);

        $this->assertEquals($this->userData['id'], $this->user->getId());
        $this->assertEquals($this->userData['firstName'], $this->user->getFirstName());
        $this->assertEquals($this->userData['lastName'], $this->user->getLastName());
        $this->assertEquals($this->userData['firstName'] . ' ' . $this->userData['lastName'], $this->user->getFullName());
        $this->assertEquals($this->userData['email'], $this->user->getEmail());
        $this->assertEquals(strtolower($this->userData['email']), $this->user->getEmailCanonical());
        $this->assertEquals($this->userData['flickrUsername'], $this->user->getFlickrUsername());
        $this->assertEquals($this->userData['flickrApiKey'], $this->user->getFlickrApiKey());
        $this->assertEquals($this->userData['externalUrl'], $this->user->getExternalUrl());
        $this->assertEquals($this->userData['passwordHash'], $this->user->getPasswordHash());
        $this->assertSame($this->userData['lastLogin'], $this->user->getLastLogin());
    }

    public function testPassingDateStringToLastLoginInSetFromArraySetsLastLoginProperly()
    {
        $this->userData['lastLogin'] = '2121-11-15 08:20:12';
        $lastLogin = new \DateTime($this->userData['lastLogin']);

        $user = new User($this->userData);
        $this->assertEquals($lastLogin, $user->getLastLogin());
    }

    public function testDateTimeExceptionUnsetsLastLoginArrayKeyAndDoesNotAttemptToSetLastLogin()
    {
        $this->userData['lastLogin'] = base64_encode('\DateTime will throw an \Exception on this');
        $user = new User($this->userData);
        $this->assertNull($user->getLastLogin());
    }

    public function testGetSetNameProperties()
    {
        $firstName = 'Arthur';
        $lastName = 'Dent';

        $this->assertNull($this->user->getFirstName());
        $this->assertNull($this->user->getLastName());
        $this->assertNull($this->user->getFullName());

        $this->user->setFirstName($firstName);
        $this->user->setLastName($lastName);

        $this->assertEquals($firstName, $this->user->getFirstName());
        $this->assertEquals($lastName, $this->user->getLastName());
        $this->assertEquals($firstName . ' ' . $lastName, $this->user->getFullName());
    }

    public function testIdCanOnlyBeSetOnce()
    {
        $id = 999;

        $this->assertNull($this->user->getId());

        $this->user->setId($id);
        $this->assertEquals($id, $this->user->getId());

        $this->user->setId($id + 1000);
        $this->assertEquals($id, $this->user->getId());
    }

    public function testGetSetEmail()
    {
        $email = 'test@example.com';
        $this->assertNull($this->user->getEmail());
        $this->user->setEmail($email);
        $this->assertSame($email, $this->user->getEmail());
    }

    public function testGetEmailCanonical()
    {
        $email = 'TEST@AOL.COM'; // Because AOL users always use Caps Lock
        $emailCanonical = 'test@aol.com'; // There, that's better
        $this->assertNull($this->user->getEmail());
        $this->assertNull($this->user->getEmailCanonical());
        $this->user->setEmail($email);
        $this->assertSame($email, $this->user->getEmail());
        $this->assertSame($emailCanonical, $this->user->getEmailCanonical());
    }

    public function testGetSetPasswordHash()
    {
        $passwordHash = 'pZg9j8DBSIP2R/vfDzTQOeIt5n57r5VigCUl/HH.FrBOadi3YhdPS';
        $this->assertNull($this->user->getPasswordHash());
        $this->user->setPasswordHash($passwordHash);
        $this->assertSame($passwordHash, $this->user->getPasswordHash());
    }

    public function testGetSetFlickrUsername()
    {
        $username = 'trillian';
        $this->assertNull($this->user->getFlickrUsername());
        $this->user->setFlickrUsername($username);
        $this->assertEquals($username, $this->user->getFlickrUsername());
    }

    public function testGetSetFlickrApiKey()
    {
        $apiKey = '12345';
        $this->assertNull($this->user->getFlickrApiKey());
        $this->user->setFlickrApiKey($apiKey);
        $this->assertEquals($apiKey, $this->user->getFlickrApiKey());
    }

    public function testGetSetLastLogin()
    {
        $now = new \DateTime('now');
        $notNow = new \DateTime('2021-03-22 18:12:22');

        $this->assertNull($this->user->getLastLogin());

        $this->user->setLastLogin($now);
        $this->assertSame($now, $this->user->getLastLogin());

        $this->user->setLastLogin($notNow);
        $this->assertSame($notNow, $this->user->getLastLogin());
    }
}
