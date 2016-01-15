<?php

namespace BlogBundle\Controller\API\V1;

use BlogBundle\ArticleEvents;
use BlogBundle\Document\Article;
use BlogBundle\Event\FilterArticleEvent;
use FOS\RestBundle\Controller\FOSRestController;
use FOS\RestBundle\Util\Codes;
use Nelmio\ApiDocBundle\Annotation\ApiDoc;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\ParamConverter;
use Symfony\Component\HttpFoundation\Request;

class ApiController extends FOSRestController
{
    /**
     * Access URI /api/v1/article/list/{page}.
     *
     * @ApiDoc(
     *  resource=true,
     *  description="Returns list of articles obj & boolean 'isNextPage'",
     *  requirements={
     *      {"name"="page",    "dataType"="integer",    "requirement"="false"}
     *  },
     * filters={
     *      {"name"="sort", "Values"="'newest'|'popular'"}
     * },
     *  output="BlogBundle\Model\ArticleList",
     *  section="Article API",
     *  statusCodes={
     *      200="Returned when request was handled with success",
     *      400="Returned when bad request",
     *      500="Returned when there is a server side error",
     *  },
     *  tags={
     *      "beta" = "#10A54A"
     *  }
     * )
     *
     * @ParamConverter()
     *
     * @param $page
     * @param Request $request
     *
     * @return View
     */
    public function listArticlesAction($page, Request $request)
    {
        $sort = $request->get('sort');
        try {
            $articleDto = $this->get('blog.article')->getArticlesListDtoBy($sort);

            $pagerfanta = $this->get('blog.article')->getPagerfantaByArray($articleDto);
            $pagerfanta->setCurrentPage($page);
        } catch (\Exception $e) {
            return $this->view($e->getMessage(), Codes::HTTP_BAD_REQUEST);
        }

        return $this->view(
            [
                'data' => $pagerfanta->getCurrentPageResults(),
                'isNextPage' => $pagerfanta->hasNextPage(),
            ],
            Codes::HTTP_OK
        );
    }

    /**
     * Access URI /api/v1/article/{slug}.
     *
     * @ApiDoc(
     *  resource=true,
     *  description="Returns articles obj with first 10 comments",
     *  requirements={
     *      {"name"="slug",    "dataType"="string",    "requirement"="true"}
     *  },
     *  output="BlogBundle\Model\ArticleView",
     *  section="Article API",
     *  statusCodes={
     *      200="Returned when request was handled with success",
     *      400="Returned when bad request",
     *      500="Returned when there is a server side error",
     *  },
     *  tags={
     *      "beta" = "#10A54A"
     *  }
     * )
     *
     *
     *
     * @ParamConverter("article", class="BlogBundle\Document\Article", options={"mapping": {"slug": "slug"}})
     *
     * @param $article
     *
     * @return View
     */
    public function viewArticleAction(Article $article)
    {
        // dispatch the article view event
        $request = Request::createFromGlobals();
        $user = $this->getUser();
        $event = new FilterArticleEvent($article, $request, $user);
        $dispatcher = $this->get('event_dispatcher');
        $dispatcher->dispatch(ArticleEvents::ARTICLE_VIEW, $event);

        return $this->view(
            [
                $this->get('blog.article')->getArticleDto($article)
            ],
            Codes::HTTP_OK
        );
    }
}
