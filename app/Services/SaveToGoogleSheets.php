<?php

namespace App\Services;


use App\Repositories\GoogleSpreadsheet;
use App\Repositories\TiktokProfileData;

class SaveToGoogleSheets
{

    public function execute($search): void
    {
        $profileData = new TiktokProfileData();
        $googleSheets = new GoogleSpreadsheet();
        $googleSheets->writeToGoogleSheets($profileData->getProfilesData($search));
    }

}
