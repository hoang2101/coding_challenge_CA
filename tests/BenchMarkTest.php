<?php

use App\Controller\BenchmarkController;
use App\Entity\RankName;
use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;
use Symfony\Component\HttpFoundation\Request;
use App\DataFixtures\TestFixtures\TestBenchmarkFixtures;
use Doctrine\Common\DataFixtures\Loader;
use Doctrine\Common\DataFixtures\Executor\ORMExecutor;
use Doctrine\Common\DataFixtures\Purger\ORMPurger;
use Symfony\Component\HttpFoundation\Response;


class BenchMarkTest extends KernelTestCase
{
    /**
     * @var App\Controller\benchmarkController
     */
    private $benchmarkController;

    protected function setUp()
    {
        $kernel = self::bootKernel();
        $container = $kernel->getContainer();

        $this->benchmarkController = new BenchmarkController();
        $this->benchmarkController->setContainer($container);

        $loader = new Loader();
        $loader->addFixture(new TestBenchmarkFixtures());

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
        $result = $this->benchmarkController->index($request);

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
        $result = $this->benchmarkController->index($request);

        //then
        $this->assertEquals(Response::HTTP_NOT_FOUND, $result->getStatusCode());
    }

    public function testWithBottomRank()
    {
        //given
        $request = new Request();
        $request->query->set('id', '10');
        $request->query->set('startDate', '2018-01-01');
        $request->query->set('endDate', '2018-01-01');

        //when
        $result = $this->benchmarkController->index($request);
        $responseAsArray = json_decode($result->getContent(), true);

        //then
        $this->assertEquals(Response::HTTP_OK, $result->getStatusCode());
        $this->assertEquals(1, $responseAsArray['hotelAverage']);
        $this->assertEquals(RankName::BOTTOM, $responseAsArray['rankType']);
    }

    public function testWithTopRank()
    {
        //given
        $request = new Request();
        $request->query->set('id', '1');
        $request->query->set('startDate', '2018-01-01');
        $request->query->set('endDate', '2018-01-01');

        //when
        $result = $this->benchmarkController->index($request);
        $responseAsArray = json_decode($result->getContent(), true);

        //then
        $this->assertEquals(Response::HTTP_OK, $result->getStatusCode());
        $this->assertEquals(9, $responseAsArray['hotelAverage']);
        $this->assertEquals(RankName::TOP, $responseAsArray['rankType']);
    }

    public function testWithNoRank()
    {
        //given
        $request = new Request();
        $request->query->set('id', '6');
        $request->query->set('startDate', '2018-01-01');
        $request->query->set('endDate', '2018-01-01');

        //when
        $result = $this->benchmarkController->index($request);
        $responseAsArray = json_decode($result->getContent(), true);

        //then
        $this->assertEquals(Response::HTTP_OK, $result->getStatusCode());
        $this->assertEquals(5, $responseAsArray['hotelAverage']);
        $this->assertEquals(RankName::NONE, $responseAsArray['rankType']);
    }
}