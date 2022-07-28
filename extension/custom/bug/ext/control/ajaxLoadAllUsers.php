<?php
helper::import('../../control.php');
class myBug extends bug {
      /**
     * AJAX: get all users as assignedTo list.
     *
     * @param  string $selectedUser
     * @access public
     * @return string
     */
    public function ajaxLoadAllUsers($selectedUser = '')
    {
        $allUsers = $this->loadModel('user')->getPairs('assignto|devfirst|noclosed');
        echo 222;exit;

        return print(html::select('assignedTo', $allUsers, $selectedUser, 'class="form-control"'));
    }

}
