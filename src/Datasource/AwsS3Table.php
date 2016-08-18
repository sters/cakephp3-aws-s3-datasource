<?php
namespace CakeS3\Datasource;

use Cake\Datasource\ConnectionInterface;
use Cake\Datasource\ConnectionManager;

/**
 * Class AwsS3Table
 *
 * @package CakeS3\Database
 */
class AwsS3Table
{
    /** @var string Connection configure name */
    protected static $_connectionName = '';

    /** @var Connection Connection instance */
    protected $_connection;

    /**
     * Get default connection name
     *
     * @return string
     */
    public static function defaultConnectionName()
    {
        return static::$_connectionName;
    }

    /**
     * AwsS3Table constructor.
     *
     * @param array $config
     */
    public function __construct(array $config = [])
    {
        $this->initialize($config);
    }

    /**
     * Returns the connection instance or sets a new one
     *
     * @param Connection|null $conn The new connection instance
     *
     * @return Connection
     */
    public function connection(ConnectionInterface $conn = null)
    {
        if ($conn === null) {
            return $this->_connection;
        }

        return $this->_connection = $conn;
    }

    /**
     * Initialize method
     *
     * @param array $config The configuration for the Table.
     *
     * @return void
     */
    public function initialize(array $config)
    {
        $this->connection(ConnectionManager::get(static::$_connectionName));
    }

    /**
     * Call CopyObject API.
     *
     * @see S3Client::copyObject
     * @see http://docs.aws.amazon.com/aws-sdk-php/v3/api/api-s3-2006-03-01.html#copyobject
     *
     * @param string $srcKey
     * @param string $destKey
     * @param array  $options
     *
     * @return \Aws\Result
     */
    public function copyObject($srcKey, $destKey, array $options = [])
    {
        return $this->connection()->copyObject($srcKey, $destKey, $options);
    }

    /**
     * Call DeleteObject API.
     *
     * @see S3Client::deleteObject
     * @see http://docs.aws.amazon.com/aws-sdk-php/v3/api/api-s3-2006-03-01.html#deleteobject
     *
     * @param string $key
     * @param array  $options
     *
     * @return \Aws\Result
     */
    public function deleteObject($key, array $options = [])
    {
        return $this->connection()->deleteObject($key, $options);
    }

    /**
     * Call DeleteObjects API.
     *
     * @see S3Client::deleteObjects
     * @see http://docs.aws.amazon.com/aws-sdk-php/v3/api/api-s3-2006-03-01.html#deleteobjects
     *
     * @param array $keys
     * @param array $options
     *
     * @return \Aws\Result
     */
    public function deleteObjects($keys, array $options = [])
    {
        return $this->connection()->deleteObjects($keys, $options);
    }

    /**
     * Call doesObjectExists API.
     *
     * @see S3Client::doesObjectExist
     * @see http://docs.aws.amazon.com/aws-sdk-php/v3/api/api-s3-2006-03-01.html#headobject
     *
     * @param string $key
     * @param array  $options
     *
     * @return bool
     */
    public function doesObjectExist($key, array $options = [])
    {
        return $this->connection()->doesObjectExist($key, $options);
    }

    /**
     * Call GetObject API.
     *
     * @see S3Client::getObject
     * @see http://docs.aws.amazon.com/aws-sdk-php/v3/api/api-s3-2006-03-01.html#getobject
     *
     * @param string $key
     * @param array  $options
     *
     * @return \Aws\Result
     */
    public function getObject($key, array $options = [])
    {
        return $this->connection()->getObject($key, $options);
    }

    /**
     * Call HeadObject API.
     *
     * @see S3Client::headObject
     * @see http://docs.aws.amazon.com/aws-sdk-php/v3/api/api-s3-2006-03-01.html#headobject
     *
     * @param string $key
     * @param array  $options
     *
     * @return \Aws\Result
     */
    public function headObject($key, array $options = [])
    {
        return $this->connection()->headObject($key, $options);
    }

    /**
     * Call PutObject API.
     *
     * @see S3Client::putObject
     * @see http://docs.aws.amazon.com/aws-sdk-php/v3/api/api-s3-2006-03-01.html#putobject
     *
     * @param string $key
     * @param string $content
     * @param array  $options
     *
     * @return \Aws\Result
     */
    public function putObject($key, $content, array $options = [])
    {
        return $this->connection()->putObject($key, $content, $options);
    }

    /**
     * Call GetObject API and get Body attribute.
     *
     * @see S3Client::getObject
     * @see http://docs.aws.amazon.com/aws-sdk-php/v3/api/api-s3-2006-03-01.html#getobject
     *
     * @param string $key
     * @param array  $options
     *
     * @return \GuzzleHttp\Psr7\Stream
     */
    public function getObjectBody($key, array $options = [])
    {
        return $this->getObject($key, $options)->get('Body');
    }

    /**
     * To mimic the moving Object using CopyObject API and DeleteObject API.
     *
     * @see S3Client::copyObject
     * @see S3Client::deleteObject
     * @see http://docs.aws.amazon.com/aws-sdk-php/v3/api/api-s3-2006-03-01.html#copyobject
     * @see http://docs.aws.amazon.com/aws-sdk-php/v3/api/api-s3-2006-03-01.html#deleteobject
     *
     * @param string $srcKey
     * @param string $destKey
     * @param array  $options
     *
     * @return \Aws\Result Return the CopyObject API result.
     */
    public function moveObject($srcKey, $destKey, array $options = [])
    {
        $result = $this->copyObject($srcKey, $destKey, $options);
        $this->deleteObject($srcKey);

        return $result;
    }
}
