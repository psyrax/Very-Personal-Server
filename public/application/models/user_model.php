<?php
class User_model extends CI_Model {

    function __construct()
    {
        // Call the Model constructor
        parent::__construct();
    }
    
    function get_user($user)
    {
        $query = $this->db->get_where('users', array('user' => $user),1,0);
        return $query->result();
    }
    
    function get_user_id($id)
    {
        $this->db->select('user')->from('users')->where('id', $id);
        $query = $this->db->get();
        return $query->result();
    }



}
?>