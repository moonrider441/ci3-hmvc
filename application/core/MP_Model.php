<?php
load_class('Model', 'core');
//abstract Class MP_model extends CI_Model
Class MP_Model extends CI_Model
{
    public    $ci;
    public    $_cnf;
    public    $_enc;
    public    $config;
    public    $id;
    public    $fields;
    public    $enc;
    public    $fields_array = array();
    protected $table = NULL;
    protected $pre = NULL;
    protected static $prefix= NULL;
    protected $ident = NULL;
    protected $field_required = array();
    /** mixed Object instance for singleton */
    private static $_instance;
    protected  $db;


    function __construct($db_group='default')
    {
        parent::__construct();


        $this->ci =& get_instance();
        $this->db = $this->ci->load->database($db_group, TRUE);
        $this->ci->load->helper('form');
        $this->ci->load->library('form_validation');
        $this->ci->load->library('security');


        // table prefix
        $this->pre = self::$prefix = $this->db->dbprefix;
        $this->table = $this->pre.$this->table;

    }

    /*/////////////////////////////////////////*/
    /*      - GET_INSTANCE
    /*      - Get Db object instance (Singleton)
    /*      - Example:: Mp_model::get_instance()->get_row(...)
    /*/////////////////////////////////////////*/
    /** Get Db object instance (Singleton) */
    public static function get_instance()
    {
        if (!isset(self::$_instance))
            self::$_instance = new MP_Model();
        return self::$_instance;
    }

    /*/////////////////////////////////////////*/
    /*      - INTRO
    /*      - Set Fields and Get params for edit if has ID
    /*      - Example:: intro($id)
    /*/////////////////////////////////////////*/
    function intro($id=NULL){

        $fields = new stdClass();
        $fields->id = $this->id = intval($id);
        $fields->id_enc = encrypt('id');

        foreach(get_class_vars(get_class($this)) as $field => $val) {
            if(!in_array($field,array_keys(get_class_vars('MP_Model')))) {
                $this->{$field.'_enc'} = $fields->{$field.'_enc'} = encrypt($field);
                $fields->{$field} = null;
            }
        }


        /* Load object from database if object id is present */
        if (intval($id))
        {
            $where = array($this->ident=>$id);
            $result = $this->get_row($this->table,array('where'=>$where));
            if (!$result){redirect('admin/banned', 'refresh');}// return false;
            foreach ($result AS $key => $value)
                if (in_array($key,array_keys(get_class_vars(get_class($this)))))
                    $this->{$key} = $fields->{$key} = stripslashes($value);
        }


        return $fields;
    }

    /*/////////////////////////////////////////*/
    /*      - GET_FIELDS
    /*      - get fields from child class and value from your request
    /*      - Example:: get_fields()
    /*/////////////////////////////////////////*/
    function get_val($key, $default_value=NULL, $xss_filter=TRUE, $enc=TRUE, $no_filter=FALSE)	{
        if($no_filter)
            return get_value($key,$default_value,'md5');
        $key = $enc ? encrypt($key) : $key;
        $value = $this->input->post_get($key, $xss_filter);
        return ($value ? $value :$default_value);
    }

    /*/////////////////////////////////////////*/
    /*      - GET_FIELDS
    /*      - get fields from child class and value from your request
    /*      - Example:: arg_enc_exec()
    /*/////////////////////////////////////////*/
    function arg_enc_exec($keys)	{
        $this->enc = new stdClass();
        $keys = $keys ? $keys : get_class_vars(get_class($this));
        $this->enc->id = encrypt('id');
        foreach($keys as $args => $value)
            $this->enc->{$args} = encrypt($args);
        return $this->enc;
    }

    /*/////////////////////////////////////////*/
    /*      - GET_FIELDS
    /*      - get fields from child class and value
    /*      - Example:: get_fields()
    /*/////////////////////////////////////////*/
    function get_fields()	{return array();}

    /*/////////////////////////////////////////*/
    /*      - Validate Fields
    /*      - validate model fields
    /*      - Example:: do_validate()
    /*/////////////////////////////////////////*/
    function do_validate(){
        $error = '';

        /* Checking for required fields */
        foreach ($this->field_required AS $name=>$param) {
            $this->form_validation->set_rules(encrypt($name), $param[1], $param[0]);
            $this->form_validation->set_message('required', 'فیلد "%s" الزامی است لطفا آن را تکمیل کنید!');
            if($this->form_validation->run() == FALSE)
                $error = validation_errors_array();
        }

        /*
         * get All Fields From view
         */
        foreach(get_class_vars(get_class($this)) as $field => $val) {
            if(!in_array($field,array_keys(get_class_vars('MP_Model')))) {
                if (in_array($field,array_keys(get_class_vars(get_class($this)))) && get_value($this->{$field . '_enc'}, NULL))
                    $this->{$field} = get_value($this->{$field . '_enc'});
            }
        }

        return ($error?$error:array());
    }

    /*/////////////////////////////////////////*/
    /*      - SAVE
    /*      - INSERT OR UPDATE Class Fields
    /*      - Example:: save()
    /*/////////////////////////////////////////*/
    function save($NULL_VALUE=TRUE){
        $fields = $this->get_fields();
        return (!$this->id ? $this->execute($fields, $this->table,NULL,$NULL_VALUE) : ($is_update = $this->execute($fields, $this->table, array($this->ident=>$this->id),$NULL_VALUE) ? $this->id : false));
    }

    /*/////////////////////////////////////////*/
    /*      - Get Rows
    /*      - SELECT by Your params
    /*      - Example:: get_rows('table_name', array('where'=>array(your conditions),'limit'=>array(0,10),...)
    /*/////////////////////////////////////////*/
    function get_rows($table_name=null, $params=null, $return_Object=FALSE, $return_FALSE=TRUE) {
        $table_name = $table_name!=null ? $table_name : $this->table;

        // Distinct
        if(isset($params['distinct']))
            $this->db->distinct();

        // Field
        if(isset($params['field']))
            $this->db->select($params['field'], false);

        // From
        $this->db->from($table_name);

        // Join
        if(isset($params['join']) && is_array($params['join']) && sizeof($params['join'])>0) {
            foreach ($params['join'] as $indx => $join_where) {
                if (sizeof($join_where) > 2)
                    $this->db->join($join_where[0], $join_where[1], $join_where[2]);
                else
                    $this->db->join($indx, $join_where);
            }
        }

        // Where
        if(isset($params['where']))
            $this->db->where($params['where']);

        // exists
        if(isset($params['exists']))
            foreach ($params['exists'] as $exist)
                $this->db->where($exist);

        // Where IN
        if(isset($params['in']))
            $this->db->where_in($params['in'][0], $params['in'][1]);

        // Where Not IN
        if(isset($params['not_in']))
            foreach ($params['not_in'] as $not_in_fields => $not_in_params)
                $this->db->where_not_in($not_in_fields, $not_in_params);

        // Like
        if(isset($params['like']) && is_array($params['like']))
            $this->db->like($params['like'][0], $params['like'][1]);

        // Or Like
        if(isset($params['or_like']) && is_array($params['or_like'])) {
            $c=0;
            if(sizeof($params['or_like'][0])==2)
                foreach ($params['or_like'] as $indx=>$or_like_data) {
                        $this->db->or_like($or_like_data[0], $or_like_data[1]);
                }
            else {
                foreach ($params['or_like'] as $field => $match) {
                    if (sizeof($params['or_like']) > 1) {
                        if ($c = 0)
                            $this->db->like($field, $match);
                        else
                            $this->db->or_like($field, $match);
                    } else
                        $this->db->or_like($field, $match);
                    $c++;
                }
            }
//            foreach ($params['or_like'] as $field => $match) {
//            }
        }

        // Or Like
        if(isset($params['or_where']) && is_array($params['or_where'])) {
            foreach ($params['or_where'] as $field => $match) {
                $this->db->or_where($field, $match);
            }
        }

        // Or exists
        if(isset($params['or_exists']) && is_array($params['or_where'])) {
            foreach ($params['or_where'] as $field ) {
                $this->db->or_where($field);
            }
        }

        // Order
        if(isset($params['order']) && is_array($params['order']) && sizeof($params['order'])>0)
            foreach($params['order'] as $indx=>$join_where)
                $this->db->order_by($indx,$join_where);

        // Group
        if(isset($params['group'])){
            if(is_array($params['group']) && sizeof($params['group'])>0)
                foreach($params['group'] as $item)
                    $this->db->group_by($item);
            else
                $this->db->group_by($params['group']);
        }

        // Limit
        if(isset($params['limit'])) {
            if (is_array($params['limit']) && sizeof($params['limit'])==2)
                $this->db->limit($params['limit'][0], $params['limit'][1]);
            else
                $this->db->limit($params['limit']);
        }

        $query = $this->db->get();

        if(isset($params['count']))
            return (($rows_count=$query->num_rows()) ? $rows_count : 0);

        if($query->num_rows() >= 1)
        {
            $result = $query->result_array();
            return ($return_Object?__toObject($result):$result);
        }
        else
        {
            return ($return_FALSE ? false : array());
        }
    }

    /*/////////////////////////////////////////*/
    /*      - Get Row
    /*      - SELECT by Your params and LIMIT 1
    /*      - Example:: get_row('table_name', array('where'=>'your conditions','limit'=>array(0,10),...)
    /*/////////////////////////////////////////*/
    function get_row($table_name=NULL, $params=NULL, $return_Object=FALSE) {
        $table_name = $table_name!=NULL ? $table_name : $this->table;
        $params['limit'] = '1';
        $result = $this->get_rows($table_name, $params, $return_Object);
        return (isset($result[0])?$result[0]:array());
    }

    /*/////////////////////////////////////////*/
    /*      - Get Count
    /*      - SELECT by Your params and LIMIT 1
    /*      - Example:: get_row('table_name', array('where'=>'your conditions','limit'=>array(0,10),...)
    /*/////////////////////////////////////////*/
    function get_count($table_name=NULL, $params=NULL) {
        $table_name = $table_name!=NULL ? $table_name : $this->table;
        $params['count'] = true;
        return $this->get_rows($table_name, $params);
    }

    /*/////////////////////////////////////////*/
    /*      - Get Value
    /*      - SELECT one params by Your query string
    /*      - Example:: get_value(SELECT field_name FROM table_name WHERE ...)
    /*/////////////////////////////////////////*/
    function get_value_query($string) {
        if ($query = $this->db->query($string.' LIMIT 1') AND is_array($tmpArray = $query->result_array()))
            return (isset($tmpArray[0]) ? array_shift($tmpArray[0]) : '');
        return false;
    }

    /*/////////////////////////////////////////*/
    /*      - Get Value
    /*      - SELECT one params by Your query string
    /*      - Example:: get_value(SELECT field_name FROM table_name WHERE ...)
    /*/////////////////////////////////////////*/
    function get_value($table_name=NULL, $params=NULL, $return_Object=FALSE) {
        if(!isset($params['field']))
            return false;
        $table_name = $table_name!=NULL ? $table_name : $this->table;
        $params['limit'] = '1';
        $result = $this->get_rows($table_name, $params, $return_Object);

        return (isset($result[0])?$result[0][$params['field']]:array());
    }

    /*/////////////////////////////////////////*/
    /*      - Query
    /*      - SELECT by string code
    /*      - Example:: query('SELECT * FROM sample_table WHERE ...')
    /*/////////////////////////////////////////*/
    function query($string, $return_Object=false, $return_FALSE=true) {
        $query = $this->db->query($string);
        if($query->num_rows() >= 1)
        {
            $result = $query->result_array();
            return ($return_Object?__toObject($result):$result);
        }
        else
        {
            return ($return_FALSE?false:array());
        }
    }

    /*/////////////////////////////////////////*/
    /*      - insert_id
    /*      - Get Last row inserted id
    /*      - Example:: insert_id()
    /*/////////////////////////////////////////*/
    function insert_id()
    {
        return $this->db->insert_id();
    }

    /*/////////////////////////////////////////*/
    /*      - Count All
    /*      - Get count all field of table
    /*      - Example:: count_all('sample_table')
    /*/////////////////////////////////////////*/
    function count_all($table_name=NULL)
    {
        $table_name = $table_name!=NULL ? $table_name : $this->table;
        return $this->db->count_all($table_name);
    }

    /*/////////////////////////////////////////*/
    /*      - Delete row
    /*      - DELETE by your conditions
    /*      - Example:: delete(array(ident=>1,id=>23,...),'sample_table')
    /*/////////////////////////////////////////*/
    function delete($wheres, $table_name=NULL)
    {
        if (!is_array($wheres))
            return FALSE;
        $table_name = $table_name!=NULL ? $table_name : $this->table;
        foreach($wheres as $indx=>$wh)
            $this->db->where($indx, $wh);
        return $this->db->delete($table_name);
    }

    /*/////////////////////////////////////////*/
    /*      - Delete Multi row
    /*      - DELETE by your conditions
    /*      - Example:: delete_multi(array(ident, array(1,23,2,41,...)),'sample_table')
    /*/////////////////////////////////////////*/
    function delete_multi($wheres, $table_name=NULL)
    {
        if (!is_array($wheres) && sizeof($wheres)>1)
            return FALSE;
        $table_name = $table_name!=NULL ? $table_name : $this->table;
        $this->db->where_in($wheres[0],$wheres[1]);
        return $this->db->delete($table_name);
    }

    /*/////////////////////////////////////////*/
    /*      - Empty Table
    /*      - Empty your table
    /*      - Example:: delete(array(ident=>1,id=>23,...),'sample_table')
    /*/////////////////////////////////////////*/
    function empty_table($table_name=NULL)
    {
        $table_name = $table_name!=NULL ? $table_name : $this->table;
        return $this->db->empty_table($table_name);
    }

    /*/////////////////////////////////////////*/
    /*      - EXECUTE
    /*      - INSERT OR UPDATE by your conditions
    /*      - Example INSERT:: save(array('title'=>'sample',...), 'sample_table')
    /*      - Example UPDATE:: save(array('title'=>'sample',...), 'sample_table', array('id'=>2 , ...))
    /*/////////////////////////////////////////*/
    function execute($params, $table_name=null,$WHERE=null, $NULL_value=false){
        if (!is_array($params))
            return false;
        $table_name = $table_name!=null ? $table_name : $this->table;
        foreach($params as $indx=>$param) {
            if (!$NULL_value && $param == null)
                continue;
            $prepare = ($param===$indx.'+1') ? false : true;
            $this->db->set($indx, $param, $prepare);
        }

        if($WHERE==null) {
            $this->db->insert($table_name);
            return $this->db->insert_id();
        }else{
            $this->db->where($WHERE);
            return $this->db->update($table_name);
        }
    }

    /*/////////////////////////////////////////*/
    /*      - Insert Multi data
    /*      - Insert one time for multi rows
    /*      - Example:: save_multi(array(array(first_field_params...),array(second_field_params...),...),'sample_table')
    /*/////////////////////////////////////////*/
    function save_multi($params, $table_name=null, $update_field=null){
        if (!is_array($params))
            return FALSE;
        $table_name = $table_name!=null ? $table_name : $this->table;
        if($update_field!=null)
            return $this->db->update_batch($table_name, $params, $update_field);
        return $this->db->insert_batch($table_name, $params);
    }

    /*/////////////////////////////////////////*/
    /*      - Exist Data
    /*      - Check exist data befor by your conditions
    /*/////////////////////////////////////////*/
    function exist_data($params, $table_name=false) {
        $table_name = $table_name!=NULL ? $table_name : $this->table;
        if(!is_array($params)){return false;}
        return $this->get_row($table_name, $params);
    }

    /*/////////////////////////////////////////*/
    /*      - Exist db connection
    /*      - Check exist data base connection
    /*/////////////////////////////////////////*/
    function is_connect() {
        return $this->db->db_connect();
    }

    /*/////////////////////////////////////////*/
    /*      - afma user  get enc               */
    /*/////////////////////////////////////////*/
    function get_enc(){
        if(!$this->is_lib('_enc')) {
            $this->load->library('encrypt');
            $this->_enc = $this->encrypt;
        }
    }

    /**
     * @param $lib
     * @return bool
     */
    public function is_lib($lib)
    {
        return is_object(@$this->{$lib}) ? TRUE : FALSE;
    }

}
?>