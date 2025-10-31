<?php

namespace App\Helper;

use Pimple\Psr11\Container;
use Mpdf\Mpdf;
use Slim\Views\Twig;

use PhpOffice\PhpSpreadsheet\IOFactory;
use PhpOffice\PhpSpreadsheet\Reader\Html;
use PhpOffice\PhpSpreadsheet\Shared\File;
use PhpOffice\PhpSpreadsheet\Style\Border;

use Psr\Http\Message\ServerRequestInterface as Request;

final class General {
    private Container $container;
    private array $request;

    public function __construct(Container $container, Request $request=null) {
        $this->container = $container;
        if ($request)
            $this->request = $request;
    }

	public function checkStrongPassword($text) {
		$result = true;
		if (strlen($text) < 8) {
			$result = false;
		} if (!preg_match("#[0-9]+#", $text)) {
			$result = false;
		} if (!preg_match("#[a-zA-Z]+#", $text)) {
			$result = false;
		}

		return $result;
	}

    function arraySort($array, $on, $order=SORT_ASC)
    {
        $new_array = array();
        $sortable_array = array();
    
        if (count($array) > 0) {
            foreach ($array as $k => $v) {
                if (is_array($v)) {
                    foreach ($v as $k2 => $v2) {
                        if ($k2 == $on) {
                            $sortable_array[$k] = $v2;
                        }
                    }
                } else {
                    $sortable_array[$k] = $v;
                }
            }
    
            switch ($order) {
                case SORT_ASC:
                    asort($sortable_array);
                break;
                case SORT_DESC:
                    arsort($sortable_array);
                break;
            }
    
            foreach ($sortable_array as $k => $v) {
                $new_array[$k] = $array[$k];
            }
        }
    
        return array_values($new_array);
    }
    
    function replaceNullInArray(array $array, $replacement='') {
        array_walk_recursive($array, function (&$item) use ($replacement) {
            if (is_null($item) || $item == 'null') {
                $item = $replacement;
            }
        });
        return $array;
    }

    public function generateExcel(Request $request, string $template, $colWidth, array $data = [], array $additionalData=[], $startCell=1, $titleTab="Format Impor", $config=['wrap_text' => true, 'shrink_text' => true])
    {
        $view           = Twig::fromRequest($request);
        $htmlString     = $view->fetch($template, ['data' => $data]);
        $reader         = IOFactory::createReader('Html');
        $spreadsheet    = $reader->loadFromString($htmlString);
    
        $titleTab = preg_replace('/[\[\]\/\\\\\*?\:\'"]+/', '-', $titleTab);
        $titleTab = strlen($titleTab) > 30 ? substr($titleTab, 0, 30) : $titleTab;
    
        $spreadsheet->getActiveSheet()->setTitle($titleTab);
        $spreadsheet->getActiveSheet()->getDefaultColumnDimension()->setWidth(20);
        $lastColumn = $spreadsheet->getActiveSheet()->getHighestColumn();
    
        for ($i = 'A'; $i != $lastColumn; $i++) {
            for ($j=0; $j < $data['total']; $j++) {
                $spreadsheet->getActiveSheet()->getStyle($i . ($j + $startCell))->getAlignment()->setVertical('center');
                if (!empty($config['wrap_text'])) {
                    $spreadsheet->getActiveSheet()->getStyle($i . ($j + $startCell))->getAlignment()->setWrapText(true);
                } if (!empty($config['shrink_text'])) {
                    $spreadsheet->getActiveSheet()->getStyle($i . ($j + $startCell))->getAlignment()->setShrinkToFit(true);
                }
                $spreadsheet->getActiveSheet()->getStyle($i . ($j + $startCell))->getBorders()->getAllBorders()->setBorderStyle(Border::BORDER_THIN);
            }
        }

        // change format cell to text
        if (!empty($config['cell_text'])) {
            foreach ($config['cell_text'] as $cellAlphabet) {
                for ($j=0; $j < $data['total']; $j++) {
                    $spreadsheet->getActiveSheet()->getStyle($cellAlphabet . ($j + $startCell))->getNumberFormat()->setFormatCode(NumberFormat::FORMAT_TEXT);
                    $spreadsheet->getActiveSheet()->setCellValueExplicit($cellAlphabet . ($j + $startCell), $spreadsheet->getActiveSheet()->getCell($cellAlphabet . ($j + $startCell))->getValue(), DataType::TYPE_STRING);
                }
            }
        }
        
        for ($j=0; $j < $data['total']; $j++) {
            $spreadsheet->getActiveSheet()->getStyle($lastColumn . ($j + $startCell))->getAlignment()->setVertical('center');
            $spreadsheet->getActiveSheet()->getStyle($lastColumn . ($j + $startCell))->getAlignment()->setWrapText(true);
            $spreadsheet->getActiveSheet()->getStyle($lastColumn . ($j + $startCell))->getAlignment()->setShrinkToFit(true);
            $spreadsheet->getActiveSheet()->getStyle($lastColumn . ($j + $startCell))->getBorders()->getAllBorders()->setBorderStyle(Border::BORDER_THIN);
        }
    
        foreach ($colWidth as $col => $width) {
            $spreadsheet->getActiveSheet()->getColumnDimension($col)->setWidth($width);
        }
    
        if (!empty($additionalData)) {
            foreach ($additionalData as $key=>$additional) {
                $spreadsheet = $this->addNewSheet($request, $reader, $spreadsheet, $additional['template'], $additional['title'], $additional['data'], $additional['col_width'], $key + 1, (isset($additional['start_cell']) ? $additional['start_cell'] : 1));
            }
            $spreadsheet->setActiveSheetIndex(0);
        }
    
        $timestamp = time();
        $writer     = IOFactory::createWriter($spreadsheet, 'Xlsx');
        $tempFile   = tempnam(File::sysGetTempDir(), 'phpxltmp');
        $tempFile   = $tempFile ?: __DIR__ . '/../../../public/uploads/export_user_' . $timestamp . ".xlsx";
        $writer->save($tempFile);
        return $tempFile;
    }

    public function addNewSheet(Request $request, $reader, $spreadsheet, $template, $title, $data, $colWidth, $index, $startCell=1) {
        $view           = Twig::fromRequest($request);
        $htmlString     = $view->fetch($template, ['data' => $data]);

        $reader->setSheetIndex($index);
        $reader->loadFromString($htmlString, $spreadsheet);

        $lastColumn = $spreadsheet->getActiveSheet()->getHighestColumn();

        if (isset($data['total']))
            $totalData = $data['total'];
        else
            $totalData = count($data) + 1;

        foreach ($colWidth as $col => $width) {
            $spreadsheet->getActiveSheet()->getColumnDimension($col)->setWidth($width);
        }

        for ($i = 'A'; $i != $lastColumn; $i++) {
            for ($j=0; $j < $totalData; $j++) {
                $spreadsheet->getActiveSheet()->getStyle($i . ($j + $startCell))->getAlignment()->setVertical('center');
                $spreadsheet->getActiveSheet()->getStyle($i . ($j + $startCell))->getAlignment()->setWrapText(true);
                $spreadsheet->getActiveSheet()->getStyle($i . ($j + $startCell))->getAlignment()->setShrinkToFit(true);
                $spreadsheet->getActiveSheet()->getStyle($i . ($j + $startCell))->getBorders()->getAllBorders()->setBorderStyle(Border::BORDER_THIN);
            }
        }

        for ($j=0; $j < $totalData; $j++) {
            $spreadsheet->getActiveSheet()->getStyle($lastColumn . ($j + $startCell))->getAlignment()->setVertical('center');
            $spreadsheet->getActiveSheet()->getStyle($lastColumn . ($j + $startCell))->getAlignment()->setWrapText(true);
            $spreadsheet->getActiveSheet()->getStyle($lastColumn . ($j + $startCell))->getAlignment()->setShrinkToFit(true);
            $spreadsheet->getActiveSheet()->getStyle($lastColumn . ($j + $startCell))->getBorders()->getAllBorders()->setBorderStyle(Border::BORDER_THIN);
        }

        $title = preg_replace('/[\[\]\/\\\\\*?\:\'"]+/', '-', $title);
        $title = strlen($title) > 30 ? substr($title, 0, 30) : $title;
        $spreadsheet->getActiveSheet()->setTitle($title);

        return $spreadsheet;
    }
    
    public function formatDateIndonesia($date, $format='%d %F %Y') {
        $listMonth = ["Januari", "Februari", "Maret", "April", "Mei", "Juni", "Juli", "Agustus", "September", "Oktober", "November", "Desember"];
        $listDay = ["Mon" => "Senin", "Tue" => "Selasa", "Wed" => "Rabu", "Thu" => "Kamis", "Fri" => "Jumat", "Sat" => "Sabtu", "Sun" => "Minggu"];

        $timeStamp = strtotime($date);

        $tmpDate = ['day' => $listDay[date('D', $timeStamp)], 'month' => $listMonth[date('m', $timeStamp) - 1], 'year' => date('Y', $timeStamp), 'date' => date('d', $timeStamp)];

        $resultDate = $format;
        $resultDate = str_replace('%d', $tmpDate['date'], $resultDate);
        $resultDate = str_replace('%F', $tmpDate['month'], $resultDate);
        $resultDate = str_replace('%Y', $tmpDate['year'], $resultDate);
        $resultDate = str_replace('%l', $tmpDate['day'], $resultDate);

        return $resultDate;
    }
    
    public function baseUrl($extended_url="") {

        $http = 'http' . ((isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] == 'on') ? 's' : '') . '://';
        $newurl = str_replace("index.php", "", $_SERVER['SCRIPT_NAME']);


        $domain = $_SERVER['HTTP_HOST'];

        if (strpos($domain, 'dev') !== false || strpos($domain, 'localhost') !== false) {
            if($_SERVER['SERVER_NAME'] == 'localhost') {
                $baseUrl    = "$http" . $_SERVER['SERVER_NAME'].':'.$_SERVER['SERVER_PORT'] . "" . $newurl;
            } else {
                $baseUrl    = "$http" . $_SERVER['SERVER_NAME'] . "" . $newurl;
            }
        } else {
            $baseUrl = 'https://eraport-worker.edmission.id/';
        }
        
        return $baseUrl.''.$extended_url;
    }
    
    public function deleteFolder($folderPath) {
        // Cek apakah folder ada dan merupakan direktori
        if (is_dir($folderPath)) {
            // Ambil semua file dan sub-folder dalam folder (kecuali . dan ..)
            $files = array_diff(scandir($folderPath), array('.', '..'));
            
            // Loop melalui semua file dan sub-folder
            foreach ($files as $file) {
                $filePath = $folderPath . DIRECTORY_SEPARATOR . $file;
                
                // Jika item adalah direktori, hapus isinya secara rekursif
                if (is_dir($filePath)) {
                    deleteFolder($filePath);
                } else {
                    // Jika item adalah file, hapus file tersebut
                    chmod($filePath, 0777); // Berikan izin penuh jika diperlukan
                    if (!unlink($filePath)) {
                        echo "Gagal menghapus file: $filePath\n";
                    }
                }
            }
    
            // Setelah semua isinya dihapus, hapus folder
            if (!rmdir($folderPath)) {
                echo "Gagal menghapus folder: $folderPath\n";
            }
        } else {
            echo "Folder tidak ditemukan: $folderPath\n";
        }
    }

    public function cmToPx($cm, $dpi = 96) {
        return $cm * ($dpi / 2.54);
    }
}

?>