<?php

namespace App\Repositories;

use App\Collections\ProfileDataCollection;
use Exception;
use Google_Client;
use Google_Service_Sheets;
use Google_Service_Sheets_Spreadsheet;
use Google_Service_Sheets_ValueRange;

class GoogleSpreadsheet
{
    public function writeToGoogleSheets(ProfileDataCollection $profileCollection): void
    {
        try {
            $client = new Google_Client();
            $client->setAuthConfig($_ENV['GOOGLE_CREDENTIALS_PATH']);
            $client->addScope(Google_Service_Sheets::SPREADSHEETS);

            $service = new Google_Service_Sheets($client);

            $timestamp = date('YmdHis');
            $spreadsheetTitle = 'Spreadsheet_' . $timestamp;

            $spreadsheet = new Google_Service_Sheets_Spreadsheet([
                'properties' => [
                    'title' => $spreadsheetTitle,
                ],
            ]);

            $createdSpreadsheet = $service->spreadsheets->create($spreadsheet);

            $newSpreadsheetId = $createdSpreadsheet->spreadsheetId;

            $values = [];
            foreach ($profileCollection->getAll() as $profile) {
                $row = [
                    $profile->getProfileUrl(),
                    $profile->getFollowers(),
                    $profile->getLikes(),
                    implode(', ', $profile->getLastFiveVideosLikes()),
                    $profile->getSumOfLastFiveVideoViews(),
                ];
                $values[] = $row;
            }

            $range = 'Sheet1';
            $body = new Google_Service_Sheets_ValueRange([
                'values' => $values,
            ]);
            $params = [
                'valueInputOption' => 'RAW',
            ];
            $service->spreadsheets_values->append($newSpreadsheetId, $range, $body, $params);

            $spreadsheetLink = 'https://docs.google.com/spreadsheets/d/' . $newSpreadsheetId;
            error_log('Data successfully written to the new spreadsheet. Access it here: ' . $spreadsheetLink);
        } catch (Exception $e) {

            error_log('Error creating or writing to the new spreadsheet: ' . $e->getMessage());
        }
    }
}
