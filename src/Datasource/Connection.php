<?php
namespace CakeS3\Datasource;

use Aws\S3\S3Client;
use Cake\Datasource\ConnectionInterface;

/**
 * Class S3 Connection
 *
 * @package CakeS3\Database
 */
class Connection implements ConnectionInterface
{
    /** @var array Connection configure parameter */
    protected $_config = [];

    /** @var \Aws\S3\S3Client|null */
    protected $_s3Client = null;

    /**
     * Connection constructor.
     *
     * @param array $config
     */
    public function __construct(array $config = [])
    {
        if (empty($config['key']) || empty($config['secret']) ||
            empty($config['region']) || empty($config['bucketName'])
        ) {
            throw new \InvalidArgumentException('Config "key" or "secret" or "region" or "bucketName" missing.');
        }

        $this->_config = $config;

        $this->_s3Client = new S3Client([
            'credentials' => [
                'key'    => $this->_config['key'],
                'secret' => $this->_config['secret'],
            ],
            'region'      => $this->_config['region'],
            'version'     => '2006-03-01',
        ]);
        $this->_s3Client->registerStreamWrapper();

        if ($this->_s3Client->doesBucketExist($this->_config['bucketName']) === false) {
            throw new \InvalidArgumentException("Bucket '{$this->_config['bucketName']}' is not found.");
        }
    }

    /**
     * Get configure name.
     *
     * @return mixed|string
     */
    public function configName()
    {
        if (empty($this->_config['name'])) {
            return '';
        }

        return $this->_config['name'];
    }

    /**
     * Get configure
     *
     * @return array
     */
    public function config()
    {
        return $this->_config;
    }

    /**
     * This method is not supported.
     *
     * @param callable $transaction
     */
    public function transactional(callable $transaction)
    {
    }

    /**
     * This method is not supported.
     *
     * @param callable $operation
     */
    public function disableConstraints(callable $operation)
    {
    }

    /**
     * This method is not supported.
     *
     * @param callable $operation
     */
    public function logQueries($enable = null)
    {
    }

    /**
     * This method is not supported.
     *
     * @param callable $operation
     */
    public function logger($instance = null)
    {
    }

    /**
     * Pre processing to convert the key.
     * ex) '/key' => 'key'
     *
     * @param $key
     *
     * @return string
     */
    private function __keyPreProcess($key)
    {
        if (strpos($key, '/') === 0) {
            $key = substr($key, 1);
        }

        return $key;
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
        $srcKey  = $this->__keyPreProcess($srcKey);
        $destKey = $this->__keyPreProcess($destKey);

        $options += [
            'Bucket'     => $this->_config['bucketName'],
            'Key'        => $destKey,
            'CopySource' => $this->_config['bucketName'] . '/' . $srcKey,
            'ACL'        => 'public-read',
        ];

        return $this->_s3Client->copyObject($options);
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
        $key = $this->__keyPreProcess($key);

        $options += [
            'Bucket' => $this->_config['bucketName'],
            'Key'    => $key,
        ];

        return $this->_s3Client->deleteObject($options);
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
        foreach ($keys as $index => $key) {
            $keys[$index] = [
                'Key' => $this->__keyPreProcess($key),
            ];
        }

        $options += [
            'Bucket' => $this->_config['bucketName'],
            'Delete' => [
                'Objects' => $keys,
            ],
        ];

        return $this->_s3Client->deleteObjects($options);
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
        $key = $this->__keyPreProcess($key);

        return $this->_s3Client->doesObjectExist(
            $this->_config['bucketName'],
            $key,
            $options
        );
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
        $key = $this->__keyPreProcess($key);

        $options += [
            'Bucket' => $this->_config['bucketName'],
            'Key'    => $key,
            'ACL'    => 'public-read',
        ];

        return $this->_s3Client->getObject($options);
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
        $key = $this->__keyPreProcess($key);

        $options += [
            'Bucket' => $this->_config['bucketName'],
            'Key'    => $key,
        ];

        return $this->_s3Client->headObject($options);
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
        $key = $this->__keyPreProcess($key);

        $options += [
            'Bucket' => $this->_config['bucketName'],
            'Key'    => $key,
            'ACL'    => 'public-read',
            'Body'   => $content,
        ];

        return $this->_s3Client->putObject($options);
    }
}

