<?php
public function getPairs($params = '', $usersToAppended = '', $maxCount = 0, $accounts = '') {

    return $this->loadExtension('assignto')->getPairs($params, $usersToAppended, $maxCount, $accounts);
}