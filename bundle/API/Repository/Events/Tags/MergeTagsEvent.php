<?php

declare(strict_types=1);

namespace Netgen\TagsBundle\API\Repository\Events\Tags;

use Ibexa\Contracts\Core\Repository\Event\AfterEvent;
use Netgen\TagsBundle\API\Repository\Values\Tags\Tag;

final class MergeTagsEvent extends AfterEvent
{
    /**
     * @var \Netgen\TagsBundle\API\Repository\Values\Tags\Tag
     */
    private $targetTag;

    public function __construct(Tag $targetTag)
    {
        $this->targetTag = $targetTag;
    }

    public function getTargetTag(): Tag
    {
        return $this->targetTag;
    }
}
