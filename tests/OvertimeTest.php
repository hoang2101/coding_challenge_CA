<?php

use App\Controller\OvertimeController;
use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;
use Symfony\Component\HttpFoundation\Request;
use App\DataFixtures\TestFixtures\TestReviewFixtures;
use Doctrine\Common\DataFixtures\Loader;
use Doctrine\Common\DataFixtures\Executor\ORMExecutor;
use Doctrine\Common\DataFixtures\Purger\ORMPurger;
use Symfony\Component\HttpFoundation\Response;


class OvertimeTest extends KernelTestCase
{
    /**
     * @var App\Controller\OvertimeController
     */
    private $overtimeController;

    protected function setUp()
    {
        $kernel = self::bootKernel();
        $container = $kernel->getContainer();

        $this->overtimeController = new OvertimeController();
        $this->overtimeController->setContainer($container);

        $loader = new Loader();
        $loader->addFixture(new TestReviewFixtures());

        $purger = new ORMPurger();
        $executor = new ORMExecutor($container->get('doctrine')->getManager(), $purger);
        $executor->execute($loader->getFixtures());

        parent::setUp();
    }

    public function testWithMissingParameter()
    {
        //given
        $request = new Request();
        $request->query->set('id', '1234');

        //when
        $result = $this->overtimeController->index($request);

        //then
        $this->assertEquals(Response::HTTP_NOT_FOUND, $result->getStatusCode());
    }

    public function testWithWrongDate()
    {
        //given
        $request = new Request();
        $request->query->set('id', '1234');
        $request->query->set('startDate', '2020-01-01');
        $request->query->set('endDate', '2018-01-25');

        //when
        $result = $this->overtimeController->index($request);

        //then
        $this->assertEquals(Response::HTTP_NOT_FOUND, $result->getStatusCode());
    }

    public function testByDay()
    {
        //given
        $request = new Request();
        $request->query->set('id', '1234');
        $request->query->set('startDate', '2018-01-01');
        $request->query->set('endDate', '2018-01-25');

        //when
        $result = $this->overtimeController->index($request);
        $responseAsArray = json_decode($result->getContent(), true);

        //then
        $this->assertEquals(Response::HTTP_OK, $result->getStatusCode());
        $this->assertEquals(7, $responseAsArray[0]['averageScore']);
        $this->assertEquals(2, $responseAsArray[0]['reviewCount']);
    }

    public function testByWeek()
    {
        //given
        $request = new Request();
        $request->query->set('id', '1234');
        $request->query->set('startDate', '2018-01-01');
        $request->query->set('endDate', '2018-03-01');

        //when
        $result = $this->overtimeController->index($request);
        $responseAsArray = json_decode($result->getContent(), true);

        //then
        $this->assertEquals(Response::HTTP_OK, $result->getStatusCode());
        $this->assertEquals(6.3, $responseAsArray[0]['averageScore']);
        $this->assertEquals(3, $responseAsArray[0]['reviewCount']);
    }

    public function testByMonth()
    {
        //given
        $request = new Request();
        $request->query->set('id', '1234');
        $request->query->set('startDate', '2018-01-01');
        $request->query->set('endDate', '2019-01-01');

        //when
        $result = $this->overtimeController->index($request);
        $responseAsArray = json_decode($result->getContent(), true);

        //then
        $this->assertEquals(Response::HTTP_OK, $result->getStatusCode());
        $this->assertEquals(6.5, $responseAsArray[0]['averageScore']);
        $this->assertEquals(4, $responseAsArray[0]['reviewCount']);
    }
}