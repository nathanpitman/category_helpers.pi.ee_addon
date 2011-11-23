<?php

if ( ! defined('BASEPATH')) exit('No direct script access allowed');

$plugin_info = array(
  'pi_name' => 'Category Helpers',
  'pi_version' =>'1.0',
  'pi_author' =>'Nine Four',
  'pi_author_url' => 'http://ninefour.co.uk/labs/',
  'pi_description' => 'Fetch info about a given category',
  'pi_usage' => Category_helpers::usage()
  );

class Category_helpers {
    
    function __construct()
    {        
    	$this->EE =& get_instance(); 

    }
   
  function is_here()
  {
       
    $TMPL = $this->EE->TMPL;
    $DB = $this->EE->db;

	$endpoint_id = is_numeric($TMPL->fetch_param('endpoint_id')) ? $TMPL->fetch_param('endpoint_id') : '0';
	$current_id = is_numeric($TMPL->fetch_param('current_id')) ? $TMPL->fetch_param('current_id') : '0';
	
	if (($endpoint_id<=0) OR (!isset($current_id))) return;
  
  	$status = $this->_traverse($endpoint_id);

    $here = "";
	while($status['parent_id']>=0) {
	
		if (($status['cat_id'] == $current_id)) {
	    	// if we found a match in the category tree for the current node
	    	if($status['cat_id'] == $endpoint_id) {
	    		$here = "here endpoint";
	    	} else {
	    		$here = "here";
	    	}
	    	$status['parent_id'] = 	-1;
	    	break;
	    } elseif ($status['parent_id']==0) {
	    	// if we hit the top of the category tree with no matches then stop
	    	$status['parent_id'] = 	-1;
	    	break;
	    } else {
	    // else keep traversing up the tree
	    	$status = $this->_traverse($status['parent_id']);
	    }
	
	}
	
    $this->return_data = $here;
    return $this->return_data;
  }
  
  function _traverse($endpoint_id)
  {
	if ( ! isset($this->EE->session->cache['category_helpers'][$endpoint_id])) {
    
    	$DB = $this->EE->db;
    
    	$q = $DB->query("SELECT * FROM exp_categories WHERE cat_id='$endpoint_id'");
    
    	$this->EE->session->cache['category_helpers'][$endpoint_id]['cat_id'] = $q->row('cat_id');
    	$this->EE->session->cache['category_helpers'][$endpoint_id]['parent_id'] = $q->row('parent_id');
    }
    
   	$tmp = $this->EE->session->cache['category_helpers'][$endpoint_id];
    
    return $tmp;
    
  }

  
  // ----------------------------------------
  //  Plugin Usage
  // ----------------------------------------

  // This function describes how the plugin is used.
  //  Make sure and use output buffering

  function usage()
  {
	  ob_start(); 
	  ?>

    
	  <?php
	  $buffer = ob_get_contents();
		
	  ob_end_clean(); 
	
	  return $buffer;
  }
  // END

}
?>