<?php


/**
 * ProjectFiles class
 *
 * @author Ilija Studen <ilija.studen@gmail.com>
 */
abstract class BaseProjectFiles extends ProjectDataObjects {

	/**
	 * Column name => Column type map
	 *
	 * @var array
	 * @static
	 */
	static private $columns = array(
	    'id' => DATA_TYPE_INTEGER, 
	    'filename' => DATA_TYPE_STRING, 
	    'description' => DATA_TYPE_STRING, 
	    'is_private' => DATA_TYPE_BOOLEAN, 
	    'is_important' => DATA_TYPE_BOOLEAN, 
	    'is_locked' => DATA_TYPE_BOOLEAN, 
	    'is_visible' => DATA_TYPE_BOOLEAN, 
	    'expiration_time' => DATA_TYPE_DATETIME, 
	    'comments_enabled' => DATA_TYPE_BOOLEAN, 
	    'anonymous_comments_enabled' => DATA_TYPE_BOOLEAN, 
	    'created_on' => DATA_TYPE_DATETIME, 
	    'created_by_id' => DATA_TYPE_INTEGER, 
	    'updated_on' => DATA_TYPE_DATETIME, 
	    'updated_by_id' => DATA_TYPE_INTEGER,
	    'checked_out_on' => DATA_TYPE_DATETIME,
	    'checked_out_by_id' => DATA_TYPE_INTEGER,
	    'was_auto_checked_out' => DATA_TYPE_BOOLEAN,
    	'trashed_on' => DATA_TYPE_DATETIME,
     	'trashed_by_id' => DATA_TYPE_INTEGER,
		'type' => DATA_TYPE_INTEGER,
		'url' => DATA_TYPE_STRING,
		'mail_id' => DATA_TYPE_INTEGER,
    	'archived_on' => DATA_TYPE_DATETIME,
    	'archived_by_id' => DATA_TYPE_INTEGER,
	);

	/**
	 * Construct
	 *
	 * @return BaseProjectFiles
	 */
	function __construct() {
		Hook::fire('object_definition', 'ProjectFile', self::$columns);
		parent::__construct('ProjectFile', 'project_files', true);
	} // __construct

	// -------------------------------------------------------
	//  Description methods
	// -------------------------------------------------------

	/**
	 * Return array of object columns
	 *
	 * @access public
	 * @param void
	 * @return array
	 */
	function getColumns() {
		return array_keys(self::$columns);
	} // getColumns

	/**
	 * Return column type
	 *
	 * @access public
	 * @param string $column_name
	 * @return string
	 */
	function getColumnType($column_name) {
		if(isset(self::$columns[$column_name])) {
			return self::$columns[$column_name];
		} else {
			return DATA_TYPE_STRING;
		} // if
	} // getColumnType

	/**
	 * Return array of PK columns. If only one column is PK returns its name as string
	 *
	 * @access public
	 * @param void
	 * @return array or string
	 */
	function getPkColumns() {
		return 'id';
	} // getPkColumns

	/**
	 * Return name of first auto_incremenent column if it exists
	 *
	 * @access public
	 * @param void
	 * @return string
	 */
	function getAutoIncrementColumn() {
		return 'id';
	} // getAutoIncrementColumn

	/**
	 * Return system columns
	 *
	 * @access public
	 * @param void
	 * @return array
	 */
	function getSystemColumns() {
		return array_merge(parent::getSystemColumns(), array(
      		'checked_out_by_id', 'was_auto_checked_out', 'mail_id', 'type')
		);
	} // getSystemColumns
	
	/**
    * Return external columns
    *
    * @access public
    * @param void
    * @return array
    */
    function getExternalColumns() {
      return array_merge(parent::getExternalColumns(), array());
    } // getExternalColumns
	
	/**
    * Return report object title columns
    *
    * @access public
    * @param void
    * @return array
    */
    function getReportObjectTitleColumns() {
      return array('filename');
    } // getReportObjectTitleColumns
    
    /**
    * Return report object title
    *
    * @access public
    * @param void
    * @return string
    */
    function getReportObjectTitle($values) {
    	$filename = isset($values['filename']) ? $values['filename'] : ''; 
    	return $filename;
    } // getReportObjectTitle

	// -------------------------------------------------------
	//  Finders
	// -------------------------------------------------------

	/**
	 * Do a SELECT query over database with specified arguments
	 *
	 * @access public
	 * @param array $arguments Array of query arguments. Fields:
	 *
	 *  - one - select first row
	 *  - conditions - additional conditions
	 *  - order - order by string
	 *  - offset - limit offset, valid only if limit is present
	 *  - limit
	 *
	 * @return one or ProjectFiles objects
	 * @throws DBQueryError
	 */
	function find($arguments = null) {
		if(isset($this) && instance_of($this, 'ProjectFiles')) {
			return parent::find($arguments);
		} else {
			return ProjectFiles::instance()->find($arguments);
			//$instance =& ProjectFiles::instance();
			//return $instance->find($arguments);
		} // if
	} // find

	/**
	 * Find all records
	 *
	 * @access public
	 * @param array $arguments
	 * @return one or ProjectFiles objects
	 */
	function findAll($arguments = null) {
		if(isset($this) && instance_of($this, 'ProjectFiles')) {
			return parent::findAll($arguments);
		} else {
			return ProjectFiles::instance()->findAll($arguments);
			//$instance =& ProjectFiles::instance();
			//return $instance->findAll($arguments);
		} // if
	} // findAll

	/**
	 * Find one specific record
	 *
	 * @access public
	 * @param array $arguments
	 * @return ProjectFile
	 */
	function findOne($arguments = null) {
		if(isset($this) && instance_of($this, 'ProjectFiles')) {
			return parent::findOne($arguments);
		} else {
			return ProjectFiles::instance()->findOne($arguments);
			//$instance =& ProjectFiles::instance();
			//return $instance->findOne($arguments);
		} // if
	} // findOne

	/**
	 * Return object by its PK value
	 *
	 * @access public
	 * @param mixed $id
	 * @param boolean $force_reload If true cache will be skipped and data will be loaded from database
	 * @return ProjectFile
	 */
	function findById($id, $force_reload = false) {
		if(isset($this) && instance_of($this, 'ProjectFiles')) {
			return parent::findById($id, $force_reload);
		} else {
			return ProjectFiles::instance()->findById($id, $force_reload);
			//$instance =& ProjectFiles::instance();
			//return $instance->findById($id, $force_reload);
		} // if
	} // findById

	/**
	 * Return number of rows in this table
	 *
	 * @access public
	 * @param string $conditions Query conditions
	 * @return integer
	 */
	function count($condition = null) {
		if(isset($this) && instance_of($this, 'ProjectFiles')) {
			return parent::count($condition);
		} else {
			return ProjectFiles::instance()->count($condition);
			//$instance =& ProjectFiles::instance();
			//return $instance->count($condition);
		} // if
	} // count

	/**
	 * Delete rows that match specific conditions. If $conditions is NULL all rows from table will be deleted
	 *
	 * @access public
	 * @param string $conditions Query conditions
	 * @return boolean
	 */
	function delete($condition = null) {
		if(isset($this) && instance_of($this, 'ProjectFiles')) {
			return parent::delete($condition);
		} else {
			return ProjectFiles::instance()->delete($condition);
			//$instance =& ProjectFiles::instance();
			//return $instance->delete($condition);
		} // if
	} // delete

	/**
	 * This function will return paginated result. Result is an array where first element is
	 * array of returned object and second populated pagination object that can be used for
	 * obtaining and rendering pagination data using various helpers.
	 *
	 * Items and pagination array vars are indexed with 0 for items and 1 for pagination
	 * because you can't use associative indexing with list() construct
	 *
	 * @access public
	 * @param array $arguments Query argumens (@see find()) Limit and offset are ignored!
	 * @param integer $items_per_page Number of items per page
	 * @param integer $current_page Current page number
	 * @return array
	 */
	function paginate($arguments = null, $items_per_page = 10, $current_page = 1) {
		if(isset($this) && instance_of($this, 'ProjectFiles')) {
			return parent::paginate($arguments, $items_per_page, $current_page);
		} else {
			return ProjectFiles::instance()->paginate($arguments, $items_per_page, $current_page);
			//$instance =& ProjectFiles::instance();
			//return $instance->paginate($arguments, $items_per_page, $current_page);
		} // if
	} // paginate

	/**
	 * Return manager instance
	 *
	 * @return ProjectFiles
	 */
	function instance() {
		static $instance;
		if(!instance_of($instance, 'ProjectFiles')) {
			$instance = new ProjectFiles();
		} // if
		return $instance;
	} // instance

} // ProjectFiles

?>