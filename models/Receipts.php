<?php

class Receipts extends Core
{
    public function get_receipt($id)
    {
        $query = $this->db->placehold("
            SELECT * 
            FROM __receipts
            WHERE id = ?
        ", (int)$id);
        $this->db->query($query);
        $result = $this->db->result();
    
        return $result;
    }
    
    public function get_receipts($filter = array())
    {
        $id_filter = '';
        $user_id_filter = '';
        $order_id_filter = '';
        $contract_id_filter = '';
        $keyword_filter = '';
        $limit = 1000;
        $page = 1;
        
        if (!empty($filter['id'])) {
            $id_filter = $this->db->placehold("AND id IN (?@)", array_map('intval', (array)$filter['id']));
        }
        
        if (!empty($filter['user_id'])) {
            $user_id_filter = $this->db->placehold("AND user_id IN (?@)", array_map('intval', (array)$filter['user_id']));
        }
        
        if (!empty($filter['order_id'])) {
            $order_id_filter = $this->db->placehold("AND order_id IN (?@)", array_map('intval', (array)$filter['order_id']));
        }
        
        if (!empty($filter['contract_id'])) {
            $contract_id_filter = $this->db->placehold("AND contract_id IN (?@)", array_map('intval', (array)$filter['contract_id']));
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
            FROM __receipts
            WHERE 1
                $id_filter
				$user_id_filter
                $order_id_filter
                $contract_id_filter
                $keyword_filter
            ORDER BY id DESC 
            $sql_limit
        ");
        $this->db->query($query);
        $results = $this->db->results();
        
        return $results;
    }
    
    public function count_receipts($filter = array())
    {
        $id_filter = '';
        $user_id_filter = '';
        $order_id_filter = '';
        $contract_id_filter = '';
        $keyword_filter = '';
        
        if (!empty($filter['id'])) {
            $id_filter = $this->db->placehold("AND id IN (?@)", array_map('intval', (array)$filter['id']));
        }
        
        if (!empty($filter['user_id'])) {
            $user_id_filter = $this->db->placehold("AND user_id IN (?@)", array_map('intval', (array)$filter['user_id']));
        }
        
        if (!empty($filter['order_id'])) {
            $order_id_filter = $this->db->placehold("AND order_id IN (?@)", array_map('intval', (array)$filter['order_id']));
        }
        
        if (!empty($filter['contract_id'])) {
            $contract_id_filter = $this->db->placehold("AND contract_id IN (?@)", array_map('intval', (array)$filter['contract_id']));
        }
        
        if (isset($filter['keyword'])) {
            $keywords = explode(' ', $filter['keyword']);
            foreach ($keywords as $keyword) {
                $keyword_filter .= $this->db->placehold('AND (name LIKE "%'.$this->db->escape(trim($keyword)).'%" )');
            }
        }
                
        $query = $this->db->placehold("
            SELECT COUNT(id) AS count
            FROM __receipts
            WHERE 1
                $id_filter
                $user_id_filter
                $order_id_filter
                $contract_id_filter
                $keyword_filter
        ");
        $this->db->query($query);
        $count = $this->db->result('count');
    
        return $count;
    }
    
    public function add_receipt($receipt)
    {
        $query = $this->db->placehold("
            INSERT INTO __receipts SET ?%
        ", (array)$receipt);
        $this->db->query($query);
        $id = $this->db->insert_id();
        
        return $id;
    }
    
    public function update_receipt($id, $receipt)
    {
        $query = $this->db->placehold("
            UPDATE __receipts SET ?% WHERE id = ?
        ", (array)$receipt, (int)$id);
        $this->db->query($query);
        
        return $id;
    }
    
    public function delete_receipt($id)
    {
        $query = $this->db->placehold("
            DELETE FROM __receipts WHERE id = ?
        ", (int)$id);
        $this->db->query($query);
    }
}
