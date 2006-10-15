<?php
/**
 * MyBB 1.2
 * Copyright � 2006 MyBB Group, All Rights Reserved
 *
 * Website: http://www.mybboard.com
 * License: http://www.mybboard.com/eula.html
 *
 * $Id$
 */

define("IN_MYBB", 1);

$templatelist = "showthread,postbit,postbit_author_user,postbit_author_guest,showthread_newthread,showthread_newreply,showthread_newreply_closed,postbit_sig,showthread_newpoll,postbit_avatar,postbit_profile,postbit_find,postbit_pm,postbit_www,postbit_email,postbit_edit,postbit_quote,postbit_report,postbit_signature, postbit_online,postbit_offline,postbit_away,showthread_ratingdisplay,showthread_ratethread,showthread_moderationoptions";
$templatelist .= ",multipage_prevpage,multipage_nextpage,multipage_page_current,multipage_page,multipage_start,multipage_end,multipage";
$templatelist .= ",postbit_editedby,showthread_similarthreads,showthread_similarthreads_bit,postbit_iplogged_show,postbit_iplogged_hiden,showthread_quickreply";
$templatelist .= ",forumjump_advanced,forumjump_special,forumjump_bit,showthread_multipage,postbit_reputation,postbit_quickdelete,postbit_attachments,thumbnails_thumbnail,postbit_attachments_attachment,postbit_attachments_thumbnails,postbit_attachments_images_image,postbit_attachments_images,postbit_posturl";
$templatelist .= ",postbit_inlinecheck,showthread_inlinemoderation,postbit_attachments_thumbnails_thumbnail,postbit_quickquote,postbit_qqmessage,postbit_seperator,postbit_groupimage,postbit_multiquote";

require_once "./global.php";
require_once MYBB_ROOT."inc/functions_post.php";
require_once MYBB_ROOT."inc/class_parser.php";
$parser = new postParser;

// Load global language phrases
$lang->load("showthread");

// If there is no tid but a pid, trick the system into thinking there was a tid anyway.
if($mybb->input['pid'] && !$mybb->input['tid'])
{
	$options = array(
		"limit" => 1
	);
	$query = $db->simple_select("posts", "tid", "pid=".$mybb->input['pid'], $options);
	$post = $db->fetch_array($query);
	$mybb->input['tid'] = $post['tid'];
}

// Get the thread details from the database.
$options = array(
	"limit" => 1
);
$query = $db->simple_select("threads", "*", "tid='".$mybb->input['tid']."' AND closed NOT LIKE 'moved|%'");
$thread = $db->fetch_array($query);
$thread['subject'] = htmlspecialchars_uni($parser->parse_badwords($thread['subject']));
$tid = $thread['tid'];
$fid = $thread['fid'];

// Is the currently logged in user a moderator of this forum?
if(is_moderator($fid) == "yes")
{
	$ismod = true;
}
else
{
	$ismod = false;
}

// Make sure we are looking at a real thread here.
if(!$thread['tid'] || ($thread['visible'] == 0 && $ismod == false) || ($thread['visible'] > 1 && $ismod == true))
{
	error($lang->error_invalidthread);
}

$archive_url = build_archive_link("thread", $tid);

// Build the navigation.
build_forum_breadcrumb($fid);
add_breadcrumb($thread['subject'], "showthread.php?tid=$tid");

// Does the thread belong to a valid forum?
$forum = get_forum($fid);
if(!$forum || $forum['type'] != "f")
{
	error($lang->error_invalidforum);
}

// Does the user have permission to view this thread?
$forumpermissions = forum_permissions($forum['fid']);

if($forumpermissions['canview'] != "yes" || $forumpermissions['canviewthreads'] != "yes")
{
	error_no_permission();
}

// Check that this forum is not password protected.
check_forum_password($forum['fid'], $forum['password']);

// If there is no specific action, we must be looking at the thread.
if(!$mybb->input['action'])
{
	$mybb->input['action'] = "thread";
}

// Jump to the last post.
if($mybb->input['action'] == "lastpost")
{
	if(strstr($thread['closed'], "moved|"))
	{
		$query = $db->query("
			SELECT p.pid
			FROM ".TABLE_PREFIX."posts p, ".TABLE_PREFIX."threads t
			WHERE t.fid='".$thread['fid']."' AND t.closed NOT LIKE 'moved|%' AND p.tid=t.tid
			ORDER BY p.dateline DESC
			LIMIT 0, 1
		");
		$pid = $db->fetch_field($query, "pid");
	}
	else
	{
		$options = array(
			'order_by' => 'dateline',
			'order_dir' => 'desc',
			'limit_start' => 0,
			'limit' => 1
		);
		$query = $db->simple_select('posts', 'pid', "tid={$tid}", $options);
		$pid = $db->fetch_field($query, "pid");
	}
	header("Location:showthread.php?tid={$tid}&pid={$pid}#pid{$pid}");
	exit;
}

// Jump to the next newest posts.
if($mybb->input['action'] == "nextnewest")
{
	$options = array(
		"limit_start" => 0,
		"limit" => 1,
		"order_by" => "lastpost"
	);
	$query = $db->simple_select('threads', '*', "fid={$thread['fid']} AND lastpost > {$thread['lastpost']} AND visible=1 AND closed NOT LIKE 'moved|%'", $options);
	$nextthread = $db->fetch_array($query);

	// Are there actually next newest posts?
	if(!$nextthread['tid'])
	{
		error($lang->error_nonextnewest);
	}
	$options = array(
		"limit_start" => 0,
		"limit" => 1,
		"order_by" => "dateline",
		"order_dir" => "desc"
	);
	$query = $db->simple_select('posts', 'pid', "tid={$nextthread['tid']}");

	// Redirect to the proper page.
	$pid = $db->fetch_field($query, "pid");
	header("Location:showthread.php?tid={$nextthread['tid']}&pid={$pid}#pid{$pid}");
}

// Jump to the next oldest posts.
if($mybb->input['action'] == "nextoldest")
{
	$options = array(
		"limit" => 1,
		"limit_start" => 0,
		"order_by" => "lastpost",
		"order_dir" => "desc"
	);
	$query = $db->simple_select("threads", "*", "fid=".$thread['fid']." AND lastpost < ".$thread['lastpost']." AND visible=1 AND closed NOT LIKE 'moved|%'", $options);
	$nextthread = $db->fetch_array($query);

	// Are there actually next oldest posts?
	if(!$nextthread['tid'])
	{
		error($lang->error_nonextoldest);
	}
	$options = array(
		"limit_start" => 0,
		"limit" => 1,
		"order_by" => "dateline",
		"order_dir" => "desc"
	);
	$query = $db->simple_select("posts", "pid", "tid=".$nextthread['tid']);

	// Redirect to the proper page.
	$pid = $db->fetch_field($query, "pid");
	header("Location:showthread.php?tid={$nextthread['tid']}&pid=$pid#pid$pid");
}

// Jump to the unread posts.
if($mybb->input['action'] == "newpost")
{
	// First, figure out what time the thread or forum were last read
	$query = $db->simple_select("threadsread", "dateline", "uid='{$mybb->user['uid']}' AND tid='{$thread['tid']}'");
	$thread_read = $db->fetch_field($query, "dateline");

	// Get forum read date
	$forumread = my_get_array_cookie("forumread", $fid);

	// If last visit is greater than forum read, change forum read date
	if($mybb->user['lastvisit'] > $forumread)
	{
		$forumread = $mybb->user['lastvisit'];
	}
	if($mybb->settings['threadreadcut'] > 0 && $mybb->user['uid'] && $thread['lastpost'] > $forumread)
	{
		$cutoff = time()-$mybb->settings['threadreadcut']*60*60*24;
		if($thread['lastpost'] > $cutoff)
		{
			if($thread_read)
			{
				$lastread = $thread_read;
			}
			else
			{
				$lastread = 1;
			}
		}
	}
	if(!$lastread)
	{
		$readcookie = $threadread = my_get_array_cookie("threadread", $thread['tid']);
		if($readcookie > $forumread)
		{
			$lastread = $readcookie;
		}
		else
		{
			$lastread = $forumread;
		}
	}
	// Next, find the proper pid to link to.
	$options = array(
		"limit_start" => 0,
		"limit" => 1,
		"order_by" => "dateline",
		"order_dir" => "asc"
	);
	$query = $db->simple_select("posts", "pid", "tid=".$tid." AND dateline > '{$lastread}'");
	$newpost = $db->fetch_array($query);
	if($newpost['pid'])
	{
		header("Location:showthread.php?tid={$tid}&pid={$newpost['pid']}#pid{$newpost['pid']}");
	}
	else
	{
		header("Location:showthread.php?action=lastpost&tid={$tid}");
	}
}

$plugins->run_hooks("showthread_start");

// Show the entire thread (taking into account pagination).
if($mybb->input['action'] == "thread")
{
	if($thread['firstpost'] == 0)
	{
		update_first_post($tid);
	}
	// Does this thread have a poll?
	if($thread['poll'])
	{
		$options = array(
			"limit" => 1
		);
		$query = $db->simple_select("polls", "*", "pid='".$thread['poll']."'");
		$poll = $db->fetch_array($query);
		$poll['timeout'] = $poll['timeout']*60*60*24;
		$expiretime = $poll['dateline'] + $poll['timeout'];
		$now = time();

		// If the poll or the thread is closed or if the poll is expired, show the results.
		if($poll['closed'] == "yes" || $thread['closed'] == "yes" || ($expiretime < $now && $poll['timeout'] > 0))
		{
			$showresults = 1;
		}

		// If the user is not a guest, check if he already voted.
		if($mybb->user['uid'] != 0)
		{
			$query = $db->simple_select("pollvotes", "*", "uid='".$mybb->user['uid']."' AND pid='".$poll['pid']."'");
			while($votecheck = $db->fetch_array($query))
			{
				$alreadyvoted = 1;
				$votedfor[$votecheck['voteoption']] = 1;
			}
		}
		else
		{
			if($_COOKIE['pollvotes'][$poll['pid']])
			{
				$alreadyvoted = 1;
			}
		}
		$optionsarray = explode("||~|~||", $poll['options']);
		$votesarray = explode("||~|~||", $poll['votes']);
		$poll['question'] = htmlspecialchars_uni($poll['question']);
		$polloptions = '';

		for($i = 1; $i <= $poll['numoptions']; ++$i)
		{
			$poll['totvotes'] = $poll['totvotes'] + $votesarray[$i-1];
		}

		// Loop through the poll options.
		for($i = 1; $i <= $poll['numoptions']; ++$i)
		{
			// Set up the parser options.
			$parser_options = array(
				"allow_html" => $forum['allowhtml'],
				"allow_mycode" => $forum['allowmycode'],
				"allow_smilies" => $forum['allowsmilies'],
				"allow_imgcode" => $forum['allowimgcode']
			);

			$option = $parser->parse_message($optionsarray[$i-1], $parser_options);
			$votes = $votesarray[$i-1];
			$number = $i;

			// Mark the option the user voted for.
			if($votedfor[$number])
			{
				$optionbg = "trow2";
				$votestar = "*";
			}
			else
			{
				$optionbg = "trow1";
				$votestar = "";
			}

			// If the user already voted or if the results need to be shown, do so; else show voting screen.
			if($alreadyvoted || $showresults)
			{
				if(intval($votes) == "0")
				{
					$percent = "0";
				}
				else
				{
					$percent = number_format($votes / $poll['totvotes'] * 100, 2);
				}
				$imagewidth = round(($percent/3) * 5);
				eval("\$polloptions .= \"".$templates->get("showthread_poll_resultbit")."\";");
			}
			else
			{
				if($poll['multiple'] == "yes")
				{
					eval("\$polloptions .= \"".$templates->get("showthread_poll_option_multiple")."\";");
				}
				else
				{
					eval("\$polloptions .= \"".$templates->get("showthread_poll_option")."\";");
				}
			}
		}

		// If there are any votes at all, all votes together will be 100%; if there are no votes, all votes together will be 0%.
		if($poll['totvotes'])
		{
			$totpercent = "100%";
		}
		else
		{
			$totpercent = "0%";
		}

		// Check if user is allowed to edit posts; if so, show "edit poll" link.
		if(is_moderator($fid, 'caneditposts') != 'yes')
		{
			$edit_poll = '';
		}
		else
		{
			$edit_poll = "| <a href=\"polls.php?action=editpoll&amp;pid={$poll['pid']}\">{$lang->edit_poll}</a>";
		}

		// Decide what poll status to show depending on the status of the poll and whether or not the user voted already.
		if($alreadyvoted || $showresults)
		{
			if($alreadyvoted)
			{
				$pollstatus = $lang->already_voted;
			}
			else
			{
				$pollstatus = $lang->poll_closed;
			}
			$lang->total_votes = sprintf($lang->total_votes, $poll['numvotes']);
			eval("\$pollbox = \"".$templates->get("showthread_poll_results")."\";");
			$plugins->run_hooks("showthread_poll_results");
		}
		else
		{
			$publicnote = '&nbsp;';
			if($poll['public'] == "yes")
			{
				$publicnote = $lang->public_note;
			}
			eval("\$pollbox = \"".$templates->get("showthread_poll")."\";");
			$plugins->run_hooks("showthread_poll");
		}

	}
	else
	{
		$pollbox = "";
	}

	// Create the forum jump dropdown box.
	$forumjump = build_forum_jump("", $fid, 1);

	// Mark this thread read for the currently logged in user.
	if($mybb->settings['threadreadcut'] && ($mybb->user['uid'] != 0))
	{
		// For registered users, store the information in the database.
		$db->shutdown_query("
			REPLACE INTO ".TABLE_PREFIX."threadsread
			SET tid='$tid', uid='".$mybb->user['uid']."', dateline='".time()."'
		");
	}
	else
	{
		// For guests, store the information in a cookie.
		my_set_array_cookie("threadread", $tid, time());
	}

	// If the forum is not open, show closed newreply button unless the user is a moderator of this forum.
	if($forum['open'] != "no")
	{
		eval("\$newthread = \"".$templates->get("showthread_newthread")."\";");

		// Show the appropriate reply button if this thread is open or closed
		if($thread['closed'] == "yes")
		{
			eval("\$newreply = \"".$templates->get("showthread_newreply_closed")."\";");
		}
		else
		{
			eval("\$newreply = \"".$templates->get("showthread_newreply")."\";");
		}
	}

	// Create the admin tools dropdown box.
	if($ismod == true)
	{
		if($pollbox)
		{
			$adminpolloptions = "<option value=\"deletepoll\">".$lang->delete_poll."</option>";
		}
		if($thread['visible'] != 1)
		{
			$approveunapprovethread = "<option value=\"approvethread\">".$lang->approve_thread."</option>";
		}
		else
		{
			$approveunapprovethread = "<option value=\"unapprovethread\">".$lang->unapprove_thread."</option>";
		}
		if($thread['closed'] == "yes")
		{
			$closelinkch = "checked";
		}
		if($thread['sticky'])
		{
			$stickch = "checked";
		}
		$closeoption = "<br /><label><input type=\"checkbox\" class=\"checkbox\" name=\"modoptions[closethread]\" value=\"yes\" $closelinkch />&nbsp;<strong>".$lang->close_thread."</strong></label>";
		$closeoption .= "<br /><label><input type=\"checkbox\" class=\"checkbox\" name=\"modoptions[stickthread]\" value=\"yes\" $stickch />&nbsp;<strong>".$lang->stick_thread."</strong></label>";
		$inlinecount = "0";
		$inlinecookie = "inlinemod_thread".$tid;
		$plugins->run_hooks("showthread_ismod");
	}
	else
	{
		$adminoptions = "&nbsp;";
		$inlinemod = "";
	}

	// Decide whether or not to include signatures.
	if($forumpermissions['canpostreplys'] != "no" && ($thread['closed'] != "yes" || is_moderator($fid) == "yes") && $mybb->settings['quickreply'] != "off" && $mybb->user['showquickreply'] != "no" && $forum['open'] != "no")
	{
		if($mybb->user['signature'])
		{
			$postoptionschecked['signature'] = "checked";
		}
		if($mybb->user['emailnotify'] == "yes")
		{
			$postoptionschecked['emailnotify'] = "checked";
		}
	    mt_srand ((double) microtime() * 1000000);
	    $posthash = md5($mybb->user['uid'].mt_rand());
		eval("\$quickreply = \"".$templates->get("showthread_quickreply")."\";");
	}
	else
	{
		$quickreply = "";
	}

	// Increment the thread view.
	$db->shutdown_query("UPDATE ".TABLE_PREFIX."threads SET views=views+1 WHERE tid='$tid'");
	++$thread['views'];

	// Work out the thread rating for this thread.
	if($forum['allowtratings'] != "no" && $thread['numratings'] > 0)
	{
		$thread['averagerating'] = round(($thread['totalratings']/$thread['numratings']), 2);
		$rateimg = intval(round($thread['averagerating']));
		$thread['rating'] = $rateimg."stars.gif";
		$thread['numratings'] = intval($thread['numratings']);
		$ratingav = sprintf($lang->rating_average, $thread['numratings'], $thread['averagerating']);
		eval("\$rating = \"".$templates->get("showthread_ratingdisplay")."\";");
	}
	else
	{
		$rating = "";
	}
	if($forum['allowtratings'] == "yes" && $forumpermissions['canratethreads'] == "yes")
	{
		eval("\$ratethread = \"".$templates->get("showthread_ratethread")."\";");
	}
	// Work out if we are showing unapproved posts as well (if the user is a moderator etc.)
	if($ismod)
	{
		$visible = "AND (p.visible='0' OR p.visible='1')";
	}
	else
	{
		$visible = "AND p.visible='1'";
	}

	// Threaded or lineair display?
	if($mybb->input['mode'] == "threaded")
	{
		$isfirst = 1;

		// Are we linked to a specific pid?
		if($mybb->input['pid'])
		{
			$where = "AND p.pid='".$mybb->input['pid']."'";
		}
		else
		{
			$where = " ORDER BY dateline ASC LIMIT 0, 1";
		}
		$query = $db->query("
			SELECT u.*, u.username AS userusername, p.*, f.*, eu.username AS editusername
			FROM ".TABLE_PREFIX."posts p
			LEFT JOIN ".TABLE_PREFIX."users u ON (u.uid=p.uid)
			LEFT JOIN ".TABLE_PREFIX."userfields f ON (f.ufid=u.uid)
			LEFT JOIN ".TABLE_PREFIX."users eu ON (eu.uid=p.edituid)
			WHERE p.tid='$tid' $visible $where
		");
		$showpost = $db->fetch_array($query);

		// Choose what pid to display.
		if(!$mybb->input['pid'])
		{
			$mybb->input['pid'] = $showpost['pid'];
		}

		// Is there actually a pid to display?
		if(!$showpost['pid'])
		{
			error($lang->error_invalidpost);
		}

		// Get the attachments for this post.
		$query = $db->simple_select("attachments", "*", "pid=".$mybb->input['pid']);
		while($attachment = $db->fetch_array($query))
		{
			$attachcache[$attachment['pid']][$attachment['aid']] = $attachment;
		}

		// Build the threaded post display tree.
		$query = $db->query("
            SELECT u.username, u.username AS userusername, p.pid, p.replyto, p.subject, p.dateline
            FROM ".TABLE_PREFIX."posts p
            LEFT JOIN ".TABLE_PREFIX."users u ON (u.uid=p.uid)
            WHERE p.tid='$tid' 
            $visible
            ORDER BY p.dateline
        ");
        while($post = $db->fetch_array($query))
        {
            if(!$postsdone[$post['pid']])
            {
                if($post['pid'] == $mybb->input['pid'] || ($isfirst && !$mybb->input['pid']))
                {
                    $isfirst = 0;
                }
                $tree[$post['replyto']][$post['pid']] = $post;
                $postsdone[$post['pid']] = 1;
            }
        }
		$threadedbits = buildtree();
		$posts = build_postbit($showpost);
		eval("\$threadexbox = \"".$templates->get("showthread_threadedbox")."\";");
		$plugins->run_hooks("showthread_threaded");
	}
	else // Linear display
	{
		// Figure out if we need to display multiple pages.
		$perpage = $mybb->settings['postsperpage'];
		if($mybb->input['page'] != "last")
		{
			$page = intval($mybb->input['page']);
		}
		if($mybb->input['pid'])
		{
			$query = $db->query("
				SELECT COUNT(p.pid) AS count FROM ".TABLE_PREFIX."posts p
				WHERE p.tid='$tid'
				AND p.pid <= '".$mybb->input['pid']."'
				$visible
			");
			$result = $db->fetch_field($query, "count");
			if(($result % $perpage) == 0)
			{
				$page = $result / $perpage;
			}
			else
			{
				$page = intval($result / $perpage) + 1;
			}
		}
		// Recount replies if user is a moderator to take into account unapproved posts.
		if($ismod)
		{
			$query = $db->simple_select("posts p", "COUNT(*) AS replies", "p.tid='$tid' $visible");
			$thread['replies'] = $db->fetch_field($query, 'replies')-1;
		}
		$postcount = intval($thread['replies'])+1;
		$pages = $postcount / $perpage;
		$pages = ceil($pages);

		if($mybb->input['page'] == "last")
		{
			$page = $pages;
		}

		if($page > $pages)
		{
			$page = 1;
		}

		if($page)
		{
			$start = ($page-1) * $perpage;
		}
		else
		{
			$start = 0;
			$page = 1;
		}
		$upper = $start+$perpage;

		$multipage = multipage($postcount, $perpage, $page, "showthread.php?tid=$tid");
		if($postcount > $perpage)
		{
			eval("\$threadpages = \"".$templates->get("showthread_multipage")."\";");
		}

		// Lets get the pids of the posts on this page.
		$pids = "";
		$comma = '';
		$query = $db->simple_select("posts p", "p.pid", "p.tid='$tid' $visible", array('order_by' => 'p.dateline', 'limit_start' => $start, 'limit' => $perpage));
		while($getid = $db->fetch_array($query))
		{
			$pids .= "$comma'{$getid['pid']}'";
			$comma = ",";
		}
		if($pids)
		{
			$pids = "pid IN($pids)";
			// Now lets fetch all of the attachments for these posts.
			$query = $db->simple_select("attachments", "*", $pids);
			while($attachment = $db->fetch_array($query))
			{
				$attachcache[$attachment['pid']][$attachment['aid']] = $attachment;
			}
		}
		else
		{
			// If there are no pid's the thread is probably awaiting approval.
			error($lang->error_invalidthread);
		}

		// Get the actual posts from the database here.
		$pfirst = true;
		$posts = '';
		$query = $db->query("
			SELECT u.*, u.username AS userusername, p.*, f.*, eu.username AS editusername
			FROM ".TABLE_PREFIX."posts p
			LEFT JOIN ".TABLE_PREFIX."users u ON (u.uid=p.uid)
			LEFT JOIN ".TABLE_PREFIX."userfields f ON (f.ufid=u.uid)
			LEFT JOIN ".TABLE_PREFIX."users eu ON (eu.uid=p.edituid)
			WHERE $pids
			ORDER BY p.dateline
		");
		while($post = $db->fetch_array($query))
		{
			if($pfirst && $thread['visible'] == 0)
			{
				$post['visible'] = 0;
			}
			$posts .= build_postbit($post);
			$post = '';
			$pfirst = false;
		}
		$plugins->run_hooks("showthread_linear");
	}

	// Show the similar threads table if wanted.
	if($mybb->settings['showsimilarthreads'] != "no")
	{
		$query = $db->query("
			SELECT t.*, t.username AS threadusername, u.username, MATCH (t.subject) AGAINST ('".$db->escape_string($thread['subject'])."') AS relevance
			FROM ".TABLE_PREFIX."threads t
			LEFT JOIN ".TABLE_PREFIX."users u ON (u.uid = t.uid)
			WHERE t.fid='{$thread['fid']}' AND t.tid!='{$thread['tid']}' AND t.visible='1' AND t.closed NOT LIKE 'moved|%' AND MATCH (t.subject) AGAINST ('".$db->escape_string($thread['subject'])."') >= '{$mybb->settings['similarityrating']}'
			ORDER BY t.lastpost DESC
			LIMIT 0, {$mybb->settings['similarlimit']}
		");
		$count = 0;
		$similarthreadbits = '';
		$icon_cache = $cache->read("posticons");
		while($similar_thread = $db->fetch_array($query))
		{
			++$count;
			$trow = alt_trow();
			if($similar_thread['icon'] > 0 && $icon_cache[$similar_thread['icon']])
			{
				$icon = $icon_cache[$similar_thread['icon']];
				$icon = "<img src=\"{$icon['path']}\" alt=\"{$icon['name']}\" />";
			}
			else
			{
				$icon = "&nbsp;";
			}				
			if(!$similar_thread['username'])
			{
				$similar_thread['username'] = $similar_thread['threadusername'];
				$similar_thread['profilelink'] = $similar_thread['threadusername'];
			}
			else
			{
				$similar_thread['profilelink'] = build_profile_link($similar_thread['username'], $similar_thread['uid']);
			}
			$similar_thread['subject'] = $parser->parse_badwords($similar_thread['subject']);
			$similar_thread['subject'] = htmlspecialchars_uni($similar_thread['subject']);

			$lastpostdate = my_date($mybb->settings['dateformat'], $similar_thread['lastpost']);
			$lastposttime = my_date($mybb->settings['timeformat'], $similar_thread['lastpost']);
			$lastposter = $similar_thread['lastposter'];
			$lastposteruid = $similar_thread['lastposteruid'];

			// Don't link to guest's profiles (they have no profile).
			if($lastposteruid == 0)
			{
				$lastposterlink = $lastposter;
			}
			else
			{
				$lastposterlink = build_profile_link($lastposter, $lastposteruid);
			}
			$similar_thread['replies'] = my_number_format($similar_thread['replies']);
			$similar_thread['views'] = my_number_format($similar_thread['views']);
			eval("\$similarthreadbits .= \"".$templates->get("showthread_similarthreads_bit")."\";");
		}
		if($count)
		{
			eval("\$similarthreads = \"".$templates->get("showthread_similarthreads")."\";");
		}
	}

	// If the user is a moderator, show the moderation tools.
	if($ismod)
	{
		$customthreadtools = $customposttools = '';
		$query = $db->simple_select("modtools", "tid, name, type", "CONCAT(',',forums,',') LIKE '%,$fid,%' OR CONCAT(',',forums,',') LIKE '%,-1,%'");
		while($tool = $db->fetch_array($query))
		{
			if($tool['type'] == 'p')
			{
				eval("\$customposttools .= \"".$templates->get("showthread_inlinemoderation_custom_tool")."\";");
			}
			else
			{
				eval("\$customthreadtools .= \"".$templates->get("showthread_moderationoptions_custom_tool")."\";");
			}
		}
		// Build inline moderation dropdown
		if(!empty($customposttools))
		{
			eval("\$customposttools = \"".$templates->get("showthread_inlinemoderation_custom")."\";");
		}
		eval("\$inlinemod = \"".$templates->get("showthread_inlinemoderation")."\";");

		// Build thread moderation dropdown
		if(!empty($customthreadtools))
		{
			eval("\$customthreadtools = \"".$templates->get("showthread_moderationoptions_custom")."\";");
		}
		eval("\$moderationoptions = \"".$templates->get("showthread_moderationoptions")."\";");
	}
	$lang->newthread_in = sprintf($lang->newthread_in, $forum['name']);
	eval("\$showthread = \"".$templates->get("showthread")."\";");
	$plugins->run_hooks("showthread_end");
	output_page($showthread);
}

/**
 * Build a navigation tree for threaded display.
 *
 * @param unknown_type $replyto
 * @param unknown_type $indent
 * @return unknown
 */
function buildtree($replyto="0", $indent="0")
{
	global $tree, $mybb, $theme, $mybb, $pid, $tid, $templates, $parser;
	if($indent)
	{
		$indentsize = 13 * $indent;
	}
	else
	{
		$indentsize = 0;
	}
	++$indent;
	if(is_array($tree[$replyto]))
	{
		foreach($tree[$replyto] as $key => $post)
		{
			$postdate = my_date($mybb->settings['dateformat'], $post['dateline']);
			$posttime = my_date($mybb->settings['timeformat'], $post['dateline']);
			$post['subject'] = htmlspecialchars_uni($parser->parse_badwords($post['subject']));
			if(!$post['subject'])
			{
				$post['subject'] = "[".$lang->no_subject."]";
			}
			if($post['userusername'])
			{
				$post['profilelink'] = build_profile_link($post['userusername'], $post['uid']);
			}
			else
			{
				$post['profilelink'] = $post['username'];
			}
			if($mybb->input['pid'] == $post['pid'])
			{
				eval("\$posts .= \"".$templates->get("showthread_threaded_bitactive")."\";");
			}
			else
			{
				eval("\$posts .= \"".$templates->get("showthread_threaded_bit")."\";");
			}
			if($tree[$post['pid']])
			{
				$posts .= buildtree($post['pid'], $indent);
			}
		}
		--$indent;
	}
	return $posts;
}

?>
