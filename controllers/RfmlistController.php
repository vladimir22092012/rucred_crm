<?php
class RfmlistController extends Controller
{

    public $import_files_dir = 'files/import/';
    public $import_file = 'rfmlist.xml';

    public function fetch()
    {
        if ($this->request->method('post')) {
            $action = $this->request->post('action', 'string');

            switch ($action):

                case 'start_check':
                    $this->action_start_check();
                    break;
            endswitch;
        }

        $this->design->assign('import_files_dir', $this->import_files_dir);
        if (!is_writable($this->import_files_dir))
            $this->design->assign('message_error', 'no_permission');

        if ($this->request->post('run')) {
            $import_file = $this->request->files("import_file");
            $ext = strtolower(pathinfo($import_file['name'], PATHINFO_EXTENSION));

            if (empty($import_file)) {
                $this->design->assign('error', 'Загрузите файл');
            } elseif (!in_array($ext, array('xml', 'xlsx'))) {
                $this->design->assign('error', 'Принимаются файлы в формате xml, xlsx');
            } else {

                $uploaded_name = $this->request->files("import_file", "tmp_name");
                $success = false;
                if ($ext == 'xml') {
                    $xml = simplexml_load_file($uploaded_name);
                    foreach ($xml as $item) {
                        if (isset($item->Субъект)) {
                            RfmscoringORM::where('type', '=', 'first_list')->delete();
                            foreach ($item->Субъект as $value) {
                                $data = '';
                                if (isset($value->Орг)) {
                                    $data = $value->Орг->Наименование;
                                }
                                if (isset($value->ФЛ)) {
                                    $data = $value->ФЛ->ФИО;
                                }
                                RfmscoringORM::create([
                                    'data' => mb_strtolower($data),
                                    'type' => 'first_list',
                                    'created' => date('Y-m-d H:i:s')
                                ]);
                            }
                            $success = true;
                        }
                        if (isset($item->Решение)) {
                            $success = true;
                            RfmscoringORM::where('type', '=', 'second_list')->delete();
                            foreach ($item->Решение as $value) {
                                foreach ($value->СписокСубъектов->Субъект as $subject) {
                                    if (!isset($subject->ФЛ) && $subject->ФЛ->ФИО == '?') {
                                        continue;
                                    }
                                    RfmscoringORM::create([
                                        'data' => mb_strtolower($subject->ФЛ->ФИО),
                                        'type' => 'second_list',
                                        'created' => date('Y-m-d H:i:s')
                                    ]);
                                }
                            }
                        }
                        if (isset($item->INDIVIDUAL)) {
                            $success = true;
                            RfmscoringORM::where('type', '=', 'third_list')->delete();
                            foreach ($item->INDIVIDUAL as $value) {
                                $data = $value->FIRST_NAME . ' ' . $value->SECOND_NAME . ' ';
                                if (is_string($value->THIRD_NAME)) {
                                    $data .= $value->THIRD_NAME;
                                }

                                if (isset($value->FOURTH_NAME) && is_string($value->FOURTH_NAME)) {
                                    $data .= ' '.$value->FOURTH_NAME;
                                }
                                RfmscoringORM::create([
                                    'data' => mb_strtolower($data),
                                    'type' => 'third_list',
                                    'created' => date('Y-m-d H:i:s')
                                ]);
                            }
                        }
                    }
                }
                if ($ext == 'xlsx') {
                    $filename = $this->config->root_dir.'files/'.md5(time()).'.xlsx';
                    if (move_uploaded_file($import_file['tmp_name'], $filename)) {
                        RfmscoringORM::where('type', '=', 'fourth_list')->delete();
                        $success = true;
                        $reader = new PhpOffice\PhpSpreadsheet\Reader\Xlsx();
                        $sheet = $reader->load($filename);
                        if (count($sheet->getActiveSheet()->toArray()) > 0) {
                            foreach ($sheet->getActiveSheet()->toArray() as $value) {
                                $success = true;
                                if (isset($value[1]) && !in_array($value[1], ['ФИО','фио','Фио'])) {
                                    RfmscoringORM::create([
                                        'data' => mb_strtolower($value[1]),
                                        'type' => 'fourth_list',
                                        'created' => date('Y-m-d H:i:s')
                                    ]);
                                }
                            }
                        }
                    } else {
                        $success = false;
                    }

                }

                $this->design->assign('success', $success);

            }
        }

        $types = [
            'rfm_first_list' => 'Перечень террористов и экстремистов',
            'rfm_second_list' => 'Перечень о замораживании средств',
            'rfm_third_list' => 'Список ООН',
            'rfm_fourth_list' => 'Решение судов',
        ];
        foreach ($types as $type) {
            $isExist = RfmscoringORM::query()->where('type', '=', $type)->first();
            if (!$isExist) {
                unset($types[$type]);
            }
        }
        $this->design->assign('types', $types);

        return $this->design->fetch('rfmlist.tpl');
    }

    public function action_start_check() {

        $type = $this->request->post('type', 'string');

        $filter = array();
        $filter['status'] = [5, 7, 17, 18, 19];

        $orders = $this->orders->get_orders($filter);

        foreach ($orders as $order) {

            $scoring = ScoringsORM::create([
                'user_id' => $order->user_id,
                'order_id' => $order->order_id,
                'type' => $type,
                'status' => 'completed',
                'start_date' => date('Y-m-d H:i:s'),
            ]);
            if ($scoring) {
                $classname = $scoring->type."_scoring";
                $this->{ucfirst($classname)}->run_scoring($scoring->id);
            }
        }

        echo json_encode(['error' => 0, 'text' => 'Проверки пройдены.']);
        exit;
    }
}
