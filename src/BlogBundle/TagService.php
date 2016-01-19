<?php

namespace BlogBundle;

use BlogBundle\Document\Article;
use BlogBundle\Document\Tag;
use BlogBundle\Document\TagRepository;
use Doctrine\ODM\MongoDB\DocumentManager;
use Doctrine\ODM\MongoDB\DocumentRepository;

class TagService
{
    /**
     * @var TagRepository
     */
    protected $tagRepository;

    /**
     * @var DocumentManager
     */
    protected $documentManager;

    /**
     * @param DocumentRepository $tagRepository
     * @param DocumentManager    $documentManager
     */
    public function __construct(DocumentRepository $tagRepository, DocumentManager $documentManager)
    {
        $this->tagRepository = $tagRepository;
        $this->documentManager = $documentManager;
    }

    /**
     * @param Article $article
     *
     * @return mixed
     */
    public function createTags(Article $article)
    {
        $tags = $article->getTags();

        foreach ($tags as $tag) {
            $this->createNewTag($tag);
        }

        return $tags;
    }

    /**
     * @param $tag string
     *
     * @return Tag
     */
    public function createNewTag($tag)
    {
        if (!$tagDocument = $this->tagRepository->getTagByName($tag)) {
            $tagDocument = new Tag();
            $tagDocument->setName($tag);
            $this->documentManager->persist($tagDocument);
        }

        $tagDocument->incrementNumberArticles();

        return $tagDocument;
    }

    public function updateTags(Article $articleNew, Article $articleOld)
    {
        $tagsNew = $articleNew->getTags();
        $tagsOld = $articleOld->getTags() ?: [];

        foreach ($tagsNew as $key => $tag) {
            if (empty($tag)) {
                // skip empty tags
                unset($tagsNew[$key]);
                continue;
            }
            if (false === array_search($tag, $tagsOld)) {
                // create tag & increment number
                $this->createNewTag($tag);
            }
        }

        foreach ($tagsOld as $key => $tag) {
            if (false === array_search($tag, $tagsNew)) {
                // decrement removed or changed tags
                $tagDocument = $this->tagRepository->getTagByName($tag)->decrementNumberArticles();
                $this->documentManager->persist($tagDocument);
            }
        }

        return $tagsNew;
    }

    public function removeTags(Article $article)
    {
        $tags = $article->getTags();
        foreach ($tags as $tag) {
            $tagDocument = $this->tagRepository->getTagByName($tag)->decrementNumberArticles();
            $this->documentManager->persist($tagDocument);
        }
    }

    public function getTagsWithWeigh($tags)
    {
        $max = max(array_column($tags, 'numberArticles'));

        foreach ($tags as $key => &$tag) {
            if (1 > $tag['numberArticles']) {
                unset($tags[$key]);
            }
            $percent = ($tag['numberArticles'] / $max) * 100;
            $tag['weight'] = ceil($percent / 10);
        }

        return $tags;
    }
}
