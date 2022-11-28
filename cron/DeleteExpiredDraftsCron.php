<?php
error_reporting(-1);
ini_set('display_errors', 'On');

chdir(dirname(__FILE__) . '/../');

require __DIR__ . '/../vendor/autoload.php';

class DeleteExpiredDraftsCron extends Core
{
    public function __construct()
    {
        parent::__construct();
        $this->run();
    }

    private function run()
    {
        $drafts = OrdersORM::where('status', 12)->get();
        $now = new DateTime(date('Y-m-d'));

        foreach ($drafts as $draft) {
            $createDate = new DateTime(date('Y-m-d', strtotime($draft->date)));

            if(date_diff($now, $createDate)->h >= 72)
                OrdersORM::destroy($draft->id);
        }
    }
}

new DeleteExpiredDraftsCron();