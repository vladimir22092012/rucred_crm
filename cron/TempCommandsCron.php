<?php

require __DIR__ . '/../vendor/autoload.php';

class TempCommandsCron extends Core
{
    public function __construct()
    {
        parent::__construct();
        $this->execute();
    }

    private function execute() {
        foreach ([1,2,3,4,6,7] as $role) {
            $this->DocksPermissions->add_permission([
                'docktype_id' => 16,
                'role_id' => $role,
            ]);
        }
    }
}

new TempCommandsCron();
