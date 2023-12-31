<?php

class Pages extends Core
{

    public function get_page($id)
    {
        if (gettype($id) == 'string') {
            $where = $this->db->placehold(' WHERE url=? ', $id);
        } else {
            $where = $this->db->placehold(' WHERE id=? ', intval($id));
        }
        
        $query = "SELECT id, url, header, name, meta_title, meta_description, meta_keywords, body, menu_id, position, visible
		          FROM __pages $where LIMIT 1";

        $this->db->query($query);
        return $this->db->result();
    }
    
    public function get_pages($filter = array())
    {
        $menu_filter = '';
        $visible_filter = '';
        $pages = array();

        if (isset($filter['menu_id'])) {
            $menu_filter = $this->db->placehold('AND menu_id in (?@)', (array)$filter['menu_id']);
        }

        if (isset($filter['visible'])) {
            $visible_filter = $this->db->placehold('AND visible = ?', intval($filter['visible']));
        }
        
        $query = "SELECT id, url, header, name, meta_title, meta_description, meta_keywords, body, menu_id, position, visible
		          FROM __pages WHERE 1 $menu_filter $visible_filter ORDER BY position";
    
        $this->db->query($query);
        
        foreach ($this->db->results() as $page) {
            $pages[$page->id] = $page;
        }
            
        return $pages;
    }

    public function add_page($page)
    {
        $query = $this->db->placehold('INSERT INTO __pages SET ?%', $page);
        if (!$this->db->query($query)) {
            return false;
        }

        $id = $this->db->insert_id();
        $this->db->query("UPDATE __pages SET position=id WHERE id=?", $id);
        return $id;
    }
    
    public function update_page($id, $page)
    {
        $query = $this->db->placehold('UPDATE __pages SET ?% WHERE id in (?@)', $page, (array)$id);
        if (!$this->db->query($query)) {
            return false;
        }
        return $id;
    }
    
    public function delete_page($id)
    {
        if (!empty($id)) {
            $query = $this->db->placehold("DELETE FROM __pages WHERE id=? LIMIT 1", intval($id));
            if ($this->db->query($query)) {
                return true;
            }
        }
        return false;
    }
}
