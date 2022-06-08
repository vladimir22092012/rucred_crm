<?php

class Issuances extends Core
{
    public function get_order_issuance($order_id)
    {
        $query = $this->db->placehold("
            SELECT *
            FROM __issuances
            WHERE order_id = ?
            ORDER BY id DESC
            LIMIT 1
        ", (int)$order_id);
        $this->db->query($query);
//echo __FILE__.' '.__LINE__.'<br /><pre>';var_dump($query);echo '</pre><hr />';
        return $this->db->result();
    }
    
    public function get_issuance($id)
    {
        $query = $this->db->placehold("
            SELECT * 
            FROM __issuances
            WHERE id = ?
        ", (int)$id);
        $this->db->query($query);
        if ($result = $this->db->result()) {
            $result->body = unserialize($result->body);
            $result->response = unserialize($result->response);
        }
    
        return $result;
    }
    
    public function get_issuances($filter = array())
    {
        $id_filter = '';
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
            FROM __issuances
            WHERE 1
                $id_filter
 	           $keyword_filter
            ORDER BY id DESC 
            $sql_limit
        ");
        $this->db->query($query);
        if ($results = $this->db->results()) {
            foreach ($results as $result) {
                $result->body = unserialize($result->body);
                $result->response = unserialize($result->response);
            }
        }
        
        return $results;
    }
    
    public function count_issuances($filter = array())
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
            FROM __issuances
            WHERE 1
                $id_filter
                $keyword_filter
        ");
        $this->db->query($query);
        $count = $this->db->result('count');
    
        return $count;
    }
    
    public function add_issuance($issuance)
    {
        $issuance = (array)$issuance;
        
        if (isset($issuance['body'])) {
            $issuance['body'] = serialize($issuance['body']);
        }
        if (isset($issuance['response'])) {
            $issuance['response'] = serialize($issuance['response']);
        }
        
        $query = $this->db->placehold("
            INSERT INTO __issuances SET ?%
        ", $issuance);
        $this->db->query($query);
        $id = $this->db->insert_id();
//echo __FILE__.' '.__LINE__.'<br /><pre>';var_dump($query);echo '</pre><hr />';
        return $id;
    }
    
    public function update_issuance($id, $issuance)
    {
        $issuance = (array)$issuance;
        
        if (isset($issuance['body'])) {
            $issuance['body'] = serialize($issuance['body']);
        }
        if (isset($issuance['response'])) {
            $issuance['response'] = serialize($issuance['response']);
        }
        
        $query = $this->db->placehold("
            UPDATE __issuances SET ?% WHERE id = ?
        ", $issuance, (int)$id);
        $this->db->query($query);
        
        return $id;
    }
    
    public function delete_issuance($id)
    {
        $query = $this->db->placehold("
            DELETE FROM __issuances WHERE id = ?
        ", (int)$id);
        $this->db->query($query);
    }
}