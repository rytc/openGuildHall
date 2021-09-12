<?php
/* openGuildHall
 * Copyright (C) 2011 Ryan Capote <trooper777@gmail.com>
 *
 * This program is free software; you can redistribute it and/or modify it under the terms of 
 * the GNU General Public License as published by the Free Software Foundation; either version 
 * 2 of the License, or (at  your option) any later version.
 *
 * This program is distributed in the hope that it will be useful, but WITHOUT ANY WARRANTY; 
 * without even the implied warranty of MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE. 
 * See the GNU General Public License for more details.
 *
 * You should have received a copy of the GNU General Public License along with this program; 
 * if not, write to the Free Software Foundation, Inc., 59 Temple Place, Suite 330, Boston, MA 02111-1307 USA
 */
 
/**
 * Basic controller action
 */
class Index_Index extends Action {
	
	public function init() {
        Acl::getInstance()->setPageAccessRule(ALLOW);
        PageTracker::track();
        
        return true;
	}
	
	public function action() {
	
		$model = new Model_Forum();
		$userModel = new Model_User();
        
        $boards = $model->fetchBoardsByCategory(NEWS_CATEGORY);
        $latestThreads = $model->fetchLatestThreads(10);

        foreach($latestThreads as &$thread) {
            $thread['post'] = $model->fetchLastPost($thread['id']);
            $thread['post']['author_username'] = $userModel->fetchUsernameById($thread['post']['author_id']);
        }

        if(!empty($boards) || !empty($latestThreads)) {
            // We need to create to arrays.
            // One for fetching the topics
            // and one so we can display
            // which board the topic came
            // from by the board's title.
            $boardList = array();
            $boardAssoc = array();
            foreach($boards as $board) {
                $boardList[] = $board['id'];
                $boardAssoc[$board['id']] = $board['title'];
            }
            
            $threadList = $model->fetchThreads($boardList, 0, '10', 'original_date DESC');
            $news = array();
            for($x = 0; $x < count($threadList); $x++) {
                $thread = $threadList[$x];
                $news[$x]['thread_id'] = $thread['id'];
                $news[$x] = $model->fetchFirstPost($thread['id']);
                $news[$x]['board_title'] = $boardAssoc[$thread['board']];
                $news[$x]['board_id'] = $thread['board'];
    			$news[$x]['author'] = $userModel->fetchUsernameById($news[$x]['author_id']);
            }
            $this->view->assign('boards', $boardAssoc);
            $this->view->assign('latestThreads', $latestThreads);
            $this->view->assign('news', $news);
        }
        
		$this->view->assign('pagetitle', 'Index');
        
		//$this->view->addStylesheet(SITE_THEME.'index.css');
		//LayoutHelper::display($this->view, 'Scripts/Index/Index.phtml');
        
        $this->view->display('index.tpl');
	}
	
}