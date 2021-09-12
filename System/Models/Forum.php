<?php if(!defined('SITE_PATH')) die('No direct access allowed');
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
 
class Model_Forum {
    private $_categoryTable;
    private $_boardTable;
    private $_threadTable;
    private $_postTable;
    


    //
	// CATEGORIES
	//


	/**
	 * Fetch all of the forum categories
	 * \return Returns an array of associative arrays of category data
	 */
	public function fetchCategories() {
        $table = $this->getCategoryTable();
        
        $result = $table->select(array(), null, 'position ASC');
        
        return $result;
    }

    /**
     * Fetch the data for the category
     * \param $categoryId The ID of the category to fetch
     */
	public function fetchCategory($categoryId) {
		$table = $this->getCategoryTable();
        
        $where = $table->getMySQL()->quoteInto('id = ?', $categoryId);
        $result = $table->select(array(), $where);
        
        return $result[0];
	}

	/**
	 * Update the category
	 * \param $title The title of the category
	 * \param $position The numberical position the category appears.
	 * \param $categoryId The ID of the category to update
	 */
	public function updateCategory($title, $position, $categoryId) {
		$table = $this->getCategoryTable();
		
		$data = $this->_prepareCategory($title, $position);
		
		$where = $table->getMySQL()->quoteInto('id = ?', $categoryId);
		$result = $table->update($data, $where);
		
		return $result[0];
	}
	
	/**
	 * Inserts a new category into the database
	 * \param $title The title of the category
	 * \param $position The numberical position of the category
	 * \return Returns the ID of the newly inserted category
	 */
	public function insertCategory($title, $position) {
		$table = $this->getCategoryTable();
		
		$data = $this->_prepareCategory($title, $position);
		
		$result = $table->insert($data);
		
		return $result;
	}
	
	/**
	 * Deletes the given category
	 * \param $categoryId The ID of the category to delete
	 */
	public function deleteCategory($categoryId) {
		$table = $this->getCategoryTable();
		
		$table->deleteId($categoryId);
	}
	
	protected function _prepareCategory($title, $position) {
		$data = array('title' => $title, 'position' => $position);
		return $data;
	}
	

	//
	// BOARDS
	//

	/** 
	 * Fetches all of the boards in the database
	 */
	public function fetchBoards() {
        $table = $this->getBoardTable();
        
        $result = $table->select(array());
        
        return $result;
    }

    /*
     * Fetch the boards with given parent board
     *
    public function fetchBoardsWithParent($parentid = null) {
    	$table = $this->getBoardTable();

    	if($parentid == null) {
    		$where = 'parent IS NULL';
    	} else {
    		$where = $table->getMySQL()->quoteInto('parent = ?', $parentid);	
    	}
    	
    	$result = $table->select(array(), $where, 'position ASC');

    	return $result;

    	$table = $this->getBoardTable();
        
        $query = "SELECT {prefix}forum_boards.*,
					COUNT(DISTINCT {prefix}forum_threads.id) AS thread_count,
					COUNT({prefix}forum_posts.id) AS post_count
					FROM
					{prefix}forum_boards
					LEFT OUTER JOIN {prefix}forum_threads ON {prefix}forum_boards.id = {prefix}forum_threads.board_id
					LEFT OUTER JOIN {prefix}forum_posts ON {prefix}forum_threads.id = {prefix}forum_posts.thread_id
					GROUP BY {prefix}forum_boards.id
					ORDER BY {prefix}forum_boards.position";

		$result = $table->query($query);
        //$where = $table->getMySQL()->quoteInto('category = ?', $categoryId);
        //$result = $table->select(array(), $where);
        return $result;
    }*/
    
    /**
     * Returns all of the boards in order of the categories
     * \return returns an array of associative arrays of board data
     */
	public function fetchBoardsOrderByCategory() {
	    $table = $this->getBoardTable();
        
        $result = $table->select(array(), null, 'category ASC, position ASC');
        
        return $result;
	}
	
	/** 
	 * Fetches the given board
	 * \param $boardId
	 * \return Returns an associative array of the board's data
	 */
    public function fetchBoard($boardId) {
        $table = $this->getBoardTable();
        
        $where = $table->getMySQL()->quoteInto('id = ?', $boardId);
        $result = $table->select(array(), $where, 'position ASC');
        
        return $result[0];
        
    }
	
	/**
	 * Fetch all the boards in the given category
	 * \param $categoryId The ID of the category that the boards belong to
	 * \return Returns an array of associative arrays of board data including the number of threads as "thread_count" and number of posts as "post_count"
	 */
    public function fetchBoardsByCategory($categoryId) {
        $table = $this->getBoardTable();
        
        $query = "SELECT {prefix}forum_boards.*,
				COUNT(DISTINCT {prefix}forum_threads.id) AS thread_count,
				COUNT({prefix}forum_posts.id) AS post_count
				FROM {prefix}forum_boards
				LEFT OUTER JOIN {prefix}forum_threads ON {prefix}forum_boards.id = {prefix}forum_threads.board_id
				LEFT OUTER JOIN {prefix}forum_posts ON {prefix}forum_threads.id = {prefix}forum_posts.thread_id
				WHERE {prefix}forum_boards.category = '".$categoryId.
				"' GROUP BY {prefix}forum_boards.id
				ORDER BY {prefix}forum_boards.position";

		$result = $table->query($query);
        //$where = $table->getMySQL()->quoteInto('category = ?', $categoryId);
        //$result = $table->select(array(), $where);
        $result = $result->fetch_all(MYSQLI_ASSOC);
        return $result;
    }

    /**
     * Fetch the number of threads in the board
     * \param $boardId The ID of the board that the posts belong to
     * \return Returns the number of threads in the board
     */
	public function fetchThreadCount($boardId) {
        $table = $this->getThreadTable();
        
        $result = $table->query($table->getMySQL()->quoteInto('SELECT COUNT(*) FROM {prefix}forum_threads WHERE board_id = ?', $boardId));
        $result = $result->fetch_array();
        
        return $result[0];
    }

    /** 
     * Fetch the latest thread in the board
     * \param $boardId The ID the board to fetch the thread from
     * \return Returns an associative array of thread data
     */
    public function fetchLatestThread($boardId) {
        $table = $this->getThreadTable();
        
		$where = $table->getMySQL()->quoteInto('board_id = ?', $boardId);
        $result = $table->select(array(), $where, 'date DESC', 1);
        
        return $result;
    }
    
    /**
     * Fetches the number of posts in the board
     * \param $boardId The ID of the board to fetch the post count for
     * \return Returns the number of posts in the board
     */
	public function fetchReplyCountForBoard($boardId) {
        $table = $this->getThreadTable();
        
        // COUNT each post in each thread in our board
        // We need to join the post and thread table
        // because our posts don't know which board
        // they are in
        $result = $table->query('SELECT COUNT(*) FROM `{prefix}forum_posts` AS posts 
                                 JOIN `{prefix}forum_threads` AS threads ON posts.thread_id = threads.id 
                                 WHERE threads.board_id = '.$boardId);
                                 
        $result = $result->fetch_array();
        
        return $result[0];
        
    }
	

	/**
	 * Insert a new board into the database
	 * \param $title The new title of the board
	 * \param $description The new description of the board
	 * \param $category The category that the board belongs to
	 * \param $position The numerical position of the board inside the category
	 */
	public function insertBoard($title, $description, $category, $position) {
		$table = $this->getBoardTable();
		
		$data = $this->_prepareBoard($title, $description, $category, $position);
		
		$result = $table->insert($data);
		
		return $result;
	}

	/** 
	 * Updates a board
	 * \param $title The title of the board
	 * \param $description The description of the board
	 * \param $categoryId The ID of the category that the board belongs to
	 * \param $position The numberical position of the board inside the category
	 * \param $id The ID of the board to update
	 */
	public function updateBoard($title, $description, $categoryId, $position, $id) {
		$table = $this->getBoardTable();
		
		$data = $this->_prepareBoard($title, $description, $categoryId, $position);
		$where = $table->getMySQL()->quoteInto('id = ?', $id);
		
		$result = $table->update($data, $where);
		
		return $result;
	}
	
	/**
	 * Deletes the board
	 * \param $boardId The ID of the board to delete
	 */
	public function deleteBoard($boardId) {
		$table = $this->getBoardTable();
		
		$table->deleteId($boardId);
	}
	
	protected function _prepareBoard($title, $description, $category, $position) {
		$data['title'] = $title;
		$data['description'] = $description;
		$data['category'] = $category;
		$data['position'] = $position;
		
		return $data;
	}
	

	
	//
	// THREADS
	//

	/**
	 * Returns a thread
	 * \param $theadId ID of the thread of the thread to fetch
	 * \return An associative array of thread data
	 */
	public function fetchThread($threadId) {
		$table = $this->getThreadTable();
		
		$where = $table->getMySQL()->quoteInto('id = ?', $threadId);
		$result = $table->select(array(), $where, 'date ASC', 1);
		
		return $result;
	}
   
    /**
     * Returns an array of threads
     * \param $boardId The ID of the board to fetch the threads for. If it's an array, it will fetch the threads for all of the boards.
     * \param $page The page of the returned results. Based on $limit
     * \param $limit The number of rows to fetch
     * \param $order The SQL query for the order to fetch the threads
     * \return Returns an array of associative arrays of thread data including the author's username as "author" and the number of posts as "post_count"
     */
    public function fetchThreads($boardId, $page, $limit = 16, $order = 'date DESC') {
        $table = $this->getThreadTable();
        
        if(!empty($page) && !empty($limit)) {
            $page -= 1;
            $limit = ($page * $limit).','.$limit;
        } elseif(is_numeric($limit) && empty($page)) {
            // We don't need to set a limit or anything
        } else {
            $limit = 0;
        }
        
        // We can pass an array of IDs to match
        // Default is a single ID
        $where = "{prefix}forum_threads.board_id = " . $boardId;
        if(is_array($boardId)) {
        	$where = "{prefix}forum_threads.board_id IN (";
        	for($i = 0; $i < count($boardId);) {
        		$where .= $boardId[$i];
        		$i++;
        		if($i < count($boardId)) {
					$where .= ",";        			
        		}        	
        	}	  
        	$where .= ")";      
        }
        
        $query = 'SELECT {prefix}forum_threads.* , 
        		  {prefix}users.username as author, 
        		  COUNT({prefix}forum_posts.id) AS post_count
        		  FROM  `{prefix}forum_threads` 
        		  LEFT OUTER JOIN {prefix}users ON {prefix}forum_threads.author_id = {prefix}users.id
        		  LEFT OUTER JOIN {prefix}forum_posts ON {prefix}forum_threads.id = {prefix}forum_posts.thread_id 
        		  WHERE '.$where.'
        		  GROUP BY {prefix}forum_threads.id
        		  ORDER BY {prefix}forum_threads.sticky DESC, '.$order.' LIMIT '.$limit;

        $rows = $table->query($query);
        																																						
        $result = $rows->fetch_all(MYSQLI_ASSOC);

        return $result;
    }
    
    /*
     * Returns all of the newest threads based on date.
     * \param $limit The number of rows to return
     * \return Returns an array of associative arrays of thread data
     */
    public function fetchLatestThreads($limit = 16) {
    	$table = $this->getThreadTable();
    	
    	$result = $table->select(array(), '', 'date DESC', $limit);

    	return $result;

    }
	

	/**
	 * Returns the newest thread in a board.
	 * \param $boardId The ID of the board
	 * \return Returns an assocaitive array that contains thread data.
	 */
    public function fetchLatestThreadInBoard($boardId) {
    	$table = $this->getThreadTable();

    	$result = $table->select(array(), 'board_id = '.$boardId, 'date DESC', 1);
    	
    	return $result;
    }
    
    /**
     * Returns all of the threads since a given date
     * \param $date The timestamp of the earliest date of rows to fetch
     * \param $limit The number of rows to return.
     * \param Returns an array of associative arrays that contain thread data
     */
	public function fetchThreadsSinceDate($date, $limit = 16) {
		$table = $this->getThreadTable();
		
		$where = $table->getMySQL()->quoteInto('`date` >= ?', $date);
		$result = $table->select(array(), $where, 'date ASC', $limit);
		
		return $result;
	}

	/**
	 * Returns all threads that have been marked as important. (sticky = 2)
	 * \return Returns an array of associative arrays that contain thread data
	 */ 
	public function fetchImportantThreads() {
		$table = $this->getThreadTable();
		
		$where = 'sticky = 2';
		$result = $table->select(array(), $where, 'date ASC');
		
		return $result;
	}

	/**
	 * Increment the number of views the given thread has
	 * \param $threadId The ID of the thread to increment the views of.
	 */
	public function incrementThreadViews($threadId) {
		$table = $this->getThreadTable();
		
		$where = $table->getMySQL()->quoteInto('id = ?', $threadId);
		$result = $table->incrementCell('views', $where);
		
		return $result;
	}


	/**
	 * Mark a thread as sticky
	 * \param $threadId The ID of the thread to mark as sticky
	 */
	public function stickyThread($threadId) {
		$table = $this->getThreadTable();
		
		$where = $table->getMySQL()->quoteInto('id = ?', $threadId);
		$result = $table->updateCell('sticky', 1, $where);
		
		return $result;
	}
	
	/**
	 * Unflag a thread as sticky
	 * \param $threadId The ID of the thread to mark as
	 */
	public function unstickyThread($threadId) {
		$table = $this->getThreadTable();
		
		$where = $table->getMySQL()->quoteInto('id = ?', $threadId);
		$result = $table->updateCell('sticky', 0, $where);
		
		return $result;
	}
	
	/**
	 * Mark a thread as locked
	 * \param $threadId The ID of the thread to lock
	 */
	public function lockThread($threadId) {
		$table = $this->getThreadTable();
		
		$where = $table->getMySQL()->quoteInto('id = ?', $threadId);
		$result = $table->updateCell('locked', 1, $where);
		
		return $result;
	}

	/**
	 * Unlock a thread
	 * \param $threadId The ID of the thread to unlock
	 */
	public function unlockThread($threadId) {
		$table = $this->getThreadTable();
		
		$where = $table->getMySQL()->quoteInto('id = ?', $threadId);
		$result = $table->updateCell('locked', 0, $where);
		
		return $result;
	}

	/**
	 * Move a thread to a different board.
	 * \param $threadId The ID of the thread to move
	 * \param $destination The ID of the board to move the thread to
	 */
	public function moveThread($threadId, $destination) {
		$table = $this->getThreadTable();
		$data['board'] = $destination;
		
		$where = $table->getMySQL()->quoteInto('id = ?', $threadId);
		$result = $table->update($data, $where);
		
		return $result;
	}

	/**
	 * Mark a thread as important
	 * \param $threadId The ID of the thread to mark as important
	 */
	public function makeThreadImportant($threadId) {
		$table = $this->getThreadTable();
		
		$where = $table->getMySQL()->quoteInto('id = ?', $threadId);
		$result = $table->updateCell('sticky', 2, $where);
		
		return $result;
	}
	
	/**
	 * Update the date of a given thread.
	 * \param $date The new date for the thread
	 * \param $threadId The ID of the thread to update
	 */
	public function updateThreadDate($date, $threadId) {
		$table = $this->getThreadTable();
		$data['date'] = $date;
		
		$where = $table->getMySQL()->quoteInto('id = ?', $threadId);
        $result = $table->update($data, $where);
        
        return $result;
	}

	/**
	 * Update the title of a thread
	 * \param $title The new title
	 * \param $threadId The ID of the thread to update
	 */
	public function updateThreadTitle($title, $threadId) {
		$table = $this->getThreadTable();
		$data['title'] = $title;
		
		$where = $table->getMySQL()->quoteInto('id = ?', $threadId);
        $result = $table->update($data, $where);
        
        return $result;
	}
	
	/**
	 * Insert a new thread into the database
	 * \see insertNewPost
	 * \param $title The title of the thread
	 * \param $date The date the thread was created
	 * \param $authorId The ID of the author
	 * \param $boardId The ID of the board where the thread is located
	 * \return Returns the ID of the newly created thread
	 */
	public function insertNewThread($title, $date, $authorId, $boardId) {
		$data['title'] = $title;
		$data['date'] = $date;
		$data['original_date'] = $date;
		$data['author_id'] = $authorId;
		$data['board'] = $boardId;
		
		$table = $this->getThreadTable();
		$table->insert($data);
		
		return $table->getInsertId();
	}
	
	/**
	 * Deletes the threads, and their relative posts, in a board.
	 * \param $boardId The ID of the board to drop.
	 */
	public function deleteAllThreadsInBoard($boardId) {
		$threadTable = $this->getThreadTable();
		$postTable = $this->getPostTable();

		// Only delete 50 at a time
		$threads = $this->fetchThreads($boardId, 0, 50);
		
		while(!empty($threads)) {
			foreach($threads as $thread) {
				$postTable->deleteAllWhere($postsTable->getMySQL()->quoteInto('thread_id = ?', $thread['id']));
				$threadTable->deleteId($thread['id']);
			}
			
			unset($threads);
			$threads = $this->fetchThreads($boardId, 0, 50);
		}
	}
	
	/**
	 * Deletes the given thread and it's posts.
	 * \param $threadId The ID of the thread to delete.
	 */
	public function deleteThread($threadId) {
		$threadTable = $this->getThreadTable();
		$postTable = $this->getPostTable();
		
		$threadTable->deleteId($threadId);
		$postTable->deleteAllWhere($postTable->getMySQL()->quoteInto('thread_id = ?', $threadId));
	}

	/**
	 * Returns the number of replies to a thread
	 * \param $threadId The ID of the thread for the reply count.
	 * \return Returns the number of replies in the given thread.
	 */
    public function fetchReplyCount($threadId) {
        $table = $this->getPostTable();
        
        $result = $table->query('SELECT COUNT(*) FROM `{prefix}forum_posts` WHERE `thread_id` = ' . $threadId);
        $result = $result->fetch_array();
        
        return $result[0];
    }
    
	

	//
	// POSTS
	//

	/**
	 * Returns the requested post in an associative array
	 * \param $postId The ID of the post to fetch
	 * \return Returns an associative array of the post's data including the author's username as "author"
	 */
	public function fetchPost($postId) {
		$table = $this->getPostTable();
		
		$query = "SELECT {prefix}forum_posts.*, {prefix}users.username AS author 
				FROM {prefix}forum_posts, {prefix}users 
				WHERE {prefix}users.id = {prefix}forum_posts.author_id 
				AND {prefix}forum_posts.id = $postId 
				ORDER BY {prefix}forum_posts.date 
				ASC LIMIT 1";
		
		$result = $table->query($query);
		
		return $result->fetch_assoc();
	}

	/**
	 * Fetch posts based on thread and page number.
	 * \param $threaId The ID of the thread to fetch the posts for.
	 * \param $page The page number to fetch the posts for. Based on $limit
	 * \param $limit The number of rows to fetch
	 * \return Returns an array of associative arrays of forum posts.
	 */
    public function fetchPosts($threadId, $page = null, $limit = null) {
		$table = $this->getPostTable();
		
		
		$where = $table->getMySQL()->quoteInto('thread_id = ?', $threadId);
		if($page == null || $limit == null) {
			$result = $table->select(array(), $where);
		} else {
			$page -= 1;
			$result = $table->select(array(), $where, 'date ASC', ($page*$limit).','.$limit);
		}
		
		return $result;
		
	}

	/**
	 * Fetch the last post in the given thread
	 * \param $threadId The ID of the thread to fetch the last post from.
	 * \return Returns an array of associative arrays with post data, and the author's username as "author"
	 */
    public function fetchLastPost($threadId) {
        $table = $this->getPostTable();

        $query = "SELECT {prefix}forum_posts.*, {prefix}users.username AS author
					FROM {prefix}forum_posts, {prefix}users 
					WHERE {prefix}users.id = {prefix}forum_posts.author_id
					AND {prefix}forum_posts.thread_id = $threadId
					ORDER BY {prefix}forum_posts.date DESC";
        $result = $table->query($query);
        
        return $result->fetch_assoc();
    }
    
    /**
     * Fetches the last post in the given board
     * \param $boardId The ID of the board to fetch the last post of.
     * \return Returns an associative array of thread and post data including the thread title as "thread_title" and the author's username as "author"
     */
    public function fetchLastPostInBoard($boardId) {
    	$table = $this->getThreadTable();
    	
    	$query = "SELECT {prefix}forum_threads.title as thread_title, {prefix}forum_posts.*, {prefix}users.username as author
				FROM {prefix}forum_threads 
				LEFT OUTER JOIN {prefix}forum_posts ON {prefix}forum_threads.id = {prefix}forum_posts.thread_id
				LEFT OUTER JOIN {prefix}users ON {prefix}forum_posts.author_id = {prefix}users.id
				WHERE {prefix}forum_threads.board_id = ".$boardId."
				ORDER BY {prefix}forum_threads.date ASC, {prefix}forum_posts.date ASC
				LIMIT 1";
    	
    	$result = $table->query($query);
    	
    	return $result->fetch_assoc();
    }
    
    /**
     * Returns the first post in the given thread
     * \param $threadId The ID of the thread to fetch the first post from.
     */ 
    public function fetchFirstPost($threadId) {
        $table = $this->getPostTable();
        
        $where = 'thread_id = '.$threadId;
        $result = $table->select(array(), $where, 'date ASC', 1);
        
        return $result;
    }
	
	/**
	 * Inserts a new post into the database
	 * \see insetNewThread
	 * \param $title The title of the post. Can be empty
	 * \param $copy The body text of the post
	 * \param $date The date the post was created
	 * \param $authorId The ID of the user who created the post
	 * \param $threadId The ID of the thread of which the post belongs to
	 * \param $disableSmileys A flag for disabling smileys.
	 * \param $disableBBcode A flag for disabling BBCode.
	 */ 
	public function insertNewPost($title, $copy, $date, $authorId, $threadId, 
									$disableSmileys, $disableBBcode) {
		$table = $this->getPostTable();
		$data['title'] = $title;
		$data['copy'] = $copy;
		$data['date'] = $date;
		//$data['original_date'] = $date;
		$data['author_id'] = $authorId;
		$data['thread_id'] = $threadId;
		$data['disable_smileys'] = $disableSmileys;
		$data['disable_bbcode'] = $disableBBcode;
		
		
		$table->insert($data);
	}

	/**
	 * Updates a given post
	 * \param $title The new title for a post. Can be empty
	 * \param $copy The body text of the post
	 * \param $disableSmileys A flag for disabling smileys
	 * \param $disableBBcode A flag for disabling BBCode
	 * \param $postId The ID of the post to update
	 */
	public function updatePost($title, $copy, $disableSmileys, $disableBBcode, $postId) {
		$table = $this->getPostTable();
		$data['title'] = $title;
		$data['copy'] = $copy;
		$data['disable_smileys'] = $disableSmileys;
		$data['disable_bbcode'] = $disableBBcode;
		
		$where = $table->getMySQL()->quoteInto('id = ?', $postId);
        $result = $table->update($data, $where);
        
        return $result;
	}

	/**
	 * Deletes a post
	 * \param $postId The ID of the post to delete
	 */
	public function deletePost($postId) {
		$postTable = $this->getPostTable();
		$postTable->deleteId($postId);
	}

	/** 
	 * Fetch the post count of a thread
	 * \param $threadId The ID of the thread to fetch the post count for.
	 */
	/*public function fetchPostCount($threadId) {
		$table = $this->getPostTable();
		
		$result = $table->query("SELECT COUNT(*) FROM {prefix}forum_posts WHERE thread_id = ".$threadId);
		$array = $result->fetch_array();
		
		return $array[0];
	}*/

	//
	// TABLES
	//

    protected function getCategoryTable() {
        if(!isset($this->_categoryTable)) {
            $this->_categoryTable = new MySQLTable($this->prefix().'forum_categories');
        }
        
        return $this->_categoryTable;
    }
    
    protected function getBoardTable() {
        if(!isset($this->_boardTable)) {
            $this->_boardTable = new MySQLTable($this->prefix().'forum_boards');
        }
        
        return $this->_boardTable;
    }

    protected function getThreadTable() {
        if(!isset($this->_threadTable)) {
            $this->_threadTable = new MySQLTable($this->prefix().'forum_threads');
        }
		
        return $this->_threadTable;
    }
    
    protected function getPostTable() {
        if(!isset($this->_postTable)) {
            $this->_postTable = new MySQLTable($this->prefix().'forum_posts');
        }
        
        return $this->_postTable;
    }

    protected function prefix() {
    	return MySQL::getInstance()->getTablePrefix();
    }
};
