<?php

namespace App\Client\Controller\News;

use App\Client\ViewModel\News\NewsViewModel;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\JsonResponse;
use App\Core\Components\Logs\Entity\Enum\LogsAlertEnum;
use App\Core\Components\Logs\Entity\Enum\LogsContextEnum;
use App\Core\Components\Logs\Services\LogsServiceInterface;
use App\Infrastructure\Response\Services\JsonResponseService;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

final class NewsController extends AbstractController
{
    public function __construct(
        private JsonResponseService $jsonResponseService,
        private LogsServiceInterface $logsService,
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

        if (empty($news)) {
            $this->logsService->add(404, LogsContextEnum::NEWS, LogsAlertEnum::WARNING, "NewsNotFoundException");
            return new JsonResponse("no news found", 404);
        }

        return $this->jsonResponseService->create($news, 200);
    }
}
