<?php

declare(strict_types=1);

namespace App\Models;

class ProfileData
{
    private string $profileUrl;
    private int $followers;
    private int $likes;
    private array $lastFiveVideosLikes;
    private int $sumOfLastFiveVideoViews;

    public function __construct(
        string $profileUrl,
        int    $followers,
        int    $likes,
        array  $lastFiveVideosLikes,
        int    $sumOfLastFiveVideoViews)
    {
        $this->profileUrl = $profileUrl;
        $this->followers = $followers;
        $this->likes = $likes;
        $this->lastFiveVideosLikes = $lastFiveVideosLikes;
        $this->sumOfLastFiveVideoViews = $sumOfLastFiveVideoViews;
    }

    public function getProfileUrl(): string
    {
        return $this->profileUrl;
    }

    public function getFollowers(): int
    {
        return $this->followers;
    }

    public function getLikes(): int
    {
        return $this->likes;
    }

    public function getLastFiveVideosLikes(): array
    {
        return $this->lastFiveVideosLikes;
    }

    public function getSumOfLastFiveVideoViews(): int
    {
        return $this->sumOfLastFiveVideoViews;
    }
}