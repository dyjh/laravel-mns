# Aliyun MNS Queue Driver For Laravel 6.0

[![Latest Version on Packagist][ico-version]][link-packagist]
[![Software License][ico-license]](LICENSE.md)
[![Total Downloads][ico-downloads]][link-downloads]


The Laravel adaptation of aliyun messaging service (MNS) is essentially the addition of MNS drivers to Laravel's queues. Includes aliyun MNS SDK, which is necessary for Laravel to use MNS transparently.

 > Changes from [lokielse/laravel-mns](https://github.com/lokielse/laravel-mns) to modify some of the content added manually call news release, added the laravel 6.0 support.

## Install

Via Composer

``` bash
$ composer require dyjh/laravel-mns
```

## Config

Add following service providers into your providers array in `config/app.php`

``` php
Dyjh\LaravelMNS\LaravelMNSServiceProvider::class
```

Edit your `config/queue.php`, add `mns` connection

```php
'mns'        => [
	'driver'       => 'mns',
	'key'          => env('QUEUE_MNS_ACCESS_KEY'),
	'secret'       => env('QUEUE_MNS_SECRET_KEY'),
	'endpoint'     => env('QUEUE_MNS_ENDPOINT'),
	'queue'        => env('QUEUE_NAME'),
	'wait_seconds' => 30,
    'receiveController' => ReceiveController::class,
]
```
About [wait_seconds](https://help.aliyun.com/document_detail/35136.html)

Edit your `.env` file

```bash
QUEUE_DRIVER=mns
QUEUE_NAME=foobar-local
QUEUE_MNS_ACCESS_KEY=your_acccess_key
QUEUE_MNS_SECRET_KEY=your_secret_key
QUEUE_MNS_ENDPOINT=http://12345678910.mns.cn-hangzhou.aliyuncs.com/
```
You should update `QUEUE_MNS_ENDPOINT` to `internal endpoint` in production mode
## MessageReceiver Example

About [ReceiveController], please look at [Example](ReceiveExample.php)

## Usage

First create a queue and get queue endpoint at [Aliyun MNS Console](https://mns.console.aliyun.com/)

Then update `MNS_ENDPOINT` in `.env`

Push a test message to queue

```php
Queue::push(function($job){
	/**
	 * Your statments go here
	 */
	$job->delete();
});
```

Create queue listener, run command in terminal

```bash
$ php artisan queue:listen
```
or only create the receiver queue

```bash
$ php artisan queue:work mns
```
## Commands
Flush MNS messages on Aliyun

```bash
$ php artisan queue:mns:flush
```
## Manually publish message
Send The Message to Aliyun MNS

```php
$sender = new MNSSender("test");
$res = $sender->push("testMessage");
```

## Security

Create RAM access control at [Aliyun RAM Console](https://ram.console.aliyun.com)

1. Create a custom policy such as `AliyunMNSFullAccessFoobar`

	```
	{
	  "Version": "1",
	  "Statement": [
		{
		  "Action": "mns:*",
		  "Resource": [
			"acs:mns:*:*:*/foobar-local",
			"acs:mns:*:*:*/foobar-sandbox",
			"acs:mns:*:*:*/foobar-production"
		  ],
		  "Effect": "Allow"
		}
	  ]
	}
	```

2. Create a user for you app such as `foobar`

3. Assign the policy `AliyunMNSFullAccessFoobar` to the user `foobar`

4. Create and get the `AccessKeyId` and `AccessKeySecret` for user `foorbar`

5. update `QUEUE_MNS_ACCESS_KEY` and `QUEUE_MNS_ACCESS_SECRET` in `.env`

## Testing

``` bash
$ composer test
```

## License

The MIT License (MIT). Please see [License File](LICENSE.md) for more information.

[ico-version]: https://img.shields.io/packagist/v/dyjh/laravel-mns.svg?style=flat-square
[ico-license]: https://img.shields.io/badge/license-MIT-brightgreen.svg?style=flat-square
[ico-downloads]: https://img.shields.io/packagist/dt/dyjh/laravel-mns.svg?style=flat-square

[link-packagist]: https://packagist.org/packages/dyjh/laravel-mns
[link-downloads]: https://packagist.org/packages/dyjh/laravel-mns
[link-author]: https://github.com/dyjh
