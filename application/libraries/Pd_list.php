<?php if (!defined('BASEPATH')) exit('No direct script access allowed');

/**
 * PD modules Class
 *
 * Load Modules Config Autoload library for Code Igniter.
 *
 * @author		Afshin Mansourzadeh
 * @version		1.0
 *
 */
//require_once APPPATH.'core/MP_Model.php';
class Pd_list extends MP_Model
{
    private $state;
    private $_render_access = true;
    private $_data = array();
	private $_table;
	private $_cols   		= array();
	private $_get_url 		= array();
	private $_ident = '';
	private $_search= '';
	private $_where = '';
	private $_where_custom = '';
	private $_group = '';
	private $_order = '';
	private $_filter = array();
	private $_action_link;


	public $table_option 	= array();
	public $before_delete	= array();


	function __construct($options)
	{
//		pre($options);
		parent::__construct();
		$this->ci =& get_instance();
		if($this->ci->pdm->module==$this->ci->pdm->controler)
			$this->module_path = $this->ci->pdm->module.'/'.$this->ci->pdm->function;
		else
			$this->module_path = $this->ci->pdm->module.'/'.$this->ci->pdm->controler.'/'.$this->ci->pdm->function;
		$default = array(
            'table_ident'		=> 0,
            'state'	 			=> 'database',// handler
            'data'	 	 		=> array(),
		    'fields'	 	 	=> '*',
			'table' 		 	=> '',
			'title' 		 	=> '',
			'cols' 		  	 	=> array(),
			'last_row'	 	 	=> array(),
			'ident' 		 	=> 'field_id',
			'where' 		 	=> '',
			'where_custom' 	 	=> '',
			'order' 		 	=> array(),
			'group' 		 	=> '',
			'search' 		 	=> array(),
			'action_link' 	 	=> array(	array('link'=>'edit/@id@','type'=>'edit'),array('link'=>'@id@','type'=>'delete')),
			'action_form' 	 	=> 'list',
            'action_form_custom'=> '',
			'limit'          	=> 0, // limit for your select
			'limit_box'      	=> true,
			'before_delete'	 	=> 0,
			'filter'	 	 	=> array(),//array('action'=>'field_name','html'=>'html_content')
			'filter_box'	 	=> true,
			'table_empty_first'	=> false,
			'disable_form'		=> false,
			'header_box'	 	=> true,
			'header_text'	 	=> ' به منظور اضافه کردن سطر جدید از طریق لینک زیر اقدام کنید',
			'header_button'	 	=> array(
				array('txt'=>'<i class="fa fa-plus"></i> اضافه کردن سطر جدید', 'color'=>'primary', 'url'=>$this->module_path),
				array('txt'=>'<i class="fa fa-trash"></i> حذف گروهی آیتم ها', 'color'=>'red', 'id'=>'delete-all', 'url'=>$this->module_path)
			),
            'field_checkbox'    => true,
			'footer_box'	 	=> false,
			'footer_text'	 	=> '',
			'footer_button'	 	=> array()
		);

		$this->table_option		= array_merge($default, $options);
		$this->before_delete 	= $this->table_option['before_delete'];
		$this->_action_link		= $this->table_option['action_link'] ? $this->table_option['action_link'] : array();
		$this->_cols			= $this->table_option['cols'];
		$table = explode(' ',$this->table_option['table']);

		$this->_set_var('_table', (($table=array_shift($table))!=''?$table:$this->table_option['table']));
		$this->_set_var('_ident', $this->table_option['ident']);
		$this->_set_var('_search', (is_array($this->table_option['search'])?('CONCAT('.rtrim(implode('," ",',$this->table_option['search']),'," ",').')'):$this->table_option['search']));
		$this->_set_var('_where', $this->table_option['where'], '=','AND');
		$this->_where_custom = $this->table_option['where_custom'];
		$this->_set_var('_group', $this->table_option['group']);
		$this->_set_var('_order', $this->table_option['order']);


		/********************/
		/****** Filter ******/
		/********************/
		if(isset($this->table_option['filter']['name'])){$this->_filter[encrypt($this->table_option['filter']['action'])] = $this->table_option['filter'];}
		elseif(isset($this->table_option['filter'][0]['name']))
			foreach ($this->table_option['filter'] as $filter)
				$this->_filter[encrypt($filter['action'])] = $filter;
		else
			$this->_filter = array();
		/********************/
		/**** End Filter ****/
		/********************/

		$this->get_url_value();

        if($this->_get_url['ajax_mode']!=0 && $this->_get_url['table_ident']!=$this->table_option['table_ident'])
            $this->_render_access = false;


        if($this->table_option['table_empty_first'] && !$this->_get_url['ajax_mode'])
            $this->_total_rows = 0;
        else
            $this->_total_rows = $this->get_total_rows();
        if($this->_get_url['current_page']>$this->_total_rows)
            $this->_get_url['current_page']=1;

        if(isset($this->_get_url['delete_item']) && $this->_get_url['delete_item'][0]!=='0')
            $this->delete_mode();
	}

	private function _set_var( $variable, $values, $needl='', $seprate=',' )
	{
		if (is_array($values)) {
			foreach ($values as $value => $type) {
				$this->{$variable} .= $value . ($needl != '' ? $needl : ' ' . $needl) . ' ' . $type . ' ' . $seprate . ' ';
			}
			$this->{$variable} = rtrim($this->{$variable}, ' ' . $seprate . ' ');
		} else
			$this->{$variable} = $values;
	}

	private function get_url_value(){
        $table_ident = $this->table_option['table_ident'];
        $dynamic_data_handler	= intval(get_value(encrypt_it('dynamic_data_handler'), 0));

        if($this->table_option['state']=='handler' && !$dynamic_data_handler) {
//			$data = Tools::array_decompress(Tools::getValue($table_ident.'data', '', 'md5'));
            $data = decompress_text(get_value($table_ident.'data', '', 'md5'));
//			if(!empty($data))
            if(isset($_SESSION[$data])) {
                $data_arr =array_decompress($_SESSION[$data]);
                if(!empty($data))
                    $this->_data = $this->_data + $data_arr;
            }
        }
		$value = array();
		$value['current_page']	= ($cp=intval(get_value(encrypt_it($table_ident.'current_page'),1)))!=0?$cp:1;
		$value['item_in_page'] 	= (($limit=intval(get_value(encrypt_it($table_ident.'item_in_page'),0))) ? $limit : $this->table_option['limit'] );
		$value['order_name']   	= 'default';
		$value['order_type']   	= get_value(encrypt_it($table_ident.'order_type'), 'ASC');
		$order_name   			= get_value(encrypt_it($table_ident.'order_name'), encrypt_it('default'));
		if($order_name AND decrypt_it($order_name)!='default' AND $value['order_type']) {
			$value['order_name'] = ($on=decrypt_it($order_name))==''?$this->_ident:$on;
			$this->_order = $value['order_name'] . ' ' . $value['order_type'];
		}
		$value['search_field'] 	= (($sf=get_value(encrypt_it($table_ident.'search_field'), ''))!=''?decrypt_it($sf):$this->_search);
		$value['search_value'] 	= get_value(encrypt_it($table_ident.'search_value'), '');
        $value['ajax_mode'] 	= get_value(encrypt_it('ajax_mode'), 0);
        $value['table_ident'] 	= get_value(encrypt_it($table_ident.'table_ident'), '', 'md5');
        $value['delete_item'] 	= explode(',',get_value(encrypt_it($table_ident.'delete_item'), 0));
		$value['filter_item'] 	= get_value($table_ident.'filter', 0);

		$this->_get_url = $value;
	}

	function get_table_rows($params=FALSE)
    {
        $has_total = !!isset($params['total']);
        switch ($this->state) {
            default:
            case 'database':
                $query = 'SELECT ';
                $query .= ($has_total ? ' COUNT('.$this->_ident.') as cnt ' : $this->table_option['fields']);
                $query .= " FROM {$this->table_option['table']} ";
                $pre = ' WHERE ';
                if (!empty($this->_get_url['search_field']) && $this->_get_url['search_value'] != '') {
                    $query .= $pre . "   {$this->_get_url['search_field']} LIKE '%{$this->_get_url['search_value']}%' ";
                    $pre = ' AND ';
                }
                if (!empty($this->_get_url['filter_item'])) {
                    foreach ($this->_get_url['filter_item'] as $filter_field => $filter_val) {
                        if (isset($this->_filter[$filter_field]) && $filter_val != '') {
                            $query .= $pre . "   {$this->_filter[$filter_field]['action']} = '{$filter_val}' ";
                            $pre = ' AND ';
                        }
                    }
                }

                if (!empty($this->_where)) {
                    $query .= $pre . "  {$this->_where}";
                    $pre = ' AND ';
                }
                if (!empty($this->_where_custom)) {
                    $query .= $pre . "  {$this->_where_custom}";
                }
                if (!empty($this->_group)) {
                    $query .= "  GROUP BY {$this->_group}";
                }
                if (!$has_total)
                    $query .= " ORDER BY {$this->_order}";
                if (!$has_total && $this->table_option['limit'] != 'all') {
                    $limit = (($this->_get_url['current_page'] - 1) * $this->_get_url['item_in_page']);
                    $query .= " LIMIT {$limit}, {$this->_get_url['item_in_page']}";
                }
                $result = $this->query($query, null, false);
                if($has_total)
                    $result = $result[0]['cnt'];
                break;

            case 'handler':
                $result = $this->_data;
                if (is_array($this->_data) && !empty($this->_data)) {

                    if (!empty($this->_get_url['search_field']) && $this->_get_url['search_value'] != '') {
                        foreach ($result as $indx => $data) {
                            $searcher = '';
                            if (isset($data[$this->_get_url['search_field']]))
                                $searcher = $data[$this->_get_url['search_field']];
                            else
                                foreach ($data as $itemchi)
                                    $searcher .= $itemchi . ' ';
                            if (strpos($searcher, $this->_get_url['search_value']) === false) {
                                unset($result[$indx]);
//								$result[] = $data;
                            }
                        }
                    }

                    if (!empty($this->_get_url['filter_item'])) {
                        foreach ($this->_get_url['filter_item'] as $filter_field => $filter_val) {
                            if (isset($this->_filter[$filter_field]) && $filter_val != '') {
                                foreach ($result as $indx => $data) {
                                    if (isset($data[$this->_filter[$filter_field]['action']]) && $data[$this->_filter[$filter_field]['action']] != $filter_val)
                                        unset($result[$indx]);
                                }
                            }
                        }
                    }

                    if (!$has_total && $this->_order != '') {
                        $sort_res = $result;
                        $orders = explode(',', $this->_order);
                        foreach ($orders as $order) {
                            $order = explode(' ', $order);
                            $sort = array();
                            $sort_res = sort_array($sort_res, $order[0], null, $order[1]);
                            foreach ($sort_res as $res_items) {
                                foreach ($res_items as $item)
                                    $sort[] = $item;
                            }
                            $sort_res = $sort;
                        }
                        $result = $sort_res;
                    }
                    if (!$has_total && $this->table_option['limit'] != 'all') {
                        $limit = (($this->_get_url['current_page'] - 1) * $this->_get_url['item_in_page']);
                        $result = array_splice($result, $limit, $this->_get_url['item_in_page']);
//						pr(sizeof($result));
//						pr($result);
//						pre($this->_get_url['item_in_page']);
                    }


                    if ($has_total) {
                        $result = sizeof($result);
                    }

                }
                break;
        }
        return $result;
    }

	function get_total_rows(){
		return $this->get_table_rows(array('total'=>1));

	}

	function get_action_link($row, $action_link=FALSE)
	{
        $ident_char = isset($action_link['ident']) ? $action_link['ident']:$this->_ident;
        $real_ident = isset($row[$ident_char]) ? $row[$ident_char] : 0;
		$actions = $action_link ? $action_link:$this->_action_link;
		$content = '';
		$class   = '';
		foreach($actions as $action) {
			$title = '';
			$type = isset($action['type']) ? $action['type'] : 'other';
            $target  = isset($action['target']) ? $action['target'] : '_self';
            $ident_val = $real_ident;

            $translate_mode = isset($action['translate']) ? $action['translate'] : 'nope';

            if($translate_mode!='nope'){
//                $translate_mode = $action['translate'];
                switch ($translate_mode){
                    case 'md5':
                        $ident_val = encrypt_it($ident_val);
                        break;
                    case 'enc':
                        $ident_val = encrypt($ident_val);
                        break;
                    case 'comp':
                        $ident_val = compress_text($ident_val);
                        break;
                    default:
                        break;
                }
            }

			switch ($type) {
				case 'edit':
					$title  = isset($action['title']) ? $action['title'] : 'ویرایش رکورد';
					$name  = isset($action['name']) ? $action['name'] : 'ویرایش';
					$color  = isset($action['color']) ? $action['color'] : 'yellow';
					$icon  = isset($action['icon']) ? $action['icon'] : 'fa-edit';
					$link  = base_url($this->module_path.'/'.str_replace('@id@',$ident_val,$action['link']));
					$class  = isset($action['class']) ? $action['class'] : '';
					break;
				case 'delete':
                    if($translate_mode=='nope')
                        $ident_val = compress_text($ident_val);
					$title = isset($action['title']) ? $action['title'] : 'حذف رکورد';
					$name  = isset($action['name']) ? $action['name'] : 'حذف';
					$color = isset($action['color']) ? $action['color'] : 'red';
					$icon  = isset($action['icon']) ? $action['icon'] : 'fa-trash';
					$link  = str_replace('@id@',$ident_val,$action['link']);
					$class  = 'delete-row '.(isset($action['class']) ? $action['class'] : '');
					break;
				case 'full-delete':
                    if($translate_mode=='nope')
                        $ident_val = compress_text('00'.$ident_val);
					$title = isset($action['title']) ? $action['title'] : 'حذف کلی رکورد';
					$name  = isset($action['name']) ? $action['name'] : 'حذف کلی';
					$color = isset($action['color']) ? $action['color'] : 'bg-blue-ebonyclay bg-font-blue-ebonyclay';
					$icon  = isset($action['icon']) ? $action['icon'] : 'fa-bomb';
					$link  = str_replace('@id@',$ident_val,$action['link']);
					$class  = 'delete-row '.(isset($action['class']) ? $action['class'] : '');
					break;
				case 'view':
					$title = isset($action['title']) ? $action['title'] : 'مشاهده رکورد';
					$name  = isset($action['name']) ? $action['name'] : 'مشاهده';
					$color = isset($action['color']) ? $action['color'] : 'purple';
					$icon  = isset($action['icon']) ? $action['icon'] : 'fa-eye';
					$link  = base_url($this->module_path.'/'.str_replace('@id@',$ident_val,$action['link']));
					$class  = isset($action['class']) ? $action['class'] : '';
					break;
				default:
					$title = isset($action['title']) ? $action['title'] : '';
					$name  = isset($action['name']) ? $action['name'] : 'لینک';
					$icon  = isset($action['icon']) ? $action['icon'] : 'fa-tik';
					$color = isset($action['color']) ? $action['color'] : 'default';
					$link  = str_replace('@id@',$ident_val,$action['link']);
					$class  = isset($action['class']) ? $action['class'] : '';
					break;
			}
            $attr = isset($action['attr']) ? ' '.$action['attr'] : '';
			$title = (isset($action['name']) && $action['name']!='')? $action['name'] : $title;
            $name  = sprintf('<span class="btn btn-outline btn-circle btn-sm %s"><i class="fa %s"></i> %s</span>',$color, $icon, $name);
            $content .= sprintf('<a class="%s" title="%s" alt="%s"  href="%s" target="%s"%s >%s</a>', $class, $title, $title, $link, $target, $attr, $name);
		}
		return $content;
	}

	function init_list()
	{
		$td_header = '';
		$td_content = '';
        if($this->table_option['field_checkbox'])
            $td_header .= '<th width="30px"><div rel="title" class="text-center field-sorting "><input id="check_all" type="checkbox" name="check_all"></div></th>';
		foreach ($this->_cols as $col) {
            $class = isset($col['class_header']) ? ' '.$col['class_header'] : '';
            $td_header .= sprintf('<th width="%dpx" class="%s" %s ><div rel="title" class="text-%s field-sorting ">%s</div></th>',
                $col['width'],
                ((($col['type'] == 'field' && (!isset($col['sort']) || (isset($col['sort']) && $col['sort']))) || ($col['type'] == 'function' && isset($col['action']))) ? 'sort-hdlr ' . ($col['action'] == $this->_get_url['order_name'] ? 'sorting_' . strtolower($this->_get_url['order_type']) : 'sorting') : '') . $class,
                ((($col['type'] == 'field' && (!isset($col['sort']) || (isset($col['sort']) && $col['sort']))) || ($col['type'] == 'function' && isset($col['action']))) ? 'data-sorting="' . encrypt_it($col['action']) . '"' : ''),
                (isset($col['align']) && $col['align'] != '' ? $col['align'] : 'right'), $col['name']);
        }
		if($this->_action_link)
			$td_header .= '<th><div rel="title" class="text-center field-sorting ">عملیات</div></th>';
//		pre($this->get_table_rows());

		if($this->_get_url['ajax_mode'] || (!$this->_get_url['ajax_mode'] && !$this->table_option['table_empty_first'])) {
			$rows = $this->get_table_rows();
            if($rows) {
                foreach ($rows as $row) {
                    $td_content .= '<tr>';
                    foreach ($this->_cols as $indx => $col) {
                        if (!$indx)
                            $td_content .= sprintf('<td><div class="text-center"><input type="checkbox" class="check_items" value="%d"><div class="clear"></div></div></td>', $row[$this->_ident]);

//                        $func_params = (isset($col['model']) ? array('model' => $col['model']) : null);
                        $func_params = (isset($col['model']) ? array('model' => $col['model']) : null);
                        $class = isset($col['class']) ? ' class="'.$col['class'].'"' : '';
                        $value = ($col['type'] == 'field' ? $row[$col['action']] : call_user_function($col['function']['name'], ($row + $col['function']['params']), '', $func_params));
                        if(isset($col['number']))
                            $value = persian_num(number_format($value));
                        $td_content .= sprintf('<td%s><div class="text-%s">%s</div></td>', $class, (isset($col['align']) && $col['align'] != '' ? $col['align'] : 'reight'), $value);
                    }
                    if ($this->_action_link)
                        $td_content .= sprintf('<td><div class="text-center">%s<div class="clear"></div></div></td>', $this->get_action_link($row));
                    $td_content .= '</tr>';
                }
                if (!empty($this->table_option['last_row'])) {
                    foreach ($this->table_option['last_row'] as $last_row) {
                        switch ($last_row['type']) {
                            case 'dynamic':
                                $content = call_user_function($last_row['action'], $rows);
                                break;
                            default:
                            case 'static':
                                $content = $last_row['content'];
                                break;
                        }
                        $td_content .= $content;
                    }
                }
            }
		}

		$content = '<div class="table-responsive bDiv">
						<table class="table table-striped table-bordered table-hover dataTable" id="list_table">
							<thead>
								<tr class="hDiv">
									' . $td_header . '
								</tr>
							</thead>
							<tbody>
								' . $td_content . '
							</tbody>
						</table>
					</div>';
		return $content;
		
	}

	function init_search(){
        $table_ident = $this->table_option['table_ident'];
		if(is_array($this->table_option['search']) && empty($this->table_option['search']))
			return false;
		$content = '<div class="sDiv2 col-sm-6 form-inline">
						<div class="form-group">
							<label for="search_text" class="">جستجو:</label>
							<input type="text" class="qsbsearch_fieldox search_text form-control input-sm" name="'.encrypt_it($table_ident.'search_value').'" value="'.$this->_get_url['search_value'].'" size="30" id="sv" placeholder="جستجو" />
						</div>
						<div class="form-group">
							<select name="'.encrypt_it($table_ident.'search_field').'" id="sf" class="form-control input-sm m-r-xs"><option value="">جستجوی همه</option>';

		foreach($this->table_option['search'] as $indx=>$search){
			$selected = $this->_get_url['search_field']==$search ? 'selected="selected"' : '';
			$content .= sprintf('<option value="%s" %s >%s&nbsp;&nbsp;</option>', encrypt_it($search), $selected, $indx);
		}

		$content .= '</select></div><div class="form-group"><input type="button" value="جستجو" class="crud_search btn btn-primary btn-sm m-b-none m-r-xs" id="sp"></div></div>';
		return $content;
	}

	function init_limit(){
        $table_ident = $this->table_option['table_ident'];
		$content = '';
		if($this->table_option['limit_box']===true) {
			$content_option = '';
			foreach (array(5, 10, 25, 50, 100, 500) as $limit) {
				$selected = $this->_get_url['item_in_page'] == $limit ? 'selected="selected"' : '';
				$content_option .= sprintf('<option value="%d" %s >%s</option>', $limit, $selected, persian_num($limit));
			}
			$content = '<div class="form-group sort_row m-b-xl">
							<hr />
							<div class="pDiv row">
								<div class="pDiv2 col-sm-12 form-inline">
									<div class="form-group">
										<div class="pcontrol btn-group">
											<label for="per_page">نمایش</label>
											<select name="' . encrypt_it($table_ident.'item_in_page') . '" id="iip" class="per_page form-control input-sm m-r-xs">' . $content_option . '</select>
											<label class="m-r-xs">رکورد</label>
										</div>
									</div>
									' . $this->init_pageing() . '
								</div>
							</div>
						</div>';
		}
		return $content;
	}

	function init_pageing(){
        $table_ident = $this->table_option['table_ident'];
		$content = '';
		$nopaging = FALSE;
		$end			= ceil($this->_total_rows/$this->_get_url['item_in_page']);
		$current_page 	= $this->_get_url['current_page'];

		if(!intval($current_page) || $current_page>$this->_total_rows || $this->_get_url['item_in_page']>$this->_total_rows)
			$nopaging = TRUE;


		$start_page 	= $current_page!=1 		? '<span class="btn btn-outline btn-circle btn-sm btn-default pPrev pButton first-button paging-btn" data-page="1" title="صفحه اول" ><i class="fa fa-step-forward"></i></span>' : '';
		$prev_page 		= $current_page>1 		? '<span class="btn btn-outline btn-circle btn-sm btn-default pPrev pButton prev-button paging-btn" data-page="'.($current_page-1).'" title="صفحه قبلی"><i class="fa fa-fast-forward"></i></span>' : '';
		$next_page 		= $current_page<$end 	? '<span class="btn btn-outline btn-circle btn-sm btn-default pNext pButton next-button paging-btn" data-page="'.($current_page+1).'" title="صفحه بعد" ><i class="fa fa-fast-backward"></i></span>' : '';
		$end_page 		= $current_page!=$end? '<span class="btn btn-outline btn-circle btn-sm btn-default pNext pButton last-button paging-btn" data-page="'.($end).'" title="صفحه آخر" ><i class="fa fa-step-backward"></i></div>' : '';


		if(!$nopaging)
			$content .= '<div class="form-group m-r-xs">'.$start_page.$prev_page.'</div>';

		$content .= '<div class="form-group m-r-xs">
						<span class="pcontrol btn-group">
							<label for="crud_page">صفحه</label>
							<input name="'.encrypt_it($table_ident.'default_page').'" type="text" value="'.persian_num($current_page).'" size="2" id="dp" class="crud_page form-control input-sm m-r-xs">
							<label class="m-r-xs">از</label>
							<label id="last-page-number" class="last-page-number m-r-xs">
								'.persian_num($end).'
								صفحه
							</label>
						</span>
					</div>';
		if(!$nopaging)
			$content .= '<div class="form-group m-r-xs">'.$next_page.$end_page.'</div>';
		$content .= '<div class="form-group pull-right">
						<span class="pPageStat">
							نمایش
							<span id="page-starts-from" class="page-starts-from">'.persian_num((($current_page*$this->_get_url['item_in_page'])-$this->_get_url['item_in_page'])+1).'</span>
							تا
							<span id="page-ends-to" class="page-ends-to">'.persian_num(($iip=$current_page*$this->_get_url['item_in_page'])<$this->_total_rows?$iip:$this->_total_rows).'</span>
							از
							<span id="total_items" class="total_items">'.persian_num($this->_total_rows).'</span>
							رکورد
						</span>
					</div>';

		return $content;
	}

	function init_export(){
		$export_content = '<div class="tDiv3 col-sm-6 text-left">
							<div class="btn-group">
								<a class="m-b-none export-anchor btn btn-outline btn-primary btn-sm" data-url="" target="_blank">
									<div class="fbutton">
										<div>
											<span class="export"><i class="fa fa-file-excel-o"></i>خروجی اکسل</span>
										</div>
									</div>
								</a>
								<a class="m-b-none print-anchor btn btn-outline btn-primary btn-sm" data-url="">
									<div class="fbutton">
										<div>
											<span class="print"><i class="fa fa-print"></i>نسخه چاپی</span>
										</div>
									</div>
								</a>
							</div>
							</div>';
		return $export_content;
	}

	function init_header(){
		$content = '';
		if($this->table_option['header_box']===true) {

			$header_content = '';
			if ($this->table_option['header_text'] != '')
				$header_content .= sprintf('<p class="inline-text text-success m-b">%s</p><hr />', $this->table_option['header_text']);
			$header_content .= '<div class="tDiv row m-b-sm">';
			$header_content .= '<div class="tDiv2 col-sm-12">';
			$header_content .= '<div class="btn-group">';

			foreach ($this->table_option['header_button'] as $txt) {
				$class = '';
				$color = 'primary';
				$ident = '';
				$url = '#';
				$size = 'small';
				$attr = '';
				if (is_array($txt)) {

                    if (isset($txt['role'])){
                        $this->ci->load->model('admin/user/role_model', 'role');
                        if(!$this->ci->role->check_role($txt['role']))
                            continue;
                    }

					$class = isset($txt['class']) ? $txt['class'] : $class;
					$ident = isset($txt['id']) ? $txt['id'] : $ident;
					$color = isset($txt['color']) ? $txt['color'] : $color;
					$url = isset($txt['link']) ? base_url($txt['link']) : $url;
					$size = isset($txt['size']) ? $txt['size'] : $size;
					$attr = isset($txt['attr']) ? $txt['attr'] : $attr;
					$txt = $txt['text'];
				}
				$header_content .= sprintf('<a href="%s" id="%s" title="" class="m-b-none add-anchor add_button btn btn-%s btn-%s %s" %s><div class="fbutton"><div><span class="add">%s</span></div></div></a>', $url, $ident, $size, $color, $class, $attr, $txt);
			}
			$header_content .= '</div>';
			$header_content .= '</div>';
//		$header_content .= $this->init_export();
			$header_content .= '</div>';
			$content = '<div class="row"><div class="col-md-12"> ' . $header_content . '</div></div><hr/>';

		}
        else{
            $content .= '<div class="clearfix"></div>';
        }
		return $content;
	}

	function init_footer(){
		$content = '';
		if($this->table_option['footer_box']===true) {

			$footer_content = '';
			$footer_content .= '<div class="tDiv row m-b-sm">';
			$footer_content .= '<div class="tDiv2 col-sm-12">';
			$footer_content .= '<div class="btn-group">';

			foreach ($this->table_option['footer_button'] as $txt) {
				$class = '';
				$color = 'primary';
				$ident = '';
				$url = '#';
				$size = 'small';
				if (is_array($txt)) {

                    if (isset($txt['role'])){
                        $this->ci->load->model('admin/user/role_model', 'role');
                        if(!$this->ci->role->check_role($txt['role']))
                            continue;
                    }

					$class = isset($txt['class']) ? $txt['class'] : $class;
					$ident = isset($txt['id']) ? $txt['id'] : $ident;
					$color = isset($txt['color']) ? $txt['color'] : $color;
					$url = isset($txt['link']) ? base_url($txt['link']) : $url;
					$size = isset($txt['size']) ? $txt['size'] : $size;
					$txt = $txt['text'];

//					if (isset($txt['role']) && !Role::handle_check_role($txt['role']))
//						continue;
				}
				$footer_content .= sprintf('<a href="%s" id="%s" title="" class="m-b-none add-anchor add_button btn btn-%s btn-%s %s"><div class="fbutton"><div><span class="add">%s</span></div></div></a>', $url, $ident, $size, $color, $class, $txt);
			}
			$footer_content .= '</div>';
			$footer_content .= '</div>';
			$footer_content .= '</div>';

			if ($this->table_option['footer_text'] != '')
				$footer_content .= sprintf('<hr /><p class="inline-text text-success m-b">%s</p>', $this->table_option['footer_text']);
			$content = '<div class="row"><div class="col-md-12"> ' . $footer_content . '</div></div>';

		}
		return $content;
	}

	function init_filter(){
        $table_ident = $this->table_option['table_ident'];

		$result = '';
		if($this->table_option['filter_box']===true) {

			$result = '<div class="search-div-clear-button col-sm-6 text-right"><input type="button" value="حذف فیلتر" id="sc" class="search_clear btn btn-sm btn-warning m-b-none"></div>';
			$result .= '<hr class="col-md-12"/><div class="search-div-clear-button col-sm-12 text-right">';
			if (!empty($this->_filter)) {
				$filters = $this->_filter;
				$content = '';
				foreach ($filters as $key => $filter) {
					$filter_content = '';
					$selected_val = isset($this->_get_url['filter_item'][$key]) ? $this->_get_url['filter_item'][$key] : '';

					foreach ($filter['data'] as $data_val => $data_name) {
                        $selected = (encrypt($selected_val) === encrypt($data_val)) ? 'selected="selected"' : '';
						$filter_content .= sprintf('<option value="%s" %s>%s</option>', $data_val, $selected, $data_name);
					}
					if ($filter_content != '')
                        $content .= sprintf('<span class="btn pull-left margin-top-10"><select name="%sfilter[%s]" class="form-control col-md-6 filters list-select2-filter" data-title="بر اساس:: %s"><option class="text-primary" value="">بر اساس:: %s</option>%s</select></span>', $table_ident,encrypt($filter['action']), $filter['name'], $filter['name'], $filter_content);
//						$content .= sprintf('<div class="form-group col-md-3"><select name="filter[%s]" class="form-control col-md-6 filters list-select2-filter" data-title="بر اساس:: %s"><option class="text-primary" value="">بر اساس:: %s</option>%s</select></div>', encrypt($filter['action']), $filter['name'], $filter_content);
				}
				if ($content != '')
                    $content = '<label class="col-md-1 control-label">فیلتر:</label>' . '<div class="col-md-11">'.$content.'</div>';
				$result .= $content;
			}
			$result .= '</div>';
		}

		return $result;
	}

	function render(){

        if($this->_get_url['ajax_mode'] && !$this->_render_access)
            return false;

        $table_ident = $this->table_option['table_ident'];
        $output = '';

        if($this->ci->pdm->module==$this->ci->pdm->controler)
            $action_path = $this->ci->pdm->module.'/'.$this->ci->pdm->function;
        else
            $action_path = $this->ci->pdm->module.'/'.$this->ci->pdm->controler.'/'.$this->ci->pdm->function;

        if($this->table_option['action_form_custom']!='')
            $action_path .= '/'.trim($this->table_option['action_form_custom'],'/');
        else {
            $action_path .= '/'.trim($this->table_option['action_form'], '/').'/';
        }
		$output .= $this->init_header();
//		$output .= '<hr />';
        if($this->table_option['disable_form']===false)
            $output .= '<form method="post" action="'.base_url($action_path).'" autocomplete="off" id="list_form" class="filtering_form list_form" >';
        $output .= '<div class="table_ident_elem" id="table-number-'.$table_ident.'">';
        $data_input = '';
        if($this->table_option['state']=='handler') {
//			$data_input .= '<input type="hidden" name="' . Tools::encrypt_it($table_ident.'state') . '" value="1" />';
            $_SESSION[$table_ident.'_data'] = array_compress($this->_data);
            $data_input .= '<input type="hidden" name="' . encrypt_it($table_ident.'data') . '" value="'.compress_text($table_ident.'_data').'" />';
        }

		$output .= '<div class="form-group sort_row m-b-xl">
						<input type="hidden" name="'.encrypt_it($table_ident.'base_data').'" value="'.encrypt_it(serialize(array($this->table_option['table'], $this->table_option['ident']))).'" />
						<input type="hidden" name="'.encrypt_it($table_ident.'current_page').'" value="'.$this->_get_url['current_page'].'" id="cp" />
						<input type="hidden" name="'.encrypt_it($table_ident.'order_name').'" value="'.encrypt_it($this->_get_url['order_name']).'" id="on" />
						<input type="hidden" name="'.encrypt_it($table_ident.'order_type').'" value="'.$this->_get_url['order_type'].'" id="ot" />
						<input type="hidden" name="'.encrypt_it($table_ident.'delete_item').'" value="" id="di" />
						<input type="hidden" name="'.encrypt_it($table_ident.'table_ident').'" value="'.encrypt_it($table_ident).'" />
						<input type="hidden" name="'.encrypt_it('ajax_mode').'" value="1" />
						'.$data_input.'
						<div class="sDiv quickSearchBox row form-group" id="quickSearchBox">
							'.$this->init_search().'
							'.$this->init_filter().'
						</div>
					</div>
					<div class="ajax_row m-b-md">
						<div id="ajax_list" class="ajax_list">'.$this->init_list().'</div>
					</div>';
        $output .= $this->init_limit();
        $output .= '</div>';
        if($this->table_option['disable_form']===false)
            $output .= '</form>';
        $output .= $this->init_footer();

        if($this->_get_url['ajax_mode']){send_value(array('content'=>$output,'ident'=>'#table-number-'.$table_ident,'error'=>0),'json');}

        return '<div class="grid-list-box">'.$output.'</div>';
	}

	function delete_mode(){

		$full_delete = false;
		$del_item = $this->_get_url['delete_item'];
		if (isset($del_item[0]) && $del_item[0] != '') {
			$result = array('error' => 0, 'msg' => '');

			if(substr(decompress_text($del_item[0]),0,2)=='00')
				$full_delete = true;
            foreach ($del_item as $indx=>$item){
                $item = decompress_text($item);
                $del_item[$indx] = $full_delete?substr($item,2):$item;
            }
            // check if exist function to handler before delete
			if(isset($this->before_delete['function']) && $this->before_delete['function']!='') {

				$params['in'] = array($this->_ident, (is_array($del_item) ? $del_item : array($del_item)));
				if (sizeof(($rows = $this->get_rows($this->_table, $params))) > 0) {

					if ($this->before_delete['params'])
						$params = array( 'base' => $this->before_delete['params'] ,'db' => $rows, 'full_delete' => 0 );
					else
						$params = array( 'db'=>$rows, 'full_delete' => 0);

                    if($full_delete===true)
                        $params['full_delete'] = 1;

                    $func_params = (isset($this->before_delete['model']) ? array('model' => $this->before_delete['model']) : null);
					$result = call_user_function($this->before_delete['function'], $params, array('error'=>1 , 'msg'=>'مشکل در اجرای حذف لطفا بعدا اقدام کنید!', 'mode'=>'json'), $func_params);

				} else {
					$result = array('error'=>1 , 'msg'=>'خطا در حذف درخواست غیرمجاز!' );
				}
			}

			if (!$result['error']) {
                // if we have no error you can delete the rows selected from databse
				if($this->before_delete['params'] && !isset($this->before_delete['params']['no_delete']) && $full_delete) {
                    if ($this->delete_multi(array($this->_ident, $del_item), $this->_table))
                        $result = array('error' => 0);
                    else
                        array('error' => 1, 'msg' => 'خطا در حذف - درخواست غیرمجاز ! - با پشتیبانی سیستم تماس حاصل کنید.');
                }
			} else {
				$result = array('error' => 1, 'msg' => 'خطا در حذف - درخواست غیرمجاز !  '.($result['msg'] ? $result['msg'] : '') );
			}
			send_value($result,'json');
		}
	}
}