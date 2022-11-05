<?php

namespace App\Client\ViewModel\News;

final class NewsViewModel
{
    public function __construct(
        private string $title,
        private string $description,
        private string $link,
        private string $pubDate,
    ) {
    }

    public function getTitle(): string
    {
        return $this->title;
    }

    public function getDescription(): string
    {
        return $this->description;
    }

    public function getLink(): string
    {
        return $this->link;
    }

    public function getPubDate(): string
    {
        return $this->pubDate;
    }
}
