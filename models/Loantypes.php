<?php

class Loantypes extends Core
{
    public function get_loantype($id)
    {
        $query = $this->db->placehold("
            SELECT * 
            FROM __loantypes
            WHERE id = ?
        ", (int)$id);
        $this->db->query($query);
        $result = $this->db->result();
    
        return $result;
    }
    
    public function get_loantypes($filter = array())
    {
        $id_filter = '';
        $keyword_filter = '';
        $sort = $this->db->placehold("ORDER BY id ASC");
        $limit = 1000;
        $page = 1;
        
        if (!empty($filter['id'])) {
            $id_filter = $this->db->placehold("AND id IN (?@)", array_map('intval', (array)$filter['id']));
        }

        if (isset($filter['sort'])) {
            $sort = $this->db->placehold("ORDER BY ".$filter['sort']);
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
            FROM __loantypes
            WHERE 1
            $id_filter
			$keyword_filter
            $sort 
            $sql_limit
        ");
        $this->db->query($query);
        $results = $this->db->results();
        
        return $results;
    }
    
    public function count_loantypes($filter = array())
    {
        $id_filter = '';
        $keyword_filter = '';
        
        if (!empty($filter['id'])) {
            $id_filter = $this->db->placehold("AND id IN (?@)", array_map('intval', (array)$filter['id']));
        }
        
        if (isset($filter['keyword'])) {
            $keywords = explode(' ', $filter['keyword']);
            foreach ($keywords as $keyword) {
                $keyword_filter .= $this->db->placehold('AND (name LIKE "%'.$this->db->escape(trim($keyword)).'%" )');
            }
        }
                
        $query = $this->db->placehold("
            SELECT COUNT(id) AS count
            FROM __loantypes
            WHERE 1
                $id_filter
                $keyword_filter
        ");
        $this->db->query($query);
        $count = $this->db->result('count');
    
        return $count;
    }
    
    public function add_loantype($loantype)
    {
        $query = $this->db->placehold("
            INSERT INTO __loantypes SET ?%
        ", (array)$loantype);
        $this->db->query($query);
        $id = $this->db->insert_id();
        
        return $id;
    }
    
    public function update_loantype($id, $loantype)
    {
        $query = $this->db->placehold("
            UPDATE __loantypes SET ?% WHERE id = ?
        ", (array)$loantype, (int)$id);
        $this->db->query($query);

        return $id;
    }
    
    public function delete_loantype($id)
    {
        $query = $this->db->placehold("
            DELETE FROM __loantypes WHERE id = ?
        ", (int)$id);
        $this->db->query($query);
    }
}
