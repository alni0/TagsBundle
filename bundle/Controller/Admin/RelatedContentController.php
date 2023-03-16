<?php

declare(strict_types=1);

namespace Netgen\TagsBundle\Controller\Admin;

use Ibexa\Contracts\Core\Repository\Values\Content\Query\Criterion\ContentTypeIdentifier;
use Netgen\TagsBundle\API\Repository\Values\Tags\Tag;
use Netgen\TagsBundle\Core\Pagination\Pagerfanta\RelatedContentAdapter;
use Netgen\TagsBundle\Core\Search\RelatedContent\SortClauseMapper;
use Netgen\TagsBundle\Form\Type\RelatedContentFilterType;
use Pagerfanta\Adapter\AdapterInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

use function count;

final class RelatedContentController extends Controller
{
    private AdapterInterface $adapter;

    private SortClauseMapper $sortClauseMapper;

    public function __construct(AdapterInterface $adapter, SortClauseMapper $sortClauseMapper)
    {
        $this->adapter = $adapter;
        $this->sortClauseMapper = $sortClauseMapper;
    }

    /**
     * Rendering a view which shows related content of tag.
     */
    public function relatedContentAction(Request $request, Tag $tag): Response
    {
        $this->denyAccessUnlessGranted('ibexa:tags:read');

        $currentPage = (int) $request->query->get('page');
        $configResolver = $this->getConfigResolver();
        $filterApplied = false;

        $form = $this->createForm(
            RelatedContentFilterType::class,
            null,
            [
                'tag' => $tag,
            ],
        );

        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $contentTypeFilter = $form->get('content_types')->getData();
            $sortOption = $form->get('sort')->getData();

            if ($this->adapter instanceof RelatedContentAdapter) {
                if (count($contentTypeFilter) > 0) {
                    $additionalCriteria = [
                        new ContentTypeIdentifier($contentTypeFilter),
                    ];

                    $this->adapter->setAdditionalCriteria($additionalCriteria);
                }

                $sortClauses = $this->sortClauseMapper->mapSortClauses([$sortOption]);
                $this->adapter->setSortClauses($sortClauses);

                $filterApplied = true;
            }
        }

        $pager = $this->createPager(
            $this->adapter,
            $currentPage,
            $configResolver->getParameter('admin.related_content_limit', 'netgen_tags'),
            $tag,
        );

        return $this->render(
            '@NetgenTags/admin/tag/related_content.html.twig',
            [
                'tag' => $tag,
                'related_content' => $pager,
                'filter_form' => $form->createView(),
                'filter_applied' => $filterApplied,
            ],
        );
    }
}
