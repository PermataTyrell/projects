<?php
declare(strict_types=1);

namespace App\Test\TestCase\Lib\Storage;

use Laminas\Diactoros\UploadedFile;
use PHPUnit\Framework\TestCase;
use TestApp\Lib\Storage\FileStorage;

/**
 * TestApp\Lib\Storage\FileStorage Test Case
 */
class FileStorageTest extends TestCase
{
    private const IMG_PATH = TESTS . 'test_app' . DS . 'TestApp' . DS . 'resources' . DS . 'images' . DS . 'cakephp.png';
    private FileStorage $storage;

    protected function setUp(): void
    {
        $this->storage = new FileStorage([
            'username' => 'AKIAEXAMPLEEXAMPLEEX', // MinIO access key
            'password' => 'EXAMPLESECRETKEY/EXAMPLESECRETKEY/EXAMPL', // MinIO secret key
            'host' => 'test-private',
            'endpoint' => 'http://minio:9000', // MinIO endpoint
            'use_path_style_endpoint' => true, // Ensures proper path resolution of MinIO
        ]);
    }

    /**
     * Test Write
     *
     * @return void
     */
    public function testWriteSuccess(): void
    {
        $result = $this->storage->write('test.txt', 'Sample content');
        $this->assertTrue($result, 'The file was not written successfully.');
    }

    /**
     * Test Write Stream
     *
     * @return void
     */
    public function testWriteStream(): void
    {
        // Create a temporary file to simulate an uploaded file
        $tempFile = tmpfile();
        fwrite($tempFile, 'This is a test file content.');
        rewind($tempFile);

        // Simulate an uploaded file
        $uploadedFile = new UploadedFile($tempFile, 28, UPLOAD_ERR_OK, 'test.txt', 'text/plain');

        // Write the file to the storage
        $stream = $uploadedFile->getStream()->detach();
        $result = $this->storage->writeStream('test.txt', $stream);

        // Assert the write operation
        $this->assertTrue($result, 'The file stream was not written successfully.');

        // Cleanup
        fclose($tempFile);
    }

    /**
     * Test Write Stream with image
     *
     * @return void
     */
    public function testWriteStreamWithImage(): void
    {
        // Verify the test image exists
        $this->assertFileExists(self::IMG_PATH, 'Test image file does not exist.');

        // Open the image file as a resource
        $resource = fopen(self::IMG_PATH, 'r');

        // Write the file to storage
        $result = $this->storage->writeStream('cakephp.png', $resource);

        // Assert the write operation
        $this->assertTrue($result, 'The image was not written successfully.');

        // Cleanup
        fclose($resource);
    }

    /**
     * Test Put
     *
     * @return void
     */
    public function testPutSuccess(): void
    {
        $this->storage->write('test.txt', 'Sample content');
        $destination = '/tmp/test-download.txt';

        $result = $this->storage->put('test.txt', $destination);
        $this->assertTrue($result);
        $this->assertFileExists($destination);
        $this->assertSame('Sample content', file_get_contents($destination));

        unlink($destination);
    }

    /**
     * Test Read
     *
     * @return void
     */
    public function testReadSuccess(): void
    {
        $this->storage->write('test.txt', 'Sample content');
        $result = $this->storage->read('test.txt');
        $this->assertSame('Sample content', $result, 'Fail to read the original file.');
    }

    /**
     * Test Read Stream
     *
     * @return void
     */
    public function testReadStream(): void
    {
        $this->storage->write('test.txt', 'This is a test file content.');
        // Path to the file in storage
        $path = 'test.txt';

        // Read the file from storage
        $stream = $this->storage->readStream($path);

        // Assert the read operation
        $this->assertIsResource($stream, 'The file stream was not retrieved successfully.');

        // Read content from the stream and assert
        $content = stream_get_contents($stream);
        $this->assertEquals('This is a test file content.', $content, 'File content does not match.');

        // Cleanup
        fclose($stream);
    }

    /**
     * Test Read Stream with image
     *
     * @return void
     */
    public function testReadStreamWithImage(): void
    {
        // Path to the file in storage
        $path = 'cakephp.png';

        // Read the file from storage
        $stream = $this->storage->readStream($path);

        // Assert the read operation
        $this->assertIsResource($stream, 'The stream was not retrieved successfully.');

        // Read content from the stream and assert
        $content = stream_get_contents($stream);

        // Verify the original image's content matches the retrieved content
        $expectedContent = file_get_contents(self::IMG_PATH);
        $this->assertEquals($expectedContent, $content, 'The image content does not match.');

        // Cleanup
        fclose($stream);
        $this->storage->delete('cakephp.png');
    }

    /**
     * Test Delete
     *
     * @return void
     */
    public function testDeleteSuccess(): void
    {
        $this->storage->write('test.txt', 'Sample content');
        $result = $this->storage->delete('test.txt');
        $this->assertTrue($result, 'Fail to delete the file.');
    }

    /**
     * Test File Exists
     *
     * @return void
     */
    public function testFileExists(): void
    {
        $this->storage->write('test.txt', 'Sample content');
        $result = $this->storage->fileExists('test.txt');
        $this->assertTrue($result, 'The file does not exists in the storage.');
    }

    /**
     * Data provider for list contents tests
     *
     * @return array
     */
    public function fileListProvider(): array
    {
        return [
            ['file1.txt', 'Content 1'],
            ['file2.txt', 'Content 2'],
            ['file3.txt', 'Content 3'],
        ];
    }

    /**
     * Test List Contents
     *
     * @dataProvider fileListProvider
     */
    public function testListContents(string $fileName, string $content): void
    {
        // Write the file
        $this->storage->write($fileName, $content);

        // Get list of files
        $files = $this->storage->listContents();

        // Assert the file is in the list
        $this->assertContains($fileName, $files, "Failed asserting that {$fileName} is in the list.");

        // Cleanup
        $this->storage->delete($fileName);
    }

    /**
     * tearDown method
     *
     * @return void
     */
    protected function tearDown(): void
    {
        $this->storage->delete('test.txt'); // Cleanup test file
    }
}
