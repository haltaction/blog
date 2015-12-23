<?php

namespace BlogBundle;

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
     * @param DocumentManager $documentManager
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
        $tagsHash = [];

        if (is_array($tags)) {
            foreach ($tags as $tag) {
                $tag = trim($tag);
                $tagDocument = $this->tagRepository->getTagByName($tag);

                if (empty($tagDocument)) {
                    $tagDocument = new Tag();
                    $tagDocument->setName($tag);
                    $this->documentManager->persist($tagDocument);
                }

                $tagDocument->incrementNumberArticles();
                $tagsHash[$tag] = $tagDocument->getId();
            }
        }

        return $tagsHash;
    }
}
