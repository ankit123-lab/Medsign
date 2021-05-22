<?php

/**
 * @author Dipesh Shihora
 * Modified Date : 2016-09-15
 */
class MY_Model extends CI_Model {

    function __construct() {
        parent::__construct();
        $this->load->database();
        $this->utc_time_formated = date('Y-m-d H:i:s', time());
    }

  
    /**
     * This function help you to get signle record from database without prepare query
     * 
     * @param type $table_name
     * @param type $fields
     * @param type $where
     * @param type $join
     * @param type $join_type
     * @param type $like_array
     * @param type $or_where
     * @return type
     */
    function get_single_row($table_name, $fields, $where = array(), $join = array(), $join_type = '', $like_array = array(), $or_where = array()) {
        $this->db->select($fields);
        if (count($join) > 0) {
            foreach ($join as $key => $value) {
                if (!empty($join_type) && $join_type) {
                    $this->db->join($key, $value, $join_type);
                } else {
                    $this->db->join($key, $value);
                }
            }
        }
        if (count($where) > 0) {
            $this->db->where($where);
        }

        if (count($or_where) > 0) {
            $this->db->or_where($where);
        }

        if (is_array($like_array) && count($like_array) > 0) {
            foreach ($like_array as $key => $value) {
                $like_statements[] = " " . $key . " LIKE '%" . $value . "%'";
            }
            $like_string = "(" . implode(' OR ', $like_statements) . ")";
            $this->db->where($like_string);
        }

        $database_object = $this->db->get($table_name);
        $table_data = array();
        if ($database_object->num_rows() > 0) {
            // IF WE PASS ARRAY PARAMETER THEN ITS PASS IN ARRAY FORMATE OTHERWISE  ITS PASS BYDEFAULT IN OBJECT TYPE
            $table_data = $database_object->first_row('array');
        }
        return $table_data;
    }

    /**
     * 
     * This function help you to get signle recored using query from database
     * 
     * @param string $query
     * @return array
     */
    function get_single_row_by_query($query) {
        $database_object = $this->db->query($query);
        $table_data = array();
        if ($database_object->num_rows() > 0) {
            // IF WE PASS ARRAY PARAMETER THEN ITS PASS IN ARRAY FORMATE OTHERWISE  ITS PASS BYDEFAULT IN OBJECT TYPE
            $table_data = $database_object->first_row('array');
        }
        return $table_data;
    }

    /**
     * 
     * This function help you to get multiple recored from database without prepare query
     * 
     * @param type $table_name
     * @param type $fields
     * @param type $where
     * @param type $join
     * @param type $order_by
     * @param type $limit
     * @param type $join_type
     * @param type $like_array
     * @param type $group_by
     * @return type
     */
    function get_all_rows($table_name, $fields, $where = array(), $join = array(), $order_by = array(), $limit = '', $join_type = '', $like_array = array(), $group_by = '') {
        $this->db->select($fields);
        if (is_array($join) && count($join) > 0) {
            foreach ($join as $key => $value) {
                if (!empty($join_type) && $join_type) {
                    $this->db->join($key, $value, $join_type);
                } else {
                    $this->db->join($key, $value);
                }
            }
        }
        if (is_array($where) && count($where) > 0) {
            $this->db->where($where);
        }
        if (is_array($order_by) && count($order_by) > 0) {
            foreach ($order_by as $key => $value) {
                $this->db->order_by($key, $value);
            }
        }
        if (is_array($like_array) && count($like_array) > 0) {
            foreach ($like_array as $key => $value) {
                $like_statements[] = " " . $key . " LIKE '%" . $value . "%'";
            }
            $like_string = "(" . implode(' OR ', $like_statements) . ")";
            $this->db->where($like_string);
        }
        if ($group_by != '') {
            $this->db->group_by($group_by);
        }
        if ($limit != '') {
            //$limit = explode(',', $limit);
            $this->db->limit($limit);
        }
        $database_object = $this->db->get($table_name);
        $table_data = array();
        foreach ($database_object->result_array() as $row) {
            $table_data[] = $row;
        }
        return $table_data;
    }

    /**
     * 
     * This function is help you to get data from table using query
     * 
     * @param string $query
     * @return array
     */
    function get_all_rows_by_query($query) {
        $database_object = $this->db->query($query);
        $table_data = array();
        foreach ($database_object->result_array() as $row) {
            $table_data[] = $row;
        }
        return $table_data;
    }

    /**
     * 
     * This function is help you to count record without prepare query
     * 
     * @param string $table_name
     * @param string $fields
     * @param array $where
     * @param array $join
     * @param array $order_by
     * @return integer
     */
    function get_count($table_name, $fields, $where = array(), $join = array(), $order_by = array()) {
        $this->db->select($fields);
        if (is_array($join) && count($join) > 0) {
            foreach ($join as $key => $value) {
                $this->db->join($key, $value);
            }
        }
        if (is_array($where) && count($where) > 0) {
            $this->db->where($where);
        }
        if (is_array($order_by) && count($order_by) > 0) {
            foreach ($order_by as $key => $value) {
                $this->db->order_by($key, $value);
            }
        }
        $result_count = $this->db->count_all_results($table_name);
        return $result_count;
    }

    /**
     * 
     * This function help you to get number of recored using query from database
     * 
     * @param string $query
     * @return integer
     */
    function get_count_by_query($query) {
        $databse_object = $this->db->query($query);
        return $databse_object->num_rows();
    }

    /**
     * 
     * This function help you to add data in database
     * 
     * @param string $table_name
     * @param array $data
     * @return integer
     */
    function insert($table_name, $data) {
        $this->db->insert($table_name, $data);
        return $this->db->insert_id();
    }

    /**
     * 
     * This function is help to insert multiple recored in database
     * 
     * @param string $table_name
     * @param array $data
     * @return boolean
     */
    function insert_multiple($table_name, $data) {
        $this->db->insert_batch($table_name, $data);
        return true;
    }

    /**
     * 
     * This function help you to update record without prepare query
     * 
     * @param string $table_name
     * @param array $data
     * @param array $where
     * @return integer
     */
    function update($table_name, $data, $where) {
        $this->db->update($table_name, $data, $where);
        return $this->db->affected_rows();
    }

    /**
     * 
     * This function to escape (mysqli_real_escape_string()) data
     * 
     * @param string/array $data
     * @return string/array
     */
    function escape_data($data) {
        return $this->db->escape_str($data);
    }

    /**
     * 
     * This function help you to run query without get any data from database
     * 
     * @param string $query
     */
    function query($query) {
        $this->db->query($query);
    }

    /**
     * 
     * This function help you to get last insert id
     * 
     * @return integer
     */
    function get_last_insert_id() {
        return $this->db->insert_id();
    }

    /**
     * 
     * Getting number of affected rows from the last query
     * 
     * @return integer
     */
    function affetcteRows() {
        return $this->db->affected_rows();
    }

    /**
     * 
     * This function help you to get last exicuted query
     * 
     * @return string
     */
    function get_last_query() {
        return $this->db->last_query();
    }
    function lq() {
        return $this->db->last_query();
    }

    /**
     * 
     * This function help you to hard delete any record from database
     * 
     * @param string $table
     * @param array $where
     * @return integer
     */
    function delete_data($table, $where = array()) {
        $this->db->delete($table, $where);
        return $this->db->affected_rows();
    }

}
