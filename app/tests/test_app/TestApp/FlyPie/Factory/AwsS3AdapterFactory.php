<?php
declare(strict_types=1);

namespace TestApp\FlyPie\Factory;

use Aws\S3\S3Client;
use League\Flysystem\AwsS3V3\AwsS3V3Adapter;
use RuntimeException;

/**
 * Class AwsS3AdapterFactory
 *
 * @package TestApp\FlyPie\Factory
 */
class AwsS3AdapterFactory
{
    /**
     * Return new Adapter
     *
     * @param array $config list of options parsed from dsn
     * @return \League\Flysystem\AwsS3V3\AwsS3V3Adapter
     */
    public static function client(array $config): AwsS3V3Adapter
    {
        $defaults = [
            'path' => '',
        ];
        $config += $defaults;
        if (!isset($config['host'])) {
            throw new RuntimeException('Expected configuration key "host" not found.');
        }
        $bucket = $config['host'];
        $prefix = $config['path'];
        unset($config['className'], $config['scheme'], $config['host'], $config['path']);

        return new AwsS3V3Adapter(
            self::createS3Client($config),
            $bucket,
            $prefix
        );
    }

    /**
     * オプションの処理を行う
     * 非AWS環境用。AWS環境ではEC2 Roleを利用する。
     *
     * @param array $config \Aws\S3\S3Clientのオプション配列
     * @return array
     */
    public static function getS3ClientOptions(array $config): array
    {
        $defaults = [
            'region' => env('AWS_DEFAULT_REGION', 'ap-northeast-1'),
            'version' => 'latest',
        ];

        $config += $defaults;
        if (!empty($config['username']) && !empty($config['password'])) {
            $config['credentials'] = [
                'key' => $config['username'],
                'secret' => $config['password'],
            ];
            unset($config['username'], $config['password']);
        }

        if (
            isset($config['http']['verify'])
            && in_array($config['http']['verify'], ['true', 'false'], true)
        ) {
            $config['http']['verify'] = filter_var($config['http']['verify'], FILTER_VALIDATE_BOOLEAN);
        }

        return $config;
    }

    /**
     * Create S3 Client instance.
     *
     * @param array $options S3Client options
     * @return \Aws\S3\S3Client
     */
    public static function createS3Client(array $options = []): S3Client
    {
        return new S3Client(self::getS3ClientOptions($options));
    }
}
