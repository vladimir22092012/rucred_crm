<?php

class Mailings extends Core
{
    public function get_mailing($id)
    {
        $query = $this->db->placehold("
            SELECT * 
            FROM __mailings
            WHERE id = ?
        ", (int)$id);
        $this->db->query($query);
        $result = $this->db->result();
    
        return $result;
    }
    
    public function get_mailings($filter = array())
    {
        $id_filter = '';
        $manager_id_filter = '';
        $keyword_filter = '';
        $limit = 1000;
        $page = 1;
        
        if (!empty($filter['id'])) {
            $id_filter = $this->db->placehold("AND id IN (?@)", array_map('intval', (array)$filter['id']));
        }
        
        if (!empty($filter['manager_id'])) {
            $manager_id_filter = $this->db->placehold("AND manager_id IN (?@)", array_map('intval', (array)$filter['manager_id']));
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
            FROM __mailings
            WHERE 1
                $id_filter
                $manager_id_filter
				$keyword_filter
            ORDER BY id DESC 
            $sql_limit
        ");
        $this->db->query($query);
        $results = $this->db->results();
        
        return $results;
    }
    
    public function count_mailings($filter = array())
    {
        $id_filter = '';
        $manager_id_filter = '';
        $keyword_filter = '';
        
        if (!empty($filter['id'])) {
            $id_filter = $this->db->placehold("AND id IN (?@)", array_map('intval', (array)$filter['id']));
        }
        
        if (!empty($filter['manager_id'])) {
            $manager_id_filter = $this->db->placehold("AND manager_id IN (?@)", array_map('intval', (array)$filter['manager_id']));
        }
        
        if (isset($filter['keyword'])) {
            $keywords = explode(' ', $filter['keyword']);
            foreach ($keywords as $keyword) {
                $keyword_filter .= $this->db->placehold('AND (name LIKE "%'.$this->db->escape(trim($keyword)).'%" )');
            }
        }
                
        $query = $this->db->placehold("
            SELECT COUNT(id) AS count
            FROM __mailings
            WHERE 1
                $id_filter
                $manager_id_filter
                $keyword_filter
        ");
        $this->db->query($query);
        $count = $this->db->result('count');
    
        return $count;
    }
    
    public function add_mailing($mailing)
    {
        $query = $this->db->placehold("
            INSERT INTO __mailings SET ?%
        ", (array)$mailing);
        $this->db->query($query);
        $id = $this->db->insert_id();
        
        return $id;
    }
    
    public function update_mailing($id, $mailing)
    {
        $query = $this->db->placehold("
            UPDATE __mailings SET ?% WHERE id = ?
        ", (array)$mailing, (int)$id);
        $this->db->query($query);
        
        return $id;
    }
    
    public function delete_mailing($id)
    {
        $query = $this->db->placehold("
            DELETE FROM __mailings WHERE id = ?
        ", (int)$id);
        $this->db->query($query);
    }



    public function get_status_item($status)
    {
        $query = $this->db->placehold("
            SELECT * 
            FROM __mailing_items
            WHERE status = ?
        ", (int)$status);
        $this->db->query($query);
        $result = $this->db->result();
    
        return $result;
    }
    
    public function get_item($id)
    {
        $query = $this->db->placehold("
            SELECT * 
            FROM __mailing_items
            WHERE id = ?
        ", (int)$id);
        $this->db->query($query);
        $result = $this->db->result();
    
        return $result;
    }
    
    public function get_items($filter = array())
    {
        $id_filter = '';
        $keyword_filter = '';
        $limit = 1000;
        $page = 1;
        $mailing_id_filter = '';
        $user_id_filter = '';
        $status_filter = '';
        $type_filter = '';
        
        if (!empty($filter['id'])) {
            $id_filter = $this->db->placehold("AND id IN (?@)", array_map('intval', (array)$filter['id']));
        }
        
        if (!empty($filter['mailing_id'])) {
            $mailing_id_filter = $this->db->placehold("AND mailing_id IN (?@)", array_map('intval', (array)$filter['mailing_id']));
        }
        
        if (!empty($filter['user_id'])) {
            $user_id_filter = $this->db->placehold("AND user_id IN (?@)", array_map('intval', (array)$filter['user_id']));
        }
        
        if (!empty($filter['status'])) {
            $status_filter = $this->db->placehold("AND status IN (?@)", array_map('intval', (array)$filter['status']));
        }
        
        if (!empty($filter['type'])) {
            $type_filter = $this->db->placehold("AND type = ?", $filter['type']);
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
            FROM __mailing_items
            WHERE 1
                $id_filter
                $mailing_id_filter
                $user_id_filter
                $status_filter
                $type_filter
				$keyword_filter
            ORDER BY id DESC 
            $sql_limit
        ");
        $this->db->query($query);
        $results = $this->db->results();
        
        return $results;
    }
    
    public function count_items($filter = array())
    {
        $id_filter = '';
        $mailing_id_filter = '';
        $keyword_filter = '';
        $user_id_filter = '';
        $status_filter = '';
        $type_filter = '';
        
        if (!empty($filter['id'])) {
            $id_filter = $this->db->placehold("AND id IN (?@)", array_map('intval', (array)$filter['id']));
        }
        
        if (!empty($filter['user_id'])) {
            $user_id_filter = $this->db->placehold("AND user_id IN (?@)", array_map('intval', (array)$filter['user_id']));
        }
        
        if (!empty($filter['mailing_id'])) {
            $mailing_id_filter = $this->db->placehold("AND mailing_id IN (?@)", array_map('intval', (array)$filter['mailing_id']));
        }
        
        if (!empty($filter['status'])) {
            $status_filter = $this->db->placehold("AND status IN (?@)", array_map('intval', (array)$filter['status']));
        }
        
        if (!empty($filter['type'])) {
            $type_filter = $this->db->placehold("AND type = ?", $filter['type']);
        }
        
        if (isset($filter['keyword'])) {
            $keywords = explode(' ', $filter['keyword']);
            foreach ($keywords as $keyword) {
                $keyword_filter .= $this->db->placehold('AND (name LIKE "%'.$this->db->escape(trim($keyword)).'%" )');
            }
        }
                
        $query = $this->db->placehold("
            SELECT COUNT(id) AS count
            FROM __mailing_items
            WHERE 1
                $id_filter
                $mailing_id_filter
                $user_id_filter
                $status_filter
                $type_filter
                $keyword_filter
        ");
        $this->db->query($query);
        $count = $this->db->result('count');
    
        return $count;
    }
    
    public function add_item($mailing_item)
    {
        $query = $this->db->placehold("
            INSERT INTO __mailing_items SET ?%
        ", (array)$mailing_item);
        $this->db->query($query);
        $id = $this->db->insert_id();
        
        return $id;
    }
    
    public function update_item($id, $mailing_item)
    {
        $query = $this->db->placehold("
            UPDATE __mailing_items SET ?% WHERE id = ?
        ", (array)$mailing_item, (int)$id);
        $this->db->query($query);
        
        return $id;
    }
    
    public function delete_item($id)
    {
        $query = $this->db->placehold("
            DELETE FROM __mailing_items WHERE id = ?
        ", (int)$id);
        $this->db->query($query);
    }
}
