<?php
namespace CakeS3\Test\TestCase\Datasource;

use CakeS3\Datasource\Connection;
use Cake\TestSuite\TestCase;
use \Mockery as m;

/**
 * Connection Testcase
 */
class ConnectionTest extends TestCase
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
     * @return \Mockery\MockInterface
     */
    private function __getS3ClientMock()
    {
        $mock = m::mock('overload:\Aws\S3\S3Client');
        $mock->shouldReceive('registerStreamWrapper')
            ->once();
        $mock->shouldReceive('getCommand')
            ->once()
            ->andReturn(true);

        return $mock;
    }

    /**
     * @return \CakeS3\Datasource\Connection
     */
    private function __getConnectionInstance()
    {
        $params = [
            'key'        => 'test-key',
            'secret'     => 'test-secret',
            'region'     => 'test-region',
            'bucketName' => 'test-bucket',
        ];

        return new Connection($params);
    }

    /**
     * Test new instance success
     *
     * @return void
     */
    public function testNewInstanceSuccess()
    {
        $this->__getS3ClientMock();

        $connection = $this->__getConnectionInstance();

        $config = $connection->config();
        $this->assertEquals('test-key', $config['key']);
    }

    /**
     * Test new instance failed, missing arguments
     *
     * @return void
     */
    public function testNewInstanceMissingArguments()
    {
        $this->expectException(\InvalidArgumentException::class);

        $params = [];
        new Connection($params);
    }

    /**
     * Test copyObject method
     */
    public function testCopyObject()
    {
        $mock = $this->__getS3ClientMock();
        $mock->shouldReceive('copyObject')
            ->once()
            ->with([
                'Bucket'     => 'test-bucket',
                'Key'        => 'test-dest-key',
                'CopySource' => 'test-bucket/test-src-key',
                'ACL'        => 'public-read',
            ]);

        $connection = $this->__getConnectionInstance();
        $connection->copyObject('/test-src-key', '/test-dest-key');
    }

    /**
     * Test copyObject method
     */
    public function testCopyObjectOverWroteOptions()
    {
        $mock = $this->__getS3ClientMock();
        $mock->shouldReceive('copyObject')
            ->once()
            ->with([
                'Bucket'            => 'test-bucket',
                'Key'               => 'test-dest-key',
                'CopySource'        => 'test-bucket/test-src-key',
                'ACL'               => 'overwrote',
                'overwrote-options' => true,
            ]);

        $connection = $this->__getConnectionInstance();
        $connection->copyObject(
            '/test-src-key',
            '/test-dest-key',
            [
                'ACL'               => 'overwrote',
                'overwrote-options' => true,
            ]
        );
    }

    /**
     * Test deleteObject method
     */
    public function testDeleteObject()
    {
        $mock = $this->__getS3ClientMock();
        $mock->shouldReceive('deleteObject')
            ->once()
            ->with([
                'Bucket' => 'test-bucket',
                'Key'    => 'test-key',
            ]);

        $connection = $this->__getConnectionInstance();
        $connection->deleteObject('/test-key');
    }

    /**
     * Test deleteObject method
     */
    public function testDeleteObjectOverWroteOptions()
    {
        $mock = $this->__getS3ClientMock();
        $mock->shouldReceive('deleteObject')
            ->once()
            ->with([
                'Bucket'            => 'test-bucket',
                'Key'               => 'test-key',
                'overwrote-options' => true,
            ]);

        $connection = $this->__getConnectionInstance();
        $connection->deleteObject(
            '/test-key',
            [
                'overwrote-options' => true,
            ]
        );
    }

    /**
     * Test deleteObjects method
     */
    public function testDeleteObjects()
    {
        $mock = $this->__getS3ClientMock();
        $mock->shouldReceive('deleteObjects')
            ->once()
            ->with([
                'Bucket' => 'test-bucket',
                'Delete' => [
                    'Objects' => [
                        ['Key' => 'test-key1'],
                        ['Key' => 'test-key2'],
                        ['Key' => 'test-key3'],
                    ],
                ],
            ]);

        $connection = $this->__getConnectionInstance();
        $connection->deleteObjects([
            '/test-key1',
            '/test-key2',
            '/test-key3',
        ]);
    }

    /**
     * Test deleteObjects method
     */
    public function testDeleteObjectsOverWroteOptions()
    {
        $mock = $this->__getS3ClientMock();
        $mock->shouldReceive('deleteObjects')
            ->once()
            ->with([
                'Bucket'            => 'test-bucket',
                'Delete'            => [
                    'Objects' => [
                        ['Key' => 'test-key1'],
                        ['Key' => 'test-key2'],
                        ['Key' => 'test-key3'],
                    ],
                ],
                'overwrote-options' => true,
            ]);

        $connection = $this->__getConnectionInstance();
        $connection->deleteObjects(
            [
                '/test-key1',
                '/test-key2',
                '/test-key3',
            ],
            [
                'overwrote-options' => true,
            ]
        );
    }

    /**
     * Test doesObjectExist method
     */
    public function testDoesObjectExist()
    {
        $mock = $this->__getS3ClientMock();
        $mock->shouldReceive('doesObjectExist')
            ->once()
            ->with(
                'test-bucket',
                'test-key',
                []
            );

        $connection = $this->__getConnectionInstance();
        $connection->doesObjectExist('/test-key');
    }

    /**
     * Test doesObjectExist method
     */
    public function testDoesObjectExistOverWroteOptions()
    {
        $mock = $this->__getS3ClientMock();
        $mock->shouldReceive('doesObjectExist')
            ->once()
            ->with(
                'test-bucket',
                'test-key',
                [
                    'overwrote-options' => true,
                ]
            );

        $connection = $this->__getConnectionInstance();
        $connection->doesObjectExist(
            '/test-key',
            [
                'overwrote-options' => true,
            ]
        );
    }

    /**
     * Test getObject method
     */
    public function testGetObject()
    {
        $mock = $this->__getS3ClientMock();
        $mock->shouldReceive('getObject')
            ->twice()
            ->with([
                'Bucket' => 'test-bucket',
                'Key'    => 'test-key',
                'ACL'    => 'public-read',
            ]);

        $connection = $this->__getConnectionInstance();
        $connection->getObject('test-key');
        $connection->getObject('/test-key');
    }

    /**
     * Test getObject method
     */
    public function testGetObjectOverWroteOptions()
    {
        $mock = $this->__getS3ClientMock();
        $mock->shouldReceive('getObject')
            ->once()
            ->with([
                'Bucket'            => 'test-bucket',
                'Key'               => 'test-key',
                'ACL'               => 'overwrote',
                'overwrote-options' => true,
            ]);

        $connection = $this->__getConnectionInstance();
        $connection->getObject(
            'test-key',
            [
                'ACL'               => 'overwrote',
                'overwrote-options' => true,
            ]
        );
    }

    /**
     * Test headObject method
     */
    public function testHeadObject()
    {
        $mock = $this->__getS3ClientMock();
        $mock->shouldReceive('headObject')
            ->once()
            ->with([
                'Bucket' => 'test-bucket',
                'Key'    => 'test-key',
            ]);

        $connection = $this->__getConnectionInstance();
        $connection->headObject('/test-key');
    }

    /**
     * Test headObject method
     */
    public function testHeadObjectOverWroteOptions()
    {
        $mock = $this->__getS3ClientMock();
        $mock->shouldReceive('headObject')
            ->once()
            ->with([
                'Bucket'            => 'test-bucket',
                'Key'               => 'test-key',
                'overwrote-options' => true,
            ]);

        $connection = $this->__getConnectionInstance();
        $connection->headObject(
            '/test-key',
            [
                'overwrote-options' => true,
            ]
        );
    }


    /**
     * Test putObject method
     */
    public function testPutObject()
    {
        $mock = $this->__getS3ClientMock();
        $mock->shouldReceive('putObject')
            ->once()
            ->with([
                'Bucket' => 'test-bucket',
                'Key'    => 'test-key',
                'Body'   => 'test-body',
                'ACL'    => 'public-read',
            ]);

        $connection = $this->__getConnectionInstance();
        $connection->putObject('/test-key', 'test-body');
    }

    /**
     * Test putObject method
     */
    public function testPutObjectOverWroteOptions()
    {
        $mock = $this->__getS3ClientMock();
        $mock->shouldReceive('putObject')
            ->once()
            ->with([
                'Bucket'            => 'test-bucket',
                'Key'               => 'test-key',
                'Body'              => 'test-body',
                'ACL'               => 'public-read',
                'overwrote-options' => true,
            ]);

        $connection = $this->__getConnectionInstance();
        $connection->putObject(
            '/test-key',
            'test-body',
            [
                'overwrote-options' => true,
            ]
        );
    }
}
