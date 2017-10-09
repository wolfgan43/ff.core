<?php
$tqx = explode(";", $_REQUEST["tqx"]);
foreach ($tqx as $key => $value)
{
	$tmp = explode(":", $value);
	unset($tqx[$key]);
	$tqx[$tmp[0]] = $tmp[1];
}
reset($tqx);

$db = ffDB_Sql::factory();
$db2 = ffDB_Sql::factory();

$sSQL = "SELECT
			 " . CM_TABLE_PREFIX . "mod_graph_chart.ID AS idchart
			," . CM_TABLE_PREFIX . "mod_graph_chart.name AS chartname
			," . CM_TABLE_PREFIX . "mod_graph_chart.titleX 
			," . CM_TABLE_PREFIX . "mod_graph_chart.titleXType
			," . CM_TABLE_PREFIX . "mod_graph_chart.titleYType
			," . CM_TABLE_PREFIX . "mod_graph_data.ID AS iddata
			," . CM_TABLE_PREFIX . "mod_graph_data.sql AS `sql`
			," . CM_TABLE_PREFIX . "mod_graph_data.column AS `column`
			," . CM_TABLE_PREFIX . "mod_graph_data_detail.ID_graph AS iddetailgraph
			," . CM_TABLE_PREFIX . "mod_graph_data_detail.ID_data AS iddetaildata
			," . CM_TABLE_PREFIX . "mod_graph_data_detail.title AS title_detail
			," . CM_TABLE_PREFIX . "mod_graph_type.name AS typename
		FROM
			" . CM_TABLE_PREFIX . "mod_graph_chart
		LEFT JOIN
			" . CM_TABLE_PREFIX . "mod_graph_data_detail
				ON " . CM_TABLE_PREFIX . "mod_graph_chart.ID = " . CM_TABLE_PREFIX . "mod_graph_data_detail.ID_graph
		LEFT JOIN
			" . CM_TABLE_PREFIX . "mod_graph_data
				ON	" . CM_TABLE_PREFIX . "mod_graph_data.ID = " . CM_TABLE_PREFIX . "mod_graph_data_detail.ID_data
		LEFT JOIN
			" . CM_TABLE_PREFIX . "mod_graph_type
				ON " . CM_TABLE_PREFIX . "mod_graph_type.ID = " . CM_TABLE_PREFIX . "mod_graph_chart.ID_type
		WHERE " . CM_TABLE_PREFIX . "mod_graph_chart.name = " . $db->toSql($_REQUEST["name"]) . "
		ORDER BY " . CM_TABLE_PREFIX . "mod_graph_data_detail.`order`
					";

$db->query($sSQL);

if($db->nextRecord())
{
	$type = $db->getField("typename")->getValue();
	
	switch($type)
	{
		// Identifico il tipo di grafico che sto elaborando
		case "Istogramma":
		case "Torta":
		case "Linee":
		case "Area":

			// creo la l'array "table" che contiene la definizione del grafico creato
			$table = array (
				"cols" => array()
				,"rows" => array()
				,"p" => null
			);

			// creo le colonne del grafico
			$table["cols"][] = array (
						"id" => "x"
						, "label" => $db->getField("titleX")->getValue()
						, "type" => ffType_to_googleType($db->getField("titleXType")->getValue())
				);

			$array_temp = array();

			$counter_query = -1;
			$numero_query = $db->numRows(); //numero delle query selezionate per quel grafico

			// ciclo per estrarre la query
			do
			{
				$counter_query++;

				$query = $db->getField("sql")->getValue();
				$columns = $db->getField("column")->getValue();

				if(is_array($_REQUEST["keys"]))
				{
					foreach ($_REQUEST["keys"] as $key => $value)
					{
						$query = str_replace("[keys " . $key . "]", $db2->toSql($value), $query);
					} reset ($_REQUEST["keys"]);
				}

				foreach ($_REQUEST as $key => $value)
				{
					if (is_array($value))
						continue;
					$query = str_replace("[" . $key . "]", $db2->toSql($value), $query);
				} reset ($_REQUEST);

				$db2->query($query);
				
				$field = explode(",", $columns);

				$id_query = ffCommon_url_rewrite($db->getField("title_detail")->getValue());
				$table["cols"][] = array (
							"id" => $id_query
							, "label" => ffCommon_charset_encode($db->getField("title_detail")->getValue(), "UTF-8")
							, "type" => ffType_to_googleType($db->getField("titleYType")->getValue())
					);

				if($db2->nextRecord())
				{
					do
					{
						$dato = $db2->getField(trim($field[0]))->getValue();
						$valore = $db2->getField(trim($field[1]))->getValue();

						$array_temp[$dato][$counter_query] = $valore;
					} while ($db2->nextRecord());
				}
			} while ($db->nextRecord());

			//ksort($array_temp);

			// ciclo per generare le righe del grafico
			foreach ($array_temp as $key => $value)
			{
				$colonna = array();
				$colonna[] = array(
									"v" => new ffData($key, $db->getField("titleXType")->getValue())
									, "f" => null
								);

				for ($i = 0; $i < $numero_query; $i++)
				{
					$colonna[] = array(
											"v" => new ffData(($value[$i] ? $value[$i] : 0), $db->getField("titleYType")->getValue())
											, "f" => null
										);
				}
				$table["rows"][] = array (
											"c" => $colonna
										);

			}
		break;		
	}
}

// genero il json
header("Content-Type: text/plain; charset=UTF-8");
die(
	"google.visualization.Query.setResponse(" .
		ffCommon_google_jsonenc(array(
					"reqId"		=> $tqx["reqId"]
					, "status"	=> "ok"
					, "version"	=> "0.6"
					, "table"	=> $table
					, "p"		=> null
				))
	. ");"
);

function ffType_to_googleType($type)
{
	switch ($type)
	{
		case "Number":
			return "number";

		default:
			return "string";
	}
}