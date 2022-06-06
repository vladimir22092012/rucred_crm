<?php
error_reporting(-1);
ini_set('display_errors', 'On');

chdir('../..');
require __DIR__ . '/../../vendor/autoload.php';

class PrintAjax extends Ajax
{
    public function __construct()
    {
        parent::__construct();
    
        $this->run();
    }
    
    private function run()
    {
        $file_id = array_map('intval', (array)$this->request->get('file_id'));
        if ($documents = $this->sudblock->get_documents(array('id' => $file_id)))
        {
            $files = array();
            foreach ($documents as $doc)
            {
                $files[] = $this->config->root_dir.'files/sudblock/'.$doc->sudblock_contract_id.'/'.$doc->filename;
            }
            $filename = md5(rand().time()).'.pdf';
            $outputName = $this->config->root_dir.'files/sudblock/merged/'.$filename;
        
            $cmd = "gs -q -dNOPAUSE -dBATCH -sDEVICE=pdfwrite -sOutputFile=$outputName ";
            //Add each pdf file to the end of the command
            foreach($files as $file) {
                $cmd .= $file." ";
            }
            shell_exec($cmd);
            
            if (file_exists($outputName))
            {
                $this->response = array(
                    'success' => 1,
                    'filename' => $this->config->root_url.'/files/sudblock/merged/'.$filename,
                );
            }
            else
            {
                $this->response = array('error'=>'Не удалось создать файл');
            }
            
        }
        else
        {
            $this->response = array('error'=>'Нет документов для печати');
        }
        
        $this->output();
    }
}

new PrintAjax();