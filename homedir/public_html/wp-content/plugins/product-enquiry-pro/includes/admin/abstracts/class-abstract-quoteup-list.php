<?php

namespace Includes\Admin\Abstracts;

if (!defined('ABSPATH')) {
    exit; // Exit if accessed directly
}

if (!class_exists('WP_List_Table')) {
    require_once ABSPATH.'wp-admin/includes/class-wp-list-table.php';
}

abstract class QuoteupList extends \WP_List_Table
{
    public $perPageEnquiries = 10;

    public $pageNumber = 1;

    public $totalResults = 0;

    public $sendback = '';

    /*
     * text to be displayed when there are no records
     */
    abstract public function noItems();

    abstract public function sortableColumns();

    abstract protected function columnsTitles();

    public function __construct($displayNames = array())
    {
        if (empty($displayNames)) {
            $displayNames = array(
                    'singular' => __('Enquiry', QUOTEUP_TEXT_DOMAIN)/* singular name of the listed records */,
                    'plural' => __('Enquiries', QUOTEUP_TEXT_DOMAIN), /* plural name of the listed records */
            );
        }

        parent::__construct($displayNames);

        add_filter('removable_query_args', array($this, 'customRemovableArgs'), 10, 1);
    }

    protected function _processRowAction()
    {
        //Detect when a single delete is being triggered...
        if ('delete' === $this->current_action()) {
            $nonce = esc_attr($_REQUEST[ '_wpnonce' ]);

            if (!wp_verify_nonce($nonce, 'wdm_enquiry_actions')) {
                die('Go get a life script kiddies');
            } else {
                $this->_deleteEnquiry(absint($_GET[ 'id' ]));
                echo "<div class='updated'><p>Enquiry is deleted successfully</p></div>";
            }
        }

        if (method_exists($this, 'processRowAction')) {
            $this->processRowAction();
        }
    }

    public function _processBulkAction($currentPage, $sendback)
    {
        $this->sendback = add_query_arg('paged', $currentPage, $sendback);
        $this->_delteEnquiriesInBulk();
        if (method_exists($this, 'processBulkAction')) {
            $this->processBulkAction($currentPage, $sendback);
        }
    }

    public function _delteEnquiriesInBulk()
    {
        // If the delete bulk action is triggered
        if ((isset($_POST[ 'action' ]) && $_POST[ 'action' ] == 'bulk-delete') || (isset($_POST[ 'action2' ]) && $_POST[ 'action2' ] == 'bulk-delete')
            ) {
            if (isset($_POST[ 'bulk-select' ])) {
                $delete_ids = esc_sql($_POST[ 'bulk-select' ]);

                // loop over the array of record IDs and delete them
                foreach ($delete_ids as $id) {
                    $id = strip_tags($id);
                    $this->_deleteEnquiry($id);
                }
                $count = count($delete_ids);
                $this->sendback = add_query_arg('bulk-delete', $count, $this->sendback);
            } else {
                $this->sendback = add_query_arg('selectnone', 'yes', $this->sendback);
            }

            if ($this->current_action()) {
                wp_redirect($this->sendback);
                exit;
            }
        }
    }

    public function _bulkActionNotices()
    {
        if (!$this->current_action()) {
            $args = $_GET;
            foreach ($args as $key => $value) {
                switch ($key) {
                    case 'bulk-delete':
                        echo $div = "<div class='updated'><p> $value ".__('enquiries are deleted', QUOTEUP_TEXT_DOMAIN).'</p></div>';
                        break;

                    case 'selectnone':
                        echo $div = "<div class='error'><p>".__('Select Enquiries to delete', QUOTEUP_TEXT_DOMAIN).'</p></div>';
                        break;
                    default:
                        break;
                }
            }
        }

        if (method_exists($this, 'bulkActionNotices')) {
            $this->bulkActionNotices();
        }
    }

    protected function table()
    {
        global $wpdb;

        return "{$wpdb->prefix}enquiry_detail_new";
    }

    /**
     * This function add arguments which should be removed from URL on load.
     *
     * @param [type] $removableQueryArgs [description]
     */
    public function customRemovableArgs($removableQueryArgs)
    {
        $customArgs = array('action', 'id', '_wpnonce', 'bulk-delete');

        $removableQueryArgs = array_merge($removableQueryArgs, $customArgs);

        return apply_filters('quoteup_add_custom_removable_args', $removableQueryArgs);
    }

    public function getEnquiries($perPageEnquiries = 10, $pageNumber = 1)
    {
        global $wpdb;

        $this->convertPostSearchToGet();

        $this->perPageEnquiries = $perPageEnquiries;
        $this->pageNumber = $pageNumber;

        $finalResults = array();

        $sql = $this->createDbSelectQuery();

        if (empty($sql)) {
            return;
        }

        $results = $wpdb->get_results($sql, 'ARRAY_A');
        if ($results) {
            $this->setTotalResultsFound();

            foreach ($results as $result) {
                $currentData = array();
                $currentData['enquiry_id'] = isset($result['enquiry_id']) ? $result['enquiry_id'] : '';

                foreach ($this->columnsTitles() as $columnSlug => $columnTitle) {
                    $currentData[$columnSlug] = call_user_func_array(array($this, 'display'.$this->toCamelCase($columnSlug)), array($result));
                    unset($columnTitle);
                }

                $finalResults[] = apply_filters('enquiry_list_table_data', $currentData, $result);
            }
        }

        return $finalResults;
    }

    protected function toCamelCase($string)
    {
        $string = str_replace('-', ' ', $string);
        $string = str_replace('_', ' ', $string);
        $string = ucwords(strtolower($string));
        return str_replace(' ', '', $string);
    }

    protected function setTotalResultsFound()
    {
        global $wpdb;
            //We will avoid firing another query to calculate total number of results
        if ($this->perPageEnquiries >= 0) {
            $this->totalResults = $wpdb->get_var('SELECT FOUND_ROWS()');
        } else {
            $this->totalResults = count($results);
        }
    }

    protected function convertPostSearchToGet()
    {
        if (isset($_POST['s']) && $_POST['s'] != '') {
            $actual_link = (isset($_SERVER['HTTPS']) ? 'https' : 'http')."://$_SERVER[HTTP_HOST]$_SERVER[REQUEST_URI]";
            header("Location: $actual_link&s=$_POST[s]");
        } elseif (isset($_POST['s']) && $_POST['s'] == '') {
            $requestedURL = get_admin_url('', 'admin.php?page=quoteup-details-new');
            header("Location: $requestedURL");
        }
    }

    protected function createDbSelectQuery()
    {
        global $wpdb;

        $tableName = $this->table();
        $query = array();
        $columns = $this->get_columns();

        if (empty($columns)) {
            return;
        }

        $filteredColumns = array();

        //Check if columns exist in table
        foreach ($columns as $columnSlug => $columnTitle) {
            if ($columnSlug == 'cb' || $columnSlug == 'product_details') {
                continue;
            }

            $columnExists = $wpdb->get_var($wpdb->prepare("SHOW COLUMNS FROM {$tableName} LIKE %s", $columnSlug));

            if ($columnExists != null) {
                $filteredColumns[] = $columnSlug;
            }
        }

        $columns = implode(', ', $filteredColumns);

        $foundRows = '';

        //We will avoid firing another query to calculate total number of results
        if ($this->perPageEnquiries >= 0) {
            $foundRows = 'SQL_CALC_FOUND_ROWS';
        }

        $query[] = "SELECT {$foundRows} {$columns} FROM {$tableName}";
        $query['where'][] = $this->appendSearchQuery();

        //Remove blank entries from query
        $statusFilterResults = $this->statusSpecificEnquiries();
        if (!empty($statusFilterResults)) {
            $enquiryIds = implode(', ', $statusFilterResults);
            $query['where'][] = "enquiry_id IN ({$enquiryIds})";
        }

        $query['where'] = 'WHERE '.implode(' AND ', array_filter($query['where']));

        if ($query['where'] == 'WHERE ') {
            unset($query['where']);
        }

        $query[] = $this->orderByQuery();
        $query[] = $this->limitQuery();
        $query[] = $this->offsetQuery();

        //Remove blank entries from array
        $query = array_filter($query);

        return implode(' ', $query);
    }

    protected function orderByQuery()
    {
        $sql = '';
        if (!empty($_REQUEST['orderby'])) {
            $sql = 'ORDER BY '.esc_sql($_REQUEST['orderby']);
            $sql .= !empty($_REQUEST['order']) ? ' '.esc_sql($_REQUEST['order']) : ' ASC';
        } else {
            $sql = 'ORDER BY enquiry_id DESC';
        }

        return $sql;
    }

    protected function limitQuery()
    {
        return 'LIMIT '.$this->perPageEnquiries;
    }

    protected function offsetQuery()
    {
        return 'OFFSET '.($this->pageNumber - 1) * $this->perPageEnquiries;
    }

    protected function appendSearchQuery()
    {
        if ($this->isSearchRequest()) {
            $searchParameter = $this->getSearchParameter();
            if (!empty($searchParameter)) {
                return "(name LIKE '%{$searchParameter}%' OR email LIKE '%{$searchParameter}%')";
            }
        }

        return;
    }

    protected function isSearchRequest()
    {
        return isset($_GET['s']) && !empty(trim($_GET['s']));
    }

    protected function statusSpecificEnquiries()
    {
        return array();
    }

    protected function getSearchParameter()
    {
        if (isset($_GET['s']) && $_GET['s'] != '') {
            return filter_var($_GET['s'], FILTER_SANITIZE_STRING);
        } else {
            return '';
        }
    }

    /**
     * This function is used to delete enquiry.
     *
     * @param [int] $enquiry_id [enquiry id to be deleted]
     *
     * @return [type] [description]
     */
    public function _deleteEnquiry($enquiryId)
    {
        global $wpdb;

        do_action('quoteup_before_enquiry_delete_enquiry', $enquiryId);

        $wpdb->delete(getEnquiryDetailsTable(), array('enquiry_id' => $enquiryId), array('%d'));
        $wpdb->delete(getEnquiryHistoryTable(), array('enquiry_id' => $enquiryId), array('%d'));
        $wpdb->delete(getEnquiryMetaTable(), array('enquiry_id' => $enquiryId), array('%d'));
        $wpdb->delete(getEnquiryProductsTable(), array('enquiry_id' => $enquiryId), array('%d'));
        $wpdb->delete(getQuotationProductsTable(), array('enquiry_id' => $enquiryId), array('%d'));
        $wpdb->delete(getEnquiryThreadTable(), array('enquiry_id' => $enquiryId), array('%d'));
        $wpdb->delete(getVersionTable(), array('enquiry_id' => $enquiryId), array('%d'));

        do_action('quoteup_after_enquiry_delete_enquiry', $enquiryId);
    }

    public function _rowActions($enquiryId, $adminPath, $currentPage)
    {
        $nonce = wp_create_nonce('wdm_enquiry_actions');

        $actions = array(
                'delete' => sprintf('<a href="?page=%s&paged=%s&action=%s&id=%s&_wpnonce=%s">%s</a>', esc_attr($_REQUEST[ 'page' ]), $currentPage, 'delete', $enquiryId, $nonce, __('Delete', QUOTEUP_TEXT_DOMAIN)),
            );

        if (method_exists($this, 'rowActions')) {
            $actions = array_merge($this->rowActions($enquiryId, $adminPath, $currentPage), $actions);
        }

        return $actions;
    }

    public function displayName($item)
    {
        return $item['name'];
    }

    public function displayEmail($item)
    {
        return $item['email'];
    }

    public function displayEnquiryDate($item)
    {
        return $item['enquiry_date'];
    }

    public function column_cb($item)
    {
        $enquiry_id = strip_tags($item[ 'enquiry_id' ]);

        return sprintf('<input type="checkbox" name="bulk-select[]" pr-id="%d" value="%s" />', $enquiry_id, $item[ 'enquiry_id' ]);
    }

    public function column_default($item, $column_name)
    {
        return $item[ $column_name ];
    }

    public function column_enquiry_id($item)
    {
        $enquiryId = strip_tags($item[ 'enquiry_id' ]);
        $adminPath = get_admin_url();
        $currentPage = $this->get_pagenum();
        $actions = $this->_rowActions($enquiryId, $adminPath, $currentPage);

        return sprintf('%s %s', $item[ 'enquiry_id' ], $this->row_actions($actions));
    }

    public function no_items()
    {
        return $this->noItems();
    }

    public function get_hidden_columns()
    {
        $hidden_columns = get_user_option('managetoplevel_page_quoteup-details-newcolumnshidden');
        if (!$hidden_columns) {
            $hidden_columns = array();
        }

        return $hidden_columns;
    }

    public function get_columns()
    {
        $columns = array(
            'cb' => '<input type="checkbox" />',
            'enquiry_id' => __('ID', QUOTEUP_TEXT_DOMAIN),
        );

        $columns = array_merge($columns, $this->columnsTitles());

        return apply_filters('quoteup_enquiries_get_columns', $columns);
    }

    public function prepare_items()
    {
        $columns = $this->get_columns();
        $hidden = $this->get_hidden_columns();
        $sortable = $this->get_sortable_columns();

        $this->_column_headers = array($columns, $hidden, $sortable);

            /* Process bulk action */
            $this->process_bulk_action();
        $this->views();

        $perPage = $this->get_items_per_page('request_per_page', 10);
        $currentPage = $this->get_pagenum();
        $this->items = $this->getEnquiries($perPage, $currentPage);

        $this->set_pagination_args(array(
                'total_items' => $this->totalResults, //WE have to calculate the total number of items
                'per_page' => $perPage, //WE have to determine how many items to show on a page
            ));
    }

    public function get_bulk_actions()
    {
        $actions = array(
                'bulk-export' => __('Export', QUOTEUP_TEXT_DOMAIN),
                'bulk-export-all' => __('Export all enquiries', QUOTEUP_TEXT_DOMAIN),
                'bulk-delete' => __('Delete', QUOTEUP_TEXT_DOMAIN),
            );

        if (method_exists($this, 'bulkActions')) {
            $actions = array_merge($actions, $this->bulkActions());
        }

        return apply_filters('quoteup_enquiries_get_sortable_columns', $actions);
    }

    public function get_sortable_columns()
    {
        return $this->sortableColumns();
    }

    public function process_bulk_action()
    {
        $currentPage = $this->get_pagenum();
        $url = admin_url('/admin.php?page=quoteup-details-new');
        $this->_processRowAction();
        $this->_processBulkAction($currentPage, $url);
        $this->_bulkActionNotices();
    }

    public function single_row($item)
    {
        global $wpdb;
        $metaTbl = getEnquiryMetaTable();
        $class = '';
        $enquiryId = $item['enquiry_id'];
        $sql = "SELECT meta_value FROM $metaTbl WHERE meta_key = '_unread_enquiry' AND enquiry_id= $enquiryId";
        $metaValue = $wpdb->get_var($sql);
        if ($metaValue == 'yes') {
            $class = 'unread-enquiry';
        }
        echo '<tr class = "'.$class.'">';
        $this->single_row_columns($item);
        echo '</tr>';
    }
}
