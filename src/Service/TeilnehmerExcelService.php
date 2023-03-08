<?php


namespace App\Service;


use App\Entity\Group;
use App\Entity\Rooms;
use App\Entity\User;
use Doctrine\ORM\EntityManagerInterface;
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
    private $em;
    private $sheet;
    private $alphas;
    private $lineCounter;
    private $mapping;
    private $userEventCreateService;
    public function __construct(TranslatorInterface $translator, TokenStorageInterface $tokenStorage, EntityManagerInterface $entityManager, UserEventCreateService $userEventCreateService)
    {
        $this->spreadsheet = new Spreadsheet();
        $this->writer = new Xlsx($this->spreadsheet);
        $this->translator = $translator;
        $this->tokenStorage = $tokenStorage;
        $this->em = $entityManager;
        $this->userEventCreateService = $userEventCreateService;
    }

    function generateSpreadsheet()
    {
        $this->sheet = $this->spreadsheet->createSheet();
        $this->sheet->setTitle($this->translator->trans('Teilnehmer'));
        $this->alphas = $this->createColumnsArray('ZZ');
        $this->lineCounter = 1;
    }

    function generateHeader(Rooms $rooms)
    {
        $this->mapping = array();
        $count = 0;
        $this->sheet->setCellValue($this->alphas[$count++] . $this->lineCounter, $this->translator->trans('Standort'));
        $this->sheet->setCellValue($this->alphas[$count++] . $this->lineCounter, $rooms->getStandort()->getName());
        $count = 0;
        $this->lineCounter++;
        $this->sheet->setCellValue($this->alphas[$count++] . $this->lineCounter, $this->translator->trans('Adresse'));
        $this->sheet->setCellValue($this->alphas[$count++] . $this->lineCounter,
            $rooms->getStandort()->getStreet() . ' ' . $rooms->getStandort()->getNumber() . ', ' . $rooms->getStandort()->getPlz() . ' ' . $rooms->getStandort()->getCity());
        $count = 0;
        $this->lineCounter++;
        $this->sheet->setCellValue($this->alphas[$count++] . $this->lineCounter, $this->translator->trans('Raumnummer'));
        $this->sheet->setCellValue($this->alphas[$count++] . $this->lineCounter,
            $rooms->getStandort()->getRoomnumber());
        $count = 0;
        $this->lineCounter++;
        $this->sheet->setCellValue($this->alphas[$count++] . $this->lineCounter, $this->translator->trans('Datum'));
        $this->sheet->setCellValue($this->alphas[$count++] . $this->lineCounter,
            $rooms->getStart()->format('d.m.Y'));
        $count = 0;
        $this->lineCounter++;
        $this->sheet->setCellValue($this->alphas[$count++] . $this->lineCounter, $this->translator->trans('Uhrzeit'));
        $this->sheet->setCellValue($this->alphas[$count++] . $this->lineCounter,
            $rooms->getStart()->format('H:i'));
        $this->sheet->setCellValue($this->alphas[$count++] . $this->lineCounter,
            $rooms->getEnddate()->format('H:i'));
        $count = 0;
        $this->lineCounter++;
        $this->lineCounter++;
        $this->sheet->setCellValue($this->alphas[$count++] . $this->lineCounter, $this->translator->trans('Anwesend'));
        $this->sheet->setCellValue($this->alphas[$count++] . $this->lineCounter, $this->translator->trans('Vorname'));
        $this->sheet->setCellValue($this->alphas[$count++] . $this->lineCounter, $this->translator->trans('Nachname'));
        $this->sheet->setCellValue($this->alphas[$count++] . $this->lineCounter, $this->translator->trans('Email'));
        $this->sheet->setCellValue($this->alphas[$count++] . $this->lineCounter, $this->translator->trans('Adresse'));
        $this->sheet->setCellValue($this->alphas[$count++] . $this->lineCounter, $this->translator->trans('Telefon'));
        $this->sheet->setCellValue($this->alphas[$count++] . $this->lineCounter, $this->translator->trans('Status'));
        $this->sheet->setCellValue($this->alphas[$count++] . $this->lineCounter, $this->translator->trans('Angemeldet am'));
        $this->sheet->setCellValue($this->alphas[$count++] . $this->lineCounter, $this->translator->trans('Organisator'));
        $this->sheet->setCellValue($this->alphas[$count++] . $this->lineCounter, $this->translator->trans('Gruppenleiter ID'));
        $this->sheet->setCellValue($this->alphas[$count++] . $this->lineCounter, $this->translator->trans('GruppenmitgliederID'));
        foreach ($rooms->getFreeFields() as $ff) {
            $this->mapping[$ff->getId()] = $count;
            $this->sheet->setCellValue($this->alphas[$count++] . $this->lineCounter, $ff->getLabel());
        }

    }

    private function generateParticipants(Rooms $rooms)
    {

        $count = 0;
        $this->lineCounter++;
        $userTmp = array();
        foreach ($rooms->getUser() as $data) {
            if (sizeof($this->em->getRepository(Group::class)->atendeeIsInGroup($data, $rooms)) === 0) {
                $userTmp[] = $data;
                $userTmp = array_merge($userTmp,$this->em->getRepository(User::class)->userFromLeaderAndRoom($data, $rooms));
            }
        }


        foreach ($userTmp as $data) {
            $group = $this->em->getRepository(Group::class)->atendeeIsInGroup($data, $rooms);
            $groupArr = array();
            foreach ($group as $data2) {
                $groupArr[] = $data2->getId();
            }
            $groupleader = $this->em->getRepository(Group::class)->findBy(array('leader' => $data, 'rooms' => $rooms));
            $groupLeaderArr = array();
            foreach ($groupleader as $data2) {
                $groupLeaderArr[] = $data2->getId();
            }
            $this->sheet->setCellValue($this->alphas[$count++] . $this->lineCounter, '');
            $this->sheet->setCellValue($this->alphas[$count++] . $this->lineCounter, $data->getFirstName());
            $this->sheet->setCellValue($this->alphas[$count++] . $this->lineCounter, $data->getLastName());
            $this->sheet->setCellValue($this->alphas[$count++] . $this->lineCounter, $data->getEmail());
            $this->sheet->setCellValue($this->alphas[$count++] . $this->lineCounter, $data->getAddress());
            $this->sheet->setCellValueExplicit($this->alphas[$count++] . $this->lineCounter, $data->getPhone(), \PhpOffice\PhpSpreadsheet\Cell\DataType::TYPE_STRING);
            $this->sheet->setCellValue($this->alphas[$count++] . $this->lineCounter, $this->translator->trans('Teilnehmer'));
            $this->sheet->setCellValue($this->alphas[$count++] . $this->lineCounter, $this->userEventCreateService->generateCreatedString($data,$rooms));
            $this->sheet->setCellValue($this->alphas[$count++] . $this->lineCounter, $rooms->getModerator() == $data ? $this->translator->trans('Ja') : $this->translator->trans('Nein'));
            $this->sheet->setCellValue($this->alphas[$count++] . $this->lineCounter, implode(', ', $groupLeaderArr));
            $this->sheet->setCellValue($this->alphas[$count++] . $this->lineCounter, implode(', ', $groupArr));
            foreach ($data->getFreeFieldsFromRoom($rooms) as $ff) {
                $this->sheet->setCellValue($this->alphas[$this->mapping[$ff->getFreeField()->getId()]] . $this->lineCounter, $ff->getAnswer());
            }
            $this->lineCounter++;
            $count = 0;
        }
        foreach ($rooms->getWaitinglists() as $data) {
            $group = $this->em->getRepository(Group::class)->atendeeIsInGroup($data->getUser(), $rooms);
            $groupArr = array();
            foreach ($group as $data2) {
                $groupArr[] = $data2->getId();
            }
            $groupleader = $this->em->getRepository(Group::class)->findBy(array('leader' => $data->getUser(), 'rooms' => $rooms));
            $groupLeaderArr = array();
            foreach ($groupleader as $data2) {
                $groupLeaderArr[] = $data2->getId();
            }
            $this->sheet->setCellValue($this->alphas[$count++] . $this->lineCounter, '');
            $this->sheet->setCellValue($this->alphas[$count++] . $this->lineCounter, $data->getUser()->getFirstName());
            $this->sheet->setCellValue($this->alphas[$count++] . $this->lineCounter, $data->getUser()->getLastName());
            $this->sheet->setCellValue($this->alphas[$count++] . $this->lineCounter, $data->getUser()->getEmail());
            $this->sheet->setCellValue($this->alphas[$count++] . $this->lineCounter, $data->getUser()->getAddress());
            $this->sheet->setCellValueExplicit($this->alphas[$count++] . $this->lineCounter, $data->getUser()->getPhone(), \PhpOffice\PhpSpreadsheet\Cell\DataType::TYPE_STRING);
            $this->sheet->setCellValue($this->alphas[$count++] . $this->lineCounter, $this->translator->trans('Warteliste'));
            $this->sheet->setCellValue($this->alphas[$count++] . $this->lineCounter, $this->translator->trans('Nein'));
            $this->sheet->setCellValue($this->alphas[$count++] . $this->lineCounter, implode(', ', $groupLeaderArr));
            $this->sheet->setCellValue($this->alphas[$count++] . $this->lineCounter, implode(', ', $groupArr));
            $this->lineCounter++;
            $count = 0;
        }
        foreach ($rooms->getStorno() as $data) {
            $group = $this->em->getRepository(Group::class)->atendeeIsInGroup($data, $rooms);
            $groupArr = array();
            foreach ($group as $data2) {
                $groupArr[] = $data2->getId();
            }
            $groupleader = $this->em->getRepository(Group::class)->findBy(array('leader' => $data, 'rooms' => $rooms));
            $groupLeaderArr = array();
            foreach ($groupleader as $data2) {
                $groupLeaderArr[] = $data2->getId();
            }
            $this->sheet->setCellValue($this->alphas[$count++] . $this->lineCounter, '');
            $this->sheet->setCellValue($this->alphas[$count++] . $this->lineCounter, $data->getFirstName());
            $this->sheet->setCellValue($this->alphas[$count++] . $this->lineCounter, $data->getLastName());
            $this->sheet->setCellValue($this->alphas[$count++] . $this->lineCounter, $data->getEmail());
            $this->sheet->setCellValue($this->alphas[$count++] . $this->lineCounter, $data->getAddress());
            $this->sheet->setCellValueExplicit($this->alphas[$count++] . $this->lineCounter, $data->getPhone(), \PhpOffice\PhpSpreadsheet\Cell\DataType::TYPE_STRING);
            $this->sheet->setCellValue($this->alphas[$count++] . $this->lineCounter, $this->translator->trans('Storniert'));
            $this->sheet->setCellValue($this->alphas[$count++] . $this->lineCounter, $this->translator->trans('Nein'));
            $this->sheet->setCellValue($this->alphas[$count++] . $this->lineCounter, implode(', ', $groupLeaderArr));
            $this->sheet->setCellValue($this->alphas[$count++] . $this->lineCounter, implode(', ', $groupArr));
        }
    }

    function generateTeilnehmerliste(Rooms $rooms)
    {
        $this->generateSpreadsheet();
        $this->generateHeader($rooms);
        $this->generateParticipants($rooms);
        $this->spreadsheet->removeSheetByIndex(0);
        $fileName = 'teilnehmer_' . $rooms->getName() . '.xlsx';
        $temp_file = tempnam(sys_get_temp_dir(), $fileName);

        // Create the excel file in the tmp directory of the system
        $this->writer->save($temp_file);

        // Return the excel file as an attachment
        return $temp_file;

    }

    function generateTeilnehmerDayList($rooms, $fileName)
    {
        $this->generateSpreadsheet();
        foreach ($rooms as $data) {
            $this->generateHeader($data);
            $this->generateParticipants($data);
            $this->lineCounter++;
            $this->lineCounter++;
        }

        $this->spreadsheet->removeSheetByIndex(0);
        $fileName = $fileName . '.xlsx';
        $temp_file = tempnam(sys_get_temp_dir(), $fileName);

        // Create the excel file in the tmp directory of the system
        $this->writer->save($temp_file);

        // Return the excel file as an attachment
        return $temp_file;

    }

    private
    function createColumnsArray($end_column, $first_letters = '')
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