<?php


use App\Services\SaveToLocalFile;
use Symfony\Component\Dotenv\Dotenv;

require_once __DIR__ . '/vendor/autoload.php';

$dotenv = new Dotenv();
$dotenv->load(__DIR__ . '/.env');

// Array of TikTok hashtags to scrape
$search = ['latviešutiktok', 'latvija'];


//Save to local Spreadsheet file

$tiktokData = new SaveToLocalFile();
$tiktokData->execute($search);







