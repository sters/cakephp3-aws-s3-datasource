<?php
namespace CakeS3\Test\TestCase\Datasource;

use CakeS3\Datasource\AwsS3Table;
use Cake\TestSuite\TestCase;
use GuzzleHttp\Psr7\Stream;
use \Mockery as m;

/**
 * AwsS3Table Testcase
 */
class AwsS3TableTest extends TestCase
{
    /**
     * tear down method
     *
     * @return void
     */
    public function tearDown()
    {
        m::close();
    }

    /**
     * Test Connection APIs
     */
    public function testConnectionApi()
    {
        // Create Connection mock -> using ConnectionManager mock
        $connectionMock = m::mock('\CakeS3\Datasource\Connection');
        $connectionMock->shouldReceive('copyObject')
            ->once()
            ->with('/test-src-key', '/test-dest-key', ['option' => true]);
        $connectionMock->shouldReceive('deleteObject')
            ->once()
            ->with('/test-key', ['option' => true]);
        $connectionMock->shouldReceive('deleteObjects')
            ->once()
            ->with(['/test-key1', '/test-key2', '/test-key3'], ['option' => true]);
        $connectionMock->shouldReceive('doesObjectExist')
            ->once()
            ->with('/test-key', ['option' => true]);
        $connectionMock->shouldReceive('getObject')
            ->once()
            ->with('/test-key', ['option' => true]);
        $connectionMock->shouldReceive('headObject')
            ->once()
            ->with('/test-key', ['option' => true]);
        $connectionMock->shouldReceive('putObject')
            ->once()
            ->with('/test-key', 'test-content', ['option' => true]);

        // Create ConnectionManager mock
        $connectionManagerMock = m::mock('overload:\Cake\Datasource\ConnectionManager');
        $connectionManagerMock->shouldReceive('get')
            ->once()
            ->andReturn($connectionMock);

        // Test start.
        $AwsS3Table = new AwsS3Table();
        $AwsS3Table->copyObject('/test-src-key', '/test-dest-key', ['option' => true]);
        $AwsS3Table->deleteObject('/test-key', ['option' => true]);
        $AwsS3Table->deleteObjects(['/test-key1', '/test-key2', '/test-key3'], ['option' => true]);
        $AwsS3Table->doesObjectExist('/test-key', ['option' => true]);
        $AwsS3Table->getObject('/test-key', ['option' => true]);
        $AwsS3Table->headObject('/test-key', ['option' => true]);
        $AwsS3Table->putObject('/test-key', 'test-content', ['option' => true]);
    }

    /**
     * Test getObjectBody method
     */
    public function testGetObjectBody()
    {
        // Create string stream -> using \Aws\Result mock
        $contentString = 'Sample Text';
        $stream        = fopen('php://memory', 'r+');
        fwrite($stream, $contentString);
        rewind($stream);

        // Create \Aws\Result mock -> using Connection mock
        $awsResultMock = m::mock('\Aws\Result');
        $awsResultMock->shouldReceive('get')
            ->once()
            ->with('Body')
            ->andReturn(new Stream($stream));

        // Create Connection mock -> using ConnectionManager mock
        $connectionMock = m::mock('\CakeS3\Datasource\Connection');
        $connectionMock->shouldReceive('getObject')
            ->once()
            ->with('/test-key', [])
            ->andReturn($awsResultMock);

        // Create ConnectionManager mock
        $connectionManagerMock = m::mock('overload:\Cake\Datasource\ConnectionManager');
        $connectionManagerMock->shouldReceive('get')
            ->once()
            ->andReturn($connectionMock);

        // Test start.
        $AwsS3Table = new AwsS3Table();
        $result     = $AwsS3Table->getObjectBody('/test-key');

        // Assertion
        $this->assertEquals($contentString, $result->__toString());
    }

    /**
     * Test moveObject method
     */
    public function testMoveObject()
    {
        // Create Connection mock -> using ConnectionManager mock
        $connectionMock = m::mock('\CakeS3\Datasource\Connection');
        $connectionMock->shouldReceive('copyObject')
            ->once()
            ->with('/test-src-key', '/test-dest-key', []);
        $connectionMock->shouldReceive('deleteObject')
            ->once()
            ->with('/test-src-key', []);

        // Create ConnectionManager mock
        $connectionManagerMock = m::mock('overload:\Cake\Datasource\ConnectionManager');
        $connectionManagerMock->shouldReceive('get')
            ->once()
            ->andReturn($connectionMock);

        // Test start.
        $AwsS3Table = new AwsS3Table();
        $AwsS3Table->moveObject('/test-src-key', '/test-dest-key');
    }
}
