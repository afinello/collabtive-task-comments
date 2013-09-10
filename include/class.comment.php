<?php

/**
 * The class provides methods for the realization of comments for tasks.
 * 
 * Based on original class: message (class.message.php)
 *
 * @author Vladimir Afanasyev <vovan.af@gmail.com>
 * @name comment
 * @version 1.0
 * @package Collabtive
 * @link http://www.ugdsoft.com
 * @license http://opensource.org/licenses/gpl-license.php GNU General Public License v3 or later
 */

class comment {
    public $mylog;

    /**
     * Constructor
     * Initialisiert den Eventlog
     */
    function __construct()
    {
        $this->mylog = new mylog;
    }

    /**
     * Creates a new comment
     *
	 * @param int $task Task ID the message belongs to
     * @param int $project Project ID the message belongs to
     * @param string $text Textbody of the message
     * @param string $tags Tags for the message
     * @param int $user User ID of the user adding the message
     * @param string $username Name of the user adding the message
     * @return bool
     */
    function add($task, $project, $text, $tags, $user, $username)
    {
        global $conn;

        $insStmt = $conn->prepare("INSERT INTO tasks_comments (`task`,`project`,`text`,`tags`,`posted`,`user`,`username`) VALUES (?, ?, ?, ?, ?, ?, ? )");
        $ins = $insStmt->execute(array((int) $task, (int) $project, $text, $tags, time(), (int) $user, $username));

        $insid = $conn->lastInsertId();
        if ($ins) {
			$taskObj = new task();
			$taskInfo = $taskObj->getTask( $task );
            $this->mylog->add('Comment to ' . $taskInfo['title'], 'message', 1, $project);
            return $insid;
        } else {
            return false;
        }
    }

    /**
     * Edits a comment
     *
     * @param int $id Eindeutige Nummer der Nachricht
     * @param string $text Text der Nachricht
     * @param string $tags Tags for the message
     * @return bool
     */
    function edit($id, $text, $tags)
    {
        global $conn;

        $updStmt = $conn->prepare("UPDATE `tasks_comments` SET `text`=?, `tags`=? WHERE ID = ?");
        $upd = $updStmt->execute(array($text, $tags, (int) $id));

        if ($upd) {
            $proj = $conn->query("SELECT project, username FROM tasks_comments WHERE ID = $id")->fetch();
            $proj = $proj[0];
			$username = $proj[1];
            $this->mylog->add($username, 'message', 2, $proj);
            return true;
        } else {
            return false;
        }
    }

    /**
     * Deletes a comment
     *
     * @param int $id Eindeutige Nummer der Nachricht
     * @return bool
     */
    function del($id)
    {
        global $conn;
        $id = (int) $id;

        $msg = $conn->query("SELECT username,project FROM tasks_comments WHERE ID = $id")->fetch();

        $del = $conn->query("DELETE FROM tasks_comments WHERE ID = $id LIMIT 1");
        //$del3 = $conn->query("DELETE FROM files_attached WHERE message = $id");
        if ($del) {
            $this->mylog->add($msg[0], 'message', 3, $msg[1]);
            return true;
        } else {
            return false;
        }
    }

    /**
     * Return a comment with all comments in a chain
     *
     * @param int $id Eindeutige Nummer der Nachricht
     * @return array $message Eigenschaften der Nachricht
     */
    function getComment($id)
    {
        global $conn;
        $id = (int) $id;

        $message = $conn->query("SELECT * FROM tasks_comments WHERE ID = $id LIMIT 1")->fetch();
		
		$taskId = $message[0];

        $tagobj = new tags();
        //$milesobj = new milestone();
		$taskobj = new task();
        if (!empty($message)) {
            $replies = $conn->query("SELECT COUNT(*) FROM tasks_comments WHERE task = $taskId")->fetch();
            $replies = $replies[0];

            $user = new user();
            $avatar = $user->getAvatar($message["user"]);

            $ds = $conn->query("SELECT gender FROM user WHERE ID = $message[user]")->fetch();
            $gender = $ds[0];
            $message["gender"] = $gender;

            $project = $conn->query("SELECT name FROM projekte WHERE ID = $message[project]")->fetch();
            $message["pname"] = $project[0];
            $posted = date(CL_DATEFORMAT . " - H:i", $message["posted"]);
            $message["postdate"] = $posted;
            $message["endstring"] = $posted;
            $message["replies"] = $replies;
            $message["avatar"] = $avatar;
            $message["text"] = stripslashes($message["text"]);
            $message["username"] = stripslashes($message["username"]);
            $message["tagsarr"] = $tagobj->splitTagStr($message["tags"]);
            $message["tagnum"] = count($message["tagsarr"]);

            //$attached = $this->getAttachedFiles($message["ID"]);
            //$message["files"] = $attached;
            if ($message["task"] > 0) {
                $miles = $taskobj->getTask($message["task"]);
            } else {
                $miles = array();
            }

            $message["tasks"] = $miles;

            return $message;
        } else {
            return false;
        }
    }

    /**
     * Return all comments to the given task
     *
     * @param int $id Eindeutige Nummer der Nachricht
     * @return array $replies Antworten zur Nachricht
     */
    function getComments($taskId)
    {
        global $conn;
        $id = (int) $taskId;

        $sel = $conn->query("SELECT ID FROM tasks_comments WHERE task = $id ORDER BY posted DESC");
        $replies = array();

        $tagobj = new tags();
        $taskobj = new task();
        $user = new user();
        while ($reply = $sel->fetch()) {
            if (!empty($reply)) {
                $thereply = $this->getComment($reply["ID"]);
                array_push($replies, $thereply);
            }
        }
        if (!empty($replies)) {
            return $replies;
        } else {
            return false;
        }
    }

    function getLatestComments($limit = 5)
    {
        global $conn;
        $limit = (int) $limit;

        $userid = $_SESSION["userid"];
        $sel3 = $conn->query("SELECT projekt FROM projekte_assigned WHERE user = $userid");
        $prstring = "";
        while ($upro = $sel3->fetch()) {
            $projekt = $upro[0];
            $prstring .= $projekt . ",";
        }

        $prstring = substr($prstring, 0, strlen($prstring)-1);
        if ($prstring) {
            $sel1 = $conn->query("SELECT ID FROM tasks_comments WHERE project IN($prstring) ORDER BY posted DESC LIMIT $limit ");
            $messages = array();

            $tagobj = new tags();
            $taskobj = new task();
            while ($message = $sel1->fetch()) {
                $themessage = $this->getComment($message["ID"]);
                array_push($messages, $themessage);
            }
        }
        if (!empty($messages)) {
            return $messages;
        } else {
            return false;
        }
    }

    function attachFile($fid, $mid, $id = 0)
    {
        global $conn;
        $fid = (int) $fid;
        $mid = (int) $mid;
        $id = (int) $id;

        $myfile = new datei();
        if ($fid > 0) {
            $ins = $conn->query("INSERT INTO files_attached (ID,file,message) VALUES ('',$fid,$mid)");
        } else {
            $num = $_POST["numfiles"];

            $chk = 0;
            $insStmt = $conn->prepare("INSERT INTO files_attached (ID,file,message) VALUES ('',?,?)");
            for($i = 1;$i <= $num;$i++) {
                $fid = $myfile->upload("userfile$i", "files/" . CL_CONFIG . "/$id", $id);
                $ins = $insStmt->execute(array($fid, $mid));
            }
        }
        if ($ins) {
            return true;
        } else {
            return false;
        }
    }

    private function getAttachedFiles($msg)
    {
        global $conn;
        $msg = (int) $msg;

        $files = array();
        $sel = $conn->query("SELECT file FROM files_attached WHERE message = $msg");
        while ($file = $sel->fetch()) {
            $sel2 = $conn->query("SELECT * FROM files WHERE ID = $file[0]");
            $thisfile = $sel2->fetch();
            $thisfile["type"] = str_replace("/", "-", $thisfile["type"]);
            if (isset($thisfile["desc"])) {
                $thisfile["desc"] = stripslashes($thisfile["desc"]);
            }
            if (isset($thisfile["tags"])) {
                $thisfile["tags"] = stripslashes($thisfile["tags"]);
            }
            if (isset($thisfile["title"])) {
                $thisfile["title"] = stripslashes($thisfile["title"]);
            }
            $set = new settings();
            $settings = $set->getSettings();
            $myfile = "./templates/" . $settings["template"] . "/images/files/" . $thisfile["type"] . ".png";
            if (stristr($thisfile["type"], "image")) {
                $thisfile["imgfile"] = 1;
            } elseif (stristr($thisfile["type"], "text")) {
                $thisfile["imgfile"] = 2;
            } else {
                $thisfile["imgfile"] = 0;
            }

            if (!file_exists($myfile)) {
                $thisfile["type"] = "none";
            }
            array_push($files, $thisfile);
        }

        if (!empty($files)) {
            return $files;
        } else {
            return false;
        }
    }
}

?>