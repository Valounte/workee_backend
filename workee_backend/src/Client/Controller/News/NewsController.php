<?php

namespace App\Client\Controller\News;

use App\Client\ViewModel\News\NewsViewModel;
use App\Infrastructure\Response\Services\JsonResponseService;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

final class NewsController extends AbstractController
{
    public function __construct(
        private JsonResponseService $jsonResponseService,
    ) {
    }
    /**
     * @Route("/api/news", name="getNews", methods={"GET"})
     */
    public function getNews(): Response
    {
        $rss = simplexml_load_file('https://www.inrs.fr/rss/?feed=actualites');

        $news = [];
        foreach ($rss->channel->item as $item) {
            $news[] = new NewsViewModel(
                (string) $item->title,
                (string) $item->description,
                (string) $item->link,
                (string) $item->pubDate,
            );
        }

        return $this->jsonResponseService->create($news, 200);
    }
}
