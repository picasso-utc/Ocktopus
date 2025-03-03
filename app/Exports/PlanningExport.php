<?php

namespace App\Exports;

use Maatwebsite\Excel\Concerns\WithStyles;
use Maatwebsite\Excel\Concerns\WithTitle;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\FromArray;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;
use PhpOffice\PhpSpreadsheet\Cell\DataValidation;
use PhpOffice\PhpSpreadsheet\Style\Conditional;
use PhpOffice\PhpSpreadsheet\Style\Fill;

class PlanningExport implements FromArray, WithHeadings, WithStyles, WithTitle
{
    protected $schedule;
    protected $colors;
    protected $tasks;

    public function __construct(array $schedule)
    {
        $this->schedule = $schedule;
        
        $this->colors = [
            'Bar' => 'FFA500',
            'Caisse' => '007BFF',
            'Sécu Pente' => 'FF0000',
            'Sécu Escalier' => 'FF1D8D',
            'Ménage' => '32CD32',
            'Sécu Trottoir' => '800080',
        ];
        
        $this->tasks = array_keys($this->colors);
    }

    public function headings(): array
    {
        return array_merge(['Participant'], array_keys($this->schedule));
    }

    public function array(): array
    {
        $formattedData = [];
        $participants = [];
        
        foreach ($this->schedule as $time => $tasks) {
            foreach ($tasks as $participant => $task) {
                $participants[$participant][$time] = $task;
            }
        }

        $participants = $this->shufl($participants);

        foreach ($participants as $participant => $tasks) {
            $row = [$participant];
            foreach (array_keys($this->schedule) as $time) {
                $row[] = $tasks[$time] ?? ''; 
            }
            $formattedData[] = $row;
        }

        return $formattedData;
    }

    public function styles(Worksheet $sheet)
    {
        $highestRow = $sheet->getHighestRow();
        $highestColumn = $sheet->getHighestColumn();
        
        for ($row = 2; $row <= $highestRow; $row++) { 
            for ($col = 'B'; $col <= $highestColumn; $col++) {
                $cell = $col . $row;
                
                // Ajout du menu déroulant
                $validation = $sheet->getCell($cell)->getDataValidation();
                $validation->setType(DataValidation::TYPE_LIST);
                $validation->setErrorStyle(DataValidation::STYLE_STOP);
                $validation->setAllowBlank(false);
                $validation->setShowDropDown(true);
                $validation->setFormula1('"' . implode(',', $this->tasks) . '"');
                
                // Création des règles de mise en forme conditionnelle
                $conditionalStyles = [];
                foreach ($this->colors as $task => $color) {
                    $conditional = new Conditional();
                    $conditional->setConditionType(Conditional::CONDITION_CONTAINSTEXT);
                    $conditional->setOperatorType(Conditional::OPERATOR_CONTAINSTEXT);
                    $conditional->setText($task);
                    $conditional->getStyle()->getFill()->setFillType(Fill::FILL_SOLID)->getStartColor()->setARGB($color);
                    $conditionalStyles[] = $conditional;
                }
                $sheet->getStyle($cell)->setConditionalStyles($conditionalStyles);
            }
        }

        $sheet->getStyle(1)->getFont()->setBold(true);

        foreach (range('A', $highestColumn) as $col) {
            $sheet->getColumnDimension($col)->setWidth(15);
        }
    }

    public function title(): string
    {
        return 'Planning';
    }

    private function shufl($array)
    {
        $keys = array_keys($array);
        shuffle($keys);
        $shuffled = [];
        foreach ($keys as $key) {
            $shuffled[$key] = $array[$key];
        }
        return $shuffled;
    }
}
