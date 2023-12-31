<?php

class IndexController extends Controller
{

    public $modules_dir = 'controllers/';

    public function __construct()
    {
        parent::__construct();
    }

    function fetch()
    {

        // Страницы
        $pages = $this->pages->get_pages(array('visible' => 1));
        $this->design->assign('pages', $pages);

        // Текущий модуль (для отображения центрального блока)
        $module = $this->request->get('module', 'string');
        $module = preg_replace("/[^A-Za-z0-9]+/", "", $module);

        if (!in_array($module, ['LoginController', 'TelegramController', 'ViberController', 'Requests1cController', 'OnlineDocsController', 'RedirectApiController']) && !$this->manager) {
            header('Location: ' . $this->config->root_url . '/login?back=' . $this->request->url());
            exit;
        }


        // Если не задан - берем из настроек
        if (empty($module)) {
            $module = 'WelcomePageController';
        }

        if (class_exists($module)) {
            $this->main = new $module($this);
        } else {
            return false;
        }

        if (!$content = $this->main->fetch()) {
            return false;
        }
//echo __FILE__.' '.__LINE__.'<br /><pre>';var_dump($offline_points);echo '</pre><hr />';
        $this->design->assign('content', $content);
        $this->design->assign('module', $module);

        $wrapper = $this->design->get_var('wrapper');
        if (is_null($wrapper)) {
            $wrapper = 'index.tpl';
        }

        if (!empty($this->manager) && in_array('notifications', $this->manager->permissions)) {
            $filter = array(
                'limit' => 3,
                'notification_date' => date('Y-m-d'),
                'done' => 0
            );

            if (in_array($this->manager->role, array('collector', 'chief_collector', 'team_collector'))) {
                $filter['collection_mode'] = 1;
            }

            if (in_array($this->manager->role, array('exactor', 'chief_exactor', 'sudblock', 'chief_sudblock'))) {
                $filter['sudblock_mode'] = 1;
            }

            if (in_array($this->manager->role, array('exactor', 'sudblock', 'collector'))) {
                $filter['manager_id'] = $this->manager->id;
            }


            $active_notifications = $this->notifications->get_notifications($filter);
            if (!empty($active_notifications)) {
                foreach ($active_notifications as $an) {
                    if (!empty($an->event_id)) {
                        $an->event = $this->notifications->get_event($an->event_id);
                    }
                }
            }
            $this->design->assign('active_notifications', $active_notifications);
        }

        if (!empty($this->manager) && in_array('penalties', $this->manager->permissions)) {
            $filter = array();
            if ($this->manager->role == 'user') {
                $filter['status'] = array(1);
                $filter['manager_id'] = $this->manager->id;
            } else {
                $filter['status'] = array(2);
            }
            $penalty_types = array();
            foreach ($this->penalties->get_types() as $t) {
                $penalty_types[$t->id] = $t;
            }

            if ($penalty_notifications = $this->penalties->get_penalties($filter)) {
                foreach ($penalty_notifications as $pn) {
                    if (isset($penalty_types[$pn->type_id])) {
                        $pn->type = $penalty_types[$pn->type_id];
                    }
                }
            }
//echo __FILE__.' '.__LINE__.'<br /><pre>';var_dump($penalty_notifications);echo '</pre><hr />';

            $this->design->assign('penalty_notifications', $penalty_notifications);
        }

        if (isset($this->manager->role)) {

            $role_id = $this->ManagerRoles->gets($this->manager->role);
            $themes_permissions = $this->ManagersCommunicationsIn->gets($role_id);

            foreach ($themes_permissions as $permission){
                $themes_id[] = (int)$permission->theme_id;
            }

            if(!empty($themes_id)){
                $themes_id = implode(',', $themes_id);

                $permissions = '';

                if ($this->manager->role == 'employer') {
                    $companies_id = [];

                    $managers_companies = $this->ManagersEmployers->get_records($this->manager->id);

                    foreach ($managers_companies as $company_id => $company_name)
                        $companies_id[] = $company_id;

                    $permissions = implode(',', $companies_id);
                    $permissions = $this->db->placehold("AND company_id in ($permissions)");
                }

                $query = $this->db->placehold("
            SELECT COUNT(*) as `count`
            FROM s_tickets
            WHERE creator != ?
            and status != 6
            and theme_id in ($themes_id)
            and created > ? 
            $permissions
            and not exists (SELECT *
            FROM s_tickets_notifications
            WHERE ticket_id = s_tickets.id
            AND user_id = ?)
            ", $this->manager->id, date('Y-m-d', strtotime($this->manager->created)), $this->manager->id);

                $this->db->query($query);
                $count_in = $this->db->result('count');
            }

            $query = $this->db->placehold("
            SELECT COUNT(*) as `count`
            FROM s_tickets
            WHERE creator = ?
            and status != 6
            and not exists (SELECT *
            FROM s_tickets_notifications
            WHERE ticket_id = s_tickets.id
            AND user_id = ?)
            ", $this->manager->id, $this->manager->id);

            $this->db->query($query);
            $count_out = $this->db->result('count');

            if (empty($count_in))
                $count_in = 0;

            if (empty($count_out))
                $count_out = 0;

            $this->design->assign('count_in', $count_in);
            $this->design->assign('count_out', $count_out);
        }

        if (!empty($wrapper)) {
            return $this->body = $this->design->fetch($wrapper);
        } else {
            return $this->body = $content;
        }
    }
}
