<?php

class Changelogs extends Core
{
    public function get_types()
    {
        $types = array(
            'period_amount' => 'Срок и сумма заказа',
            'personal' => 'Персональные данные',
            'passport' => 'Паспортные данные',
            'regaddress' => 'Адрес регистрации',
            'faktaddress' => 'Фактический адрес',
            'contacts' => 'Контактные лица',
            'workdata' => 'Данные о работе',
            'workaddress' => 'Рабочий адрес',
            'socials' => 'Социальные сети',
            'images' => 'Статус изображения',
            'services' => 'Сервисные услуги',
            'accept_order' => 'Заявка принята в работу',
            'reject_order' => 'Заявка отклонена',
            'approve_order' => 'Заявка одобрена',
            'order_status' => 'Статус заявки',
            'contactdata' => 'Контактные данные',
        );
    
        return $types;
    }
    
    public function get_changelog($id)
    {
        $query = $this->db->placehold("
            SELECT * 
            FROM __changelogs
            WHERE id = ?
        ", (int)$id);
        $this->db->query($query);
        $result = $this->db->result();
    
        return $result;
    }
    
    public function get_changelogs($filter = array())
    {
        $id_filter = '';
        $manager_filter = '';
        $order_filter = '';
        $user_filter = '';
        $date_from_filter = '';
        $date_to_filter = '';
        $limit = 1000;
        $page = 1;
        $sort = 'cl.id DESC';
        $join = '';
        $search_filter = '';
        
        if (!empty($filter['id'])) {
            $id_filter = $this->db->placehold("AND cl.id IN (?@)", array_map('intval', (array)$filter['id']));
        }
            
        if (!empty($filter['manager_id'])) {
            $manager_filter = $this->db->placehold("AND cl.manager_id = ?", (int)$filter['id']);
        }
        
        if (!empty($filter['order_id'])) {
            $order_filter = $this->db->placehold("AND cl.order_id = ?", (int)$filter['order_id']);
        }
        
        if (!empty($filter['user_id'])) {
            $user_filter = $this->db->placehold("AND cl.user_id = ?", (int)$filter['user_id']);
        }
        
        if (!empty($filter['date_from'])) {
            $date_from_filter = $this->db->placehold("AND DATE(cl.created) >= ?", $filter['date_from']);
        }
            
        if (!empty($filter['date_to'])) {
            $date_to_filter = $this->db->placehold("AND DATE(cl.created) <= ?", $filter['date_to']);
        }

        if (!empty($filter['search'])) {
            if (!empty($filter['search']['date'])) {
                $search_filter .= $this->db->placehold(' AND DATE(cl.created) = ?', date('Y-m-d', strtotime($filter['search']['date'])));
            }
            if (!empty($filter['search']['type'])) {
                $search_filter .= $this->db->placehold(' AND cl.type = ?', $filter['search']['type']);
            }
            if (!empty($filter['search']['manager'])) {
                $search_filter .= $this->db->placehold(' AND cl.manager_id = ?', (int)$filter['search']['manager']);
            }
            if (!empty($filter['search']['order'])) {
                $search_filter .= $this->db->placehold(' AND cl.order_id = ?', (int)$filter['search']['order']);
            }
            if (!empty($filter['search']['user'])) {
                $search_filter .= $this->db->placehold(' 
                    AND cl.user_id IN (
                        SELECT id 
                        FROM __users 
                        WHERE lastname LIKE "%'.$this->db->escape($filter['search']['user']).'%"
                        OR firstname LIKE "%'.$this->db->escape($filter['search']['user']).'%"
                        OR patronymic LIKE "%'.$this->db->escape($filter['search']['user']).'%"
                    )
                ');
            }
        }
        
        
        if (!empty($filter['search'])) {
            if (!empty($filter['search']['order_id'])) {
                $search_filter .= $this->db->placehold(' AND o.id = ?', (int)$filter['search']['order_id']);
            }
            if (!empty($filter['search']['date'])) {
                $search_filter .= $this->db->placehold(' AND DATE(o.date) = ?', date('Y-m-d', strtotime($filter['search']['date'])));
            }
            if (!empty($filter['search']['amount'])) {
                $search_filter .= $this->db->placehold(' AND o.amount = ?', (int)$filter['search']['amount']);
            }
            if (!empty($filter['search']['period'])) {
                $search_filter .= $this->db->placehold(' AND o.period = ?', (int)$filter['search']['period']);
            }
            if (!empty($filter['search']['fio'])) {
                $fio_filter = array();
                $expls = array_map('trim', explode(' ', $filter['search']['fio']));
                $search_filter .= $this->db->placehold(' AND (');
                foreach ($expls as $expl) {
                    $expl = $this->db->escape($expl);
                    $fio_filter[] = $this->db->placehold("(u.lastname LIKE '%".$expl."%' OR u.firstname LIKE '%".$expl."%' OR u.patronymic LIKE '%".$expl."%')");
                }
                $search_filter .= implode(' AND ', $fio_filter);
                $search_filter .= $this->db->placehold(')');
            }
            if (!empty($filter['search']['birth'])) {
                $search_filter .= $this->db->placehold(' AND DATE(u.birth) = ?', date('Y-m-d', strtotime($filter['search']['birth'])));
            }
            if (!empty($filter['search']['phone'])) {
                $search_filter .= $this->db->placehold(" AND u.phone_mobile LIKE '%".$this->db->escape(str_replace(array(' ', '-', '(', ')', '+'), '', $filter['search']['phone']))."%'");
            }
            if (!empty($filter['search']['region'])) {
                $search_filter .= $this->db->placehold(" AND u.Regregion LIKE '%".$this->db->escape($filter['search']['region'])."%'");
            }
            if (!empty($filter['search']['status'])) {
                $search_filter .= $this->db->placehold(" AND o.1c_status LIKE '%".$this->db->escape($filter['search']['status'])."%'");
            }
        }

        
        
        if (!empty($filter['sort'])) {
            switch ($filter['sort']) :
                case 'date_acs':
                    $sort = 'cl.created ASC';
                    break;
            
                case 'date_desc':
                    $sort = 'cl.created DESC';
                    break;
            
                case 'type_asc':
                    $sort = 'cl.type ASC';
                    break;
            
                case 'type_desc':
                    $sort = 'cl.type DESC';
                    break;
            
                case 'manager_asc':
                    $join = $this->db->placehold('LEFT JOIN __managers AS m ON m.id = cl.manager_id');
                    $sort = 'm.name ASC';
                    break;
            
                case 'manager_desc':
                    $join = $this->db->placehold('LEFT JOIN __managers AS m ON m.id = cl.manager_id');
                    $sort = 'm.name DESC';
                    break;
            
                case 'order_asc':
                    $sort = 'cl.order_id ASC';
                    break;
            
                case 'order_desc':
                    $sort = 'cl.order_id DESC';
                    break;
                
                case 'user_asc':
                    $join = $this->db->placehold("LEFT JOIN __users AS u ON u.id = cl.user_id");
                    $sort = 'u.lastname ASC';
                    break;
            
                case 'user_desc':
                    $join = $this->db->placehold("LEFT JOIN __users AS u ON u.id = cl.user_id");
                    $sort = 'u.lastname DESC';
                    break;
                
                case 'files_asc':
                    $sort = 'cl.file_id ASC';
                    break;
            
                case 'files_desc':
                    $sort = 'cl.file_id DESC';
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
            FROM __changelogs AS cl
            $join
            WHERE 1
                $id_filter
                $manager_filter
                $order_filter
                $user_filter
                $search_filter
                $date_from_filter
                $date_to_filter
            ORDER BY $sort
            $sql_limit
        ");
        $this->db->query($query);
        if ($results = $this->db->results()) {
            foreach ($results as $result) {
                $result->new_values = unserialize($result->new_values);
                $result->old_values = unserialize($result->old_values);
            }
        }
//echo __FILE__.' '.__LINE__.'<br /><pre>';var_dump($query, $results);echo '</pre><hr />';
        return $results;
    }
    
    public function count_changelogs($filter = array())
    {
        $id_filter = '';
        $manager_filter = '';
        $order_filter = '';
        $user_filter = '';
        $search_filter = '';
        $date_from_filter = '';
        $date_to_filter = '';
        
        if (!empty($filter['id'])) {
            $id_filter = $this->db->placehold("AND id IN (?@)", array_map('intval', (array)$filter['id']));
        }
            
        if (!empty($filter['manager_id'])) {
            $manager_filter = $this->db->placehold("AND manager_id = ?", (int)$filter['id']);
        }
        
        if (!empty($filter['order_id'])) {
            $order_filter = $this->db->placehold("AND order_id = ?", (int)$filter['order_id']);
        }
        
        if (!empty($filter['user_id'])) {
            $user_filter = $this->db->placehold("AND user_id = ?", (int)$filter['user_id']);
        }
        
        if (!empty($filter['date_from'])) {
            $date_from_filter = $this->db->placehold("AND DATE(cl.created) >= ?", $filter['date_from']);
        }
            
        if (!empty($filter['date_to'])) {
            $date_to_filter = $this->db->placehold("AND DATE(cl.created) <= ?", $filter['date_to']);
        }

        
        $query = $this->db->placehold("
            SELECT COUNT(id) AS count
            FROM __changelogs
            WHERE 1
                $id_filter
                $manager_filter
                $order_filter
                $user_filter
                $search_filter
                $date_from_filter
                $date_to_filter
        ");
        $this->db->query($query);
        $count = $this->db->result('count');
    
        return $count;
    }
    
    public function add_changelog($changelog)
    {
        $query = $this->db->placehold("
            INSERT INTO __changelogs SET ?%
        ", (array)$changelog);
        $this->db->query($query);
        $id = $this->db->insert_id();
        
        return $id;
    }
    
    public function update_changelog($id, $changelog)
    {
        $query = $this->db->placehold("
            UPDATE __changelogs SET ?% WHERE id = ?
        ", (array)$changelog, (int)$id);
        $this->db->query($query);
        
        return $id;
    }
    
    public function delete_changelog($id)
    {
        $query = $this->db->placehold("
            DELETE FROM __changelogs WHERE id = ?
        ", (int)$id);
        $this->db->query($query);
    }
}
