<?php

class DocksListController extends Controller
{
    public function fetch()
    {
        $action = $this->request->post('action');

        if (!empty($action)) {
            return $this->{$action}();
        }

        if ($this->request->get('page_count'))
            $items_per_page = $this->request->get('page_count');
        else
            $items_per_page = 25;

        $sort = $this->request->get('sort', 'string');

        if (empty($sort)) {
            $sort = 'id desc';
        }

        $filter = array();

        $search = $this->request->post('search');

        if ($search) {

            $search = array();

            $fields = $this->request->post('fields');

            foreach ($fields as $field => $value) {
                $filter['search'][$field] = $value;
                $search[$field] = $value;
            }
            $this->design->assign('search', $search);
        }

        $filter['sort'] = $sort;
        $this->design->assign('sort', $sort);

        $page_count = $this->request->get('page_count');
        $this->design->assign('page_count', $page_count);

        $current_page = $this->request->get('page', 'integer');
        $current_page = max(1, $current_page);
        $this->design->assign('current_page_num', $current_page);

        $documents = $this->documents->get_documents($filter);
        $documents_count = count($documents);

        $pages_num = ceil($documents_count / $items_per_page);

        $this->design->assign('total_pages_num', $pages_num);
        $this->design->assign('total_orders_count', $documents_count);

        $filter['page'] = $current_page;
        $filter['limit'] = $items_per_page;

        $documents = $this->documents->get_documents($filter);

        $this->design->assign('documents', $documents);

        return $this->design->fetch('docks_list.tpl');
    }
}