<?php

namespace App\Repositories;

use App\Collections\ProfileDataCollection;
use Exception;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;

class LocalFileSpreadsheet
{
    private string $localFilePath = __DIR__ . '/';

    public function writeToLocalSpreadsheet(ProfileDataCollection $profileCollection): void
    {
        try {

            $spreadsheet = new Spreadsheet();

            $spreadsheet->getProperties()->setTitle('My Spreadsheet');

            $sheet = $spreadsheet->getActiveSheet();

            $headers = ['Profile URL', 'Followers', 'Likes', 'Last Five Videos Likes', 'Sum of Last Five Video Views'];
            $sheet->fromArray([$headers], null, 'A1');

            $rowIndex = 2;
            foreach ($profileCollection->getAll() as $profile) {
                $rowData = [
                    $profile->getProfileUrl(),
                    $profile->getFollowers(),
                    $profile->getLikes(),
                    implode(', ', $profile->getLastFiveVideosLikes()),
                    $profile->getSumOfLastFiveVideoViews(),
                ];
                $sheet->fromArray([$rowData], null, 'A' . $rowIndex);
                $rowIndex++;
            }


            $localFilePath = $this->localFilePath . 'local_spreadsheet.xlsx';
            $writer = new Xlsx($spreadsheet);
            $writer->save($localFilePath);


            error_log('Data successfully written to the local spreadsheet: ' . $localFilePath);
        } catch (Exception $e) {
            // Handle the exception, e.g., log the error
            error_log('Error creating or writing to the local spreadsheet: ' . $e->getMessage());
        }
    }
}
