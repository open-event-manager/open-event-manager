<?php


namespace App\Service;


use App\Entity\Rooms;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;
use Symfony\Component\Security\Core\Authentication\Token\Storage\TokenStorageInterface;
use Symfony\Contracts\Translation\TranslatorInterface;

class TeilnehmerExcelService
{
    private $spreadsheet;
    private $writer;
    private $translator;
    private $tokenStorage;

    public function __construct(TranslatorInterface $translator, TokenStorageInterface $tokenStorage)
    {
        $this->spreadsheet = new Spreadsheet();
        $this->writer = new Xlsx($this->spreadsheet);
        $this->translator = $translator;
        $this->tokenStorage = $tokenStorage;
    }

    function generateTeilnehmerliste(Rooms $rooms)
    {
        $alphas = $this->createColumnsArray('ZZ');
        $count = 0;
        $participants = $this->spreadsheet->createSheet();
        $participants->setTitle($this->translator->trans('Teilnehmer'));
        $participants->setCellValue($alphas[$count++] . '1', $this->translator->trans('Vorname'));
        $participants->setCellValue($alphas[$count++] . '1', $this->translator->trans('Nachname'));
        $participants->setCellValue($alphas[$count++] . '1', $this->translator->trans('Email'));
        $participants->setCellValue($alphas[$count++] . '1', $this->translator->trans('Telefon'));
        $participants->setCellValue($alphas[$count++] . '1', $this->translator->trans('Status'));
        $participants->setCellValue($alphas[$count++] . '1', $this->translator->trans('Moderator'));
        $count = 0;
        $count2 = 2;
        foreach ($rooms->getUser() as $data) {
            $participants->setCellValue($alphas[$count++] . $count2, $data->getFirstName());
            $participants->setCellValue($alphas[$count++] . $count2, $data->getLastName());
            $participants->setCellValue($alphas[$count++] . $count2, $data->getEmail());
            $participants->setCellValue($alphas[$count++] . $count2, $data->getPhone());
            $participants->setCellValue($alphas[$count++] . $count2, $this->translator->trans('Teilnehmer'));
            $participants->setCellValue($alphas[$count++] . $count2, $rooms->getModerator() == $data ? $this->translator->trans('Ja') : $this->translator->trans('Nein'));
            $count2++;
            $count = 0;
        }
        foreach ($rooms->getWaitinglists() as $data) {
            $participants->setCellValue($alphas[$count++] . $count2, $data->getUser()->getFirstName());
            $participants->setCellValue($alphas[$count++] . $count2, $data->getUser()->getLastName());
            $participants->setCellValue($alphas[$count++] . $count2, $data->getUser()->getEmail());
            $participants->setCellValue($alphas[$count++] . $count2, $data->getUser()->getPhone());
            $participants->setCellValue($alphas[$count++] . $count2, $this->translator->trans('Warteliste'));
            $participants->setCellValue($alphas[$count++] . $count2,  $this->translator->trans('Nein'));
            $count2++;
            $count = 0;
        }
        foreach ($rooms->getStorno() as $data) {
            $participants->setCellValue($alphas[$count++] . $count2, $data->getFirstName());
            $participants->setCellValue($alphas[$count++] . $count2, $data->getLastName());
            $participants->setCellValue($alphas[$count++] . $count2, $data->getEmail());
            $participants->setCellValue($alphas[$count++] . $count2, $data->getPhone());
            $participants->setCellValue($alphas[$count++] . $count2, $this->translator->trans('Storniert'));
            $participants->setCellValue($alphas[$count++] . $count2,  $this->translator->trans('Nein'));
            $count2++;
            $count = 0;
        }

        $this->spreadsheet->removeSheetByIndex(0);
        $fileName = 'teilnehmer_'.$rooms->getName().'.xlsx';
        $temp_file = tempnam(sys_get_temp_dir(), $fileName);

        // Create the excel file in the tmp directory of the system
        $this->writer->save($temp_file);

        // Return the excel file as an attachment
        return $temp_file;

    }

    private function createColumnsArray($end_column, $first_letters = '')
    {
        $columns = array();
        $length = strlen($end_column);
        $letters = range('A', 'Z');

        // Iterate over 26 letters.
        foreach ($letters as $letter) {
            // Paste the $first_letters before the next.
            $column = $first_letters . $letter;

            // Add the column to the final array.
            $columns[] = $column;

            // If it was the end column that was added, return the columns.
            if ($column == $end_column)
                return $columns;
        }

        // Add the column children.
        foreach ($columns as $column) {
            // Don't itterate if the $end_column was already set in a previous itteration.
            // Stop iterating if you've reached the maximum character length.
            if (!in_array($end_column, $columns) && strlen($column) < $length) {
                $new_columns = $this->createColumnsArray($end_column, $column);
                // Merge the new columns which were created with the final columns array.
                $columns = array_merge($columns, $new_columns);
            }
        }

        return $columns;
    }


}