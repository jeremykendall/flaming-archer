<?php

namespace Fa\Test\Form;

use Fa\Entity\User;
use Fa\Form\UserForm;
use Symfony\Component\Form\Forms;

class UserFormTest extends \PHPUnit_Framework_TestCase
{
    public function testPleaseDoNotBlowUp()
    {
        $formFactory = Forms::createFormFactoryBuilder()->getFormFactory();
        $form = $formFactory->create(new UserForm());
        $this->assertEquals('user', $form->getName());
    }
}
