<?php

/**
 *
 *
 * Author:  Asror Zakirov
 * https://www.linkedin.com/in/asror-zakirov
 * https://github.com/asror-z
 *
 */

namespace zetsoft\system\helpers;


use Exception;
use PHPUnit\Framework\Assert;
use PHPUnit\Framework\Constraint\ArrayHasKey;
use PHPUnit\Framework\Constraint\Callback;
use PHPUnit\Framework\Constraint\ClassHasAttribute;
use PHPUnit\Framework\Constraint\ClassHasStaticAttribute;
use PHPUnit\Framework\Constraint\Constraint;
use PHPUnit\Framework\Constraint\Count;
use PHPUnit\Framework\Constraint\DirectoryExists;
use PHPUnit\Framework\Constraint\FileExists;
use PHPUnit\Framework\Constraint\GreaterThan;
use PHPUnit\Framework\Constraint\IsAnything;
use PHPUnit\Framework\Constraint\IsEmpty;
use PHPUnit\Framework\Constraint\IsEqual;
use PHPUnit\Framework\Constraint\IsEqualCanonicalizing;
use PHPUnit\Framework\Constraint\IsEqualIgnoringCase;
use PHPUnit\Framework\Constraint\IsEqualWithDelta;
use PHPUnit\Framework\Constraint\IsFalse;
use PHPUnit\Framework\Constraint\IsFinite;
use PHPUnit\Framework\Constraint\IsIdentical;
use PHPUnit\Framework\Constraint\IsInfinite;
use PHPUnit\Framework\Constraint\IsInstanceOf;
use PHPUnit\Framework\Constraint\IsJson;
use PHPUnit\Framework\Constraint\IsNan;
use PHPUnit\Framework\Constraint\IsNull;
use PHPUnit\Framework\Constraint\IsReadable;
use PHPUnit\Framework\Constraint\IsTrue;
use PHPUnit\Framework\Constraint\IsType;
use PHPUnit\Framework\Constraint\IsWritable;
use PHPUnit\Framework\Constraint\LessThan;
use PHPUnit\Framework\Constraint\LogicalAnd;
use PHPUnit\Framework\Constraint\LogicalNot;
use PHPUnit\Framework\Constraint\LogicalOr;
use PHPUnit\Framework\Constraint\LogicalXor;
use PHPUnit\Framework\Constraint\ObjectHasAttribute;
use PHPUnit\Framework\Constraint\RegularExpression;
use PHPUnit\Framework\Constraint\StringContains;
use PHPUnit\Framework\Constraint\StringEndsWith;
use PHPUnit\Framework\Constraint\StringMatchesFormatDescription;
use PHPUnit\Framework\Constraint\StringStartsWith;
use PHPUnit\Framework\Constraint\TraversableContainsEqual;
use PHPUnit\Framework\Constraint\TraversableContainsIdentical;
use PHPUnit\Framework\Constraint\TraversableContainsOnly;
use PHPUnit\Framework\ExpectationFailedException;
use zetsoft\system\Az;

class ZTest extends Assert
{
    #region Utils


    public static function handler(Exception $e)
    {
        $trace = Az::$app->smart->tester->clearTrace($e->getTrace());

        if (ZArrayHelper::keyExists('failedTests', Az::$app->params))
            Az::$app->params['failedTests'] = ++Az::$app->params['failedTests'];
        else
            Az::$app->params['failedTests'] = 1;


        Az::$app->utility->execs->exec('echo [31m"Error: ' . $trace[0]['file'] . ':' . $trace[0]['line'] . '[39m"' . EOL);
    }

    public static function sayOk()
    {
        Az::$app->params['successTests'] = ++Az::$app->params['successTests'];
        $trace = Az::$app->smart->tester->clearTrace(trace(true));
        Az::$app->utility->execs->exec('echo [32mSuccess: ' . $trace[0]['file'] . ':' . $trace[0]['line'] . '[39m' . EOL);
    }
    #endregion
    #region 

    public static function assertArrayHasKey($key, $array, string $message = ''): void
    {
        try {
            parent::assertArrayHasKey($key, $array, $message);
            self::sayOk();
        } catch (Exception $e) {
            self::handler($e);
        }
    }

    public static function assertArrayNotHasKey($key, $array, string $message = ''): void
    {
        try {
            parent::assertArrayNotHasKey($key, $array, $message);
            self::sayOk();
        } catch (Exception $e) {
            self::handler($e);
        }
    }

    public static function assertContains($needle, iterable $haystack, string $message = ''): void
    {
        try {
            parent::assertContains($needle, $haystack, $message);
            self::sayOk();
        } catch (Exception $e) {
            self::handler($e);
        }
    }

    public static function assertContainsEquals($needle, iterable $haystack, string $message = ''): void
    {
        try {
            parent::assertContainsEquals($needle, $haystack, $message);
            self::sayOk();
        } catch (Exception $e) {
            self::handler($e);
        }
    }

    public static function assertNotContains($needle, iterable $haystack, string $message = ''): void
    {
        try {
            parent::assertNotContains($needle, $haystack, $message);
            self::sayOk();
        } catch (Exception $e) {
            self::handler($e);
        }
    }

    public static function assertNotContainsEquals($needle, iterable $haystack, string $message = ''): void
    {
        try {
            parent::assertNotContainsEquals($needle, $haystack, $message);
            self::sayOk();
        } catch (Exception $e) {
            self::handler($e);
        }
    }

    public static function assertContainsOnly(string $type, iterable $haystack, ?bool $isNativeType = null, string $message = ''): void
    {
        try {
            parent::assertContainsOnly($type, $haystack, $isNativeType, $message);
            self::sayOk();
        } catch (Exception $e) {
            self::handler($e);
        }
    }

    public static function assertContainsOnlyInstancesOf(string $className, iterable $haystack, string $message = ''): void
    {
        try {
            parent::assertContainsOnlyInstancesOf($className, $haystack, $message);
            self::sayOk();
        } catch (Exception $e) {
            self::handler($e);
        }
    }

    public static function assertNotContainsOnly(string $type, iterable $haystack, ?bool $isNativeType = null, string $message = ''): void
    {
        try {
            parent::assertNotContainsOnly($type, $haystack, $isNativeType, $message);
            self::sayOk();
        } catch (Exception $e) {
            self::handler($e);
        }
    }

    public static function assertCount(int $expectedCount, $haystack, string $message = ''): void
    {
        try {
            parent::assertCount($expectedCount, $haystack, $message);
            self::sayOk();
        } catch (Exception $e) {
            self::handler($e);
        }
    }

    public static function assertNotCount(int $expectedCount, $haystack, string $message = ''): void
    {
        try {
            parent::assertNotCount($expectedCount, $haystack, $message);
            self::sayOk();
        } catch (Exception $e) {
            self::handler($e);
        }
    }

    public static function assertEqualsCanonicalizing($expected, $actual, string $message = ''): void
    {
        try {
            parent::assertEqualsCanonicalizing($expected, $actual, $message);
            self::sayOk();
        } catch (Exception $e) {
            self::handler($e);
        }
    }

    public static function assertEqualsIgnoringCase($expected, $actual, string $message = ''): void
    {
        try {
            parent::assertEqualsIgnoringCase($expected, $actual, $message);
            self::sayOk();
        } catch (Exception $e) {
            self::handler($e);
        }
    }

    public static function assertEqualsWithDelta($expected, $actual, float $delta, string $message = ''): void
    {
        try {
            parent::assertEqualsWithDelta($expected, $actual, $delta, $message);
            self::sayOk();
        } catch (Exception $e) {
            self::handler($e);
        }
    }

    public static function assertNotEquals($expected, $actual, string $message = ''): void
    {
        try {
            parent::assertNotEquals($expected, $actual, $message);
            self::sayOk();
        } catch (Exception $e) {
            self::handler($e);
        }
    }

    public static function assertNotEqualsCanonicalizing($expected, $actual, string $message = ''): void
    {
        try {
            parent::assertNotEqualsCanonicalizing($expected, $actual, $message);
            self::sayOk();
        } catch (Exception $e) {
            self::handler($e);
        }
    }

    public static function assertNotEqualsIgnoringCase($expected, $actual, string $message = ''): void
    {
        try {
            parent::assertNotEqualsIgnoringCase($expected, $actual, $message);
            self::sayOk();
        } catch (Exception $e) {
            self::handler($e);
        }
    }

    public static function assertNotEqualsWithDelta($expected, $actual, float $delta, string $message = ''): void
    {
        try {
            parent::assertNotEqualsWithDelta($expected, $actual, $delta, $message);
            self::sayOk();
        } catch (Exception $e) {
            self::handler($e);
        }
    }

    public static function assertEmpty($actual, string $message = ''): void
    {
        try {
            parent::assertEmpty($actual, $message);
            self::sayOk();
        } catch (Exception $e) {
            self::handler($e);
        }
    }

    public static function assertNotEmpty($actual, string $message = ''): void
    {
        try {
            parent::assertNotEmpty($actual, $message);
            self::sayOk();
        } catch (Exception $e) {
            self::handler($e);
        }
    }

    public static function assertGreaterThan($expected, $actual, string $message = ''): void
    {
        try {
            parent::assertGreaterThan($expected, $actual, $message);
            self::sayOk();
        } catch (Exception $e) {
            self::handler($e);
        }
    }

    public static function assertGreaterThanOrEqual($expected, $actual, string $message = ''): void
    {
        try {
            parent::assertGreaterThanOrEqual($expected, $actual, $message);
            self::sayOk();
        } catch (Exception $e) {
            self::handler($e);
        }
    }

    public static function assertLessThan($expected, $actual, string $message = ''): void
    {
        try {
            parent::assertLessThan($expected, $actual, $message);
            self::sayOk();
        } catch (Exception $e) {
            self::handler($e);
        }
    }

    public static function assertLessThanOrEqual($expected, $actual, string $message = ''): void
    {
        try {
            parent::assertLessThanOrEqual($expected, $actual, $message);
            self::sayOk();
        } catch (Exception $e) {
            self::handler($e);
        }
    }

    public static function assertFileEquals(string $expected, string $actual, string $message = ''): void
    {
        try {
            parent::assertFileEquals($expected, $actual, $message);
            self::sayOk();
        } catch (Exception $e) {
            self::handler($e);
        }
    }

    public static function assertFileEqualsCanonicalizing(string $expected, string $actual, string $message = ''): void
    {
        try {
            parent::assertFileEqualsCanonicalizing($expected, $actual, $message);
            self::sayOk();
        } catch (Exception $e) {
            self::handler($e);
        }
    }

    public static function assertFileEqualsIgnoringCase(string $expected, string $actual, string $message = ''): void
    {
        try {
            parent::assertFileEqualsIgnoringCase($expected, $actual, $message);
            self::sayOk();
        } catch (Exception $e) {
            self::handler($e);
        }
    }

    public static function assertFileNotEquals(string $expected, string $actual, string $message = ''): void
    {
        try {
            parent::assertFileNotEquals($expected, $actual, $message);
            self::sayOk();
        } catch (Exception $e) {
            self::handler($e);
        }
    }

    public static function assertFileNotEqualsCanonicalizing(string $expected, string $actual, string $message = ''): void
    {
        try {
            parent::assertFileNotEqualsCanonicalizing($expected, $actual, $message);
            self::sayOk();
        } catch (Exception $e) {
            self::handler($e);
        }
    }

    public static function assertFileNotEqualsIgnoringCase(string $expected, string $actual, string $message = ''): void
    {
        try {
            parent::assertFileNotEqualsIgnoringCase($expected, $actual, $message);
            self::sayOk();
        } catch (Exception $e) {
            self::handler($e);
        }
    }

    public static function assertStringEqualsFile(string $expectedFile, string $actualString, string $message = ''): void
    {
        try {
            parent::assertStringEqualsFile($expectedFile, $actualString, $message);
            self::sayOk();
        } catch (Exception $e) {
            self::handler($e);
        }
    }

    public static function assertStringEqualsFileCanonicalizing(string $expectedFile, string $actualString, string $message = ''): void
    {
        try {
            parent::assertStringEqualsFileCanonicalizing($expectedFile, $actualString, $message);
            self::sayOk();
        } catch (Exception $e) {
            self::handler($e);
        }
    }

    public static function assertStringEqualsFileIgnoringCase(string $expectedFile, string $actualString, string $message = ''): void
    {
        try {
            parent::assertStringEqualsFileIgnoringCase($expectedFile, $actualString, $message);
            self::sayOk();
        } catch (Exception $e) {
            self::handler($e);
        }
    }

    public static function assertStringNotEqualsFile(string $expectedFile, string $actualString, string $message = ''): void
    {
        try {
            parent::assertStringNotEqualsFile($expectedFile, $actualString, $message);
            self::sayOk();
        } catch (Exception $e) {
            self::handler($e);
        }
    }

    public static function assertStringNotEqualsFileCanonicalizing(string $expectedFile, string $actualString, string $message = ''): void
    {
        try {
            parent::assertStringNotEqualsFileCanonicalizing($expectedFile, $actualString, $message);
            self::sayOk();
        } catch (Exception $e) {
            self::handler($e);
        }
    }

    public static function assertStringNotEqualsFileIgnoringCase(string $expectedFile, string $actualString, string $message = ''): void
    {
        try {
            parent::assertStringNotEqualsFileIgnoringCase($expectedFile, $actualString, $message);

            self::sayOk();
        } catch (Exception $e) {
            self::handler($e);
        }
    }

    public static function assertIsReadable(string $filename, string $message = ''): void
    {
        try {
            parent::assertIsReadable($filename, $message);

            self::sayOk();
        } catch (Exception $e) {
            self::handler($e);
        }
    }

    public static function assertIsNotReadable(string $filename, string $message = ''): void
    {
        try {
            parent::assertIsNotReadable($filename, $message);

            self::sayOk();
        } catch (Exception $e) {
            self::handler($e);
        }
    }

    public static function assertNotIsReadable(string $filename, string $message = ''): void
    {
        try {
            parent::assertNotIsReadable($filename, $message);
            self::sayOk();
        } catch (Exception $e) {
            self::handler($e);
        }

    }

    public static function assertIsWritable(string $filename, string $message = ''): void
    {
        try {
            parent::assertIsWritable($filename, $message);
            self::sayOk();
        } catch (Exception $e) {
            self::handler($e);
        }
    }

    public static function assertIsNotWritable(string $filename, string $message = ''): void
    {
        try {
            parent::assertIsNotWritable($filename, $message);
            self::sayOk();
        } catch (Exception $e) {
            self::handler($e);
        }
    }

    public static function assertNotIsWritable(string $filename, string $message = ''): void
    {
        try {
            parent::assertNotIsWritable($filename, $message);
            self::sayOk();
        } catch (Exception $e) {
            self::handler($e);
        }
    }

    public static function assertDirectoryExists(string $directory, string $message = ''): void
    {
        try {
            parent::assertDirectoryExists($directory, $message);
            self::sayOk();
        } catch (Exception $e) {
            self::handler($e);
        }
    }

    public static function assertDirectoryDoesNotExist(string $directory, string $message = ''): void
    {
        try {
            parent::assertDirectoryDoesNotExist($directory, $message);
            self::sayOk();
        } catch (Exception $e) {
            self::handler($e);
        }
    }

    public static function assertDirectoryNotExists(string $directory, string $message = ''): void
    {
        try {
            parent::assertDirectoryNotExists($directory, $message);
            self::sayOk();
        } catch (Exception $e) {
            self::handler($e);
        }
    }

    public static function assertDirectoryIsReadable(string $directory, string $message = ''): void
    {
        try {
            parent::assertDirectoryIsReadable($directory, $message);
            self::sayOk();
        } catch (Exception $e) {
            self::handler($e);
        }
    }

    public static function assertDirectoryIsNotReadable(string $directory, string $message = ''): void
    {
        try {
            parent::assertDirectoryIsNotReadable($directory, $message);
            self::sayOk();
        } catch (Exception $e) {
            self::handler($e);
        }
    }

    public static function assertDirectoryNotIsReadable(string $directory, string $message = ''): void
    {
        try {
            parent::assertDirectoryNotIsReadable($directory, $message);
            self::sayOk();
        } catch (Exception $e) {
            self::handler($e);
        }
    }

    public static function assertDirectoryIsWritable(string $directory, string $message = ''): void
    {
        try {
            parent::assertDirectoryIsWritable($directory, $message);
            self::sayOk();
        } catch (Exception $e) {
            self::handler($e);
        }

        self::sayOk();
    }

    public static function assertDirectoryIsNotWritable(string $directory, string $message = ''): void
    {
        try {
            parent::assertDirectoryIsNotWritable($directory, $message);
            self::sayOk();
        } catch (Exception $e) {
            self::handler($e);
        }
    }

    public static function assertDirectoryNotIsWritable(string $directory, string $message = ''): void
    {
        try {
            parent::assertDirectoryNotIsWritable($directory, $message);
            self::sayOk();
        } catch (Exception $e) {
            self::handler($e);
        }
    }

    public static function assertFileExists(string $filename, string $message = ''): void
    {
        try {
            parent::assertFileExists($filename, $message);
            self::sayOk();
        } catch (Exception $e) {
            self::handler($e);
        }
    }

    public static function assertFileDoesNotExist(string $filename, string $message = ''): void
    {
        try {
            parent::assertFileDoesNotExist($filename, $message);
            self::sayOk();
        } catch (Exception $e) {
            self::handler($e);
        }
    }

    public static function assertFileNotExists(string $filename, string $message = ''): void
    {
        try {
            parent::assertFileNotExists($filename, $message);
            self::sayOk();
        } catch (Exception $e) {
            self::handler($e);
        }
    }

    public static function assertFileIsReadable(string $file, string $message = ''): void
    {
        try {
            parent::assertFileIsReadable($file, $message);
            self::sayOk();
        } catch (Exception $e) {
            self::handler($e);
        }
    }

    public static function assertFileIsNotReadable(string $file, string $message = ''): void
    {
        try {
            parent::assertFileIsNotReadable($file, $message);
            self::sayOk();
        } catch (Exception $e) {
            self::handler($e);
        }
    }

    public static function assertFileNotIsReadable(string $file, string $message = ''): void
    {
        try {
            parent::assertFileNotIsReadable($file, $message);
            self::sayOk();
        } catch (Exception $e) {
            self::handler($e);
        }
    }

    public static function assertFileIsWritable(string $file, string $message = ''): void
    {
        try {
            parent::assertFileIsWritable($file, $message);
            self::sayOk();
        } catch (Exception $e) {
            self::handler($e);
        }
    }

    public static function assertFileIsNotWritable(string $file, string $message = ''): void
    {
        try {
            parent::assertFileIsNotWritable($file, $message);
            self::sayOk();
        } catch (Exception $e) {
            self::handler($e);
        }
    }

    public static function assertFileNotIsWritable(string $file, string $message = ''): void
    {
        try {
            parent::assertFileNotIsWritable($file, $message);
            self::sayOk();
        } catch (Exception $e) {
            self::handler($e);
        }
    }

    public static function assertTrue($condition, string $message = ''): void
    {
        try {
            parent::assertTrue($condition, $message);
            self::sayOk();
        } catch (Exception $e) {
            self::handler($e);
        }
    }

    public static function assertNotTrue($condition, string $message = ''): void
    {
        try {
            parent::assertNotTrue($condition, $message);
            self::sayOk();
        } catch (Exception $e) {
            self::handler($e);
        }
    }

    public static function assertFalse($condition, string $message = ''): void
    {
        try {
            parent::assertFalse($condition, $message);
            self::sayOk();
        } catch (Exception $e) {
            self::handler($e);
        }
    }

    public static function assertNotFalse($condition, string $message = ''): void
    {
        try {
            parent::assertNotFalse($condition, $message);
            self::sayOk();
        } catch (Exception $e) {
            self::handler($e);
        }
    }

    public static function assertNull($actual, string $message = ''): void
    {
        try {
            parent::assertNull($actual, $message);
            self::sayOk();
        } catch (Exception $e) {
            self::handler($e);
        }
    }

    public static function assertNotNull($actual, string $message = ''): void
    {
        try {
            parent::assertNotNull($actual, $message);
            self::sayOk();
        } catch (Exception $e) {
            self::handler($e);
        }
    }

    public static function assertFinite($actual, string $message = ''): void
    {
        try {
            parent::assertFinite($actual, $message);
            self::sayOk();
        } catch (Exception $e) {
            self::handler($e);
        }
    }

    public static function assertInfinite($actual, string $message = ''): void
    {
        try {
            parent::assertInfinite($actual, $message);
            self::sayOk();
        } catch (Exception $e) {
            self::handler($e);
        }
    }

    public static function assertNan($actual, string $message = ''): void
    {
        try {
            parent::assertNan($actual, $message);
            self::sayOk();
        } catch (Exception $e) {
            self::handler($e);
        }
    }

    public static function assertClassHasAttribute(string $attributeName, string $className, string $message = ''): void
    {
        try {
            parent::assertClassHasAttribute($attributeName, $className, $message);
            self::sayOk();
        } catch (Exception $e) {
            self::handler($e);
        }
    }

    public static function assertClassNotHasAttribute(string $attributeName, string $className, string $message = ''): void
    {
        try {
            parent::assertClassNotHasAttribute($attributeName, $className, $message);
            self::sayOk();
        } catch (Exception $e) {
            self::handler($e);
        }
    }

    public static function assertClassHasStaticAttribute(string $attributeName, string $className, string $message = ''): void
    {
        try {
            parent::assertClassHasStaticAttribute($attributeName, $className, $message);
            self::sayOk();
        } catch (Exception $e) {
            self::handler($e);
        }
    }

    public static function assertClassNotHasStaticAttribute(string $attributeName, string $className, string $message = ''): void
    {
        try {
            parent::assertClassNotHasStaticAttribute($attributeName, $className, $message);
            self::sayOk();
        } catch (Exception $e) {
            self::handler($e);
        }
    }

    public static function assertObjectHasAttribute(string $attributeName, $object, string $message = ''): void
    {
        try {
            parent::assertObjectHasAttribute($attributeName, $object, $message);
            self::sayOk();
        } catch (Exception $e) {
            self::handler($e);
        }
    }

    public static function assertObjectNotHasAttribute(string $attributeName, $object, string $message = ''): void
    {
        try {
            parent::assertObjectNotHasAttribute($attributeName, $object, $message);
            self::sayOk();
        } catch (Exception $e) {
            self::handler($e);
        }
    }

    public static function assertSame($expected, $actual, string $message = ''): void
    {
        try {
            parent::assertSame($expected, $actual, $message);
            self::sayOk();
        } catch (Exception $e) {
            self::handler($e);
        }
    }

    public static function assertNotSame($expected, $actual, string $message = ''): void
    {
        try {
            parent::assertNotSame($expected, $actual, $message);
            self::sayOk();
        } catch (Exception $e) {
            self::handler($e);
        }
    }

    public static function assertInstanceOf(string $expected, $actual, string $message = ''): void
    {
        try {
            parent::assertInstanceOf($expected, $actual, $message);
            self::sayOk();
        } catch (Exception $e) {
            self::handler($e);
        }
    }

    public static function assertNotInstanceOf(string $expected, $actual, string $message = ''): void
    {
        try {
            parent::assertNotInstanceOf($expected, $actual, $message);
            self::sayOk();
        } catch (Exception $e) {
            self::handler($e);
        }
    }

    public static function assertIsArray($actual, string $message = ''): void
    {
        try {
            parent::assertIsArray($actual, $message);
            self::sayOk();
        } catch (Exception $e) {
            self::handler($e);
        }
    }

    public static function assertIsBool($actual, string $message = ''): void
    {
        try {
            parent::assertIsBool($actual, $message);
            self::sayOk();
        } catch (Exception $e) {
            self::handler($e);
        }
    }

    public static function assertIsFloat($actual, string $message = ''): void
    {
        try {
            parent::assertIsFloat($actual, $message);
            self::sayOk();
        } catch (Exception $e) {
            self::handler($e);
        }
    }

    public static function assertIsInt($actual, string $message = ''): void
    {
        try {
            parent::assertIsInt($actual, $message);
            self::sayOk();
        } catch (Exception $e) {
            self::handler($e);
        }
    }

    public static function assertIsNumeric($actual, string $message = ''): void
    {
        try {
            parent::assertIsNumeric($actual, $message);
            self::sayOk();
        } catch (Exception $e) {
            self::handler($e);
        }
    }

    public static function assertIsObject($actual, string $message = ''): void
    {
        try {
            parent::assertIsObject($actual, $message);
            self::sayOk();
        } catch (Exception $e) {
            self::handler($e);
        }
    }

    public static function assertIsResource($actual, string $message = ''): void
    {
        try {
            parent::assertIsResource($actual, $message);
            self::sayOk();
        } catch (Exception $e) {
            self::handler($e);
        }
    }

    public static function assertIsString($actual, string $message = ''): void
    {
        try {
            parent::assertIsString($actual, $message);
            self::sayOk();
        } catch (Exception $e) {
            self::handler($e);
        }
    }

    public static function assertIsScalar($actual, string $message = ''): void
    {
        try {
            parent::assertIsScalar($actual, $message);
            self::sayOk();
        } catch (Exception $e) {
            self::handler($e);
        }

    }

    public static function assertIsCallable($actual, string $message = ''): void
    {
        try {
            parent::assertIsCallable($actual, $message);
            self::sayOk();
        } catch (Exception $e) {
            self::handler($e);
        }
    }

    public static function assertIsIterable($actual, string $message = ''): void
    {
        try {
            parent::assertIsIterable($actual, $message);
            self::sayOk();
        } catch (Exception $e) {
            self::handler($e);
        }
    }

    public static function assertIsNotArray($actual, string $message = ''): void
    {
        try {
            parent::assertIsNotArray($actual, $message);
            self::sayOk();
        } catch (Exception $e) {
            self::handler($e);
        }
    }

    public static function assertIsNotBool($actual, string $message = ''): void
    {
        try {
            parent::assertIsNotBool($actual, $message);
            self::sayOk();
        } catch (Exception $e) {
            self::handler($e);
        }
    }

    public static function assertIsNotFloat($actual, string $message = ''): void
    {
        try {
            parent::assertIsNotFloat($actual, $message);
            self::sayOk();
        } catch (Exception $e) {
            self::handler($e);
        }
    }

    public static function assertIsNotInt($actual, string $message = ''): void
    {
        try {
            parent::assertIsNotInt($actual, $message);
            self::sayOk();
        } catch (Exception $e) {
            self::handler($e);
        }
    }

    public static function assertIsNotNumeric($actual, string $message = ''): void
    {
        try {
            parent::assertIsNotNumeric($actual, $message);
            self::sayOk();
        } catch (Exception $e) {
            self::handler($e);
        }
    }

    public static function assertIsNotObject($actual, string $message = ''): void
    {
        try {
            parent::assertIsNotObject($actual, $message);
            self::sayOk();
        } catch (Exception $e) {
            self::handler($e);
        }
    }

    public static function assertIsNotResource($actual, string $message = ''): void
    {
        try {
            parent::assertIsNotResource($actual, $message);
            self::sayOk();
        } catch (Exception $e) {
            self::handler($e);
        }
    }

    public static function assertIsNotString($actual, string $message = ''): void
    {
        try {
            parent::assertIsNotString($actual, $message);
            self::sayOk();
        } catch (Exception $e) {
            self::handler($e);
        }
    }

    public static function assertIsNotScalar($actual, string $message = ''): void
    {
        try {
            parent::assertIsNotScalar($actual, $message);
            self::sayOk();
        } catch (Exception $e) {
            self::handler($e);
        }
    }

    public static function assertIsNotCallable($actual, string $message = ''): void
    {
        try {
            parent::assertIsNotCallable($actual, $message);
            self::sayOk();
        } catch (Exception $e) {
            self::handler($e);
        }
    }

    public static function assertIsNotIterable($actual, string $message = ''): void
    {
        try {
            parent::assertIsNotIterable($actual, $message);
            self::sayOk();
        } catch (Exception $e) {
            self::handler($e);
        }
    }

    public static function assertMatchesRegularExpression(string $pattern, string $string, string $message = ''): void
    {
        try {
            parent::assertMatchesRegularExpression($pattern, $string, $message);
            self::sayOk();
        } catch (Exception $e) {
            self::handler($e);
        }
    }

    public static function assertRegExp(string $pattern, string $string, string $message = ''): void
    {
        try {
            parent::assertRegExp($pattern, $string, $message);
            self::sayOk();
        } catch (Exception $e) {
            self::handler($e);
        }
    }

    public static function assertDoesNotMatchRegularExpression(string $pattern, string $string, string $message = ''): void
    {
        try {
            parent::assertDoesNotMatchRegularExpression($pattern, $string, $message);
            self::sayOk();
        } catch (Exception $e) {
            self::handler($e);
        }
    }

    public static function assertNotRegExp(string $pattern, string $string, string $message = ''): void
    {
        try {
            parent::assertNotRegExp($pattern, $string, $message);
            self::sayOk();
        } catch (Exception $e) {
            self::handler($e);
        }
    }

    public static function assertSameSize($expected, $actual, string $message = ''): void
    {
        try {
            parent::assertSameSize($expected, $actual, $message);
            self::sayOk();
        } catch (Exception $e) {
            self::handler($e);
        }
    }

    public static function assertNotSameSize($expected, $actual, string $message = ''): void
    {
        try {
            parent::assertNotSameSize($expected, $actual, $message);
            self::sayOk();
        } catch (Exception $e) {
            self::handler($e);
        }
    }

    public static function assertStringMatchesFormat(string $format, string $string, string $message = ''): void
    {
        try {
            parent::assertStringMatchesFormat($format, $string, $message);
            self::sayOk();
        } catch (Exception $e) {
            self::handler($e);
        }
    }

    public static function assertStringNotMatchesFormat(string $format, string $string, string $message = ''): void
    {
        try {
            parent::assertStringNotMatchesFormat($format, $string, $message);
            self::sayOk();
        } catch (Exception $e) {
            self::handler($e);
        }
    }

    public static function assertStringMatchesFormatFile(string $formatFile, string $string, string $message = ''): void
    {
        try {
            parent::assertStringMatchesFormatFile($formatFile, $string, $message);
            self::sayOk();
        } catch (Exception $e) {
            self::handler($e);
        }

        self::sayOk();
    }

    public static function assertStringNotMatchesFormatFile(string $formatFile, string $string, string $message = ''): void
    {
        try {
            parent::assertStringNotMatchesFormatFile($formatFile, $string, $message);
            self::sayOk();
        } catch (Exception $e) {
            self::handler($e);
        }
    }

    public static function assertStringStartsWith(string $prefix, string $string, string $message = ''): void
    {
        try {
            parent::assertStringStartsWith($prefix, $string, $message);
            self::sayOk();
        } catch (Exception $e) {
            self::handler($e);
        }
    }

    public static function assertStringStartsNotWith($prefix, $string, string $message = ''): void
    {
        try {
            parent::assertStringStartsNotWith($prefix, $string, $message);
            self::sayOk();
        } catch (Exception $e) {
            self::handler($e);
        }
    }

    public static function assertStringContainsString(string $needle, string $haystack, string $message = ''): void
    {
        try {
            parent::assertStringContainsString($needle, $haystack, $message);
            self::sayOk();
        } catch (Exception $e) {
            self::handler($e);
        }
    }

    public static function assertStringContainsStringIgnoringCase(string $needle, string $haystack, string $message = ''): void
    {
        try {
            parent::assertStringContainsStringIgnoringCase($needle, $haystack, $message);
            self::sayOk();
        } catch (Exception $e) {
            self::handler($e);
        }
    }

    public static function assertStringNotContainsString(string $needle, string $haystack, string $message = ''): void
    {
        try {
            parent::assertStringNotContainsString($needle, $haystack, $message);
            self::sayOk();
        } catch (Exception $e) {
            self::handler($e);
        }
    }

    public static function assertStringNotContainsStringIgnoringCase(string $needle, string $haystack, string $message = ''): void
    {
        try {
            parent::assertStringNotContainsStringIgnoringCase($needle, $haystack, $message);
            self::sayOk();
        } catch (Exception $e) {
            self::handler($e);
        }
    }

    public static function assertStringEndsWith(string $suffix, string $string, string $message = ''): void
    {
        try {
            parent::assertStringEndsWith($suffix, $string, $message);
            self::sayOk();
        } catch (Exception $e) {
            self::handler($e);
        }
    }

    public static function assertStringEndsNotWith(string $suffix, string $string, string $message = ''): void
    {
        try {
            parent::assertStringEndsNotWith($suffix, $string, $message);
            self::sayOk();
        } catch (Exception $e) {
            self::handler($e);
        }
    }

    public static function assertXmlFileEqualsXmlFile(string $expectedFile, string $actualFile, string $message = ''): void
    {
        try {
            parent::assertXmlFileEqualsXmlFile($expectedFile, $actualFile, $message);
            self::sayOk();
        } catch (Exception $e) {
            self::handler($e);
        }
    }

    public static function assertXmlFileNotEqualsXmlFile(string $expectedFile, string $actualFile, string $message = ''): void
    {
        try {
            parent::assertXmlFileNotEqualsXmlFile($expectedFile, $actualFile, $message);
            self::sayOk();
        } catch (Exception $e) {
            self::handler($e);
        }
    }

    public static function assertXmlStringEqualsXmlFile(string $expectedFile, $actualXml, string $message = ''): void
    {
        try {
            parent::assertXmlStringEqualsXmlFile($expectedFile, $actualXml, $message);
            self::sayOk();
        } catch (Exception $e) {
            self::handler($e);
        }
    }

    public static function assertXmlStringNotEqualsXmlFile(string $expectedFile, $actualXml, string $message = ''): void
    {
        try {
            parent::assertXmlStringNotEqualsXmlFile($expectedFile, $actualXml, $message);
            self::sayOk();
        } catch (Exception $e) {
            self::handler($e);
        }
    }

    public static function assertXmlStringEqualsXmlString($expectedXml, $actualXml, string $message = ''): void
    {
        try {
            parent::assertXmlStringEqualsXmlString($expectedXml, $actualXml, $message);
            self::sayOk();
        } catch (Exception $e) {
            self::handler($e);
        }
    }

    public static function assertXmlStringNotEqualsXmlString($expectedXml, $actualXml, string $message = ''): void
    {
        try {
            parent::assertXmlStringNotEqualsXmlString($expectedXml, $actualXml, $message);
            self::sayOk();
        } catch (Exception $e) {
            self::handler($e);
        }
    }

    public static function assertEqualXMLStructure(\DOMElement $expectedElement, \DOMElement $actualElement, bool $checkAttributes = false, string $message = ''): void
    {
        try {
            parent::assertEqualXMLStructure($expectedElement, $actualElement, $checkAttributes, $message);
            self::sayOk();
        } catch (Exception $e) {
            self::handler($e);
        }

    }

    public static function assertJson(string $actualJson, string $message = ''): void
    {
        try {
            parent::assertJson($actualJson, $message);
            self::sayOk();
        } catch (Exception $e) {
            self::handler($e);
        }
    }

    public static function assertJsonStringEqualsJsonString(string $expectedJson, string $actualJson, string $message = ''): void
    {
        try {
            parent::assertJsonStringEqualsJsonString($expectedJson, $actualJson, $message);
            self::sayOk();
        } catch (Exception $e) {
            self::handler($e);
        }
    }

    public static function assertJsonStringNotEqualsJsonString($expectedJson, $actualJson, string $message = ''): void
    {
        try {
            parent::assertJsonStringNotEqualsJsonString($expectedJson, $actualJson, $message);
            self::sayOk();
        } catch (Exception $e) {
            self::handler($e);
        }
    }

    public static function assertJsonStringEqualsJsonFile(string $expectedFile, string $actualJson, string $message = ''): void
    {
        try {
            parent::assertJsonStringEqualsJsonFile($expectedFile, $actualJson, $message);
            self::sayOk();
        } catch (Exception $e) {
            self::handler($e);
        }
    }

    public static function assertJsonStringNotEqualsJsonFile(string $expectedFile, string $actualJson, string $message = ''): void
    {
        try {
            parent::assertJsonStringNotEqualsJsonFile($expectedFile, $actualJson, $message);
            self::sayOk();
        } catch (Exception $e) {
            self::handler($e);
        }
    }

    public static function assertJsonFileEqualsJsonFile(string $expectedFile, string $actualFile, string $message = ''): void
    {
        try {
            parent::assertJsonFileEqualsJsonFile($expectedFile, $actualFile, $message);
            self::sayOk();
        } catch (Exception $e) {
            self::handler($e);
        }
    }

    public static function assertJsonFileNotEqualsJsonFile(string $expectedFile, string $actualFile, string $message = ''): void
    {
        try {
            parent::assertJsonFileNotEqualsJsonFile($expectedFile, $actualFile, $message);
            self::sayOk();
        } catch (Exception $e) {
            self::handler($e);
        }
    }

    public static function logicalAnd(): LogicalAnd
    {
        try {
            parent::logicalAnd();
            self::sayOk();
        } catch (Exception $e) {
            self::handler($e);
        }
    }

    public static function logicalOr(): LogicalOr
    {
        try {
            parent::logicalOr();
            self::sayOk();
        } catch (Exception $e) {
            self::handler($e);
        }
    }

    public static function logicalNot(Constraint $constraint): LogicalNot
    {
        try {
            parent::logicalNot($constraint);
            self::sayOk();
        } catch (Exception $e) {
            self::handler($e);
        }
    }

    public static function logicalXor(): LogicalXor
    {
        try {
            parent::logicalXor();
            self::sayOk();
        } catch (Exception $e) {
            self::handler($e);
        }
    }

    public static function anything(): IsAnything
    {
        try {
            parent::anything();
            self::sayOk();
        } catch (Exception $e) {
            self::handler($e);
        }
    }

    public static function isTrue(): IsTrue
    {
        try {
            parent::isTrue();
            self::sayOk();
        } catch (Exception $e) {
            self::handler($e);
        }
    }

    public static function callback(callable $callback): Callback
    {
        return parent::callback($callback);
    }

    public static function isFalse(): IsFalse
    {
        try {
            parent::isFalse();
            self::sayOk();
        } catch (Exception $e) {
            self::handler($e);
        }
    }

    public static function isJson(): IsJson
    {
        try {
            parent::isJson();
            self::sayOk();
        } catch (Exception $e) {
            self::handler($e);
        }
    }

    public static function isNull(): IsNull
    {
        try {
            parent::isNull();
            self::sayOk();
        } catch (Exception $e) {
            self::handler($e);
        }
    }

    public static function isFinite(): IsFinite
    {
        try {
            parent::isFinite();
            self::sayOk();
        } catch (Exception $e) {
            self::handler($e);
        }
    }

    public static function isInfinite(): IsInfinite
    {
        try {
            parent::isInfinite();
            self::sayOk();
        } catch (Exception $e) {
            self::handler($e);
        }
    }

    public static function isNan(): IsNan
    {
        try {
            parent::isNan();
            self::sayOk();
        } catch (Exception $e) {
            self::handler($e);
        }
    }

    public static function containsEqual($value): TraversableContainsEqual
    {
        try {
            parent::containsEqual($value);
            self::sayOk();
        } catch (Exception $e) {
            self::handler($e);
        }
    }

    public static function containsIdentical($value): TraversableContainsIdentical
    {
        try {
            parent::containsIdentical($value);
            self::sayOk();
        } catch (Exception $e) {
            self::handler($e);
        }
    }

    public static function containsOnly(string $type): TraversableContainsOnly
    {
        try {
            parent::containsOnly($type);
            self::sayOk();
        } catch (Exception $e) {
            self::handler($e);
        }
    }

    public static function containsOnlyInstancesOf(string $className): TraversableContainsOnly
    {
        try {
            parent::containsOnlyInstancesOf($className);
            self::sayOk();
        } catch (Exception $e) {
            self::handler($e);
        }
    }

    public static function arrayHasKey($key): ArrayHasKey
    {
        try {
            parent::arrayHasKey($key);
            self::sayOk();
        } catch (Exception $e) {
            self::handler($e);
        }
    }

    public static function equalTo($value): IsEqual
    {
        try {
            parent::equalTo($value);
            self::sayOk();
        } catch (Exception $e) {
            self::handler($e);
        }
    }

    public static function equalToCanonicalizing($value): IsEqualCanonicalizing
    {
        try {
            parent::equalToCanonicalizing($value);
            self::sayOk();
        } catch (Exception $e) {
            self::handler($e);
        }
    }

    public static function equalToIgnoringCase($value): IsEqualIgnoringCase
    {
        try {
            parent::equalToIgnoringCase($value);
            self::sayOk();
        } catch (Exception $e) {
            self::handler($e);
        }
    }

    public static function equalToWithDelta($value, float $delta): IsEqualWithDelta
    {
        try {
            parent::equalToWithDelta($value, $delta);
            self::sayOk();
        } catch (Exception $e) {
            self::handler($e);
        }
    }

    public static function isEmpty(): IsEmpty
    {
        try {
            parent::isEmpty();
            self::sayOk();
        } catch (Exception $e) {
            self::handler($e);
        }
    }

    public static function isWritable(): IsWritable
    {
        try {
            parent::isWritable();
            self::sayOk();
        } catch (Exception $e) {
            self::handler($e);
        }
    }

    public static function isReadable(): IsReadable
    {
        try {
            parent::isReadable();
            self::sayOk();
        } catch (Exception $e) {
            self::handler($e);
        }
    }

    public static function directoryExists(): DirectoryExists
    {
        try {
            parent::directoryExists();
            self::sayOk();
        } catch (Exception $e) {
            self::handler($e);
        }
    }

    public static function fileExists(): FileExists
    {
        try {
            parent::fileExists();
            self::sayOk();
        } catch (Exception $e) {
            self::handler($e);
        }
    }

    public static function greaterThan($value): GreaterThan
    {
        try {
            parent::greaterThan($value);
            self::sayOk();
        } catch (Exception $e) {
            self::handler($e);
        }
    }

    public static function greaterThanOrEqual($value): LogicalOr
    {
        try {
            parent::greaterThanOrEqual($value);
            self::sayOk();
        } catch (Exception $e) {
            self::handler($e);
        }
    }

    public static function classHasAttribute(string $attributeName): ClassHasAttribute
    {
        try {
            parent::classHasAttribute($attributeName);
            self::sayOk();
        } catch (Exception $e) {
            self::handler($e);
        }
    }

    public static function classHasStaticAttribute(string $attributeName): ClassHasStaticAttribute
    {
        try {
            parent::classHasStaticAttribute($attributeName);
            self::sayOk();
        } catch (Exception $e) {
            self::handler($e);
        }
    }

    public static function objectHasAttribute($attributeName): ObjectHasAttribute
    {
        try {
            parent::objectHasAttribute($attributeName);
            self::sayOk();
        } catch (Exception $e) {
            self::handler($e);
        }
    }

    public static function identicalTo($value): IsIdentical
    {
        try {
            parent::identicalTo($value);
            self::sayOk();
        } catch (Exception $e) {
            self::handler($e);
        }
    }

    public static function isInstanceOf(string $className): IsInstanceOf
    {
        try {
            parent::isInstanceOf($className);
            self::sayOk();
        } catch (Exception $e) {
            self::handler($e);
        }
    }

    public static function isType(string $type): IsType
    {
        try {
            parent::isType($type);
            self::sayOk();
        } catch (Exception $e) {
            self::handler($e);
        }
    }

    public static function lessThan($value): LessThan
    {
        try {
            parent::lessThan($value);
            self::sayOk();
        } catch (Exception $e) {
            self::handler($e);
        }
    }

    public static function lessThanOrEqual($value): LogicalOr
    {
        try {
            parent::lessThanOrEqual($value);
            self::sayOk();
        } catch (Exception $e) {
            self::handler($e);
        }
    }

    public static function matchesRegularExpression(string $pattern): RegularExpression
    {
        try {
            parent::matchesRegularExpression($pattern);
            self::sayOk();
        } catch (Exception $e) {
            self::handler($e);
        }
    }

    public static function matches(string $string): StringMatchesFormatDescription
    {
        try {
            parent::matches($string);
            self::sayOk();
        } catch (Exception $e) {
            self::handler($e);
        }
    }

    public static function stringStartsWith($prefix): StringStartsWith
    {
        try {
            parent::stringStartsWith($prefix);
            self::sayOk();
        } catch (Exception $e) {
            self::handler($e);
        }
    }

    public static function stringContains(string $string, bool $case = true): StringContains
    {
        try {
            parent::stringContains($string, $case);
            self::sayOk();
        } catch (Exception $e) {
            self::handler($e);
        }
    }

    public static function stringEndsWith(string $suffix): StringEndsWith
    {
        try {
            parent::stringEndsWith($suffix);
            self::sayOk();
        } catch (Exception $e) {
            self::handler($e);
        }
    }

    public static function countOf(int $count): Count
    {
        try {
            parent::countOf($count);
            self::sayOk();
        } catch (Exception $e) {
            self::handler($e);
        }
    }

    public static function fail(string $message = ''): void
    {
        try {
            parent::fail($message);
            self::sayOk();
        } catch (Exception $e) {
            self::handler($e);
        }
    }

    public static function markTestIncomplete(string $message = ''): void
    {
        try {
            parent::markTestIncomplete($message);
            self::sayOk();
        } catch (Exception $e) {
            self::handler($e);
        }
    }

    public static function markTestSkipped(string $message = ''): void
    {
        try {
            parent::markTestSkipped($message);
            self::sayOk();
        } catch (Exception $e) {
            self::handler($e);
        }
    }

    public static function getCount(): int
    {
        try {
            return parent::getCount();
            self::sayOk();
        } catch (Exception $e) {
            self::handler($e);
        }
    }

    public static function resetCount(): void
    {
        try {
            parent::resetCount();
            self::sayOk();
        } catch (Exception $e) {
            self::handler($e);
        }
    }


    public static function assertEquals($expected, $actual, string $message = ''): void
    {
        try {
            parent::assertEquals($expected, $actual, $message);
            self::sayOk();
        } catch (Exception $e) {
            self::handler($e);
        }

    }


}
