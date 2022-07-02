<?php

class Operations extends Core
{
    public function get_onec_operation($number_onec)
    {
        $query = $this->db->placehold("
            SELECT * 
            FROM __operations
            WHERE number_onec = ?
        ", (string)$number_onec);
        $this->db->query($query);
        $result = $this->db->result();
    
        return $result;
    }
    
    public function get_transaction_operation($transaction_id)
    {
        $query = $this->db->placehold("
            SELECT * 
            FROM __operations
            WHERE transaction_id = ?
        ", (string)$transaction_id);
        $this->db->query($query);
        $result = $this->db->result();
    
        return $result;
    }
    
    public function get_operation($filter)
    {
        $id_filter = '';
        $uid_filter = '';

        if(isset($filter['id']))
            $id_filter = $this->db->placehold("AND id = ?", $filter['id']);

        if(isset($filter['uid']))
            $uid_filter = $this->db->placehold("AND uid = ?", $filter['uid']);

        $query = $this->db->placehold("
            SELECT * 
            FROM __operations
            WHERE 1
            $id_filter
            $uid_filter");

        $this->db->query($query);
        $result = $this->db->result();
    
        return $result;
    }
    
    public function get_operations($filter = array())
    {
        $id_filter = '';
        $contract_id_filter = '';
        $order_id_filter = '';
        $type_filter = '';
        $sent_status_filter = '';
        $date_from_filter = '';
        $date_to_filter = '';
        $keyword_filter = '';
        $sort = "id ASC";
        $limit = 1000;
        $page = 1;
        
        if (!empty($filter['id'])) {
            $id_filter = $this->db->placehold("AND id IN (?@)", array_map('intval', (array)$filter['id']));
        }
        
        if (!empty($filter['type'])) {
            $type_filter = $this->db->placehold("AND type IN (?@)", array_map('strval', (array)$filter['type']));
        }
        
        if (isset($filter['sent_status'])) {
            $sent_status_filter = $this->db->placehold("AND sent_status = ?", (int)$filter['sent_status']);
        }
        
        if (!empty($filter['contract_id'])) {
            $contract_id_filter = $this->db->placehold("AND contract_id IN (?@)", array_map('intval', (array)$filter['contract_id']));
        }
        
        if (!empty($filter['order_id'])) {
            $order_id_filter = $this->db->placehold("AND order_id IN (?@)", array_map('intval', (array)$filter['order_id']));
        }
        
        if (!empty($filter['date_from'])) {
            $date_from_filter = $this->db->placehold("AND DATE(created) >= ?", $filter['date_from']);
        }
        
        if (!empty($filter['date_to'])) {
            $date_to_filter = $this->db->placehold("AND DATE(created) <= ?", $filter['date_to']);
        }
        
        if (isset($filter['keyword'])) {
            $keywords = explode(' ', $filter['keyword']);
            foreach ($keywords as $keyword) {
                $keyword_filter .= $this->db->placehold('AND (name LIKE "%'.$this->db->escape(trim($keyword)).'%" )');
            }
        }
        
        if (isset($filter['sort'])) {
            switch ($filter['sort']) :
                case 'id_desc':
                    $sort = 'id DESC';
                    break;
            endswitch;
        }
        
        if (isset($filter['limit'])) {
            $limit = max(1, intval($filter['limit']));
        }

        if (isset($filter['page'])) {
            $page = max(1, intval($filter['page']));
        }
            
        $sql_limit = $this->db->placehold(' LIMIT ?, ? ', ($page-1)*$limit, $limit);

        $query = $this->db->placehold("
            SELECT * 
            FROM __operations
            WHERE 1
                $id_filter
                $contract_id_filter 
                $order_id_filter
  	            $keyword_filter
                $type_filter
                $sent_status_filter
                $date_from_filter
                $date_to_filter
            ORDER BY $sort
            $sql_limit
        ");
        $this->db->query($query);
        $results = $this->db->results();
        
        return $results;
    }
    
    public function count_operations($filter = array())
    {
        $id_filter = '';
        $contract_id_filter = '';
        $order_id_filter = '';
        $type_filter = '';
        $sent_status_filter = '';
        $date_from_filter = '';
        $date_to_filter = '';
        $keyword_filter = '';
        
        if (!empty($filter['id'])) {
            $id_filter = $this->db->placehold("AND id IN (?@)", array_map('intval', (array)$filter['id']));
        }
        
        if (!empty($filter['contract_id'])) {
            $contract_id_filter = $this->db->placehold("AND contract_id IN (?@)", array_map('intval', (array)$filter['contract_id']));
        }
        
        if (!empty($filter['order_id'])) {
            $order_id_filter = $this->db->placehold("AND order_id IN (?@)", array_map('intval', (array)$filter['order_id']));
        }
        
        if (!empty($filter['type'])) {
            $type_filter = $this->db->placehold("AND type IN (?@)", array_map('strval', (array)$filter['type']));
        }
        
        if (isset($filter['sent_status'])) {
            $sent_status_filter = $this->db->placehold("AND sent_status = ?", (int)$filter['sent_status']);
        }
        
        if (!empty($filter['date_from'])) {
            $date_from_filter = $this->db->placehold("AND DATE(created) >= ?", $filter['date_from']);
        }
        
        if (!empty($filter['date_to'])) {
            $date_to_filter = $this->db->placehold("AND DATE(created) <= ?", $filter['date_to']);
        }
        
        if (isset($filter['keyword'])) {
            $keywords = explode(' ', $filter['keyword']);
            foreach ($keywords as $keyword) {
                $keyword_filter .= $this->db->placehold('AND (name LIKE "%'.$this->db->escape(trim($keyword)).'%" )');
            }
        }
                
        $query = $this->db->placehold("
            SELECT COUNT(id) AS count
            FROM __operations
            WHERE 1
                $id_filter
                $contract_id_filter 
                $order_id_filter
                $type_filter
                $sent_status_filter
                $date_from_filter
                $date_to_filter
                $keyword_filter
        ");
        $this->db->query($query);
        $count = $this->db->result('count');
    
        return $count;
    }
    
    public function add_operation($operation)
    {

        $query = $this->db->placehold("
            SELECT uid
            FROM __operations
            ORDER BY uid DESC
            LIMIT 1
            ");
        $this->db->query($query);
        $uid = $this->db->result('uid');
        $uid++;

        $operation['uid'] = str_pad($uid, 9, '0', STR_PAD_LEFT);

        $query = $this->db->placehold("
            INSERT INTO __operations SET ?%
        ", (array)$operation);

        $this->db->query($query);
        $id = $this->db->insert_id();
        
        return $id;
    }
    
    public function update_operation($id, $operation)
    {
        $query = $this->db->placehold("
            UPDATE __operations SET ?% WHERE id = ?
        ", (array)$operation, (int)$id);
        $this->db->query($query);
        
        return $id;
    }
    
    public function delete_operation($id)
    {
        $query = $this->db->placehold("
            DELETE FROM __operations WHERE id = ?
        ", (int)$id);
        $this->db->query($query);
    }
}
