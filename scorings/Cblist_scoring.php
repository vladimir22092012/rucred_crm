<?php

class Cblist_scoring extends Core
{
    public function run_scoring($scoring_id)
    {
        $scoring = $this->scorings->get_scoring($scoring_id);

        if (!empty($scoring)) {

            $order = $this->orders->get_order((int)$scoring->order_id);

            $fio = "$order->lastname $order->firstname $order->patronymic";

            $birth = date('Y-m-d', strtotime($order->birth));

            $inn = $order->inn;

            $passport_serial = explode(' ', $order->passport_serial);

            $client =
                [
                    'fio' => $fio,
                    'birth' => $birth,
                    'inn' => $inn,
                    'passport_serial' => $passport_serial[0],
                    'passport_number' => $passport_serial[1]
                ];

            $score = $this->Cblist->search($client);


            $update = array(
                'status' => 'completed',
                'body' => '',
                'success' => empty($score) ? 1 : 0
            );

            if ($score) {
                $person = $this->Rfmlist->get_person((int)$score);
                $update['body'] = serialize($person);
                $update['string_result'] = 'Пользователь найден в списке: ' . $person->fio;

                $scoring_types = $this->scorings->get_types();

                if ($order_scorings = $this->scorings->get_scorings(array('order_id' => $scoring->order_id))) {
                    foreach ($order_scorings as $order_scoring) {
                        if ($scoring_types[$order_scoring->type]->is_paid && $order_scoring->status == 'new') {
                            $this->scorings->update_scoring($order_scoring->id, array(
                                'status' => 'stopped',
                                'string_result' => 'Остановка по Cblist'
                            ));
                        }

                    }
                }
            } else
                $update['string_result'] = 'Клиент не найден в списке';

            if (!empty($update))
                $this->scorings->update_scoring($scoring_id, $update);

            return $update;

        }
    }
}