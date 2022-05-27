<?php

error_reporting(-1);
ini_set('display_errors', 'On');

class UploadFilesController extends Controller

{
    public function fetch()
    {
        switch ($this->request->post('action', 'string')) :

            case 'add':
                $this->add();
                break;

            case 'remove':
                $this->remove();
                break;

            default:
                $this->response->error = 'undefined action';

        endswitch;
    }

    private function add()
    {
        if ($file = $this->request->files('file')) {

            $ext = pathinfo($file['name'], PATHINFO_EXTENSION);

            do {
                $new_filename = md5(microtime() . rand()) . '.' . $ext;
            } while ($this->users->check_filename($new_filename));

            if (move_uploaded_file($file['tmp_name'], $this->config->root_dir . $this->config->user_files_dir . $new_filename)) {

                $user_id = $this->request->post('user_id');
                $type = $this->request->post('type');

                if ($this->request->post('is_it_scans') == 'yes') {
                    if ($this->request->post('template'))
                        $type = $this->request->post('template');

                    $this->Scans->delete_scan(array(
                        'order_id' => (int)$this->request->post('order_id'),
                        'type' => $type
                    ));


                    $file_id = $this->Scans->add_scan(array(
                        'user_id' => $user_id,
                        'name' => $new_filename,
                        'type' => $type,
                        'status' => 0,
                        'order_id' => (int)$this->request->post('order_id')
                    ));

                } elseif ($this->request->post('ndfl') == 'yes') {

                    $type = 'ndfl';
                    $this->Scans->delete_scan(array(
                        'order_id' => (int)$this->request->post('order_id'),
                        'type' => $type
                    ));


                    $file_id = $this->Scans->add_scan(array(
                        'user_id' => $this->user->id,
                        'name' => $this->request->post('name'),
                        'type' => $type,
                        'status' => 0,
                        'order_id' => (int)$this->request->post('order_id')
                    ));
                } else {
                    $file_id = $this->users->add_file(array(
                        'user_id' => $user_id,
                        'name' => $new_filename,
                        'type' => $type,
                        'status' => 0
                    ));
                }

                if ($card_id = $this->request->post('card_id', 'integer')) {
                    $this->cards->update_card($card_id, array('file_id' => $file_id));
                }
            }
        }

        echo json_encode(['success' => 1, 'message' => 'Файл загружен успешно'], JSON_THROW_ON_ERROR);
        exit;
    }

    private function remove()
    {
        if ($id = $this->request->post('id', 'integer')) {
            $this->users->delete_file($id);

            $this->response->success = 'removed';

        } else {
            $this->response->error = 'empty_file_id';
        }
    }
}
