<?php

  /**
  * ProjectCompanies, generated on Wed, 15 Mar 2006 22:57:46 +0100 by 
  * DataObject generation tool
  *
  * @author Ilija Studen <ilija.studen@gmail.com>
  */
  class ProjectCompanies extends BaseProjectCompanies {
  
    /**
    * Return all companies that are on specific project. Owner company is excluded from 
    * this listing (only client companies are returned)
    *
    * @access public
    * @param Project $project
    * @param string $additional_conditions Additional SQL conditions
    * @return array
    */
    static function getCompaniesByProject(Project $project, $additional_conditions = null) {
      $companies_table = Companies::instance()->getTableName(true);
      $project_companies_table=  ProjectCompanies::instance()->getTableName(true);
      
      // Restrict result only on owner company
      $owner_id = owner_company()->getId();
      
      $companies = array();
      
      $sql = "SELECT $companies_table.* FROM $companies_table, $project_companies_table WHERE ($companies_table.`client_of_id` = '$owner_id') AND ($companies_table.`id` = $project_companies_table.`company_id` AND $project_companies_table.`project_id` = " . DB::escape($project->getId()) . ')';
      if(trim($additional_conditions) <> '') $sql .= " AND ($additional_conditions)";
      
      $rows = DB::executeAll($sql);
      if(is_array($rows)) {
        foreach($rows as $row) {
          $companies[] = Companies::instance()->loadFromRow($row);
        } // foreach
      } // if
      
      return count($companies) ? $companies : null;
      
    } // getCompaniesByProject
    
    /**
    * Return all companies that are on specific projects, determined by a CVS list of project ids.
    *
    * @access public
    * @param string $projects_csv CSV list of projects
    * @param string $additional_conditions Additional SQL conditions
    * @param bool $include_owner Include the owner company
    * @return array Array of Companies
    */
    static function getCompaniesByProjects($projects_csv, $additional_conditions = null, $include_owner = true) {
    	$companies = array();
    	$companies_table = Companies::instance()->getTableName(true);
    	$project_companies_table=  ProjectCompanies::instance()->getTableName(true);

		// Restrict result only on owner company
    	$ownerCond = '';
    	if (!$include_owner){
    		$owner_id = owner_company()->getId();
    		$ownerCond = "$companies_table.`client_of_id` = '$owner_id' AND ";
    	}
    	
    	$sql = "SELECT $companies_table.* FROM $companies_table, $project_companies_table WHERE $ownerCond ($companies_table.`id` = $project_companies_table.`company_id` AND $project_companies_table.`project_id` IN ( " . $projects_csv . '))';
    	if(trim($additional_conditions) <> '') $sql .= " AND ($additional_conditions) ORDER BY $companies_table.`name`";

    	$rows = DB::executeAll($sql);
    	if(is_array($rows)) {
    		foreach($rows as $row) {
    			$companies[] = Companies::instance()->loadFromRow($row);
    		} // foreach
    	} // if

    	return count($companies) ? $companies : null;
    } // getCompaniesByProjects
    
    /**
    * Return all projects that this company is member of
    *
    * @access public
    * @param Company $company
    * @param string $additional_conditions Additional SQL conditions
    * @return array
    */
    static function getProjectsByCompany(Company $company, $additional_conditions = null) {
      if($company->isOwner()) return Projects::getAll();
      
      $projects_table = Projects::instance()->getTableName(true);
      $project_companies_table=  ProjectCompanies::instance()->getTableName(true);
      
      $projects = array();
      
      $sql = "SELECT $projects_table.* FROM $projects_table, $project_companies_table WHERE ($projects_table.`id` = $project_companies_table.`project_id` AND $project_companies_table.`company_id` = " . DB::escape($company->getId()) . ')';
      if(trim($additional_conditions) <> '') $sql .= " AND ($additional_conditions)";
      
      $rows = DB::executeAll($sql);
      if(is_array($rows)) {
        foreach($rows as $row) {
          $projects[] = Projects::instance()->loadFromRow($row);
        } // foreach
      } // if
      
      return count($projects) ? $projects : null;
      
    } // getProjectsByCompany
    
    /**
    * Return all companies associated with specific project
    *
    * @access public
    * @param Project $project
    * @return boolean
    */
    static function clearByProject(Project $project) {
      return DB::execute('DELETE FROM ' . self::instance()->getTableName(true) . ' WHERE `project_id` = ?', $project->getId());
    } // clearByProject
    
    /**
    * Clear permissions by company
    *
    * @param void
    * @return boolean
    */
    static function clearByCompany(Company $company) {
      return self::delete(array('`company_id` = ?', $company->getId()));
    } // clearByCompany
    
  } // ProjectCompanies 

?>