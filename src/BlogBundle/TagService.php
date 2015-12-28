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
     * @param string $tagsString
     *
     * @return array
     */
    public function getTagIds($tagsString = '')
    {
        $tags = explode(',', $tagsString);
        $tags = array_unique($tags);
        $tagsHash = [];

        foreach ($tags as $tag) {
            $tag = trim($tag);
            if (empty($tag)) {
                // skip empty tags
                continue;
            }
            $tagDocument = $this->createNewTag($tag);
            $tagsHash[$tag] = $tagDocument->getId();
        }

        return $tagsHash;
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
        $tagsNew = explode(',', $articleNew->getTags());
        $tagsNew = array_map('trim', $tagsNew);
        $tagsNew = array_unique($tagsNew);
        $tagsOld = explode(',', $articleOld->getTags());
        $tagsOld = array_map('trim', $tagsOld);
        $tagsOld = array_unique($tagsOld);
        $tagsHash = [];

        foreach ($tagsNew as $tag) {
            if (empty($tag)) {
                // skip empty tags
                continue;
            }
            if (in_array($tag, $tagsOld)) {
                // tag already exist, just get id
                $tagsHash[$tag] = $this->tagRepository->getTagByName($tag)->getId();
            } else {
                // create tag & increment number
                $tagsHash[$tag] = $this->createNewTag($tag)->getId();
            }
        }

        foreach ($tagsOld as $tag) {
            if (empty($tag)) {
                // skip empty tags
                continue;
            }
            if (!in_array($tag, $tagsNew)) {
                // decrement removed or changed tags
                $tagDocument = $this->tagRepository->getTagByName($tag)->decrementNumberArticles();
                $this->documentManager->persist($tagDocument);
            }
        }

        return $tagsHash;
    }

    public function removeTags(Article $article)
    {
        $tags = array_keys($article->getTags());
        foreach ($tags as $tag) {
            $tagDocument = $this->tagRepository->getTagByName($tag)->decrementNumberArticles();
            $this->documentManager->persist($tagDocument);
        }
    }
}
