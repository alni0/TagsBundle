<?php

declare(strict_types=1);

namespace Netgen\TagsBundle\Tests\Core\Repository\Values\Tags;

use Netgen\TagsBundle\API\Repository\Values\Tags\Tag;
use PHPUnit\Framework\TestCase;

use function array_keys;
use function get_object_vars;

final class TagTest extends TestCase
{
    /**
     * @covers \Netgen\TagsBundle\API\Repository\Values\Tags\Tag::getProperties
     */
    public function testObjectProperties(): void
    {
        $object = new Tag();

        $properties = array_keys(
            (function (): array {
                return get_object_vars($this);
            })->call($object)
        );

        self::assertContains('id', $properties, 'Property not found');
        self::assertContains('parentTagId', $properties, 'Property not found');
        self::assertContains('mainTagId', $properties, 'Property not found');
        self::assertContains('keywords', $properties, 'Property not found');
        self::assertContains('depth', $properties, 'Property not found');
        self::assertContains('pathString', $properties, 'Property not found');
        self::assertContains('modificationDate', $properties, 'Property not found');
        self::assertContains('remoteId', $properties, 'Property not found');
        self::assertContains('mainLanguageCode', $properties, 'Property not found');
        self::assertContains('alwaysAvailable', $properties, 'Property not found');
        self::assertContains('languageCodes', $properties, 'Property not found');
        self::assertContains('prioritizedLanguageCode', $properties, 'Property not found');
    }
}
