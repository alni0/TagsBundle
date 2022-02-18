<?php

declare(strict_types=1);

namespace Netgen\TagsBundle\SPI\Persistence\Tags;

use Ibexa\Contracts\Core\Persistence\ValueObject;

/**
 * Class representing a tag.
 */
final class Tag extends ValueObject
{
    /**
     * Tag ID.
     *
     * @var int
     */
    public $id;

    /**
     * Parent tag ID.
     *
     * @var int
     */
    public $parentTagId;

    /**
     * Main tag ID.
     *
     * Zero if tag is not a synonym
     *
     * @var int
     */
    public $mainTagId;

    /**
     * Returns the keywords in the available languages
     * Eg. array( "cro-HR" => "Hrvatska", "eng-GB" => "Croatia" ).
     *
     * @var string[]
     */
    public $keywords = [];

    /**
     * The depth tag has in tag tree.
     *
     * @var int
     */
    public $depth;

    /**
     * The path to this tag e.g. /1/6/21/42 where 42 is the current ID.
     *
     * @var string
     */
    public $pathString;

    /**
     * Tag modification date as a UNIX timestamp.
     *
     * @var int
     */
    public $modificationDate;

    /**
     * A global unique ID of the tag.
     *
     * @var string
     */
    public $remoteId;

    /**
     * Indicates if the tag is shown in the main language if its not present in an other requested language.
     *
     * @var bool
     */
    public $alwaysAvailable;

    /**
     * The main language code of the tag.
     *
     * @var string
     */
    public $mainLanguageCode;

    /**
     * List of languages in this tag.
     *
     * @var int[]
     */
    public $languageIds = [];
}
