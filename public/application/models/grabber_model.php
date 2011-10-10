<?php
class Grabber_model extends CI_Model {

    function __construct()
    {
        // Call the Model constructor
        parent::__construct();
    }
    
    function get_cats()
    {
        $query = $this->db->get('cats');
        return $query->result();
    }

    function create_job($jobarray){
    $this->db->insert('jobs', $jobarray);
    $insertedjob= mysql_insert_id();
    return $insertedjob;
    }

    function find_links($links){
    $this->db->from('links')->where_in('link', $links);
    $query = $this->db->get();
    return $query->result();
    }

    function link_jobs($links){
        $this->db->insert_batch('links', $links); 
    }

    function get_job($id)
    {
        $query = $this->db->get_where('jobs', array('id'=>$id));
        return $query->result();
    }
}
?>