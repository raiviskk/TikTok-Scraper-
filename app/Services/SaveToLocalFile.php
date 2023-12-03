<?php

namespace App\Services;

use App\Repositories\LocalFileSpreadsheet;
use App\Repositories\TiktokProfileData;

class SaveToLocalFile
{
    public function execute($search): void
    {
        $profileData = new TiktokProfileData();
        $localFile = new LocalFileSpreadsheet();
        $localFile->writeToLocalSpreadsheet($profileData->getProfilesData($search));
    }


}