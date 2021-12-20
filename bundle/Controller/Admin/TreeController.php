<?php

namespace Netgen\TagsBundle\Controller\Admin;

use Netgen\TagsBundle\API\Repository\TagsService;
use Netgen\TagsBundle\API\Repository\Values\Tags\Tag;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\Routing\RouterInterface;
use Symfony\Component\Translation\TranslatorInterface;

class TreeController extends Controller
{
    /**
     * @var \Netgen\TagsBundle\API\Repository\TagsService
     */
    protected $tagsService;

    /**
     * @var \Symfony\Component\Translation\TranslatorInterface
     */
    protected $translator;

    /**
     * @var \Symfony\Component\Routing\RouterInterface
     */
    protected $router;

    /**
     * @var array
     */
    protected $treeLabels;

    /**
     * @var array
     */
    protected $treeLinks;

    /**
     * TreeController constructor.
     *
     * @param \Netgen\TagsBundle\API\Repository\TagsService $tagsService
     * @param \Symfony\Component\Translation\TranslatorInterface $translator
     * @param \Symfony\Component\Routing\RouterInterface $router
     */
    public function __construct(
        TagsService $tagsService,
        TranslatorInterface $translator,
        RouterInterface $router
    ) {
        $this->tagsService = $tagsService;
        $this->translator = $translator;
        $this->router = $router;

        $this->treeLabels = [
            'top_level_tags' => $this->translator->trans('tag.tree.top_level_tags', [], 'eztags_admin'),
            'add_child' => $this->translator->trans('tag.tree.add_child', [], 'eztags_admin'),
            'update_tag' => $this->translator->trans('tag.tree.update_tag', [], 'eztags_admin'),
            'delete_tag' => $this->translator->trans('tag.tree.delete_tag', [], 'eztags_admin'),
            'merge_tag' => $this->translator->trans('tag.tree.merge_tag', [], 'eztags_admin'),
            'convert_tag' => $this->translator->trans('tag.tree.convert_tag', [], 'eztags_admin'),
            'add_synonym' => $this->translator->trans('tag.tree.add_synonym', [], 'eztags_admin'),
        ];

        $this->treeLinks = [
            'top_level_tags' => $this->router->generate('netgen_tags_admin_root'),
            'show_tag' => $this->router->generate('netgen_tags_admin_tag_show', ['tagId' => ':tagId']),
            'add_child' => $this->router->generate('netgen_tags_admin_tag_add_select', ['parentId' => ':parentId']),
            'update_tag' => $this->router->generate('netgen_tags_admin_tag_update_select', ['tagId' => ':tagId']),
            'delete_tag' => $this->router->generate('netgen_tags_admin_tag_delete', ['tagId' => ':tagId']),
            'merge_tag' => $this->router->generate('netgen_tags_admin_tag_merge', ['tagId' => ':tagId']),
            'convert_tag' => $this->router->generate('netgen_tags_admin_tag_convert', ['tagId' => ':tagId']),
            'add_synonym' => $this->router->generate('netgen_tags_admin_synonym_add_select', ['mainTagId' => ':mainTagId']),
        ];
    }

    /**
     * Returns JSON string containing all children tags for given tag.
     * It is called in AJAX request from jsTree Javascript plugin to render tree with tags.
     * It supports lazy loading; when a tag is clicked in a tree, it calls this method to fetch it's children.
     *
     * @param \Netgen\TagsBundle\API\Repository\Values\Tags\Tag|null $tag
     * @param int
     * @param bool $isRoot
     *
     * @return \Symfony\Component\HttpFoundation\JsonResponse
     */
    public function getChildrenAction(Tag $tag = null, $isRoot = false)
    {
        $this->denyAccessUnlessGranted('ez:tags:read');

        $result = [];

        if ((bool) $isRoot) {
            $result[] = $tag instanceof Tag ?
                $this->getTagTreeData($tag, $isRoot) :
                $this->getRootTreeData();
        } else {
            $configResolver = $this->getConfigResolver();
            $treeLimit = $configResolver->getParameter('admin.tree_limit', 'eztags');
            $childrenTags = $this->tagsService->loadTagChildren($tag, 0, $treeLimit > 0 ? $treeLimit : -1);
            foreach ($childrenTags as $tag) {
                $result[] = $this->getTagTreeData($tag, $isRoot);
            }
        }

        return (new JsonResponse())->setData($result);
    }

    /**
     * Generates data for root of the tree.
     *
     * @return array
     */
    protected function getRootTreeData()
    {
        return [
            'id' => '0',
            'parent' => '#',
            'text' => $this->treeLabels['top_level_tags'],
            'children' => true,
            'state' => [
                'opened' => true,
            ],
            'a_attr' => [
                'href' => $this->treeLinks['top_level_tags'],
                'rel' => '0',
            ],
            'data' => [
                'context_menu' => [
                    [
                        'name' => 'add_child',
                        'url' => str_replace(':parentId', 0, $this->treeLinks['add_child']),
                        'text' => $this->treeLabels['add_child'],
                    ],
                ],
            ],
        ];
    }

    /**
     * Generates data, for given tag, which will be converted to JSON:.
     *
     * @param \Netgen\TagsBundle\API\Repository\Values\Tags\Tag $tag
     * @param bool $isRoot
     *
     * @return array
     */
    protected function getTagTreeData(Tag $tag, $isRoot = false)
    {
        $synonymCount = $this->tagsService->getTagSynonymCount($tag);

        return [
            'id' => $tag->id,
            'parent' => $isRoot ? '#' : $tag->parentTagId,
            'text' => $synonymCount > 0 ? $this->escape($tag->keyword) . ' (+' . $synonymCount . ')' : $this->escape($tag->keyword),
            'children' => $this->tagsService->getTagChildrenCount($tag) > 0,
            'a_attr' => [
                'href' => str_replace(':tagId', $tag->id, $this->treeLinks['show_tag']),
                'rel' => $tag->id,
            ],
            'state' => [
                'opened' => $isRoot,
            ],
            'data' => [
                'context_menu' => [
                    [
                        'name' => 'add_child',
                        'url' => str_replace(':parentId', $tag->id, $this->treeLinks['add_child']),
                        'text' => $this->treeLabels['add_child'],
                    ],
                    [
                        'name' => 'update_tag',
                        'url' => str_replace(':tagId', $tag->id, $this->treeLinks['update_tag']),
                        'text' => $this->treeLabels['update_tag'],
                    ],
                    [
                        'name' => 'delete_tag',
                        'url' => str_replace(':tagId', $tag->id, $this->treeLinks['delete_tag']),
                        'text' => $this->treeLabels['delete_tag'],
                    ],
                    [
                        'name' => 'merge_tag',
                        'url' => str_replace(':tagId', $tag->id, $this->treeLinks['merge_tag']),
                        'text' => $this->treeLabels['merge_tag'],
                    ],
                    [
                        'name' => 'add_synonym',
                        'url' => str_replace(':mainTagId', $tag->id, $this->treeLinks['add_synonym']),
                        'text' => $this->treeLabels['add_synonym'],
                    ],
                    [
                        'name' => 'convert_tag',
                        'url' => str_replace(':tagId', $tag->id, $this->treeLinks['convert_tag']),
                        'text' => $this->treeLabels['convert_tag'],
                    ],
                ],
            ],
        ];
    }

    private function escape($string): string
    {
        return htmlspecialchars($string, ENT_QUOTES | ENT_SUBSTITUTE | ENT_HTML401, 'UTF-8');
    }
}
