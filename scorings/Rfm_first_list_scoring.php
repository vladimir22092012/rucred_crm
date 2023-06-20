<?php

class Rfm_first_list_scoring extends Core
{
    private $user_id;
    private $order_id;
    private $audit_id;
    private $type;


    public function run_scoring($scoring_id)
    {
        $update = array();
        $tempScore = RfmscoringORM::query()->where('type', '=', 'first_list')->first();

        $scoring_type = $this->scorings->get_type('Rfm_first_listScoring');

        if ($scoring = $this->scorings->get_scoring($scoring_id))
        {
            if ($order = $this->orders->get_order((int)$scoring->order_id))
            {

                if (empty($order->lastname))
                {
                    $update = array(
                        'status' => 'error',
                        'string_result' => 'в заявке не указана фамилия'
                    );
                }
                elseif (empty($order->firstname))
                {
                    $update = array(
                        'status' => 'error',
                        'string_result' => 'в заявке не указано имя'
                    );
                }
                elseif (empty($order->patronymic))
                {
                    $update = array(
                        'status' => 'error',
                        'string_result' => 'в заявке не указано отчество'
                    );
                }
                else
                {
                    $fio = $order->lastname.' '.$order->firstname.' '.$order->patronymic;
                    $score = RfmscoringORM::query()
                        ->where('data','=', $fio)
                        ->where('type', '=', 'first_list')
                        ->first();


                    $update = array(
                        'status' => 'completed',
                        'body' => '',
                        'success' => empty($score) ? 1 : 0
                    );
                    if ($score)
                    {
                        $update['body'] = serialize($score->attributesToArray());
                        $update['string_result'] = '<p>Клиент найден в списке: Перечень террористов и экстремистов</p>'.
                            '<p>Версия файла: ' . $score->created.'</p>';

                        $scoring_types = $this->scorings->get_types();

                        if ($order_scorings = $this->scorings->get_scorings(array('order_id' => $scoring->order_id)))
                        {
                            foreach ($order_scorings as $order_scoring)
                            {
                                if ($scoring_types[$order_scoring->type]->is_paid && $order_scoring->status == 'new')
                                {
                                    $this->scorings->update_scoring($order_scoring->id, array(
                                        'status' => 'stopped',
                                        'string_result' => 'Остановка по Rfmlist'
                                    ));
                                }

                            }
                        }
                    }
                    else
                        $update['string_result'] = '<p>Клиент не найден в списке: Перечень террористов и экстремистов</p>'.'<p>Версия файла: ' . $tempScore->created.'</p>';

                }

            }
            else
            {
                $update = array(
                    'status' => 'error',
                    'string_result' => 'не найдена заявка'
                );
            }

            if (!empty($update)) {
                RfmscoringresultORM::create([
                    'order_id' => $scoring->order_id,
                    'user_id' => $scoring->user_id,
                    'data' => $update['string_result'],
                    'created' => date('Y-m-d H:i:s'),
                ]);
                $this->scorings->update_scoring($scoring_id, $update);
            }

            return $update;
        }
    }


}
