<?php
$oRecord = ffRecord::factory($cm->oPage);
$oRecord->id = "MainRecord";
$oRecord->title = "Grafico";
$oRecord->src_table =  CM_TABLE_PREFIX . "mod_graph_chart";

$oRecord->addContent(null, true, "general");
$oRecord->addContent(null, true, "colors");
$oRecord->addContent(null, true, "dimensions");
$oRecord->addContent(null, true, "strings");
$oRecord->addContent(null, true, "angles");

$oRecord->groups["general"]["title"] = "Generale";
$oRecord->groups["colors"]["title"] = "Colori";
$oRecord->groups["dimensions"]["title"] = "Dimensioni";
$oRecord->groups["strings"]["title"] = "Testi";
$oRecord->groups["angles"]["title"] = "Angoli";

$oField = ffField::factory($cm->oPage);
$oField->id = "ID";
$oField->base_type = "Number";
$oRecord->addKeyField($oField);

$oField = ffField::factory($cm->oPage);
$oField->id = "name";
$oField->label = "Nome";
$oField->required = true;
$oRecord->addContent($oField, "general");

/*
$oField = ffField::factory($cm->oPage);
$oField->id = "ID_type";
$oField->label = "Tipo Grafico";
$oField->source_SQL = "SELECT null, ID, name FROM " . CM_TABLE_PREFIX . "mod_graph_type ORDER BY name";
$oField->widget = "activecomboex";
$oField->actex_child = "ID_data";
$oRecord->addContent($oField, "data_selection");
*/

$oField = ffField::factory($cm->oPage);
$oField->id = "ID_type";
$oField->extended_type = "Selection";
$oField->required = true;
$oField->label = "Tipologia Grafico";
$oField->source_SQL = "SELECT ID, name FROM " . CM_TABLE_PREFIX . "mod_graph_type ORDER BY name";
$oRecord->addContent($oField, "general");

/*
$oField = ffField::factory($cm->oPage);
$oField->id = "ID_data";
$oField->label = "SQL";
$oField->source_SQL = "SELECT DISTINCT ID, name, type FROM
						(
						   SELECT
									" . CM_TABLE_PREFIX . "mod_graph_data.ID
									," . CM_TABLE_PREFIX . "mod_graph_data.name
									," . CM_TABLE_PREFIX . "mod_graph_type.ID AS type

							FROM
								  " . CM_TABLE_PREFIX . "mod_graph_data
							INNER JOIN " . CM_TABLE_PREFIX . "mod_graph_type
								ON " . CM_TABLE_PREFIX . "mod_graph_data.column_count >= " . CM_TABLE_PREFIX . "mod_graph_type.array_dimension
							ORDER BY " . CM_TABLE_PREFIX . "mod_graph_data.name ASC
						) AS tbl_src
						[WHERE]						
										";
$oField->widget = "activecomboex";
$oField->actex_update_from_db = true;
$oField->actex_related_field = "type";
$oField->actex_father = "ID_type";
$oField->actex_child = "column";
$oRecord->addContent($oField, "data_selection");
 * 
 */
/*
$oField = ffField::factory($cm->oPage);
$oField->id = "column";
$oField->label = "Colonne";
$oField->source_SQL = "SELECT
							" . CM_TABLE_PREFIX . "mod_graph_data.column AS data
							," . CM_TABLE_PREFIX . "mod_graph_data.column
							," . CM_TABLE_PREFIX . "mod_graph_data.ID 
						FROM
					" . CM_TABLE_PREFIX . "mod_graph_data
						[WHERE]
							";
$oField->widget = "activecomboex";
$oField->actex_update_from_db = true;
$oField->actex_related_field = "ID";
$oField->actex_father = "ID_data";
$oRecord->addContent($oField, "data_selection");
 * 
 */

$oField = ffField::factory($cm->oPage);
$oField->id = "axisColor";
$oField->label = "Colore Assi";
$oField->description = "Definire nell'ordine Stroke', 'Fill' e 'StrokeSize'";
$oRecord->addContent($oField, "colors");

$oField = ffField::factory($cm->oPage);
$oField->id = "axisFontSize";
$oField->label = "Dimensione Font degli assi";
$oRecord->addContent($oField, "dimensions");

$oField = ffField::factory($cm->oPage);
$oField->id = "backgroundColor";
$oField->label = "Colore sfondo";
$oField->description = "Definire nell'ordine Stroke', 'Fill' e 'StrokeSize'";
$oRecord->addContent($oField, "colors");

$oField = ffField::factory($cm->oPage);
$oField->id = "borderColor";
$oField->label = "Colore bordi";
$oField->description = "Definire nell'ordine Stroke', 'Fill' e 'StrokeSize'";
$oRecord->addContent($oField, "colors");

$oField = ffField::factory($cm->oPage);
$oField->id = "colors";
$oField->label = "Colori";
$oField->description = "Definire un colore per ogni voce; se il grafico � 3D definire due colori per ogni elemento (principale e ombra)";
$oRecord->addContent($oField, "colors");

$oField = ffField::factory($cm->oPage);
$oField->id = "enableTooltip";
$oField->label = "Abilita Tooltip";
$oField->extended_type = "Boolean";
$oField->unchecked_value = new ffData("0");
$oField->checked_value = new ffData("1");
$oRecord->addContent($oField, "general");

$oField = ffField::factory($cm->oPage);
$oField->id = "focusBorderColor";
$oField->description = "Definire nell'ordine Stroke', 'Fill' e 'StrokeSize'";
$oField->label = "Colore dei bordi sul focus";
$oRecord->addContent($oField, "colors");

$oField = ffField::factory($cm->oPage);
$oField->id = "height";
$oField->label = "Altezza";
$oRecord->addContent($oField, "dimensions");

$oField = ffField::factory($cm->oPage);
$oField->id = "width";
$oField->label = "Larghezza";
$oRecord->addContent($oField, "dimensions");

$oField = ffField::factory($cm->oPage);
$oField->id = "isStacked";
$oField->label = "Linee Unite";
$oField->extended_type = "Boolean";
$oField->description = "Se settata, raggruppa i valori";
$oField->unchecked_value = new ffData("0");
$oField->checked_value = new ffData("1");
$oRecord->addContent($oField, "general");

$oField = ffField::factory($cm->oPage);
$oField->id = "is3D";
$oField->label = "Grafico 3D";
$oField->extended_type = "Boolean";
$oField->default_value = new ffData("1");
$oField->unchecked_value = new ffData("0");
$oField->checked_value = new ffData("1");
$oRecord->addContent($oField, "general");

$oField = ffField::factory($cm->oPage);
$oField->id = "legend";
$oField->label = "Legenda";
$oField->extended_type = "Selection";
$oField->multi_pairs = array(
							 array(new ffData("right"), new ffData("Destra"))
							,array(new ffData("left"), new ffData("Sinistra"))
							,array(new ffData("top"), new ffData("In alto"))
							,array(new ffData("bottom"), new ffData("In basso"))
							,array(new ffData("none"), new ffData("Nessuna legenda"))
									);
$oField->description = "Default: destra";
$oRecord->addContent($oField, "general");

$oField = ffField::factory($cm->oPage);
$oField->id = "legendBackgroundColor";
$oField->description = "Definire nell'ordine Stroke', 'Fill' e 'StrokeSize'";
$oField->label = "Colore di sfondo della legenda";
$oRecord->addContent($oField, "colors");

$oField = ffField::factory($cm->oPage);
$oField->id = "legendTextColor";
$oField->description = "Definire nell'ordine Stroke', 'Fill' e 'StrokeSize'";
$oField->label = "Colore del font della legenda";
$oRecord->addContent($oField, "colors");

$oField = ffField::factory($cm->oPage);
$oField->id = "titleColor";
$oField->description = "Definire nell'ordine Stroke', 'Fill' e 'StrokeSize'";
$oField->label = "Colore del titolo";
$oRecord->addContent($oField, "colors");

$oField = ffField::factory($cm->oPage);
$oField->id = "titleFontSize";
$oField->label = "Dimensione del titolo";
$oRecord->addContent($oField, "dimensions");

$oField = ffField::factory($cm->oPage);
$oField->id = "tooltipFontSize";
$oField->label = "Dimensione del tooltip";
$oField->description = "Default: 11";
$oRecord->addContent($oField, "dimensions");

$oField = ffField::factory($cm->oPage);
$oField->id = "tooltipWidth";
$oField->label = "Larghezza del tooltip";
$oField->description = "Default: 120";
$oRecord->addContent($oField, "dimensions");

$oField = ffField::factory($cm->oPage);
$oField->id = "tooltipHeight";
$oField->label = "Altezza del tooltip";
$oField->description = "Default: 60";
$oRecord->addContent($oField, "dimensions");

$oField = ffField::factory($cm->oPage);
$oField->id = "min";
$oField->label = "Valore min dell'asse Y";
$oRecord->addContent($oField, "dimensions");

$oField = ffField::factory($cm->oPage);
$oField->id = "max";
$oField->label = "Valore max dell'asse Y";
$oRecord->addContent($oField, "dimensions");

$oField = ffField::factory($cm->oPage);
$oField->id = "legendFontSize";
$oField->label = "Dimensione font della legenda";
$oField->description = "Default: automatico";
$oRecord->addContent($oField, "dimensions");

$oField = ffField::factory($cm->oPage);
$oField->id = "pieJoinAngle";
$oField->label = "Join Angle";
$oField->description = "Valore minimo (in gradi) tale per cui ogni fetta di torta è raggruppata sotto Altri";
$oRecord->addContent($oField, "angles");

$oField = ffField::factory($cm->oPage);
$oField->id = "pieMinimalAngle";
$oField->label = "Angolo Minimo";
$oField->description = "Valore minimo (in gradi) tale per cui ogni fetta non sarà rappresentata";
$oRecord->addContent($oField, "angles");

$oField = ffField::factory($cm->oPage);
$oField->id = "title";
$oField->label = "Titolo del grafico";
$oRecord->addContent($oField, "strings");

$oField = ffField::factory($cm->oPage);
$oField->id = "titleX";
$oField->label = "Titolo dell'asse X";
$oRecord->addContent($oField, "strings");

$oField = ffField::factory($cm->oPage);
$oField->id = "titleXType";
$oField->label = "Tipo Dati asse X";
$oField->extended_type = "Selection";
$oField->multi_select_one = false;
$oField->multi_pairs = array(
								 array(new ffData("Text"), new ffData("Testo"))
								,array(new ffData("Number"), new ffData("Numero"))
								,array(new ffData("Date"), new ffData("Data"))
								,array(new ffData("Time"), new ffData("Orario"))
								,array(new ffData("DateTime"), new ffData("Data/Orario"))
							);
$oRecord->addContent($oField, "strings");

$oField = ffField::factory($cm->oPage);
$oField->id = "titleY";
$oField->label = "Titolo dell'asse Y";

$oRecord->addContent($oField, "strings");

$oField = ffField::factory($cm->oPage);
$oField->id = "titleYType";
$oField->label = "Tipo Dati asse Y";
$oField->extended_type = "Selection";
$oField->multi_select_one = false;
$oField->default_value = new ffData("Number");
$oField->multi_pairs = array(
								 array(new ffData("Text"), new ffData("Testo"))
								,array(new ffData("Number"), new ffData("Numero"))
								,array(new ffData("Date"), new ffData("Data"))
								,array(new ffData("Time"), new ffData("Orario"))
								,array(new ffData("DateTime"), new ffData("Data/Orario"))
							);
$oRecord->addContent($oField, "strings");

$oDetail = ffDetails::factory($cm->oPage, null, null, array("name" => "ffDetails_sort"));
$oDetail->id = "query";
$oDetail->src_table = CM_TABLE_PREFIX .  "mod_graph_data_detail";
$oDetail->title = "Query SQL";
$oDetail->fields_relationship = array(
									"id_graph" => "ID"
								);
$oDetail->order_default = "order";
$oDetail->sort_order_field = "order";


$oField = ffField::factory($cm->oPage);
$oField->id = "IDDetail";
$oField->data_source = "ID";
$oField->base_type = "Number";
$oDetail->addKeyField($oField);

$oField = ffField::factory($cm->oPage);
$oField->id = "ID_data";
$oField->label = "SQL";
$oField->extended_type = "Selection";
/*
$oField->source_SQL = "SELECT DISTINCT ID, name, type FROM
						(
						   SELECT
									" . CM_TABLE_PREFIX . "mod_graph_data.ID
									," . CM_TABLE_PREFIX . "mod_graph_data.name
									," . CM_TABLE_PREFIX . "mod_graph_type.ID AS type

							FROM
								  " . CM_TABLE_PREFIX . "mod_graph_data
							INNER JOIN " . CM_TABLE_PREFIX . "mod_graph_type
								ON " . CM_TABLE_PREFIX . "mod_graph_data.column_count >= " . CM_TABLE_PREFIX . "mod_graph_type.array_dimension
							ORDER BY " . CM_TABLE_PREFIX . "mod_graph_data.name ASC
						) AS tbl_src
						[WHERE]
						[ORDER]
										";
 *
 */

$oField->source_SQL = "SELECT
							*
						FROM
						 " . CM_TABLE_PREFIX . "mod_graph_data
						ORDER BY `name`
							";
//$oField->widget = "activecomboex";
//$oField->actex_update_from_db = true;
//$oField->actex_related_field = "type";
//$oField->actex_father = "ID_type";
//$oField->actex_child = "column";
$oDetail->addContent($oField);

$oField = ffField::factory($cm->oPage);
$oField->id = "title";
$oField->label = "Titolo";
$oDetail->addContent($oField);

$cm->oPage->addContent($oRecord);
$oRecord->addContent($oDetail);
$cm->oPage->addContent($oDetail);
