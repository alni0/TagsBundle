<?php

declare(strict_types=1);

namespace Netgen\TagsBundle\Matcher\Tag\Id;

use Ibexa\Core\MVC\Symfony\View\View;
use Netgen\TagsBundle\Matcher\Tag\MultipleValued;
use Netgen\TagsBundle\View\TagValueView;

final class ParentTag extends MultipleValued
{
    public function match(View $view): bool
    {
        if (!$view instanceof TagValueView) {
            return false;
        }

        return isset($this->values[$view->getTag()->parentTagId]);
    }
}
