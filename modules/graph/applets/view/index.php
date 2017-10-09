<?php
$globals = ffGlobals::getInstance("graph");
$globals->reqId++;

$db = ffDb_Sql::factory();
$db2 = ffDb_Sql::factory();

$sHaving = "HAVING graph_name = " . $db->toSql($applet_params["name"]) . "";

$sSQL = "SELECT
				 " . CM_TABLE_PREFIX . "mod_graph_type.ID AS idtype
				," . CM_TABLE_PREFIX . "mod_graph_type.name AS typename
				," . CM_TABLE_PREFIX . "mod_graph_type.template_path AS path
				," . CM_TABLE_PREFIX . "mod_graph_chart.*
				," . CM_TABLE_PREFIX . "mod_graph_chart.ID AS idgraph
				," . CM_TABLE_PREFIX . "mod_graph_chart.name AS graph_name
		FROM
				" . CM_TABLE_PREFIX . "mod_graph_chart
		LEFT JOIN
			" . CM_TABLE_PREFIX . "mod_graph_type
				ON " . CM_TABLE_PREFIX . "mod_graph_chart.ID_type = " . CM_TABLE_PREFIX . "mod_graph_type.ID
		" . $sHaving. "
							";

$db->query($sSQL);

if($db->nextRecord())
{
	$template = $db->getField("path")->getValue();

	$filename = cm_moduleCascadeFindTemplate(CM_MODULES_ROOT . "/graph/themes", "/applets/view/" . $template , "default");
	$tpl = ffTemplate::factory(ffCommon_dirname($filename));
	$tpl->load_file($template, "main");
	$tpl->set_var("theme", $cm->oPage->getTheme());
	$tpl->set_var("site_path", $cm->oPage->site_path);
	$tpl->set_var("query_string", $_SERVER["QUERY_STRING"]);
	$tpl->set_var("encoded_this_url", rawurlencode($_SERVER["REQUEST_URI"]));
	$tpl->set_var("graph_path", (string)$cm->router->getRuleById("graph")->reverse);
	$tpl->set_var("parsegraph", FF_SITE_PATH . (string)$cm->router->getRuleById("parsegraph")->reverse);
	$tpl->set_var("id", uniqid(time(), true));
	$tpl->set_var("reqId", $globals->reqId);

	$tpl->set_var("graph_name", $applet_params["name"]);

	if($db->getField("enableTooltip")->getValue() == "0")
			$tpl->set_var("enableTooltip", "false");

	else if($db->getField("enableTooltip")->getValue() == "1")
			$tpl->set_var("enableTooltip", "true");

	$tpl->set_var("height", $db->getField("height")->getValue());

	if($db->getField("isStacked")->getValue() == "0")
		$tpl->set_var("isStacked", "false");

	else if($db->getField("isStacked")->getValue() == "1")
		$tpl->set_var("isStacked", "true");

	if($db->getField("is3D")->getValue() == "0")
		$tpl->set_var("is3D", "false");

	else if($db->getField("is3D")->getValue() == "1")
		$tpl->set_var("is3D", "true");

	$tpl->set_var("legend", $db->getField("legend")->getValue());
	$tpl->set_var("legendFontSize", $db->getField("legendFontSize")->getValue());
	$tpl->set_var("max", $db->getField("max")->getValue());
	$tpl->set_var("min", $db->getField("min")->getValue());
	$tpl->set_var("pieJoinAngle", $db->getField("pieJoinAngle")->getValue());
	$tpl->set_var("pieMinimalAngle", $db->getField("pieMinimalAngle")->getValue());
	$tpl->set_var("title", $db->getField("title")->getValue());
	$tpl->set_var("titleX", $db->getField("titleX")->getValue());
	$tpl->set_var("titleY", $db->getField("titleY")->getValue());
	$tpl->set_var("titleFontSize", $db->getField("titleFontSize")->getValue());
	$tpl->set_var("tooltipFontSize", $db->getField("tooltipFontSize")->getValue());
	$tpl->set_var("tooltipWidth", $db->getField("tooltipWidth")->getValue());
	$tpl->set_var("tooltipHeight", $db->getField("tooltipHeight")->getValue());
	$tpl->set_var("width", $db->getField("width")->getValue());

	if ($db->getField("axisColor")->getValue() != "")
	{
		$single_property = explode(",", $db->getField("axisColor")->getValue());

		$axisColor = array(
								 "stroke"     => trim($single_property[0])
								,"fill"       => trim($single_property[1])
								,"strokeSize" => trim($single_property[2])
						);

		$tpl->set_var("axisColor", ffCommon_google_jsonenc($axisColor));
		$tpl->parse("SectAxisColor", false);
	}
	else
		$tpl->set_var("SectAxisColor", "");

	if ($db->getField("axisFontSize")->getValue() != "")
		$tpl->parse("SectAxisFontSize", false);
	else
		$tpl->set_var("SectAxisFontSize", "");

	if ($db->getField("backgroundColor")->getValue() != "")
	{
		$single_property = explode(",", $db->getField("backgroundColor")->getValue());

		$backgroundColor = array(
								 "stroke"     => trim($single_property[0])
								,"fill"       => trim($single_property[1])
								,"strokeSize" => trim($single_property[2])
							);

		$tpl->set_var("backgroundColor", ffCommon_google_jsonenc($backgroundColor));
		$tpl->parse("SectBackgroundColor", false);
	}
	else
		$tpl->set_var("SectBackgroundColor", "");

	if ($db->getField("borderColor")->getValue() != "")
	{
		$single_property = explode(",", $db->getField("borderColor")->getValue());

		$borderColor = array(
								 "stroke"     => trim($single_property[0])
								,"fill"       => trim($single_property[1])
								,"strokeSize" => trim($single_property[2])
						);

		$tpl->set_var("borderColor", ffCommon_google_jsonenc($borderColor));
		$tpl->parse("SectBorderColor", false);
	}
	else
		$tpl->set_var("SectBorderColor", "");

	if ($db->getField("colors")->getValue() != "")
	{
		$single_color = explode(",", $db->getField("colors")->getValue());

		if($db->getField("is3D")->getValue() == "1")
		{
			for ($i = 0; $i < count($single_color); $i+=2)
			{
				$color[] = array(
							 "color"   => trim($single_color[$i])
							,"darker"  => 	trim($single_color[$i+1])
					);
			}
			$tpl->set_var("colors", ffCommon_google_jsonenc($color));
		}
		else
		{
			$tpl->set_var("colors", ffCommon_google_jsonenc($single_color));
		}
		$tpl->parse("SectColors", false);
	}
	else
		$tpl->set_var("SectColors", "");

	if ($db->getField("focusBorderColor")->getValue() != "")
	{
		$single_property = explode(",", $db->getField("focusBorderColor")->getValue());

		$focusBorderColor = array(
								 "stroke"     => trim($single_property[0])
								,"fill"       => trim($single_property[1])
								,"strokeSize" => trim($single_property[2])
							);

		$tpl->set_var("focusBorderColor", ffCommon_google_jsonenc($focusBorderColor));
		$tpl->parse("SectFocusBorderColor", false);
	}
	else
		$tpl->set_var("SectFocusBorderColor", "");

	if ($db->getField("height")->getValue() != "")
		$tpl->parse("SectHeight", false);
	else
		$tpl->set_var("SectHeight", "");

	if ($db->getField("isStacked")->getValue() == "1")
		$tpl->parse("SectIsStacked", false);
	else
		$tpl->set_var("SectIsStacked", "");

	if ($db->getField("is3D")->getValue() == "1")
		$tpl->parse("SectIs3D", false);
	else
		$tpl->set_var("SectIs3D", "");

	if ($db->getField("legend")->getValue() != "")
		$tpl->parse("SectLegend", false);
	else
		$tpl->set_var("SectLegend", "");

	if ($db->getField("legendBackgroundColor")->getValue() != "")
	{
		$single_property = explode(",", $db->getField("legendBackgroundColor")->getValue());
		$legendBackgroundColor = array();

		$legendBackgroundColor = array(
								 "stroke"     => trim($single_property[0])
								,"fill"       => trim($single_property[1])
								,"strokeSize" => trim($single_property[2])
							);

		$tpl->set_var("legendBackgroundColor", ffCommon_google_jsonenc($legendBackgroundColor));
		$tpl->parse("SectLegendBackgroundColor", false);
	}
	else
		$tpl->set_var("SectLegendBackgroundColor", "");

	if ($db->getField("legendFontSize")->getValue() != "")
		$tpl->parse("SectLegendFontSize", false);
	else
		$tpl->set_var("SectLegendFontSize", "");

	if ($db->getField("legendTextColor")->getValue() != "")
	{
		$single_property = explode(",", $db->getField("legendTextColor")->getValue());
		$legendTextColor = array();

		$legendTextColor = array(
								 "stroke"     => trim($single_property[0])
								,"fill"       => trim($single_property[1])
								,"strokeSize" => trim($single_property[2])
							);

		$tpl->set_var("legendTextColor", ffCommon_google_jsonenc($legendTextColor));
		$tpl->parse("SectLegendTextColor", false);
	}
	else
		$tpl->set_var("SectLegendTextColor", "");

	if ($db->getField("max")->getValue() != "")
		$tpl->parse("SectMax", false);
	else
		$tpl->set_var("SectMax", "");

	if ($db->getField("min")->getValue() != "")
		$tpl->parse("SectMin", false);
	else
		$tpl->set_var("SectMin", "");

	if ($db->getField("pieJoinAngle")->getValue() != "")
		$tpl->parse("SectPieJoinAngle", false);
	else
		$tpl->set_var("SectPieJoinAngle", "");

	if ($db->getField("pieMinimalAngle")->getValue() != "")
		$tpl->parse("SectPieMinimalAngle", false);
	else
		$tpl->set_var("SectPieMinimalAngle", "");

	if ($db->getField("title")->getValue() != "")
		$tpl->parse("SectTitle", false);
	else
		$tpl->set_var("SectTitle", "");

	if ($db->getField("titleX")->getValue() != "")
		$tpl->parse("SectTitleX", false);
	else
		$tpl->set_var("SectTitleX", "");

	if ($db->getField("titleY")->getValue() != "")
		$tpl->parse("SectTitleY", false);
	else
		$tpl->set_var("SectTitleY", "");

	if ($db->getField("titleColor")->getValue() != "")
	{
		$single_property = explode(",", $db->getField("titleColor")->getValue());
		$titleColor = array(
								 "stroke"     => trim($single_property[0])
								,"fill"       => trim($single_property[1])
								,"strokeSize" => trim($single_property[2])
							);

		$tpl->set_var("titleColor", ffCommon_google_jsonenc($titleColor));
		$tpl->parse("SectTitleColor", false);
	}
	else
		$tpl->set_var("SectTitleColor", "");

	if ($db->getField("titleFontSize")->getValue() != "")
		$tpl->parse("SectTitleFontSize", false);
	else
		$tpl->set_var("SectTitleFontSize", "");

	if ($db->getField("tooltipFontSize")->getValue() != "")
		$tpl->parse("SectTooltipFontSize", false);
	else
		$tpl->set_var("SectTooltipFontSize", "");

	if ($db->getField("tooltipHeight")->getValue() != "")
		$tpl->parse("SectTooltipHeight", false);
	else
		$tpl->set_var("SectTooltipHeight", "");

	if ($db->getField("width")->getValue() != "")
		$tpl->parse("SectWidth", false);
	else
		$tpl->set_var("SectWidth", "");

	$out_buffer = $tpl->rpparse("main", false);
}
