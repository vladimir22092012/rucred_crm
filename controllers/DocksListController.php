<?php

class DocksListController extends Controller
{
    public function fetch()
    {
        $documents = $this->documents->get_documents();

        foreach ($documents as $document){
            $document->user = $this->users->get_user($document->user_id);
            $document->order = $this->orders->get_order($document->order_id);
        }
        $this->design->assign('documents', $documents);

        return $this->design->fetch('docks_list.tpl');
    }
}