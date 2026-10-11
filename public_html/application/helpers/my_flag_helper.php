<?php  if ( ! defined('BASEPATH')) exit('No direct script access allowed');
/**
 * My Flag Helper
 *
 * @author		Toby Partridge
 */

// ------------------------------------------------------------------------

/**
 * getImg
 *
 * Returns an html img element of the correct flag for the RAR name that is passed in.
 *
 * @access	public
 * @param	string
 * @return	string
 */
if ( ! function_exists('getImg'))
{
	function getImg($rarName)
	{
	    	    $CI =& get_instance();
        $sql = "
        	SELECT rar_tag 
			FROM rar r 
			WHERE rar_name = ?
		";
		$query = $CI->db->query($sql, array($rarName));
		$result1 = $query->result();
        if (isset($result1)) {
            if ($result1[0]->rar_tag == 'XI') {
                return "<img src=\"".base_url()."images/flags24/_united-nations.png\">";
            } else {
                return "<img src=\"".base_url()."images/flags24/".$result1[0]->rar_tag.".png\">";
            }
        } else {
            return "<img src=\"".base_url()."images/flags24/_united-nations.png\">";
        }

	    //getImg24($rarName);
	    /*
	    $CI =& get_instance();
        $sql = "
        	SELECT rar_tag 
			FROM rar r 
			WHERE rar_name = ?
		";
		$query = $CI->db->query($sql, array($rarName));
		$result1 = $query->result();
        if (isset($result1)) {
            if ($result1[0]->rar_tag == 'XI') {
                return "<img src=\"".base_url()."images/flags16/_united-nations.png\">";
            } else {
                return "<img src=\"".base_url()."images/flags16/".$result1[0]->rar_tag.".png\">";
            }
        } else {
            return "<img src=\"".base_url()."images/flags16/_united-nations.png\">";
        }
		
		if ($rarName == "GB") {
			return "<img src=\"".base_url()."images/gb.gif\">";
		} else if ($rarName == "Italy") {
			return "<img src=\"".base_url()."images/it.gif\">";
		} else if ($rarName == "Australia") {
			return "<img src=\"".base_url()."images/au.gif\">";
		} else if ($rarName == "New Zealand") {
			return "<img src=\"".base_url()."images/nz.gif\">";
		} else if ($rarName == "Germany") {
			return "<img src=\"".base_url()."images/de.gif\">";
		} else if ($rarName == "Ireland") {
			return "<img src=\"".base_url()."images/ie.gif\">";
		} else if ($rarName == "Sweden") {
			return "<img src=\"".base_url()."images/se.gif\">";
		} else if ($rarName == "France") {
			return "<img src=\"".base_url()."images/fr.gif\">";
		} else if ($rarName == "Denmark") {
			return "<img src=\"".base_url()."images/dk.gif\">";
		} else if ($rarName == "Spain") {
			return "<img src=\"".base_url()."images/es.gif\">";
		} else {
			return "<img src=\"".base_url()."images/un.png\">";
		}
		*/
	}
}

if ( ! function_exists('getImg24'))
{
	function getImg24($rarName)
	{
	    $CI =& get_instance();
        $sql = "
        	SELECT rar_tag 
			FROM rar r 
			WHERE rar_name = ?
		";
		$query = $CI->db->query($sql, array($rarName));
		$result1 = $query->result();
        if (isset($result1)) {
            if ($result1[0]->rar_tag == 'XI') {
                return "<img src=\"".base_url()."images/flags24/_united-nations.png\">";
            } else {
                return "<img src=\"".base_url()."images/flags24/".$result1[0]->rar_tag.".png\">";
            }
        } else {
            return "<img src=\"".base_url()."images/flags24/_united-nations.png\">";
        }
	}
}

if ( ! function_exists('getImg16'))
{
	function getImg16($rarName)
	{
	    $CI =& get_instance();
        $sql = "
        	SELECT rar_tag 
			FROM rar r 
			WHERE rar_name = ?
		";
		$query = $CI->db->query($sql, array($rarName));
		$result1 = $query->result();
        if (isset($result1)) {
            if ($result1[0]->rar_tag == 'XI') {
                return "<img src=\"".base_url()."images/flags16/_united-nations.png\">";
            } else {
                return "<img src=\"".base_url()."images/flags16/".$result1[0]->rar_tag.".png\">";
            }
        } else {
            return "<img src=\"".base_url()."images/flags16/_united-nations.png\">";
        }
	}
}

// ------------------------------------------------------------------------


/* End of file MY_flag_helper.php */
/* Location: ./application/helpers/MY_flag_helper.php */