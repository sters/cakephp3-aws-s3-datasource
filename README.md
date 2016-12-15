# CakePHP3 AWS S3 Datasource

[![CircleCI](https://circleci.com/gh/sters/cakephp3-aws-s3-datasource/tree/master.svg?style=svg)](https://circleci.com/gh/sters/cakephp3-aws-s3-datasource/tree/master)

This CakePHP 3.0 plugin provides AWS S3 Datasource.


## Installation

Install Plugin using composer.

```
$ composer require "sters/cakephp3-aws-s3-datasource:dev-master"
```

Add `Plugin::load()` on your `config/bootstrap.php`.

```
Plugin::load('CakeS3');
```

Add S3 Datasource on your `config/app.php`.

one Datasrouce has one S3 Bucket connection.
**You can not across bucket processing**.

```
'Datasource' => [
    'my_s3_connection' => [
        'className'  => 'CakeS3\Datasource\Connection',
        'key'        => 'put your s3 access key',
        'secret'     => 'put your s3 access secret',
        'bucketName' => 'put your bucket name',
    ],
],
```


Setup new table using s3 connection.

`$_connectionName` is your wrote connection name in `config/app.php` Datasource.

```
<?php
namespace App\Model\Table\;

use CakeS3\Datasource\AwsS3Table;

class MyS3Table extends AwsS3Table
{
    protected static $_connectionName = 'my_s3_connection';
}
```

For example, declare action of get & show S3 Object. 

```
class TopController extends Controller
{
    public function index()
    {
        $MyS3Table = TableRegistry::get('MyS3');
        $content = $MyS3Table->getObjectBody('/path/to/object/file.jpg');
        
        $this->response->type('image/jpeg');
        $this->response->body($content);
        
        return $this->response;
    }
}
```


## Documentation

### AwsS3Table support methods

The methods can call on your S3 Table.

If You want more detail, go to S3Client document.  
[http://docs.aws.amazon.com/aws-sdk-php/v3/api/class-Aws.S3.S3Client.html](http://docs.aws.amazon.com/aws-sdk-php/v3/api/class-Aws.S3.S3Client.html)

#### ```copyObject(string $srcKey, string $destKey, array $options = []) : \Aws\Result```
#### ```deleteObject(string $key, array $options = []) : \Aws\Result```
#### ```deleteObjects(string $keys, array $options = []) : \Aws\Result```
#### ```doesObjectExist(string $key, array $options = []) : bool```
#### ```getObject(string $key, array $options = []) : \Aws\Result```
#### ```headObject(string $key, array $options = []) : \Aws\Result```
#### ```putObject(string $key, $content, array $options = []) : \Aws\Result```
#### ```getObjectBody(string $key, array $options = []) : \GuzzleHttp\Psr7\Stream```
#### ```moveObject(string $srcKey, string $destKey, array $options = []) : \Aws\Result```

