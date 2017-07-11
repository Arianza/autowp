<?php

namespace ApplicationTest\Frontend\Controller;

use Application\Test\AbstractHttpControllerTestCase;
use Zend\Json\Json;

use Application\Controller\Api\PictureController;

class ApiPictureControllerTest extends AbstractHttpControllerTestCase
{
    protected $applicationConfigPath = __DIR__ . '/../../../../../config/application.config.php';

    public function testRandomPicture()
    {
        $this->dispatch('http://www.autowp.ru/api/picture/random-picture', 'GET');

        $this->assertResponseStatusCode(200);
        $this->assertModuleName('application');
        $this->assertControllerName(PictureController::class);
        $this->assertMatchedRouteName('api/picture/random_picture');

        $this->assertResponseHeaderContains('Content-Type', 'application/json; charset=utf-8');

        $json = Json::decode($this->getResponse()->getContent(), Json::TYPE_ARRAY);
        $this->assertArrayHasKey('status', $json);
        $this->assertArrayHasKey('url', $json);
        $this->assertArrayHasKey('name', $json);
        $this->assertArrayHasKey('page', $json);
    }

    public function testNewPicture()
    {
        $this->dispatch('http://www.autowp.ru/api/picture/new-picture', 'GET');

        $this->assertResponseStatusCode(200);
        $this->assertModuleName('application');
        $this->assertControllerName(PictureController::class);
        $this->assertMatchedRouteName('api/picture/new-picture');

        $this->assertResponseHeaderContains('Content-Type', 'application/json; charset=utf-8');

        $json = Json::decode($this->getResponse()->getContent(), Json::TYPE_ARRAY);
        $this->assertArrayHasKey('status', $json);
        $this->assertArrayHasKey('url', $json);
        $this->assertArrayHasKey('name', $json);
        $this->assertArrayHasKey('page', $json);
    }

    public function testCarOfDayPicture()
    {
        $this->dispatch('http://www.autowp.ru/api/picture/car-of-day-picture', 'GET');

        $this->assertResponseStatusCode(200);
        $this->assertModuleName('application');
        $this->assertControllerName(PictureController::class);
        $this->assertMatchedRouteName('api/picture/car-of-day-picture');

        $this->assertResponseHeaderContains('Content-Type', 'application/json; charset=utf-8');

        $json = Json::decode($this->getResponse()->getContent(), Json::TYPE_ARRAY);
        $this->assertArrayHasKey('status', $json);
        $this->assertArrayHasKey('url', $json);
        $this->assertArrayHasKey('name', $json);
        $this->assertArrayHasKey('page', $json);
    }
}
