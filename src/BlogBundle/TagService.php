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
     * @param array $tags
     *
     * @return array
     */
    public function getTagIds($tags)
    {
        foreach ($tags as $key => &$tag) {
            if (empty($key)) {
                // skip empty tags
                unset($tags[$key]);
                continue;
            }
            $tagDocument = $this->createNewTag($key);
            $tag = $tagDocument->getId();
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

    public function updateTagsIds(Article $articleNew, Article $articleOld)
    {
        $tagsNew = $articleNew->getTags();
        $tagsOld = $articleOld->getTags() ?: [];

        foreach ($tagsNew as $key => $tag) {
            if (empty($key)) {
                // skip empty tags
                unset($tagsNew[$key]);
                continue;
            }
            if (array_key_exists($key, $tagsOld)) {
                // tag already exist, just get id
                $tagsNew[$key] = $this->tagRepository->getTagByName($key)->getId();
            } else {
                // create tag & increment number
                $tagsNew[$key] = $this->createNewTag($key)->getId();
            }
        }

        foreach ($tagsOld as $key => $tag) {
            if (empty($key)) {
                // skip empty tags
                continue;
            }
            if (!array_key_exists($key, $tagsNew)) {
                // decrement removed or changed tags
                $tagDocument = $this->tagRepository->getTagByName($key)->decrementNumberArticles();
                $this->documentManager->persist($tagDocument);
            }
        }

        return $tagsNew;
    }

    public function removeTags(Article $article)
    {
        $tags = array_keys($article->getTags());
        foreach ($tags as $tag) {
            $tagDocument = $this->tagRepository->getTagByName($tag)->decrementNumberArticles();
            $this->documentManager->persist($tagDocument);
        }
    }

    public function getTagsWithWeigh($tags)
    {
        $max = max(array_column($tags, "numberArticles"));

        foreach ($tags as $key=>&$tag) {
            if (1 > $tag['numberArticles']) {
                unset($tags[$key]);
            }
            $percent = ($tag['numberArticles'] / $max) * 100;
            $tag['weight'] = ceil($percent / 10);
        }

        return $tags;
    }
}
