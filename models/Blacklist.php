<?php

class Blacklist extends Core
{
    public function search($phone, $fio)
    {
        $fio = mb_strtolower($fio, 'utf8');
        
        $query = $this->db->placehold("
            SELECT id 
            FROM __blacklist
            WHERE phone = ?
            AND fio = ?
        ", $phone, $fio);
        $this->db->query($query);
        
        return $this->db->result('id');
    }
    

    public function get_person($id)
    {
        $query = $this->db->placehold("
            SELECT * 
            FROM __blacklist
            WHERE id = ?
        ", (int)$id);
        $this->db->query($query);
        $result = $this->db->result();
    
        return $result;
    }
    
    public function get_persons($filter = array())
    {
        $id_filter = '';
        $phone_filter = '';
        $keyword_filter = '';
        $limit = 1000;
        $page = 1;
        
        if (!empty($filter['id'])) {
            $id_filter = $this->db->placehold("AND id IN (?@)", array_map('intval', (array)$filter['id']));
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
            FROM __blacklist
            WHERE 1
                $id_filter
				$keyword_filter
            ORDER BY id DESC 
            $sql_limit
        ");
        $this->db->query($query);
        $results = $this->db->results();
        
        return $results;
    }
    
    public function count_persons($filter = array())
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
            FROM __blacklist
            WHERE 1
                $id_filter
                $keyword_filter
        ");
        $this->db->query($query);
        $count = $this->db->result('count');
    
        return $count;
    }
    
    public function add_person($person)
    {
        $query = $this->db->placehold("
            INSERT INTO __blacklist SET ?%
        ", (array)$person);
        $this->db->query($query);
        $id = $this->db->insert_id();
        
        return $id;
    }
    
    public function update_person($id, $person)
    {
        $query = $this->db->placehold("
            UPDATE __blacklist SET ?% WHERE id = ?
        ", (array)$person, (int)$id);
        $this->db->query($query);
        
        return $id;
    }
    
    public function delete_person($id)
    {
        $query = $this->db->placehold("
            DELETE FROM __blacklist WHERE id = ?
        ", (int)$id);
        $this->db->query($query);
    }
}
