<?php
/**
 * MyBB 1.0
 * Copyright � 2005 MyBulletinBoard Group, All Rights Reserved
 *
 * Website: http://www.mybboard.com
 * License: http://www.mybboard.com/eula.html
 *
 * $Id$
 */

$cssselectors = array("body" => "body",
					  "container" => "#container",
					  "content" => "#content",
					  "menu" => ".menu ul",
					  "panel" => "#panel",
					  "table" => "table",
					  "tborder" => ".tborder",
					  "thead" => ".thead",
					  "tcat" => ".tcat",
					  "trow1" => ".trow1",
					  "trow2" => ".trow2",
					  "trow_shaded" => ".trow_shaded",
					  "trow_sep" => ".trow_sep",
					  "tfoot" => ".tfoot",
					  "bottommenu" => ".bottommenu",
					  "navigation" => ".navigation",
					  "navigation_active" => ".navigation .active",
					  "smalltext" => ".smalltext",
					  "largetext" => ".largetext",
					  "area_input_select_object" => "textarea, input, select, object",
					  "toolbar_normal" => ".toolbar_normal",
					  "toolbar_hover" => ".toolbar_hover",
					  "toolbar_mousedown" => ".toolbar_mousedown",
					  "toolbar_clicked" => ".toolbar_clicked",
					  
					  // Link selectors
					  "a_link" => "a:link",
					  "a_visited" => "a:visited",
					  "a_hover" => "a:hover");

/*$cssselectors = array("body", "#container", "#content", ".menu ul", "#panel", "table", ".tborder", ".thead", ".tcat", 
					  ".trow1", ".trow2", ".navigation", ".navigation .active", ".smalltext", ".largetext", "textarea, input, select, object",
					  ".toolbar_normal", ".toolbar_hover", ".toolbar_mousedown", ".toolbar_clicked");
*/
$themebitlist = array("templateset", "imgdir", "logo", "tablespace", "tablewidth", "borderwidth", "extracss");


function cpheader($title="", $donav=1, $onload="")
{
	global $settings, $style, $lang;
	if(!$title)
	{
		$title = $lang->admin_center;
	}
	if($lang->settings['rtl'] == 1)
	{
		echo "<html dir=\"rtl\">\n";
	}
	else
	{
		echo "<html>\n";
	}
	echo "<head>\n";
	echo "<title>$title</title>\n";
	echo "<link rel=\"stylesheet\" href=\"$style\">\n";
	echo "<script type=\"text/javascript\">\n";
	echo "function hopto(url) {\n";
	echo "window.location = url;\n";
	echo "}\n";
	echo "</script>";
	echo "</head>\n";
	if($onload)
	{
		echo "<body onload=\"$onload\">\n";
	}
	else
	{
		echo "<body>\n";
	}
	if($donav != 0)
	{
		echo buildacpnav();
	}
}
function makehoptolinks($links)
{
	echo "<table cellpadding=\"0\" cellspacing=\"0\" border=\"0\" align=\"center\" width=\"90%\">\n";
	echo "<tr><td class=\"hoptobuttons\">";
	foreach($links as $key => $val)
	{
		echo $val;
	}
	echo "</td></tr>";
	echo "</table>";
}

function startform($script, $name="", $action="")
{
	global $mybb;
	echo "<form action=\"$script\" method=\"post\" name=\"$name\" enctype=\"multipart/form-data\">\n";
	if($action != "")
	{
		makehiddencode("action", $action);
	}
}
function starttable($width="90%", $border=1, $padding=6)
{
	echo "<table cellpadding=\"$border\" cellspacing=\"0\" border=\"0\" align=\"center\" width=\"$width\" class=\"bordercolor\">\n";
	echo "<tr><td>\n";
	echo "<table cellpadding=\"$padding\" cellspacing=\"0\" border=\"0\" width=\"100%\" class=\"tback\">";
}
function tableheader($title, $anchor="", $colspan=2)
{
	global $bgcolor;
	echo "<tr>\n<td class=\"header\" align=\"center\" colspan=\"$colspan\"><a name=\"$anchor\">$title</a></td>\n</tr>\n";
	$bgcolor = "altbg2";
}
function tablesubheader($titles, $anchor="", $colspan=2, $align="center")
{
	global $bgcolor;
	echo "<tr>\n";
	if(!is_array($titles))
	{
		$title[] = $titles;
	}
	else
	{
		$colspan = 1;
		$title = $titles;
	}
	foreach($title as $ttitle)
	{
		if($anchor)
		{
			$ttitle = "<a href=\"$anchor\">$ttitle</a>";
		}
		echo "<td class=\"subheader\" align=\"$align\" colspan=\"$colspan\">$ttitle</td>\n";
		$bgcolor = "altbg2";
	}
	echo "</tr>\n";
}
function makelabelcode($title, $value="", $colspan=1, $width1="40%", $width2="60%")
{
	$bgcolor = getaltbg();
	if($value != "")
	{
		$width1 = " width=\"$width1\"";
		$width2 = " width=\"$width2\"";
	}
	else
	{
		$width1 = $width2 = "";
	}
	echo "<tr>\n<td class=\"$bgcolor\" colspan=\"$colspan\" valign=\"top\"$width1>$title</td>\n";
	if($value != "")
	{
		echo "<td class=\"$bgcolor\" valign=\"top\" $width2>$value</td>\n";
	}
	echo "</tr>\n";
}
function makelinkcode($text, $url, $newwin=0, $class="")
{
	if($newwin)
	{
		$target = "target=\"_blank\"";
	}
	return " <a href=\"$url\" $target><span class=\"$class\">[$text]</span></a>";
}
function makeinputcode($title, $name, $value="", $size="25", $extra="", $maxlength="", $autocomplete=1)
{
	$bgcolor = getaltbg();
	$value = stripslashes($value);
	$value = htmlspecialchars_uni($value);
	if($autocomplete != 1)
	{
		$ac = "autocomplete=\"off\"";
	}
	echo "<tr>\n<td class=\"$bgcolor\" valign=\"top\" width=\"40%\">$title</td>\n<td class=\"$bgcolor\" valign=\"top\" width=\"60%\"><input type=\"text\" class=\"inputbox\" name=\"$name\" value=\"$value\" size=\"$size\" maxlength=\"$maxlength\" $ac>$extra</td>\n</tr>\n";
}
function makeuploadcode($title, $name, $size="25", $extra="")
{
	$bgcolor = getaltbg();
	echo "<tr>\n<td class=\"$bgcolor\" valign=\"top\" width=\"40%\">$title</td>\n<td class=\"$bgcolor\" valign=\"top\" width=\"60%\"><input type=\"file\" class=\"inputbox\" name=\"$name\" size=\"$size\">$extra</td>\n</tr>\n";
}
function makepasswordcode($title, $name, $value="", $size="25", $autocomplete=1)
{
	$bgcolor = getaltbg();
	$value = htmlspecialchars_uni($value);
	if($autocomplete != 1)
	{
		$ac = "autocomplete=\"off\"";
	}
	echo "<tr>\n<td class=\"$bgcolor\" valign=\"top\" width=\"40%\">$title</td>\n<td class=\"$bgcolor\" valign=\"top\" width=\"60%\"><input type=\"password\" class=\"inputbox\" name=\"$name\" value=\"$value\" size=\"$size\" $ac></td>\n</tr>\n";
}
function maketextareacode($title, $name, $value="", $rows="4", $columns="40")
{
	$bgcolor = getaltbg();
	$value = stripslashes($value);
	$value = htmlspecialchars_uni($value);
	echo "<tr>\n<td class=\"$bgcolor\" valign=\"top\" width=\"40%\">$title</td>\n<td class=\"$bgcolor\" valign=\"top\" width=\"60%\"><textarea name=\"$name\" rows=\"$rows\" cols=\"$columns\">$value</textarea></td>\n</tr>\n";
}
function makehiddencode($name, $value="")
{
	$value = stripslashes($value);
	$value = htmlspecialchars_uni($value);
	echo "<input type=\"hidden\" name=\"$name\" value=\"$value\">\n";
}
function makeyesnocode($title, $name, $value="yes")
{
	global $lang;
	$bgcolor = getaltbg();
	if($value == "no")
	{
		$nocheck = "checked=\"checked\"";
	}
	else
	{
		$yescheck = "checked=\"checked\"";
	}
	echo "<tr>\n<td class=\"$bgcolor\" valign=\"top\" width=\"40%\">$title</td>\n<td class=\"$bgcolor\" valign=\"top\" width=\"60%\"><label><input type=\"radio\" name=\"$name\" value=\"yes\" $yescheck />&nbsp;$lang->yes</label> &nbsp;&nbsp;<label><input type=\"radio\" name=\"$name\" value=\"no\" $nocheck />&nbsp;$lang->no</label></td>\n</tr>\n";
}
function makeonoffcode($title, $name, $value="on")
{
	global $lang;
	$bgcolor = getaltbg();
	if($value == "off")
	{
		$offcheck = "checked";
	}
	else
	{
		$oncheck = "checked";
	}
	echo "<tr>\n<td class=\"$bgcolor\" valign=\"top\" width=\"40%\">$title</td>\n<td class=\"$bgcolor\" valign=\"top\" width=\"60%\"><label><input type=\"radio\" name=\"$name\" value=\"on\" $oncheck>&nbsp;$lang->on</label> &nbsp;&nbsp;<label><input type=\"radio\" name=\"$name\" value=\"off\" $offcheck>&nbsp;$lang->off</label></td>\n</tr>\n";
}
function makeselectcode($title, $name, $table, $tableid, $optiondisp, $selected="", $extra="", $blank="", $condition="")
{
	global $db;
	$bgcolor = getaltbg();
	echo "<tr>\n<td class=\"$bgcolor\" valign=\"top\" width=\"40%\">$title</td><td class=\"$bgcolor\" valign=\"top\" width=\"60%\">\n<select name=\"$name\">\n";
	if($order)
	{
		$orderby = "ORDER BY $optiondisp";
	}
	if($condition)
	{
		$condition = "WHERE $condition";
	}
	$query = $db->query("SELECT $tableid, $optiondisp FROM ".TABLE_PREFIX."$table $condition $orderby");
	if($blank && !$selected)
	{
		echo "<option value=\"\" selected> </option>";
	}
	while($item = $db->fetch_array($query))
	{
		if($item[$tableid] == $selected)
		{
			echo "<option value=\"$item[$tableid]\" selected>$item[$optiondisp]</option>\n";
		}
		else
		{
			echo "<option value=\"$item[$tableid]\">$item[$optiondisp]</option>\n";
		}
	}
	if($extra)
	{
		if($selected == "-1")
		{
			echo "<option value=\"-1\" selected>$extra</option>\n";
		} 
		else
		{
			echo "<option value=\"-1\">$extra</option>\n";
		}
	}
	echo "</select>\n</td>\n</tr>\n";
}
function makedateselect($title, $name, $day, $month)
{
	$dname = $name."[day]";
	$mname = $name."[month]";
	$yname = $name."[year]";

	for($i=1;$i<=31;$i++)
	{
		if($day == $i)
		{
			$daylist .= "<option value=\"$i\" selected>$i</option>\n";
		}
		else
		{
			$daylist .= "<option value=\"$i\">$i</option>\n";
		}
	}

	$monthsel[$month] = "selected";
	$monthlist .= "<option value=\"\">------------</option>";
	$monthlist .= "<option value=\"01\" $monthsel[01]>January</option>\n";
	$monthlist .= "<option value=\"02\" $monthsel[02]>February</option>\n";
	$monthlist .= "<option value=\"03\" $monthsel[03]>March</option>\n";
	$monthlist .= "<option value=\"04\" $monthsel[04]>April</option>\n";
	$monthlist .= "<option value=\"05\" $monthsel[05]>May</option>\n";
	$monthlist .= "<option value=\"06\" $monthsel[06]>June</option>\n";
	$monthlist .= "<option value=\"07\" $monthsel[07]>July</option>\n";
	$monthlist .= "<option value=\"08\" $monthsel[08]>August</option>\n";
	$monthlist .= "<option value=\"09\" $monthsel[09]>September</option>\n";
	$monthlist .= "<option value=\"10\" $monthsel[10]>October</option>\n";
	$monthlist .= "<option value=\"11\" $monthsel[11]>November</option>\n";
	$monthlist .= "<option value=\"12\" $monthsel[12]>December</option>\n";
	$dateselect = "<table border=\"0\" cellspacing=\"0\" cellpadding=\"0\"><tr>\n";
	$dateselect .= "<td class=\"$bgcolor\"><b><small>Day</small></b><br>\n<select name=\"$dname\"><option value=\"\">--</option>\n$daylist</select></td>\n";
	$dateselect .= "<td class=\"$bgcolor\"><b><small>Month</small></b><br>\n<select name=\"$mname\">$monthlist</select></td>\n";
	$dateselect .= "<td class=\"$bgcolor\"><b><small>Year</small></b><br>\n<input name=\"$yname\" value=\"$year\" size=\"4\"></td>\n";
	$dateselect .= "</tr></table>";
	echo "<tr>\n<td class=\"$bgcolor\" valign=\"top\" width=\"40%\">$title</td>\n<td class=\"$bgcolor\" valign=\"top\">$dateselect</tr>\n";
}
function makebuttoncode($name, $value, $type="submit")
{
	return "<input type=\"$type\" class=\"submitbutton\" name=\"$name\" value=\"  $value  \">&nbsp;&nbsp;\n";
}

function makecssedit($css, $selector, $name, $description="", $showfonts=1, $showbackground=1, $showlinks=1, $showwidth=0)
{
	global $lang, $tid, $tcache;
	if(!is_array($tcache))
	{
		cache_themes();
	}
	if($css['inherited'] != $tid && $css['inherited'] != 1)
	{
		$inheritid = $css['inherited'];
		$inheritedfrom = $tcache[$inheritid]['name'];
		$highlight = "highlight3";
		$name .= "(".$lang->inherited_from." ".$inheritedfrom.")";
	}
	elseif($css['inherited'] == 1)
	{
	}
	else
	{
		$highlight = "highlight2";
		$name .= " (".$lang->customized_this_style.")";
		$revert = "<input type=\"checkbox\" name=\"revert_css[$selector]\" value=\"1\" id=\"revert_css_$selector\" /> <label for=\"revert_css_$selector\">".$lang->revert_customizations."</label>";
	}
	starttable();
	tableheader($name);
	echo "<tr>\n<td class=\"subheader\" align=\"center\">".$lang->main_css_attributes."</td><td class=\"subheader\" align=\"center\">".$lang->extra_css_attributes."</td>\n</tr>\n";
	echo "<tr>\n";
	echo "<td class=\"altbg1\" width=\"50%\" valign=\"top\">\n";
	echo "<table width=\"100%\">\n";
	if($showbackground)
	{
		echo "<tr>\n<td>".$lang->background."</td>\n<td><input type=\"text\" name=\"css[$selector][background]\" value=\"".$css['background']."\" size=\"25\" class=\"$highlight\"/></td>\n</tr>\n";
	}
	if($showwidth)
	{
		echo "<tr>\n<td>".$lang->width."</td>\n<td><input type=\"text\" name=\"css[$selector][width]\" value=\"".$css['width']."\" size=\"25\" class=\"$highlight\" /></td>\n</tr>\n";
	}
	if($showfonts)
	{
		echo "<tr>\n<td>".$lang->font_color."</td>\n<td><input type=\"text\" name=\"css[$selector][color]\" value=\"".$css['color']."\" size=\"25\"  class=\"$highlight\" /></td>\n</tr>\n";
		echo "<tr>\n<td>".$lang->font_family."</td>\n<td><input type=\"text\" name=\"css[$selector][font-family]\" value=\"".$css['font-family']."\" size=\"25\"  class=\"$highlight\" /></td>\n</tr>\n";
		echo "<tr>\n<td>".$lang->font_size."</td>\n<td><input type=\"text\" name=\"css[$selector][font-size]\" value=\"".$css['font-size']."\" size=\"25\"  class=\"$highlight\" /></td>\n</tr>\n";
		echo "<tr>\n<td>".$lang->font_style."</td>\n<td><input type=\"text\" name=\"css[$selector][font-style]\" value=\"".$css['font-style']."\" size=\"25\"  class=\"$highlight\" /></td>\n</tr>\n";
		echo "<tr>\n<td>".$lang->font_weight."</td>\n<td><input type=\"text\" name=\"css[$selector][font-weight]\" value=\"".$css['font-weight']."\" size=\"25\"  class=\"$highlight\" /></td>\n</tr>\n";
	}
	echo "</table>\n";
	echo "</td>\n";
	echo "<td class=\"altbg1\" width=\"50%\ valign=\"top\">\n";
	echo "<textarea style=\"width: 98%; padding: 4px;\" ";
	if($showfonts)
	{
		echo "rows=\"9\"";
	}
	else
	{
		echo "rows=\"4\"";
	}
	echo "name=\"css[$selector][extra]\" class=\"$highlight\">".htmlspecialchars_uni($css['extra'])."</textarea>\n";
	echo "</td>\n";
	echo "</tr>\n";
	if($showlinks == 1)
	{
		echo "<tr>\n";
		echo "<td colspan=\"2\" class=\"subheader\" align=\"center\">".$lang->link_css_attributes."</td>\n";
		echo "</tr>\n";
		echo "<tr>\n";
		echo "<td class=\"altbg2\" colspan=\"2\">\n";
		echo "<table width=\"100%\">\n";
		echo "<tr>\n";
		echo "<td>\n";
		makecsslinkedit($selector, "a_link", $lang->normal_links, $css['a_link'], $highlight);
		echo "</td>\n";
		echo "<td>\n";
		makecsslinkedit($selector, "a_visited", $lang->visited_links, $css['a_visited'], $highlight);
		echo "</td>\n";
		echo "<td>\n";
		makecsslinkedit($selector, "a_hover", $lang->hovered_links, $css['a_hover'], $highlight);
		echo "</td>\n";
		echo "</tr>\n</table>\n";
		echo "</td>\n";
		echo "</tr>\n";
	}
	$submit = makebuttoncode($lang->save_changes, $lang->save_changes, "submit");
	tablesubheader("<div style=\"float: right;\">$submit</div><div>$revert</div>", "", 2, "left");
	endtable();
}

function makecsslinkedit($selector, $type, $name, $css, $highlight=1)
{
	global $lang;
	echo "<fieldset>\n";
	echo "<legend>".$name."</legend>\n";
	echo "<table width=\"100%\">\n";
	echo "<tr><td>".$lang->background."</td><td><input type=\"text\" name=\"css[$selector][$type][background]\" value=\"".$css['background']."\" size=\"8\" class=\"$highlight\" /></td></tr>\n";
	echo "<tr><td>".$lang->font_color."</td><td><input type=\"text\" name=\"css[$selector][$type][color]\" value=\"".$css['color']."\" size=\"8\" class=\"$highlight\" /></td></tr>\n";
	echo "<tr><td>".$lang->text_decoration."</td><td><input type=\"text\" name=\"css[$selector][$type][text-decoration]\" value=\"".$css['text-decoration']."\" size=\"8\" class=\"$highlight\" /></td></tr>\n";
	echo "</table>\n";
	echo "</fieldset>\n";
}

function makecsstoolbaredit($css)
{
	global $lang;
	starttable();
	tableheader($lang->mycode_toolbar);
	echo "<tr>\n<td class=\"subheader\" align=\"center\">".$lang->toolbar_normal."</td><td class=\"subheader\" align=\"center\">".$lang->toolbar_hovered."</td>\n</tr>\n";
	echo "<tr>\n";
	echo "<td class=\"altbg1\" width=\"50%\">\n";
	echo "<table width=\"100%\">\n";
	echo "<tr>\n<td>".$lang->background."</td>\n<td><input type=\"text\" name=\"css[toolbar_normal][background]\" value=\"".$css['toolbar_normal']['background']."\" size=\"25\" class=\"inputbox\"/></td>\n</tr>\n";
	echo "<tr>\n<td>".$lang->border."</td>\n<td><input type=\"text\" name=\"css[toolbar_normal][border]\" value=\"".$css['toolbar_normal']['border']."\" size=\"25\" class=\"inputbox\"/></td>\n</tr>\n";
	echo "</table>\n";
	echo "</td>\n";
	echo "<td class=\"altbg1\" width=\"50%\">\n";
	echo "<table width=\"100%\">\n";
	echo "<tr>\n<td>".$lang->background."</td>\n<td><input type=\"text\" name=\"css[toolbar_hover][background]\" value=\"".$css['toolbar_hover']['background']."\" size=\"25\" class=\"inputbox\"/></td>\n</tr>\n";
	echo "<tr>\n<td>".$lang->border."</td>\n<td><input type=\"text\" name=\"css[toolbar_hover][border]\" value=\"".$css['toolbar_hover']['border']."\" size=\"25\" class=\"inputbox\"/></td>\n</tr>\n";
	echo "</table>\n";
	echo "</td>\n";
	echo "</tr>\n";
	echo "<tr>\n<td class=\"subheader\" align=\"center\"".$lang->toolbar_mousedown."</td><td class=\"subheader\" align=\"center\">".$lang->toolbar_clicked."</td>\n</tr>\n";
	echo "<tr>\n";
	echo "<td class=\"altbg1\" width=\"50%\">\n";
	echo "<table width=\"100%\">\n";
	echo "<tr>\n<td>".$lang->background."</td>\n<td><input type=\"text\" name=\"css[toolbar_mousedown][background]\" value=\"".$css['toolbar_mousedown']['background']."\" size=\"25\" class=\"inputbox\"/></td>\n</tr>\n";
	echo "<tr>\n<td>".$lang->border."</td>\n<td><input type=\"text\" name=\"css[toolbar_mousedown][border]\" value=\"".$css['toolbar_mousedown']['border']."\" size=\"25\" class=\"inputbox\"/></td>\n</tr>\n";
	echo "</table>\n";
	echo "</td>\n";
	echo "<td class=\"altbg1\" width=\"50%\">\n";
	echo "<table width=\"100%\">\n";
	echo "<tr>\n<td>".$lang->background."</td>\n<td><input type=\"text\" name=\"css[toolbar_clicked][background]\" value=\"".$css['toolbar_clicked']['background']."\" size=\"25\" class=\"inputbox\"/></td>\n</tr>\n";
	echo "<tr>\n<td>".$lang->border."</td>\n<td><input type=\"text\" name=\"css[toolbar_clicked][border]\" value=\"".$css['toolbar_clicked']['border']."\" size=\"25\" class=\"inputbox\"/></td>\n</tr>\n";
	echo "</table>\n";
	echo "</td>\n";
	echo "</tr>\n";
	endtable();
}

function endtable()
{
	echo "</table>\n";
	echo "</td></tr></table>\n";
	echo "<br />\n";
}
function endform($submit="", $reset="")
{
	if($submit || $reset)
	{
		echo "<div align=\"center\"><div class=\"formbuttons\">\n";
	}
	if($submit)
	{
		echo makebuttoncode($submit, $submit, "submit");
	}
	if($reset)
	{
		echo makebuttoncode($reset, $reset, "reset");
	}
	if($submit || $reset)
	{
		echo "</div></div>";
	}
	echo "</form>\n";
}
function cperror($message="")
{
	global $lang;
	if(!$message) { $message = $lang->error_msg; }
	cpheader("", 0);
	starttable("65%");
	tableheader($lang->cp_error_header);
	makelabelcode($message);
	endtable();
	cpfooter();
	exit;
}
function cpmessage($message="")
{
	global $lang;
	if(!$message) { $message = $lang->cp_message; }
	cpheader("", 0);
	starttable("65%");
	tableheader($lang->cp_message_header);
	makelabelcode($message);
	endtable();
	cpfooter();
	exit;
}
function cpredirect($url, $message="")
{
	global $lang;
	if(!$message) { $message = $lang->redirect_msg; }
	cpheader("", 0);
	starttable("65%");
	tableheader($lang->cp_message_header);
	makelabelcode($message);
	echo "<script type=\"text/javascript\">\n";
	echo "timeout = 10;\n";
	echo "function redirect() {\n";
	echo "	timerID = setTimeout(\"redirect();\", 100);\n";
	echo "	if(timeout > 0) {\n";
	echo "		timeout -= 1;\n";
	echo "	} else {\n";
	echo "		clearTimeout(timerID);\n";
	echo "		window.location = \"$url\";\n";
	echo "	}\n";
	echo "}\n";
	echo "redirect();\n";
	echo "</script>	\n";
	endtable();
	cpfooter();
}

function cpfooter()
{
	global $mybboard, $db, $maintimer;
	global $lang;
	echo "<center><br><br>\n";
	$totaltime = $maintimer->stop();
	$lang->footer_stats = sprintf($lang->footer_stats, $totaltime, $db->query_count);
	echo "<font size=\"1\" face=\"Verdana,Arial,Helvetica\">".$lang->footer_powered_by." <b>myBB $mybboard[internalver]</b><br>".$lang->footer_copyright." &copy; 2004 MyBulletinBoard Group<br />".$lang->footer_stats."</font></center>\n";
	echo "</body>\n";
	echo "</html>";
}

function getaltbg()
{
	global $bgcolor;
	if($bgcolor == "altbg1")
	{
		$bgcolor = "altbg2";
	}
	else
	{
		$bgcolor = "altbg1";
	}
	return $bgcolor;
}
function startnav()
{
	echo "<table cellpadding=\"1\" cellspacing=\"0\" border=\"0\" align=\"center\" width=\"100%\" class=\"lnavbordercolor\">\n";
	echo "<tr><td>\n";
	echo "<table cellpadding=\"6\" cellspacing=\"0\" border=\"0\" width=\"100%\">";
}
function makenavoption($name, $url)
{
	global $noframes, $navoptions;
	$name = htmlspecialchars_uni($name);
	$navoptions .= "<li><a href=\"$url\">$name</a></li>\n";
}
function makenavselect($name)
{
	global $noframes, $navoptions, $navselects;
	$name = htmlspecialchars_uni($name);
	echo "<table cellpadding=\"1\" cellspacing=\"0\" border=\"0\" align=\"center\" width=\"100%\" class=\"lnavbordercolor\">\n";
	echo "<tr><td>\n";
	echo "<table cellpadding=\"6\" cellspacing=\"0\" border=\"0\" width=\"100%\">";
	if($name)
	{
		echo "<tr>\n<td class=\"lnavhead\" align=\"center\">$name</td>\n</tr>\n";
	}
	echo "<tr>\n<td class=\"lnavitem\" valign=\"top\">";
	echo "<ul>\n";
	echo $navoptions;
	echo "</ul>\n</td></tr>\n";
	echo "</table>\n";
	echo "</td></tr></table>\n";
	echo "<br>\n";

	$navoptions = "";
}


function endnav()
{
	echo "</table>\n";
	echo "</td></tr></table>\n";
	echo "<br>\n";
}
function makenavgroup($name="")
{
	global $noframes, $navoptions, $navselects;
	if($noframes)
	{
		echo "<td>\n<select onchange=\"navJump(this.options[this.selectedIndex].value, this.form)\">\n";
		echo "<option value=\"\">$name</option>\n<option value=\"\">&nbsp;</option>\n";
		echo $navselects;
		echo "</select>\n</td>\n";
	}
	else
	{
		echo $navselects;
		echo "<hr>";
	}
	$navselects = "";
	$navoptions = "";
}

function makehopper($name, $values)
{
	while(list($action, $title) = each($values))
	{
		$options .= "<option value=\"$action\">$title</option>\n";
	}
	return "<select name=\"$name\"  onchange=\"this.form.submit();\">$options</select>&nbsp;<input type=\"submit\" value=\"Go\">\n";
}

function forumselect($name, $selected="",$fid="0",$depth="", $shownone="1", $extra="")
{
	global $db, $forumselect, $lang;

	if(!$fid)
	{
		$forumselect .= "<select name=\"$name\">";
		if($extra)
		{
			$forumselect .= "<option value=\"-1\">$extra</option><option value=\"0\">-----------</option>";
		}
		if($shownone)
		{
			$forumselect .= "<option value=\"0\">$lang->parentforum_none</option><option value=\"0\">-----------</option>";
		}
	}
	else
	{
		$query = $db->query("SELECT * FROM ".TABLE_PREFIX."forums WHERE fid='$fid'");
		$startforum = $db->fetch_array($query);
		$forumselect .= "<option value=\"$startforum[fid]\"";
		if($selected == $startforum[fid])
		{
			$forumselect .= " selected";
		}
		$forumselect .= ">$depth$startforum[name]</option>";
		$depth .= "--";
	}
	$query = $db->query("SELECT * FROM ".TABLE_PREFIX."forums WHERE pid='$fid' ORDER BY disporder");
	while($forum = $db->fetch_array($query))
	{
		forumselect($name, $selected, $forum[fid], $depth, $shownone, $extra);
	}
	if(!$fid)
	{
		$forumselect .= "</select>";
	}
	return $forumselect;
}

function checkadminpermissions($action)
{
	global $db, $mybbadmin, $lang;
	$perms = getadminpermissions($mybbadmin[uid]);
	if($perms[$action] != "yes")
	{
		cperror($lang->access_denied);
		exit;
	}
}

function getadminpermissions($uid="")
{
	global $db, $mybbadmin;
	if(!$uid)
	{
		$uid = $mybbadmin[uid];
	}
	$query = $db->query("SELECT * FROM ".TABLE_PREFIX."adminoptions WHERE (uid='$uid' OR uid='-1') AND permsset!='' ORDER BY uid DESC");
	$perms = $db->fetch_array($query);
	return $perms;
}

function logadmin()
{
	global $_SERVER, $mybbadmin, $db, $mybb;
	$scriptname = basename($_SERVER['PHP_SELF']);
	$qstring = explode("&", $_SERVER['QUERY_STRING']);
	while(list($key, $val) = each($qstring))
	{
		$vale = explode("=", $val, 2);
		if(trim($vale[0]) != "" && trim($vale[1]) != "")
		{
			if($vale[0] != "action")
			{
				$querystring .= "$sep$vale[0] = $vale[1]";
				$sep = " / ";
			}
		}
	}
	$now = time();
	$ipaddress = getip();
	$db->query("INSERT INTO ".TABLE_PREFIX."adminlog (uid,dateline,scriptname,action,querystring,ipaddress) VALUES ('".$mybbadmin['uid']."','".$now."','".$scriptname."','".$mybb->input['action']."','".$querystring."','".$ipaddress."')");
}

function buildacpnav($finished=1)
{
	global $nav, $navbits;
	$navsep = " &raquo; ";
	if(is_array($navbits))
	{
		reset($navbits);
		foreach($navbits as $key => $navbit)
		{
			if($navbits[$key+1])
			{
				if($navbits[$key+2]) { $sep = $navsep; } else { $sep = ""; }
				$nav .= "<a href=\"$navbit[url]\">$navbit[name]</a>$sep";
			}
		}
	}
	$navsize = count($navbits);
	$navbit = $navbits[$navsize-1];
	if($nav) {
		$activesep = "<br /><img src=\"../images//nav_bit.gif\" alt=\"---\" border=\"0\" />";
	}
	$activebit = "<span class=\"active\">$navbit[name]</span>";
	$donenav = "<div  align=\"center\"><div class=\"navigation\">\n$nav$activesep$activebit\n</div></div><br />";
	return $donenav;
}

function addacpnav($name, $url="")
{
	global $navbits;
	$navsize = count($navbits);
	$navbits[$navsize]['name'] = $name;
	$navbits[$navsize]['url'] = $url;
}

function makeacpforumnav($fid)
{
	global $pforumcache, $db, $currentitem, $forumcache, $navbits;
	if(!$pforumcache)
	{
		if(!is_array($forumcache))
		{
			cacheforums();
		}
		reset($forumcache);
		while(list($key, $val) = each($forumcache))
		{
			$pforumcache[$val['fid']][$val['pid']] = $val;
		}
	}
	if(is_array($pforumcache[$fid]))
	{
		while(list($key, $forumnav) = each($pforumcache[$fid]))
		{
			if($fid == $forumnav['fid'])
			{
				if($pforumcache[$forumnav['pid']])
				{
					makeacpforumnav($forumnav['pid']);
				}
				$navsize = count($navbits);
				$navbits[$navsize]['name'] = $forumnav['name'];
				$navbits[$navsize]['url'] = "forums.php?fid=$forumnav[fid]";
			}
		}
	}
	return 1;
}

function quickpermissions($fid="", $pid="")
{
	global $db, $cache, $lang, $fpermscache;
	if($fid)
	{
		$query = $db->query("SELECT * FROM ".TABLE_PREFIX."forums WHERE fid='$fid'");
		$forum = $db->fetch_array($query);
		$query = $db->query("SELECT * FROM ".TABLE_PREFIX."forumpermissions WHERE fid='$fid'");
		while($fperm = $db->fetch_array($query))
		{
			$fperms[$fperm[gid]] = $fperm;
		}
	}
	$groupscache = $cache->read("usergroups");
	echo "<script type=\"text/javascript\">\n";
?>
function uncheckInheritPerm(id) {
	chk = getElemRefs("inherit["+id+"]");
	chk.checked = false;
	h = getElemRefs("inheritlbl_"+id);
	h.className = "";
}

function checkInheritPerm(id) {
	chk = getElemRefs("inherit["+id+"]");
	chk.checked = true;
	h = getElemRefs("inheritlbl_"+id);
	h.className = "highlight1";
}

function checkPermRow(id, master) {
	chk = getElemRefs("canview["+id+"]");
	chk.checked = master.checked;
	chk = getElemRefs("canpostthreads["+id+"]");
	chk.checked = master.checked;
	chk = getElemRefs("canpostreplies["+id+"]");
	chk.checked = master.checked;
	chk = getElemRefs("canpostpolls["+id+"]");
	chk.checked = master.checked;
	chk = getElemRefs("canpostattachments["+id+"]");
	chk.checked = master.checked;

	uncheckInheritPerm(id);
}


function getElemRefs(id) {
	if(document.getElementById) {
		return document.getElementById(id);
	}
	else if(document.all) {
		return document.all[id];
	}
	else if(document.layers) {
		return document.layers[id];
	}
}
</script>
<?php
	starttable();
	if($fid)
	{
		tableheader("Quick Forum Permissions for $forum[name]", "", "7");
	}
	else
	{
		tableheader("Quick Forum Permissions", "", "7");
	}
	echo "<tr>\n";
	echo "<td class=\"subheader\">".$lang->quickperms_group."</td>\n";
	echo "<td class=\"subheader\" align=\"center\" width=\"10%\">".$lang->quickperms_view."</td>\n";
	echo "<td class=\"subheader\" align=\"center\" width=\"10%\">".$lang->quickperms_postthreads."</td>\n";
	echo "<td class=\"subheader\" align=\"center\" width=\"10%\">".$lang->quickperms_postreplies."</td>\n";
	echo "<td class=\"subheader\" align=\"center\" width=\"10%\">".$lang->quickperms_postpolls."</td>\n";
	echo "<td class=\"subheader\" align=\"center\" width=\"10%\">".$lang->quickperms_upload."</td>\n";
	echo "<td class=\"subheader\" align=\"center\" width=\"10%\">".$lang->quickperms_all."</td>\n";
	echo "</tr>\n";
	$query = $db->query("SELECT * FROM ".TABLE_PREFIX."usergroups ORDER BY title");
	while($usergroup = $db->fetch_array($query))
	{
		$bgcolor = getaltbg();
		if($fperms[$usergroup['gid']])
		{
			$perms = $fperms[$usergroup['gid']];
		}
		elseif($pid)
		{
			$perms = forum_permissions($pid, 0, $usergroup['gid']);
		}
		elseif($fid)
		{
			$perms = forum_permissions($fid, 0, $usergroup['gid']);
		}
		if(!is_array($perms))
		{
			$perms = usergroup_permissions($usergroup['gid']);
		}
		if($fperms[$usergroup['gid']])
		{
			$inheritcheck = "";
			$inheritclass = "";
		}
		else
		{
			$inheritcheck = "checked=\"checked\"";
			$inheritclass = "highlight1";
		}
		if($perms['canview'] == "yes")
		{
			$canview = "checked=\"checked\"";
		}
		else
		{
			$canview = "";
		}
		if($perms['canpostthreads'] == "yes")
		{
			$canpostthreads = "checked=\"checked\"";
		}
		else
		{
			$canpostthreads = "";
		}
		if($perms['canpostreplys'] == "yes")
		{
			$canpostreplies = "checked=\"checked\"";
		}
		else
		{
			$canpostreplies = "";
		}
		if($perms['canpostpolls'] == "yes")
		{
			$canpostpolls = "checked=\"checked\"";
		}
		else
		{
			$canpostpolls = "";
		}
		if($perms['canpostattachments'] == "yes")
		{
			$canpostattachments = "checked=\"checked\"";
		}
		else
		{
			$canpostattachments = "";
		}
		if($canview && $canpostthreads && $canpostreplies && $canpostpolls && $canpostattachments)
		{
			$allcheck = "checked=\"checked\"";
		}
		else
		{
			$allcheck = "";
		}
		echo "<tr>\n";
		echo "<td class=\"$bgcolor\"><strong>$usergroup[title]</strong><br /><small><input type=\"checkbox\" name=\"inherit[$usergroup[gid]]\" id=\"inherit[$usergroup[gid]]\" value=\"yes\" onclick=\"checkInheritPerm($usergroup[gid]);\" $inheritcheck> <span id=\"inheritlbl_$usergroup[gid]\" class=\"$inheritclass\">".$lang->quickperms_inheritdefault."</span></td>\n";
		echo "<td class=\"$bgcolor\" align=\"center\"><input type=\"checkbox\" name=\"canview[$usergroup[gid]]\" id=\"canview[$usergroup[gid]]\" value=\"yes\" onclick=\"uncheckInheritPerm($usergroup[gid])\" $canview /></td>\n";
		echo "<td class=\"$bgcolor\" align=\"center\"><input type=\"checkbox\" name=\"canpostthreads[$usergroup[gid]]\" id=\"canpostthreads[$usergroup[gid]]\" value=\"yes\" onclick=\"uncheckInheritPerm($usergroup[gid])\" $canpostthreads /></td>\n";
		echo "<td class=\"$bgcolor\" align=\"center\"><input type=\"checkbox\" name=\"canpostreplies[$usergroup[gid]]\" id=\"canpostreplies[$usergroup[gid]]\" value=\"yes\" onclick=\"uncheckInheritPerm($usergroup[gid])\" $canpostreplies /></td>\n";
		echo "<td class=\"$bgcolor\" align=\"center\"><input type=\"checkbox\" name=\"canpostpolls[$usergroup[gid]]\" id=\"canpostpolls[$usergroup[gid]]\" value=\"yes\" onclick=\"uncheckInheritPerm($usergroup[gid])\" $canpostpolls /></td>\n";
		echo "<td class=\"$bgcolor\" align=\"center\"><input type=\"checkbox\" name=\"canpostattachments[$usergroup[gid]]\" id=\"canpostattachments[$usergroup[gid]]\" value=\"yes\" onclick=\"uncheckInheritPerm($usergroup[gid])\" $canpostattachments /></td>\n";
		echo "<td class=\"$bgcolor\" align=\"center\"><input type=\"checkbox\" onclick=\"checkPermRow($usergroup[gid], this);\" $allcheck></td>\n";
		echo "</tr>\n";
		unset($perms);
	}
	endtable();
}

function savequickperms($fid)
{
	global $db, $inherit, $canview, $canpostthreads, $canpostreplies, $canpostpolls, $canpostattachments, $cache;
	$query = $db->query("SELECT * FROM ".TABLE_PREFIX."usergroups");
	while($usergroup = $db->fetch_array($query))
	{
		// Delete existing permissions
		$db->query("DELETE FROM ".TABLE_PREFIX."forumpermissions WHERE fid='$fid' AND gid='$usergroup[gid]'");

		// Only insert the new ones if we're using custom permissions
		if($inherit[$usergroup['gid']] != "yes")
		{
			if($canview[$usergroup['gid']] == "yes")
			{
				$pview = "yes";
			}
			else
			{
				$pview = "no";
			}
			if($canpostthreads[$usergroup['gid']] == "yes")
			{
				$pthreads = "yes";
			}
			else
			{
				$pthreads = "no";
			}
			if($canpostreplies[$usergroup['gid']] == "yes")
			{
				$preplies = "yes";
			}
			else
			{
				$preplies = "no";
			}
			if($canpostpolls[$usergroup['gid']] == "yes")
			{
				$ppolls = "yes";
			}
			else
			{
				$ppolls = "no";
			}
			if($canpostattachments[$usergroup['gid']] == "yes")
			{
				$pattachments = "yes";
			}
			else
			{
				$pattachments = "no";
			}
			if(!$preplies && !$pthreads)
			{
				$ppost = "no";
			}
			else
			{
				$ppost = "yes";
			}
			$db->query("INSERT INTO ".TABLE_PREFIX."forumpermissions (pid,fid,gid,canview,candlattachments,canpostthreads,canpostreplys,canpostattachments,canratethreads,caneditposts,candeleteposts,candeletethreads,caneditattachments,canpostpolls,canvotepolls,cansearch) VALUES (NULL,'$fid','$usergroup[gid]', '$pview', '$pview', '$pthreads', '$preplies', '$pattachments', '$pview', '$ppost', '$ppost', '$pthreads', '$pattachments', '$ppolls', '$pview', '$pview')");
		}
	}
	$cache->updateforumpermissions();
}

function build_css_array($tid, $addinherited=1)
{
	global $db, $tcache, $cssselectors;
	if(!is_array($tcache))
	{
		cache_themes();
	}
	$theme = $tcache[$tid];
	foreach($cssselectors as $selector => $realname)
	{
		$css[$selector] = build_css_selector_array($theme['tid'], $selector, $addinherited);
	}
	return $css;
}
function build_css_selector_array($tid, $selector, $addinherited=1)
{
	global $tcache;
	if(!$tcache[$tid])
	{
		return false;
	}
	$theme = $tcache[$tid];
	$cssbits = $theme['cssbits'];
	if(!$cssbits[$selector])
	{
		if($theme['pid'] > 0)
		{
			$cssbit = build_css_selector_array($theme['pid'], $selector, $addinherited);
		}
	}
	else // Is customized in this theme
	{
		$cssbit = $cssbits[$selector];
		if($addinherited)
		{
			$cssbit['inherited'] = $tid;
		}
	}
	return $cssbit;
}

function build_theme_array($tid, $addinherited=1)
{
	global $db, $tcache, $themebitlist;
	if(!is_array($tcache))
	{
		cache_themes();
	}
	$theme = $tcache[$tid];
	foreach($themebitlist as $themebit)
	{
		$thebit = build_theme_bit_array($theme['tid'], $themebit, $addinherited);
		if($thebit['inherited'])
		{
			$tdetail['inherited'][$themebit] = $thebit['inherited'];
		}
		$tdetail[$themebit] = $thebit['themebit'];
	}
	return $tdetail;
}
function build_theme_bit_array($tid, $themebit, $addinherited=1)
{
	global $tcache;
	if(!$tcache[$tid])
	{
		return false;
	}
	$theme = $tcache[$tid];
	$pid = $theme['pid'];
	$parentbits = $tcache[$pid]['themebits'];
	$themebits = $theme['themebits'];
	if($themebits[$themebit] && !$themebits['inherited'][$themebit])
	{
		$thebit = $themebits[$themebit];
		if($addinherited)
		{
			$inherited = $tid;
		}
	}
	else 
	{
		if($theme['pid'] > 0)
		{
			$thetbit = build_theme_bit_array($theme['pid'], $themebit, $addinherited);
			$inherited = $thetbit['inherited'];
			$thebit = $thetbit['themebit'];
		}

	}
	return array("themebit" => $thebit, "inherited" => $inherited);
}

function make_theme($themebits="", $css="", $pid=0, $isnew=0)
{
	global $db, $themebitlist, $cssselectors, $revert_css, $revert_themebits;
	if(!$css || !$themebits || $isnew)
	{
		$query = $db->query("SELECT * FROM ".TABLE_PREFIX."themes WHERE tid='$pid'");
		$parent = $db->fetch_array($query);
		if(!$themebits || $isnew)
		{
			$themebits = unserialize($parent['themebits']);
		}
		if(!$css || $isnew)
		{
			$css = unserialize($parent['cssbits']);
		}
		
	}
	// Build the actual css
	$cssbits = $css;
	if($isnew != 1)
	{
		// Check the inheritance stuff and unset inherited values
		// Theme bits
		$parentbits = build_theme_array($pid);
		foreach($themebitlist as $themebit)
		{
			$parentbit = $parentbits[$themebit];
			$childbit = $themebits[$themebit];
			if($parentbit == $childbit || $revert_themebits[$themebit])
			{
				$themebits['inherited'][$themebit] = $parentbits['inherited'][$themebit];
				$themebits[$themebit] = $parentbit;
			}
			$parentbit = $childbit = "";
		}
		// CSS bits
		$parentcss = build_css_array($pid, 0);
		foreach($cssselectors as $selector => $realname)
		{
			$parentbit = serialize(killempty($parentcss[$selector]));
			$childbit = serialize(killempty($css[$selector]));
			if($parentbit == $childbit)
			{
				unset($cssbits[$selector]);
			}
			if($revert_css[$selector])
			{
				$rebuildrev = 1;
				//print_r($parentbit);
				$css[$selector] = $parentbit;
				unset($cssbits[$selector]);
			}
			$parentbit = $childbit = "";
		}
		$css = array_merge($parentcss, $cssbits);
	}
	else
	{
		//unset($css); unset($cssbits);
		unset($cssbits);
		$themebits = build_theme_array($pid);
	}
	$csscontents = build_css($css);
	return array("css" => $csscontents, "cssbits" => $cssbits, "themebits" => $themebits);
}

function get_parent_theme_bits($pid)
{
	global $db, $themebits;
	$query = $db->query("SELECT * FROM ".TABLE_PREFIX."themes WHERE tid='$pid'");
	$parent = $db->fetch_array($query);
	foreach($themebits as $themebit)
	{
		$theme[$themebit] = $parent[$themebit];
	}
	return $theme;
}

function build_css($array, $name="")
{
	global $cssselectors, $revert_css;
	if(!is_array($array))
	{
		return;
	}
	foreach($array as $friendlyname => $bits)
	{
		$selector = $cssselectors[$friendlyname];
		if(is_array($bits))
		{

			foreach($bits as $attribute => $value)
			{
				if(is_array($value))
				{
					$subcss[$attribute] = $value;
					$incss .= build_css($subcss, $friendlyname);
					unset($subcss);
				}
				elseif($attribute == "extra")
				{
					$extra = $value;
				}
				else
				{
					if($value)
					{
						$cssbits .= "\t".$attribute.": ".$value.";\n";
					}
				}
			}
		}
		if($cssbits || $extra)
		{
			if($extra)
			{
				$extrabits = explode("\n", $extra);
				foreach($extrabits as $exbit)
				{
					$cssbits .= "\t".$exbit."\n";
				}
			}
			$doname = 0;
			if(($name != "body" || ($name != "body" && $selector != "a_link" && $selector != "a_visited" && $selector != "a_hover")) && $name)
			{
				$name = $cssselectors[$name];
				$css .= $name." ";
				$doname = 1;
			}
			if($selector == "a:hover")
			{
				$selector = "a:hover, ";
				if($name && $doname) { $selector .= $name." "; }
				$selector .= "a:active";
			}
			$css .= $selector." {\n".$cssbits."}\n";
			$css .= $incss;
			$cssbits = $incss = "";
		}
		$extra = "";
	}
	return $css;
}

function makethemebitedit($title, $name)
{
	global $tid, $themebits, $tcache, $lang, $db, $theme;
	if(!is_array($tcache))
	{
		cache_themes();
	}
	$bgcolor = getaltbg();
	if($name == "extracss" && $themebits['extracss'] == "")
	{
		$themebits['inherited']['extracss'] = 0;
	}
	if($themebits['inherited'][$name] && $themebits['inherited'][$name] != $tid && $themebits['inherited'][$name] != 1)
	{
		$inheritid = $themebits['inherited'][$name];
		$inheritedfrom = $tcache[$inheritid]['name'];
		$highlight = "highlight3";
		$inheritnote = "(".$lang->inherited_from." ".$inheritedfrom.")";
	}
	elseif($themebits['inherited'][$name] == 1 || $tcache[$tid]['parent'] == 0)
	{
		$highlight = "";
	}
	else
	{
		$highlight = "highlight2";
		$customnote = " (".$lang->customized_this_style.")";
		$custom = 1;
	}
	if($name != "extracss")
	{
	echo "<tr>\n<td class=\"$bgcolor\" valign=\"top\" width=\"40%\">$title</td>\n";
	echo "<td class=\"$bgcolor\" valign=\"top\" width=\"60%\">";
	echo "<table cellspacing=\"0\" cellpadding=\"0\" border=\"0\"><tr><td>\n";
	}
	if($name == "templateset")
	{
		$query = $db->query("SELECT * FROM ".TABLE_PREFIX."templatesets ORDER BY title ASC");
		while($templateset = $db->fetch_array($query))
		{
			$selected = "";
			if($templateset['sid'] == $themebits[$name])
			{
				$selected = "selected";
			}
			$templatesets .= "<option value=\"".$templateset['sid']."\" $selected>".$templateset['title']."</option>\n";
		}
		echo "<select name=\"themebits[$name]\">$templatesets</select>";
	}
	elseif($name == "extracss")
	{
		if($custom == 1)
		{
			$revcustom = "<br /><input type=\"checkbox\" name=\"revert_themebits[extracss]\" id=\"revert_themebit_extracss\" value=\"1\" /><label for=\"revert_themebit_extracss\">".$lang->revert_customizations."</label>";
		}
		echo "<tr>\n";
		echo "<td class=\"altbg1\" align=\"center\">\n";
		echo "<textarea style=\"width: 98%; padding: 4px;\"	class=\"$highlight\" rows=\"9\"name=\"themebits[extracss]\">".htmlspecialchars_uni($theme['extracss'])."</textarea>$revcustom\n";
		echo "</td>\n";
		echo "</tr>\n";
	}
	else
	{
		$value = $themebits[$name];
		$value = htmlspecialchars_uni($value);
		echo "<input type=\"text\" name=\"themebits[$name]\" value=\"$value\" size=\"20\" class=\"$highlight\" />\n";
	}
	if($name != "extracss")
	{
		echo "</td>";
		if($custom == 1)
		{
			echo "<td><small><input type=\"checkbox\" name=\"revert_themebits[$name]\" id=\"revert_themebit_$name\" value=\"1\" /><label for=\"revert_themebit_$name\">".$lang->revert_customizations."</label></small></td>\n</tr>";
			echo "<tr>\n<td colspan=\"2\" align=\"center\"><span class=\"smalltext\">$customnote</span></td>\n";
		}
		elseif($inheritid)
		{
			echo "</tr>\n<td><span class=\"smalltext\">$inheritnote</span></td>";
		}
		echo "</tr></table></td></tr>";
	}
}

function cache_themes()
{
	global $db, $tcache;
	$query = $db->query("SELECT * FROM ".TABLE_PREFIX."themes ORDER BY pid, name");
	while($theme = $db->fetch_array($query))
	{
		$theme['themebits'] = unserialize($theme['themebits']);
		$theme['cssbits'] = unserialize($theme['cssbits']);
		$tcache[$theme['tid']] = $theme;
	}
}

function make_theme_list($tid="0", $depth="")
{
	global $db, $tcache, $tcache2, $lang;
	if(!is_array($tcache))
	{
		cache_themes();
	}
	if(!is_array($tcache2))
	{
		$query = $db->query("SELECT style, COUNT(uid) AS users FROM ".TABLE_PREFIX."users GROUP BY style");
		while($userstyle = $db->fetch_array($query))
		{
			$tcache[$userstyle['style']]['users'] = $userstyle['users'];
		}
		foreach($tcache as $theme)
		{
			$tcache2[$theme['pid']][$theme['tid']] = $theme;
		}
		unset($theme);
	}
	if(is_array($tcache2[$tid]))
	{
		foreach($tcache2[$tid] as $theme)
		{
			$bgcolor = getaltbg();
			if($theme['def'] == 1)
			{
				$def = " (" . $lang->default . ")";
				$setdefault = "";
			}
			else
			{
				$setdefault = 1;
				$def = "";
			}
			if($theme['users'])
			{
				$users = sprintf($lang->theme_users, $theme['users']);
			}
			else
			{
				$users = "";
			}

			echo "<tr>\n";
			echo "<td class=\"$bgcolor\">$depth$theme[name]$users$def</td>\n";
			echo "<td class=\"$bgcolor\" align=\"right\" nowrap=\"nowrap\">";
			echo "<select name=\"theme_".$theme['tid']."\" onchange=\"theme_hop($theme[tid]);\">\n";
			echo "<option value=\"\" style=\"font-weight: bold;\">$lang->theme_options&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;</option>\n";
			if($theme['tid'] > 1)
			{
				//echo "<option value=\"settings\">- $lang->edit_theme_settings</option>\n";
				echo "<option value=\"delete\">- $lang->del_theme</option>\n";
				if($setdefault)
				{
					echo "<option value=\"default\">- $lang->set_as_default</option>";
				}
			}
			echo "<option value=\"\" style=\"font-weight: bold;\">$lang->theme_style</option>";
			echo "<option value=\"edit\" selected>- $lang->edit_theme_style</option>\n";
			echo "<option value=\"\" style=\"font-weight: bold;\">$lang->other_options</option>\n";
			echo "<option value=\"download\">- $lang->export_theme</option>\n";
			echo "</select>&nbsp;<input type=\"button\" onclick=\"theme_hop($theme[tid]);\" value=\"$lang->go\"></td>\n";
			echo "</td>\n";
			echo "</tr>\n";
			if(is_array($tcache2[$theme['tid']]))
			{
				make_theme_list($theme['tid'], $depth."&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;");
			}
		}
	}
}
	
function make_theme_select($name, $selected="", $tid="0", $depth="")
{
	global $db, $themeselect, $tcache, $tcache2;
	if(!$tid)
	{
		$themeselect .= "<select name=\"$name\">";
	}
	if(!is_array($tcache))
	{
		cache_themes();
	}
	if(!is_array($tcache2))
	{
		foreach($tcache as $theme)
		{
			$tcache2[$theme['pid']][$theme['tid']] = $theme;
		}
		unset($theme);
	}
	if(is_array($tcache2[$tid]))
	{
		foreach($tcache2[$tid] as $theme)
		{
			$sel = "";
			if($theme['tid'] == $selected)
			{
				$sel = "selected=\"selected\"";
			}
			$themeselect .= "<option value=\"".$theme['tid']."\" $sel>".$depth.$theme['name']."</option>";
			if(is_array($tcache2[$theme['tid']]))
			{
				$depth .= "--";
				make_theme_select($name, $selected, $theme['tid'], $depth);
			}
		}
	}
	if(!$tid)
	{
		$themeselect .= "</select>";
	}
	return $themeselect;
}

function update_theme($tid, $pid="", $themebits="", $css="", $child=0, $isnew=0)
{
	global $tcache, $db, $tcache2;
	if(!is_array($tcache))
	{
		cache_themes();
	}
	if(!is_array($tcache2))
	{
		foreach($tcache as $theme)
		{
			$tcache2[$theme['pid']][$theme['tid']] = $theme;
		}
		unset($theme);
	}
	if($child == 1 && $pid)
	{
		$css = $tcache[$tid]['cssbits'];
		$themebits = $tcache[$tid]['themebits'];
	}
	else
	{
		$pid = $tcache[$tid]['pid'];
	}
	$tname = $tcache[$tid]['name'];
	$updatedthemes .= "<li>$tname</li>";
	$newtheme = make_theme($themebits, $css, $pid, $isnew);
	$theme['css'] = $newtheme['css'];
	$theme['cssbits'] = $newtheme['cssbits'];
	$theme['themebits'] = $newtheme['themebits'];
	$theme['extracss'] = $newtheme['themebits']['extracss'];
	$tcache[$tid] = array_merge($tcache[$tid], $theme);
	$masterextra = $tcache[1]['extracss'];
	if(serialize($theme['extracss']) == serialize($masterextra) && $tid != 1)
	{
		unset($theme['extracss']);
	}
	if($isnew)
	{
		unset($theme['themebits']['inherited']['extracss']);
	}

	if($masterextra)
	{
		$theme['css'] .= "\n/* Additional CSS (Master) */\n";
		$theme['css'] .= $masterextra;
	}
	if($theme['extracss'] && $tid != 1 && serialize($theme['extracss']) != serialize($masterextra))
	{
		$theme['css'] .= "\n/* Additional CSS (Custom) */\n";
		$theme['css'] .= $theme['extracss'];
	}

	$theme['css'] = addslashes($theme['css']);
	$theme['themebits'] = addslashes(serialize($theme['themebits']));
	$theme['cssbits'] = addslashes(serialize($theme['cssbits']));
	$theme['extracss'] = addslashes($theme['extracss']);
	$db->query("UPDATE ".TABLE_PREFIX."themes SET css='".$theme['css']."', cssbits='".$theme['cssbits']."', themebits='".$theme['themebits']."', extracss='".$theme['extracss']."' WHERE tid='$tid'");
	// Update kids!
	if(is_array($tcache2[$tid]))
	{
		$updatedthemes .= "<ul>";
		foreach($tcache2[$tid] as $ctid => $ct)
		{
			$updatedthemes .= update_theme($ctid, $tid, "", "", 1, $isnew);
		}
		$updatedthemes . "</ul>";
	}
	return $updatedthemes;
}
function killempty($array)
{
	if(!is_array($array))
	{
		return;
	}
	foreach($array as $key => $val)
	{
		if(is_array($val))
		{
			$array[$key] = killempty($val);
			$val = $array[$key];
		}
		if(empty($val))
		{
			unset($array[$key]);
		}
	}
	return $array;
}
?>