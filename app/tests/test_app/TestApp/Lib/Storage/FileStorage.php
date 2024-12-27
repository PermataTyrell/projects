<?php
declare(strict_types=1);

namespace TestApp\Lib\Storage;

use League\Flysystem\Filesystem;
use League\Flysystem\FilesystemException;
use TestApp\FlyPie\Factory\AwsS3AdapterFactory;

/**
 * Class FileStorage
 */
class FileStorage
{
    protected Filesystem $filesystem;

    public function __construct(array $config)
    {
        $factory = new AwsS3AdapterFactory();
        $adapter = $factory::client($config);
        $this->filesystem = new Filesystem($adapter);
    }

    /**
     * Write contents to a file in storage
     *
     * @param string $destinationFile Path to destination file
     * @param string $contents File content
     * @return bool
     */
    public function write(string $destinationFile, string $contents): bool
    {
        try {
            $this->filesystem->write($destinationFile, $contents);

            return true;
        } catch (FilesystemException $e) {
            return false;
        }
    }

    /**
     * Write file stream to a file in storage
     *
     * @param string $destinationFile Path to destination file
     * @param resource $resource File handle resource
     * @return bool
     */
    public function writeStream(string $destinationFile, $resource): bool
    {
        try {
            $this->filesystem->writeStream($destinationFile, $resource);

            return true;
        } catch (FilesystemException $e) {
            return false;
        }
    }

    /**
     * Put file to destination
     *
     * @param string $sourceFile Path to source file
     * @param string $destination Path to destination file
     * @return bool
     */
    public function put(string $sourceFile, string $destination): bool
    {
        try {
            $contents = $this->read($sourceFile);
            if ($contents !== null) {
                file_put_contents($destination, $contents);

                return true;
            }

            return false;
        } catch (\Exception $e) {
            return false;
        }
    }

    /**
     * Read the content of a file in storage
     *
     * @param string $path Path
     * @return string|null
     */
    public function read(string $path): ?string
    {
        try {
            return $this->filesystem->read($path);
        } catch (FilesystemException $e) {
            return null;
        }
    }

    /**
     * Stream out the file in storage
     *
     * @param string $path Path
     * @return resource|false
     */
    public function readStream(string $path)
    {
        try {
            return $this->filesystem->readStream($path);
        } catch (FilesystemException $e) {
            return false;
        }
    }

    /**
     * Delete file from storage
     *
     * @param string $location File's location
     * @return bool
     */
    public function delete(string $location): bool
    {
        try {
            $this->filesystem->delete($location);

            return true;
        } catch (FilesystemException $e) {
            return false;
        }
    }

    /**
     * Check file is exists
     *
     * @param string $location File's location
     * @return bool
     */
    public function fileExists(string $location): bool
    {
        try {
            return $this->filesystem->fileExists($location);
        } catch (FilesystemException $e) {
            return false;
        }
    }

    /**
     * List contents
     *
     * @param string $prefix
     * @return array
     */
    public function listContents(string $prefix = ''): array
    {
        try {
            $listing = $this->filesystem->listContents($prefix);
            $files = [];
            /** @var \League\Flysystem\StorageAttributes $item */
            foreach ($listing as $item) {
                if ($item->isFile()) {
                    $files[] = $item->path();
                }
            }

            return $files;
        } catch (FilesystemException $e) {
            return [];
        }
    }
}
