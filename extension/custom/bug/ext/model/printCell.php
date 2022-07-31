<?php

public function printCell($col, $bug, $users, $builds, $branches, $modulePairs, $executions = array(), $plans = array(), $stories = array(), $tasks = array(), $mode = 'datatable', $projectPairs = array()) {
     /* Check the product is closed. */
     $canBeChanged = common::canBeChanged('bug', $bug);

     $canBatchEdit         = ($canBeChanged and common::hasPriv('bug', 'batchEdit'));
     $canBatchConfirm      = ($canBeChanged and common::hasPriv('bug', 'batchConfirm'));
     $canBatchClose        = common::hasPriv('bug', 'batchClose');
     $canBatchActivate     = ($canBeChanged and common::hasPriv('bug', 'batchActivate'));
     $canBatchChangeBranch = ($canBeChanged and common::hasPriv('bug', 'batchChangeBranch'));
     $canBatchChangeModule = ($canBeChanged and common::hasPriv('bug', 'batchChangeModule'));
     $canBatchResolve      = ($canBeChanged and common::hasPriv('bug', 'batchResolve'));
     $canBatchAssignTo     = ($canBeChanged and common::hasPriv('bug', 'batchAssignTo'));

     $canBatchAction = ($canBatchEdit or $canBatchConfirm or $canBatchClose or $canBatchActivate or $canBatchChangeBranch or $canBatchChangeModule or $canBatchResolve or $canBatchAssignTo);

     $canView = common::hasPriv('bug', 'view');

     $hasCustomSeverity = false;
     foreach($this->lang->bug->severityList as $severityKey => $severityValue)
     {
         if(!empty($severityKey) and (string)$severityKey != (string)$severityValue)
         {
             $hasCustomSeverity = true;
             break;
         }
     }

     $bugLink = helper::createLink('bug', 'view', "bugID=$bug->id");
     $account = $this->app->user->account;
     $id = $col->id;
     if($col->show)
     {
         $class = "c-$id";
         $title = '';
         switch($id)
         {
             case 'id':
                 $class .= ' cell-id';
                 break;
             case 'status':
                 $class .= ' bug-' . $bug->status;
                 $title  = "title='" . $this->processStatus('bug', $bug) . "'";
                 break;
             case 'confirmed':
                 $class .= ' text-center';
                 break;
             case 'title':
                 $class .= ' text-left';
                 $title  = "title='{$bug->title}'";
                 break;
             case 'type':
                 $title  = "title='" . zget($this->lang->bug->typeList, $bug->type) . "'";
                 break;
             case 'assignedTo':
                 $class .= ' has-btn text-left';
                 if($bug->assignedTo == $account) $class .= ' red';
                 break;
             case 'resolvedBy':
                 $class .= ' c-user';
                 $title  = "title='" . zget($users, $bug->resolvedBy) . "'";
                 break;
             case 'project':
                 $title = "title='" . zget($projectPairs, $bug->project, '') . "'";
                 break;
             case 'resolvedBuild':
                 $class .= ' text-ellipsis';
                 $title  = "title='" . $bug->resolvedBuild . "'";
                 break;
         }

         if($id == 'deadline' && isset($bug->delay) && $bug->status == 'active') $class .= ' delayed';
         if(strpos(',type,execution,story,plan,task,openedBuild,', ",{$id},") !== false) $class .= ' text-ellipsis';

         echo "<td class='" . $class . "' $title>";
         if($this->config->edition != 'open') $this->loadModel('flow')->printFlowCell('bug', $bug, $id);
         switch($id)
         {
         case 'id':
             if($canBatchAction)
             {
                 echo html::checkbox('bugIDList', array($bug->id => '')) . html::a(helper::createLink('bug', 'view', "bugID=$bug->id"), sprintf('%03d', $bug->id), '', "data-app='{$this->app->tab}'");
             }
             else
             {
                 printf('%03d', $bug->id);
             }
             break;
         case 'severity':
             $severityValue     = zget($this->lang->bug->severityList, $bug->severity);
             $hasCustomSeverity = !is_numeric($severityValue);
             if($hasCustomSeverity)
             {
                 echo "<span class='label-severity-custom' data-severity='{$bug->severity}' title='" . $severityValue . "'>" . $severityValue . "</span>";
             }
             else
             {
                 echo "<span class='label-severity' data-severity='{$bug->severity}' title='" . $severityValue . "'></span>";
             }
             break;
         case 'pri':
             echo "<span class='label-pri label-pri-" . $bug->pri . "' title='" . zget($this->lang->bug->priList, $bug->pri, $bug->pri) . "'>";
             echo zget($this->lang->bug->priList, $bug->pri, $bug->pri);
             echo "</span>";
             break;
         case 'confirmed':
             $class = 'confirm' . $bug->confirmed;
             echo "<span class='$class'>" . zget($this->lang->bug->confirmedList, $bug->confirmed, $bug->confirmed) . "</span> ";
             break;
         case 'title':
             $showBranch = isset($this->config->bug->browse->showBranch) ? $this->config->bug->browse->showBranch : 1;
             if(isset($branches[$bug->branch]) and $showBranch) echo "<span class='label label-outline label-badge' title={$branches[$bug->branch]}>{$branches[$bug->branch]}</span> ";
             if($bug->module and isset($modulePairs[$bug->module])) echo "<span class='label label-gray label-badge'>{$modulePairs[$bug->module]}</span> ";
             echo $canView ? html::a($bugLink, $bug->title, null, "style='color: $bug->color' data-app={$this->app->tab}") : "<span style='color: $bug->color'>{$bug->title}</span>";
             if($bug->case) echo html::a(helper::createLink('testcase', 'view', "caseID=$bug->case&version=$bug->caseVersion"), "[" . $this->lang->testcase->common  . "#$bug->case]", '', "class='bug' title='$bug->case'");
             break;
         case 'branch':
             echo zget($branches, $bug->branch, '');
             break;
         case 'project':
             echo zget($projectPairs, $bug->project, '');
             break;
         case 'execution':
             echo zget($executions, $bug->execution, '');
             break;
         case 'plan':
             echo zget($plans, $bug->plan, '');
             break;
         case 'story':
             if(isset($stories[$bug->story]))
             {
                 $story = $stories[$bug->story];
                 echo common::hasPriv('story', 'view') ? html::a(helper::createLink('story', 'view', "storyID=$story->id", 'html', true), $story->title, '', "class='iframe'") : $story->title;
             }
             break;
         case 'task':
             if(isset($tasks[$bug->task]))
             {
                 $task = $tasks[$bug->task];
                 echo common::hasPriv('task', 'view') ? html::a(helper::createLink('task', 'view', "taskID=$task->id", 'html', true), $task->name, '', "class='iframe'") : $task->name;
             }
             break;
         case 'toTask':
             if(isset($tasks[$bug->toTask]))
             {
                 $task = $tasks[$bug->toTask];
                 echo common::hasPriv('task', 'view') ? html::a(helper::createLink('task', 'view', "taskID=$task->id", 'html', true), $task->name, '', "class='iframe'") : $task->name;
             }
             break;
         case 'type':
             echo zget($this->lang->bug->typeList, $bug->type);
             break;
         case 'status':
             echo "<span class='status-bug status-{$bug->status}'>";
             echo $this->processStatus('bug', $bug);
             echo  '</span>';
             break;
         case 'activatedCount':
             echo $bug->activatedCount;
             break;
         case 'activatedDate':
             echo helper::isZeroDate($bug->activatedDate) ? '' : substr($bug->activatedDate, 5, 11);
             break;
         case 'keywords':
             echo $bug->keywords;
             break;
         case 'os':
             echo zget($this->lang->bug->osList, $bug->os);
             break;
         case 'browser':
             echo zget($this->lang->bug->browserList, $bug->browser);
             break;
         case 'mailto':
             $mailto = explode(',', $bug->mailto);
             foreach($mailto as $account)
             {
                 $account = trim($account);
                 if(empty($account)) continue;
                 echo zget($users, $account) . " &nbsp;";
             }
             break;
         case 'found':
             echo zget($users, $bug->found);
             break;
         case 'openedBy':
             echo zget($users, $bug->openedBy);
             break;
         case 'openedDate':
             echo helper::isZeroDate($bug->openedDate) ? '' : substr($bug->openedDate, 5, 11);
             break;
         case 'openedBuild':
             $builds = array_flip($builds);
             foreach(explode(',', $bug->openedBuild) as $build)
             {
                 $buildID = zget($builds, $build, '');
                 if($buildID == 'trunk')
                 {
                     echo $build . ' ';
                 }
                 elseif($buildID and common::hasPriv('build', 'view'))
                 {
                     echo html::a(helper::createLink('build', 'view', "buildID=$buildID"), $build, '', "title='$bug->openedBuild'") . ' ';
                 }
             }
             break;
         case 'assignedTo':
             $this->printAssignedHtml($bug, $users);
             break;
         case 'assignedDate':
             echo helper::isZeroDate($bug->assignedDate) ? '' : substr($bug->assignedDate, 5, 11);
             break;
         case 'deadline':
             echo helper::isZeroDate($bug->deadline) ? '' : substr($bug->deadline, 5, 11);
             break;
         case 'resolvedBy':
             echo zget($users, $bug->resolvedBy, $bug->resolvedBy);
             break;
         case 'resolution':
             echo zget($this->lang->bug->resolutionList, $bug->resolution);
             break;
         case 'resolvedDate':
             echo helper::isZeroDate($bug->resolvedDate) ? '' : substr($bug->resolvedDate, 5, 11);
             break;
         case 'resolvedBuild':
             echo $bug->resolvedBuild;
             break;
         case 'closedBy':
             echo zget($users, $bug->closedBy);
             break;
         case 'closedDate':
             echo helper::isZeroDate($bug->closedDate) ? '' : substr($bug->closedDate, 5, 11);
             break;
         case 'lastEditedBy':
             echo zget($users, $bug->lastEditedBy);
             break;
         case 'lastEditedDate':
             echo helper::isZeroDate($bug->lastEditedDate) ? '' : substr($bug->lastEditedDate, 5, 11);
             break;
         case 'actions':
             echo $this->buildOperateMenu($bug, 'browse');
             break;
         case 'take_time':
                echo $bug->take_time;
                break;
         }
         echo '</td>';
     }
    
}