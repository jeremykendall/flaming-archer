<?php

namespace FA\Tests\Model;

use FA\Model\User;

class UserTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @var array User data
     */
    protected $data;

    /**
     * @var User
     */
    protected $user;

    protected function setUp()
    {
        $lastLogin = \DateTime::createFromFormat('Y-m-d H:i:s', '2010-01-03 18:52:31');

        $this->data = array(
            'id' => '1',
            'email' => 'User@Example.COM',
            'password_hash' => '$2y$12$pZg9j8DBSIP2R/vfDzTQOeIt5n57r5VigCUl/HH.FrBOadi3YhdPS',
            'last_login' => $lastLogin,
        );

        $this->user = new User($this->data);
    }

    public function testSerializeUnserialize()
    {
        $serialized = serialize($this->user);
        $unserialized = unserialize($serialized);

        $this->assertEquals($this->user, $unserialized);
        $this->assertNotSame($this->user, $unserialized);
    }

    public function testEncodeAndDecodeSecureCookieWithObject()
    {
        //Prepare cookie value
        $value = serialize($this->user);
        $expires = time() + 86400;
        $secret = 'password';
        $algorithm = MCRYPT_RIJNDAEL_256;
        $mode = MCRYPT_MODE_CBC;
        $encodedValue = \Slim\Http\Util::encodeSecureCookie($value, $expires, $secret, $algorithm, $mode);
        $decodedValue = \Slim\Http\Util::decodeSecureCookie($encodedValue, $secret, $algorithm, $mode);

        //Test secure cookie value
        $parts = explode('|', $encodedValue);
        $this->assertEquals(3, count($parts));
        $this->assertEquals($expires, $parts[0]);
        $this->assertEquals($value, $decodedValue);
    }

    public function testFromArray()
    {
        $user = new User();
        $user->fromArray($this->data);

        $this->assertEquals($this->data['id'], $user->getId());
        $this->assertEquals($this->data['email'], $user->getEmail());
        $this->assertEquals(strtolower($this->data['email']), $user->getEmailCanonical());
        $this->assertEquals($this->data['password_hash'], $user->getPasswordHash());
        $this->assertEquals($this->data['last_login'], $user->getLastLogin());
    }

    public function testConstructWithData()
    {
        $this->assertEquals($this->data['id'], $this->user->getId());
        $this->assertEquals($this->data['email'], $this->user->getEmail());
        $this->assertEquals(strtolower($this->data['email']), $this->user->getEmailCanonical());
        $this->assertEquals($this->data['password_hash'], $this->user->getPasswordHash());
        $this->assertEquals($this->data['last_login'], $this->user->getLastLogin());
    }

    public function testToArray()
    {
        $this->data['emailCanonical'] = strtolower($this->data['email']);
        $data = $this->user->toArray();

        $this->assertEquals($this->data, $data);
    }
}
