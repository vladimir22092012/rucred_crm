<?php
error_reporting(-1);
ini_set('display_errors', 'On');
date_default_timezone_set('Europe/Moscow');


chdir(dirname(__FILE__) . '/../');

require __DIR__ . '/../vendor/autoload.php';

use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Style\Color;
use PhpOffice\PhpSpreadsheet\Style\Alignment;
use PhpOffice\PhpSpreadsheet\Style\Border;
use PhpOffice\PhpSpreadsheet\Style\NumberFormat;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;
use PhpOffice\PhpSpreadsheet\Style\Fill;

class CreatePayments extends Core
{
    public function __construct()
    {
        parent::__construct();
        $this->run();
    }

    private function run()
    {
        try {

            $date_from = date('Y-m-d 00:00:00', strtotime('+3 days'));
            $date_to = date('Y-m-d 23:59:59', strtotime('+3 days'));

            $contracts = $this->contracts->get_contracts(['return_date_from' => $date_from, 'return_date_to' => $date_to]);

            if (!empty($contracts)) {

                $spreadsheet = new Spreadsheet();
                $spreadsheet->getDefaultStyle()->getFont()->setName('Times New Roman')->setSize(10);

                $sheet = $spreadsheet->getActiveSheet();
                $sheet->getDefaultRowDimension()->setRowHeight(20);
                $sheet->getColumnDimension('A')->setWidth(20);
                $sheet->getColumnDimension('B')->setWidth(20);
                $sheet->getColumnDimension('C')->setWidth(20);
                $sheet->getColumnDimension('D')->setWidth(15);
                $sheet->getColumnDimension('E')->setWidth(15);
                $sheet->getColumnDimension('F')->setWidth(15);
                $sheet->getColumnDimension('G')->setWidth(15);
                $sheet->getColumnDimension('H')->setWidth(90);
                $sheet->getColumnDimension('I')->setWidth(20);
                $sheet->getColumnDimension('J')->setWidth(20);
                $sheet->getColumnDimension('K')->setWidth(20);
                $sheet->getColumnDimension('L')->setWidth(20);

                $styles =
                    [
                        'font' => [
                            'bold' => true,
                        ],
                        'alignment' => [
                            'horizontal' => Alignment::HORIZONTAL_CENTER,
                            'vertical' => Alignment::VERTICAL_CENTER,
                            'wrapText' => true,
                        ],
                        'borders' => [
                            'outline' => [
                                'borderStyle' => Border::BORDER_THIN,
                                'color' => ['argb' => 'black'],
                            ],
                        ],
                        'fill' => [
                            'fillType' => Fill::FILL_SOLID,
                            'startColor' => ['argb' => 'B5B8B1']
                        ]
                    ];

                $styles_cells =
                    [
                        'alignment' => [
                            'horizontal' => Alignment::HORIZONTAL_CENTER,
                            'vertical' => Alignment::VERTICAL_CENTER,
                            'wrapText' => true,
                        ],

                        'borders' => [
                            'outline' => [
                                'borderStyle' => Border::BORDER_THIN,
                                'color' => ['argb' => 'black'],
                            ],
                        ]
                    ];

                $result_cells =
                    [
                        'alignment' => [
                            'horizontal' => Alignment::HORIZONTAL_CENTER,
                            'vertical' => Alignment::VERTICAL_CENTER,
                            'wrapText' => true,
                        ],

                        'borders' => [
                            'outline' => [
                                'borderStyle' => Border::BORDER_THIN,
                                'color' => ['argb' => 'black'],
                            ],
                        ],
                        'fill' => [
                            'fillType' => Fill::FILL_SOLID,
                            'startColor' => ['argb' => 'ffff00']
                        ]
                    ];

                $sheet->setCellValue('A1', date('Y-m-d H:i:s'));
                $sheet->mergeCells('A3:A4')->getStyle('A3:A4')->applyFromArray($styles);
                $sheet->mergeCells('B3:B4')->getStyle('B3:B4')->applyFromArray($styles);
                $sheet->mergeCells('C3:C4')->getStyle('C3:C4')->applyFromArray($styles);
                $sheet->mergeCells('D3:D4')->getStyle('D3:D4')->applyFromArray($styles);
                $sheet->mergeCells('E3:G3')->getStyle('E3:G3')->applyFromArray($styles);
                $sheet->mergeCells('H3:H4')->getStyle('H3:H4')->applyFromArray($styles);
                $sheet->mergeCells('I3:I4')->getStyle('I3:I4')->applyFromArray($styles);
                $sheet->mergeCells('J3:K3')->getStyle('J3:K3')->applyFromArray($styles);
                $sheet->mergeCells('L3:L4')->getStyle('L3:L4')->applyFromArray($styles);

                $sheet->getStyle('A3')->applyFromArray($styles);
                $sheet->getStyle('A3')->applyFromArray($styles);
                $sheet->getStyle('B3')->applyFromArray($styles);
                $sheet->getStyle('C3')->applyFromArray($styles);
                $sheet->getStyle('D3')->applyFromArray($styles);
                $sheet->getStyle('E3')->applyFromArray($styles);
                $sheet->getStyle('E4')->applyFromArray($styles);
                $sheet->getStyle('F4')->applyFromArray($styles);
                $sheet->getStyle('G4')->applyFromArray($styles);
                $sheet->getStyle('H3')->applyFromArray($styles);
                $sheet->getStyle('I3')->applyFromArray($styles);
                $sheet->getStyle('J3')->applyFromArray($styles);
                $sheet->getStyle('J4')->applyFromArray($styles);
                $sheet->getStyle('K4')->applyFromArray($styles);
                $sheet->getStyle('L3')->applyFromArray($styles);

                $sheet->setCellValue('A3', 'Дата платежа');
                $sheet->setCellValue('B3', 'Плательщик');
                $sheet->setCellValue('C3', 'Заемщик');
                $sheet->setCellValue('D3', 'Платеж');
                $sheet->setCellValue('E3', 'Структура платежа');
                $sheet->setCellValue('E4', 'Основной долг');
                $sheet->setCellValue('F4', 'Проценты');
                $sheet->setCellValue('G4', 'Прочее');
                $sheet->setCellValue('H3', 'Назначение платежа');
                $sheet->setCellValue('I3', 'ИНН заемщика');
                $sheet->setCellValue('J3', 'Договор микрозайма');
                $sheet->setCellValue('J4', 'Номер');
                $sheet->setCellValue('K4', 'Дата');
                $sheet->setCellValue('L3', 'Код платежа');

                $i = 6;

                $all_sum_pay = 0;
                $all_body_pay = 0;
                $all_percents_pay = 0;
                $all_others_pay = 0;

                foreach ($contracts as $contract) {
                    $order = $this->orders->get_order($contract->order_id);
                    $company = $this->Companies->get_company($order->company_id);
                    $fio = "$order->lastname $order->firstname $order->patronymic";

                    $payment_schedule = json_decode($order->payment_schedule, true);
                    $payment_schedule = end($payment_schedule);
                    $date = date('Y-m-d');

                    foreach ($payment_schedule as $payday => $payment) {
                        if ($payday != 'result') {
                            $payday = date('Y-m-d', strtotime($payday));
                            if ($payday > $date) {
                                $next_payment = $payment;
                                break;
                            }
                        }
                    }

                    $operation_id = $this->operations->add_operation(array(
                        'contract_id' => $contract->id,
                        'user_id' => $contract->user_id,
                        'order_id' => $contract->order_id,
                        'type' => 'PAYMENT',
                        'amount' => $next_payment['pay_sum'],
                        'created' => date('Y-m-d H:i:s'),
                        'loan_body_summ' => $next_payment['loan_body_pay'],
                        'loan_percents_summ' => $next_payment['loan_percents_pay']
                    ));

                    $operation = $this->operations->get_operation($operation_id);

                    $destination = "{$operation->uid} Оплата по договору микрозайма № $order->uid от $contract->return_date // заёмщик - $fio, ИНН $order->inn";

                    $sheet->getRowDimension($i)->setRowHeight(30);
                    $sheet->getStyle('A' . $i)->applyFromArray($styles);
                    $sheet->getStyle('B' . $i)->applyFromArray($styles_cells);
                    $sheet->getStyle('C' . $i)->applyFromArray($styles_cells);
                    $sheet->getStyle('D' . $i)->applyFromArray($styles);
                    $sheet->getStyle('E' . $i)->applyFromArray($styles_cells);
                    $sheet->getStyle('F' . $i)->applyFromArray($styles_cells);
                    $sheet->getStyle('G' . $i)->applyFromArray($styles_cells);
                    $sheet->getStyle('H' . $i)->applyFromArray($styles_cells);
                    $sheet->getStyle('I' . $i)->applyFromArray($styles_cells);
                    $sheet->getStyle('J' . $i)->applyFromArray($styles_cells);
                    $sheet->getStyle('K' . $i)->applyFromArray($styles_cells);
                    $sheet->getStyle('L' . $i)->applyFromArray($styles_cells);

                    $sheet->setCellValue('A' . $i, date('d.m.Y', strtotime($contract->return_date)));
                    $sheet->setCellValue('B' . $i, $company->name);
                    $sheet->setCellValue('C' . $i, $fio);
                    $sheet->setCellValue('D' . $i, $next_payment['pay_sum']);
                    $sheet->setCellValue('E' . $i, $next_payment['loan_body_pay']);
                    $sheet->setCellValue('F' . $i, $next_payment['loan_percents_pay']);
                    $sheet->setCellValue('G' . $i, $next_payment['comission_pay']);
                    $sheet->setCellValue('H' . $i, $destination);
                    $sheet->setCellValue('I' . $i, " $order->inn ");
                    $sheet->setCellValue('J' . $i, $order->uid);
                    $sheet->setCellValue('K' . $i, $contract->return_date);
                    $sheet->setCellValue('L' . $i, $operation->uid);

                    $all_sum_pay += $next_payment['pay_sum'];
                    $all_body_pay += $next_payment['loan_body_pay'];
                    $all_percents_pay += $next_payment['loan_percents_pay'];
                    $all_others_pay += $next_payment['comission_pay'];

                    $i++;
                }

                $sheet->getRowDimension(5)->setRowHeight(15);
                $sheet->getStyle('A5')->applyFromArray($result_cells);
                $sheet->getStyle('B5')->applyFromArray($result_cells);
                $sheet->getStyle('C5')->applyFromArray($result_cells);
                $sheet->getStyle('D5')->applyFromArray($result_cells);
                $sheet->getStyle('E5')->applyFromArray($result_cells);
                $sheet->getStyle('F5')->applyFromArray($result_cells);
                $sheet->getStyle('G5')->applyFromArray($result_cells);
                $sheet->getStyle('H5')->applyFromArray($result_cells);
                $sheet->getStyle('I5')->applyFromArray($result_cells);
                $sheet->getStyle('J5')->applyFromArray($result_cells);
                $sheet->getStyle('K5')->applyFromArray($result_cells);
                $sheet->getStyle('L5')->applyFromArray($result_cells);

                $sheet->setCellValue('A5', 'ИТОГО');
                $sheet->setCellValue('D5', $all_sum_pay);
                $sheet->setCellValue('E5', $all_body_pay);
                $sheet->setCellValue('F5', $all_percents_pay);
                $sheet->setCellValue('G5', $all_others_pay);


                $writer = new Xlsx($spreadsheet);
                $writer->save(ROOT . "/files/paymentlist_" . date('d.m.Y') . ".xlsx");

                $payment =
                    [
                        'status' => 0,
                        'created' => date('Y-m-d H:i:s'),
                        'updated' => date('Y-m-d H:i:s'),
                        'company_id' => $order->company_id,
                        'payday' => $contract->return_date,
                        'payment_xls' => "/files/paymentlist_" . date('d.m.Y') . ".xlsx"
                    ];

                $this->Payments->add($payment);
            }
        }catch (Exception $e){
            $this->Logs->add(['text' => $e]);
        }
    }
}

new CreatePayments();