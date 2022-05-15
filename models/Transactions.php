<?php

class Transactions extends Core
{
    public function get_transaction($id)
    {
        $query = $this->db->placehold("
            SELECT * 
            FROM __transactions
            WHERE id = ?
        ", (int)$id);
        $this->db->query($query);
        $result = $this->db->result();
    
        return $result;
    }
    
    public function get_operation_transaction($register_id, $operation)
    {
        $query = $this->db->placehold("
            SELECT * 
            FROM __transactions
            WHERE register_id = ?
            AND operation = ?
        ", (int)$register_id, $operation);
        $this->db->query($query);
        $result = $this->db->result();
    
        return $result;
    }
    
    public function get_register_id_transaction($register_id)
    {
        $query = $this->db->placehold("
            SELECT * 
            FROM __transactions
            WHERE register_id = ?
        ", (int)$register_id);
        $this->db->query($query);
        $result = $this->db->result();
    
        return $result;
    }
    
    public function get_transactions($filter = array())
    {
        $id_filter = '';
        $order_id_filter = '';
        $user_id_filter = '';
        $keyword_filter = '';
        $limit = 1000;
        $page = 1;
        
        if (!empty($filter['id'])) {
            $id_filter = $this->db->placehold("AND id IN (?@)", array_map('intval', (array)$filter['id']));
        }
        
        if (!empty($filter['order_id'])) {
            $order_id_filter = $this->db->placehold("AND order_id = ?", (int)$filter['order_id']);
        }
        
        if (!empty($filter['user_id'])) {
            $user_id_filter = $this->db->placehold("AND user_id = ?", (int)$filter['user_id']);
        }
        
        if (isset($filter['keyword'])) {
            $keywords = explode(' ', $filter['keyword']);
            foreach ($keywords as $keyword) {
                $keyword_filter .= $this->db->placehold('AND (name LIKE "%'.$this->db->escape(trim($keyword)).'%" )');
            }
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
            FROM __transactions
            WHERE 1
                $id_filter
                $order_id_filter
                $user_id_filter
                $keyword_filter
            ORDER BY id DESC 
            $sql_limit
        ");
        $this->db->query($query);
        $results = $this->db->results();
        
        return $results;
    }
    
    public function count_transactions($filter = array())
    {
        $id_filter = '';
        $order_id_filter = '';
        $user_id_filter = '';
        $keyword_filter = '';
        
        if (!empty($filter['id'])) {
            $id_filter = $this->db->placehold("AND id IN (?@)", array_map('intval', (array)$filter['id']));
        }

        if (!empty($filter['order_id'])) {
            $order_id_filter = $this->db->placehold("AND order_id = ?", (int)$filter['order_id']);
        }
        
        if (!empty($filter['user_id'])) {
            $user_id_filter = $this->db->placehold("AND user_id = ?", (int)$filter['user_id']);
        }
        
        if (isset($filter['keyword'])) {
            $keywords = explode(' ', $filter['keyword']);
            foreach ($keywords as $keyword) {
                $keyword_filter .= $this->db->placehold('AND (name LIKE "%'.$this->db->escape(trim($keyword)).'%" )');
            }
        }
                
        $query = $this->db->placehold("
            SELECT COUNT(id) AS count
            FROM __transactions
            WHERE 1
                $id_filter
                $order_id_filter
                $user_id_filter
                $keyword_filter
        ");
        $this->db->query($query);
        $count = $this->db->result('count');
    
        return $count;
    }
    
    public function add_transaction($transaction)
    {
        $query = $this->db->placehold("
            INSERT INTO __transactions SET ?%
        ", (array)$transaction);
        $this->db->query($query);
        $id = $this->db->insert_id();
        
        return $id;
    }
    
    public function update_transaction($id, $transaction)
    {
        $query = $this->db->placehold("
            UPDATE __transactions SET ?% WHERE id = ?
        ", (array)$transaction, (int)$id);
        $this->db->query($query);
        
        return $id;
    }
    
    public function delete_transaction($id)
    {
        $query = $this->db->placehold("
            DELETE FROM __transactions WHERE id = ?
        ", (int)$id);
        $this->db->query($query);
    }
}
