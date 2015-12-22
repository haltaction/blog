<?php

namespace BlogBundle;

use BlogBundle\Document\Tag;
use Doctrine\ODM\MongoDB\DocumentManager;
use Doctrine\ODM\MongoDB\DocumentRepository;

class TagService
{
    protected $tagRepository;

    protected $documentManager;

    public function __construct(DocumentRepository $tagRepository, DocumentManager $documentManager)
    {
        $this->tagRepository = $tagRepository;
        $this->documentManager = $documentManager;
    }

    public function getTagIds($tagsString = '')
    {
        $tags = explode(',', $tagsString);
        $tagsHash = [];

        if (is_array($tags)) {
            foreach ($tags as $tag) {
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