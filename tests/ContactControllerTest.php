<?php

use App\Controllers\ContactController;
use PHPUnit\Framework\TestCase;
use App\Components\Exception\CustomException;

class ContactControllerTest extends TestCase
{
    protected $controller;

    protected $database;

    protected function setUp()
    {
        $_SESSION['auth'] = [
            "id" => 2,
            "login" => 'test',
            "email" => 'test@test.fr'
        ];
        $this->controller = new ContactController();

        $this->database = App::getInstance()->getDatabase();
        $this->controller->Contact->create([
            'email' => 'nasr.medh@gmail.com',
            'firstname' => 'Mohamed',
            'lastname' => 'Nasri',
            'userId' => 2
        ]);

        $this->contactId = $this->database->lastInsertId();

    }

    protected function tearDown()
    {
        $this->controller->Contact->delete($this->contactId);
    }

    /**
     * Call protected/private method of a class.
     *
     * @param object &$object Instantiated object that we will run method on.
     * @param string $methodName Method name to call
     * @param array $parameters Array of parameters to pass into method.
     *
     * @return mixed Method return.
     */
    private function invokeMethod($object, $methodName, array $parameters = array())
    {
        $class = (is_object($object)) ? get_class($object) : $object;

        $reflection = new \ReflectionClass($class);
        $method = $reflection->getMethod($methodName);
        $method->setAccessible(true);

        if ($method->isStatic()) {
            $object = null;
        }

        return $method->invokeArgs($object, $parameters);
    }

    public function testDelete()
    {
        $this->controller->delete($this->contactId);
        $this->assertFalse($this->controller->Contact->findById($this->contactId));
    }

    public function testCheckIfExist() {
        $result = $this->invokeMethod($this->controller, "checkIfExist", [$this->contactId]);
        $expected = [
            'id' => $this->contactId,
            'email' => 'nasr.medh@gmail.com',
            'firstname' => 'Mohamed',
            'lastname' => 'Nasri',
            'userId' => 2
        ];

        $this->assertEquals($expected, $result);
    }

    public function testCheckIfExistWithException() {
        $this->expectException(CustomException::class);
        $this->invokeMethod($this->controller, "checkIfExist", [0]);
    }

    public function testSanitize()
    {
        $entries = [
            'email' => 'nasrI.medh@gmail.com',
            'firstname' => 'mohamed',
            'lastname' => 'nasri',
            'userId' => 2
        ];

        $expected = [
            'email' => 'nasri.medh@gmail.com',
            'firstname' => 'Mohamed',
            'lastname' => 'Nasri',
            'userId' => 2
        ];

        $this->assertEquals($expected, $this->invokeMethod($this->controller, 'sanitize', [$entries]));
    }

    public function testIndex()
    {
        $this->controller->index();
        $this->expectOutputRegex('/^<!DOCTYPE html>/');
    }

    public function testAdd()
    {
        $_POST = [
            'email' => 'nasri.medh@gmail.com',
            'firstname' => 'Mohamed',
            'lastname' => 'Nasri',
            'userId' => 2
        ];

        ob_start();
        $this->controller->add();
        $output = ob_get_contents();
        ob_end_clean();
        $this->assertContains('Edition d\'un contact', $output);
    }

    public function testAddWitchException()
    {
        $_POST = [
            'email' => 'nasri.medh@gmail.com',
            'firstname' => 'Mohamed',
            'userId' => 2
        ];

        ob_start();
        $this->controller->add();
        $output = ob_get_contents();
        ob_end_clean();
        $this->assertContains('Le lastname est obligatoire', $output);
    }

    public function testEdit()
    {
        $_POST = [
            'email' => 'nasri.medh@gmail.com',
            'firstname' => 'Mohamed',
            'lastname' => 'Nasri',
            'userId' => 2
        ];

        ob_start();
        $this->controller->edit($this->contactId);
        $output = ob_get_contents();
        ob_end_clean();
        $this->assertContains('Edition d\'un contact', $output);
    }

    public function testEditWitchException()
    {
        $_POST = [
            'firstname' => 'Mohamed',
            'lastname' => 'Nasri',
            'userId' => 2
        ];

        ob_start();
        $this->controller->edit($this->contactId);
        $output = ob_get_contents();
        ob_end_clean();
        $this->assertContains('email est obligatoire', $output);
    }

    public function testEditWithException()
    {
        $_POST = [
            'email' => 'nasri.medh@gmail.com',
            'firstname' => 'Mohamed',
            'lastname' => 'Nasri',
            'userId' => 2
        ];

        $this->expectException(CustomException::class);
        $this->controller->edit(0);

    }

    public function testIsValid()
    {
        $entries = [
            'email' => 'nasri.medh@gmail.com',
            'firstname' => 'Mohamed',
            'lastname' => 'Nasri'
        ];
        $this->assertTrue($this->invokeMethod($this->controller, 'isValid', [$entries]));
    }

    public function testIsValidWithEmptyLastName()
    {
        $entries = [
            'email' => 'nasri.medh@gmail.com',
            'firstname' => 'Mohamed',
            'lastname' => ''
        ];

        $this->expectException(\InvalidArgumentException::class);
        $this->invokeMethod($this->controller, 'isValid', [$entries]);
    }

    public function testIsValidWithEmptyFirstName()
    {
        $entries = [
            'email' => 'nasri.medh@gmail.com',
            'lastname' => 'Nasri'
        ];

        $this->expectException(\InvalidArgumentException::class);
        $this->invokeMethod($this->controller, 'isValid', [$entries]);
    }

    public function testIsValidWithEmptyEmail()
    {
        $entries = [
            'email' => '',
            'firstname' => 'Mohamed',
            'lastname' => 'Nasri'
        ];

        $this->expectException(\InvalidArgumentException::class);
        $this->invokeMethod($this->controller, 'isValid', [$entries]);
    }

    public function testIsValidWithPalindrome()
    {
        $entries = [
            'email' => 'nasri.medh@gmail.com',
            'firstname' => 'abccba',
            'lastname' => 'Nasri'
        ];
        $this->expectException(\InvalidArgumentException::class);
        $this->invokeMethod($this->controller, 'isValid', [$entries]);
    }

    public function testIsValidWithInvalidEmail()
    {
        $entries = [
            'email' => 'nasri.medhgmail.com',
            'firstname' => 'Mohamed',
            'lastname' => 'Nasri'
        ];
        $this->expectException(\InvalidArgumentException::class);
        $this->invokeMethod($this->controller, 'isValid', [$entries]);
    }
}
