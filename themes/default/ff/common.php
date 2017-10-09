<?php
$ff = ffGlobals::getInstance("ff");

$ff->events->addEvent("dialog_onProcess", "cm_dialog_onProcess");

function cm_dialog_onProcess($tpl)
{
	/*if (cm_getMainTheme() == "default")
	{*/
		$cm = cm::getInstance();
		$cm->oPage->addContent($tpl);
		return false;
	//}
}
