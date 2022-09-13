<?php

class Requisites extends Core
{
	public function get_requisite($id)
	{
		$query = $this->db->placehold("
            SELECT * 
            FROM __bank_requisites
            WHERE id = ?
        ", (int)$id);
        $this->db->query($query);
        $result = $this->db->result();
	
        return $result;
    }
    
	public function get_requisites($filter = array())
	{
		$id_filter = '';
		$user_id_filter = '';
        $keyword_filter = '';
        $limit = 1000;
		$page = 1;
        
        if (!empty($filter['id']))
            $id_filter = $this->db->placehold("AND id IN (?@)", array_map('intval', (array)$filter['id']));
        
        if (!empty($filter['user_id']))
            $user_id_filter = $this->db->placehold("AND user_id = ?", (int)$filter['user_id']);
		
		if(isset($filter['keyword']))
		{
			$keywords = explode(' ', $filter['keyword']);
			foreach($keywords as $keyword)
				$keyword_filter .= $this->db->placehold('AND (name LIKE "%'.$this->db->escape(trim($keyword)).'%" )');
		}
        
		if(isset($filter['limit']))
			$limit = max(1, intval($filter['limit']));

		if(isset($filter['page']))
			$page = max(1, intval($filter['page']));
            
        $sql_limit = $this->db->placehold(' LIMIT ?, ? ', ($page-1)*$limit, $limit);

        $query = $this->db->placehold("
            SELECT * 
            FROM __bank_requisites
            WHERE 1
                $id_filter
        		$user_id_filter
				$keyword_filter
            ORDER BY id DESC 
            $sql_limit
        ");
        $this->db->query($query);
        $results = $this->db->results();
        
        return $results;
	}

	public function getDefault($userId)
    {
        $query = $this->db->placehold("
            SELECT * 
            FROM s_bank_requisites
            WHERE user_id = ?
            AND `default` = 1
        ", $userId);

        $this->db->query($query);

        $result = $this->db->result();

        return $result;
    }
    
	public function count_requisites($filter = array())
	{
        $id_filter = '';
		$user_id_filter = '';
        $keyword_filter = '';
        
        if (!empty($filter['id']))
            $id_filter = $this->db->placehold("AND id IN (?@)", array_map('intval', (array)$filter['id']));
		
        if (!empty($filter['user_id']))
            $user_id_filter = $this->db->placehold("AND user_id = ?", (int)$filter['user_id']);
		
        if(isset($filter['keyword']))
		{
			$keywords = explode(' ', $filter['keyword']);
			foreach($keywords as $keyword)
				$keyword_filter .= $this->db->placehold('AND (name LIKE "%'.$this->db->escape(trim($keyword)).'%" )');
		}
                
		$query = $this->db->placehold("
            SELECT COUNT(id) AS count
            FROM __bank_requisites
            WHERE 1
                $id_filter
        		$user_id_filter
                $keyword_filter
        ");
        $this->db->query($query);
        $count = $this->db->result('count');
	
        return $count;
    }
    
    public function add_requisite($bank_requisite)
    {
		$query = $this->db->placehold("
            INSERT INTO __bank_requisites SET ?%
        ", (array)$bank_requisite);
        $this->db->query($query);
        $id = $this->db->insert_id();
        
        return $id;
    }
    
    public function update_requisite($id, $bank_requisite)
    {
		$query = $this->db->placehold("
            UPDATE __bank_requisites SET ?% WHERE id = ?
        ", (array)$bank_requisite, (int)$id);
        $this->db->query($query);
        
        return $id;
    }
    
    public function delete_requisite($id)
    {
		$query = $this->db->placehold("
            DELETE FROM __bank_requisites WHERE id = ?
        ", $id);
        $this->db->query($query);
    }
}