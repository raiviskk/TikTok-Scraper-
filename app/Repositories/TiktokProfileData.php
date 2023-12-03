<?php

namespace App\Repositories;

use App\Collections\ProfileDataCollection;
use App\Models\ProfileData;


class TiktokProfileData
{
    public function getProfilesData(array $search): ProfileDataCollection
    {
        $command = sprintf('node %s %s', __DIR__ . '/scrape_tiktok.js', implode(' ', $search));
        exec($command);
        $profileDataJson = file_get_contents(__DIR__ . '/tiktok_profiles.json');
        $profileData = json_decode($profileDataJson, true);
        $profileCollection = new ProfileDataCollection();
        foreach ($profileData as $data) {
            $profile = new ProfileData(
                $data['profileUrl'],
                $data['followers'],
                $data['likes'],
                $data['lastFiveVideosLikes'],
                $data['sumOfLastFiveVideoViews']
            );
            $profileCollection->add($profile);
        }
        return $profileCollection;
    }

}
