<?
class SiteObject {
 var $DieOnError = false;
 var $Error = false;
 var $ErrorMessage = "";
 var $Table = Array();
 var $Fields = Array();
 var $Links = Array();
 var $KeyName = "";
 var $KeyValue = 0;

 //var $Table = Array("table_name" => "", "table_title" => "");
// вроде у когото вызывало ошибку Fatal error: Cannot redeclare SiteObject::$Table in \lib\lib.obj.php on line 12
// для исправления - меняли строку выше на
 var $table = Array("table_name" => "", "table_title" => "");

        function GetTableFields($table) {
                $r = @mysql_query("DESCRIBE $table");
                if (mysql_errno()!=0) $this->OnError("TableNotExists");
                while($field = mysql_fetch_array($r)) {
                        $temp["field_name"] = $field["Field"];
                        preg_match("/([a-z]{1,})(\((.*)\)){0,1}/", $field["Type"], $regs);
                        $temp["field_type"] = $regs[1];
                        switch ($regs[1]) {
                               case "int":
                                        $temp["field_width"] = "30px";
                                        break;
                               case "varchar":
                                        $temp["field_width"] = "250px";
                                        break;
                               case "enum":
                                        $temp["field_width"] = "100px";
                                        break;
                               case "set":
                                        $temp["field_width"] = "100px";
                                        break;
                               case "date":
                                        $temp["field_width"] = "50px";
                                        break;
                               case "datetime":
                                        $temp["field_width"] = "70px";
                                        break;
                        }
                        $temp["params"] = $regs[3];
                        if ($field["Key"] == "PRI") {
                                $temp["params"] = "primary";
                                $this->KeyName = $temp["field_name"];
                        }
                        if ($field["Key"] == "MUL") $temp["is_index"] = true;
                        $fields[$temp["field_name"]] = $temp;
                }
                return $fields;
        }

        function SiteObject($TableName, $DieOnError = false) {
                 $this->DieOnError = $DieOnError;
                 if (empty($TableName)) {
                    return $this->OnError(1);
                 }

                 $this->Fields = $this->GetTableFields($TableName);
                 if (!$this->Fields) {
                    return $this->OnError("TableNotExists");
                 }
                 $this->Table["table_name"] = $TableName;
                 @mysql_free_result($result);
        }

        function GetObject($id = 0, $convert = true) {

                 if (empty($id) || !is_numeric($id)) return $this->OnError("ObjEmpty");
                 $this->KeyValue = $id;
                 $result = $GLOBALS["db"]->query("select ".((!empty($this->Options["objects_options_edit_fields"])) ? $this->Options["objects_options_edit_fields"] : "*")." from ".$this->Table["table_name"]." where ".$this->KeyName." = '".$this->KeyValue."'");
                 //echo "<br>"."select ".((!empty($this->Options["objects_options_edit_fields"])) ? $this->Options["objects_options_edit_fields"] : "*")." from ".$this->Table["table_name"]." where ".$this->KeyName." = '".$this->KeyValue."'"."<br>".mysql_error()."<br>";
                 if (mysql_error()) {
                    return $this->OnError("dbError");
                 }
                 if (@mysql_num_rows($result) < 1) {
                    return $this->OnError("ObjNotExists");
                 }
                 $temp = $GLOBALS["db"]->fetch($result);
                 foreach($temp as $key => $value) {
                    //if (is_string($value) && $convert) $value = convert($value, html, sl, q);
                    $this->{$key} = $value;
                    if ($this->Fields[$key]["field_type"] == "date") {
                                 $temp = explode("-", ($value == "0000-00-00") ? '' : $value);
                                 $this->{$key."_year"} = $temp[0];
                                 $this->{$key."_month"} = $temp[1];
                                 $this->{$key."_day"} = $temp[2];
                    }
                    if ($this->Fields[$key]["field_type"] == "datetime") {

                                 $temp1 = explode(" ", ($value == "0000-00-00 00:00:00") ? '' : $value);
                                 $temp = explode("-", $temp1[0]);
                                 $temp2 = explode(":", $temp1[1]);
                                 $this->{$key."_year"} = $temp[0];
                                 $this->{$key."_month"} = $temp[1];
                                 $this->{$key."_day"} = $temp[2];
                                 $this->{$key."_hour"} = $temp2[0];
                                 $this->{$key."_minute"} = $temp2[1];
                    }

                    if ($this->Fields[$key]["field_type"] == "time") {
                                 $temp = explode(":", ($value == "00:00:00") ? '' : $value);
                                 $this->{$key."_hour"} = $temp[0];
                                 $this->{$key."_minute"} = $temp[1];
                    }
                 }
                 foreach($this->Links as $link) {
                        $result = $GLOBALS["db"]->query("select * from ".$link["TableName"]." where ".$link["DestinationFieldName"]." = '".$this->{$link["SourceFieldName"]}."'".((!empty($link["Where"])) ? "and (".$link["Where"].")" : ""));
                        //echo "<br>"."select * from ".$link["TableName"]." where ".$link["DestinationFieldName"]." = '".$this->{$link["SourceFieldName"]}."'"."<br>".mysql_error()."<br>";
                        if (!mysql_error()) {
                           if (mysql_num_rows($result) > 0) {
                              $temp = $GLOBALS["db"]->fetch($result);
                              foreach($temp as $key => $value) {
                                //if (is_string($value) && $convert) $value = convert($value, html, nl, sl, q);
                                if (!isset($this->{$key})) $this->{$key} = $value;
                              }
                           }
                        } else {
                                return false;
                        }
                 }
                 return true;
        }


        function CountItems($where = Array()) {
                 if (empty($this->Table["table_name"])) return $this->OnError("ObjTableNotExists");
                 $joins = array();
                 if (is_array($this->Links) && sizeof($this->Links) > 0) {
                     foreach($this->Links as $key => $join) {
                        if ($join["LinkType"] == "many2many") {
                                $l_res = $GLOBALS["db"]->query("select distinct ".$join["SourceFieldName"]." from ".$join["LinkTableName"]." ".((!empty($join["LinkWhere"])) ? "where ".$join["LinkWhere"] : "" ));
                                $temp = Array();
                                if ($l_res)
                                   while($row = $GLOBALS["db"]->fetch($l_res)) {
                                        $temp[] = $row[$join["SourceFieldName"]];
                                   }
                                $temp = (sizeof($temp) > 0) ? $join["SourceFieldName"]." IN (".implode(",", $temp).")" : $join["SourceFieldName"]." = 0";
                                $where = (strlen($where) > 0) ? $where." and ".$temp : "WHERE ".$temp;
                        } else {
                                $joins[] = "LEFT JOIN ".$join["TableName"]." ".(($join["SourceFieldName"] == $join["DestinationFieldName"]) ? "using(".$join["SourceFieldName"].")" : "ON ( ".$this->Table["table_name"].".".$join["SourceFieldName"]." = ".$join["TableName"].".".$join["DestinationFieldName"].")")."";
                                $this->Options["objects_options_show_fields"] = str_replace($key."_id", $this->Table["table_name"].".".$key."_id", $this->Options["objects_options_show_fields"]);
                                if (strtolower($this->OrderBy) == $key."_id asc" || strtolower($this->OrderBy) == $key."_id desc" || strtolower($this->OrderBy) == $key."_id") {
                                        $this->OrderBy = $key.".".$this->OrderBy;
                                }
                        }
                     }
                 }

                 switch(true) {
                    case (is_array($where) && sizeof($where) > 0):
                        $where = "WHERE ".implode(" and ", $where);
                        break;
                    case (is_string($where) && strlen($where) > 0):
                        $where = "WHERE ".$where;
                        break;
                    default:
                        $where = null;
                 }

                 $result = $GLOBALS["db"]->query("select COUNT(*) as count from ".$this->Table["table_name"]." ".implode(" ", $joins)." $where");
                 //echo "<br>"."select COUNT(*) as count from ".$this->Table["table_name"]." ".implode(" ", $joins)." $where"."<br>".mysql_error()."<br>";
                 if (mysql_error()) {
                    return $this->OnError("dbError");
                 }
                 return $GLOBALS["db"]->fetch($result, 0);
        }

        function GetItems($where = Array(), $convert = true) {
                 if (empty($this->Table["table_name"])) return $this->OnError("ObjTableNotExists");
                 switch(true) {
                    case (is_array($where) && sizeof($where) > 0):
                        $where = "WHERE ".implode(" and ", $where);
                        break;
                    case (is_string($where) && strlen($where) > 0):
                        $where = "WHERE ".$where;
                        break;
                    default:
                        $where = null;
                 }
                 if ($this->items_per_page > 0) {
                    $this->items_page = ($this->items_page > 0) ? $this->items_page : 1;
                    $limit = "limit ".($this->items_per_page*($this->items_page-1)).", ".$this->items_per_page;
                 }

                 if (is_array($this->Links) && sizeof($this->Links) > 0) {
                     $joins = array();
                     foreach($this->Links as $key => $join) {
                        if ($join["LinkType"] == "many2many") {
                                $l_res = $GLOBALS["db"]->query("select distinct ".$join["SourceFieldName"]." from ".$join["LinkTableName"]." ".((!empty($join["LinkWhere"])) ? "where ".$join["LinkWhere"] : "" ));
                                //echo "<br>"."select distinct ".$join["SourceFieldName"]." from ".$join["LinkTableName"]." ".((!empty($join["LinkWhere"])) ? "where ".$join["LinkWhere"] : "" )."<br>".mysql_error()."<br>";
                                $temp = Array();
                                if ($l_res)
                                   while($row = $GLOBALS["db"]->fetch($l_res)) {
                                        $temp[] = $row[$join["SourceFieldName"]];
                                   }
                                $temp = (sizeof($temp) > 0) ? $join["SourceFieldName"]." IN (".implode(",", $temp).")" : $join["SourceFieldName"]." = 0";
                                $where = (strlen($where) > 0) ? $where." and ".$temp : "WHERE ".$temp;
                        } else {
                                //$joins[] = "LEFT JOIN ".$join["TableName"]." ON ( ".$this->Table["table_name"].".".$join["SourceFieldName"]." = ".$join["TableName"].".".$join["DestinationFieldName"].")";
                                $joins[] = "LEFT JOIN ".$join["TableName"]." ".(($join["SourceFieldName"] == $join["DestinationFieldName"]) ? "using(".$join["SourceFieldName"].")" : "ON ( ".$this->Table["table_name"].".".$join["SourceFieldName"]." = ".$join["TableName"].".".$join["DestinationFieldName"].")")."";
                                $this->Options["objects_options_show_fields"] = str_replace($key."_id", $this->Table["table_name"].".".$key."_id", $this->Options["objects_options_show_fields"]);
                                if (strtolower($this->OrderBy) == $key."_id asc" || strtolower($this->OrderBy) == $key."_id desc" || strtolower($this->OrderBy) == $key."_id") {
                                        $this->OrderBy = $key.".".$this->OrderBy;
                                }
                        }
                     }
                 }

                 $order = (!empty($this->OrderBy)) ? "ORDER by ".(($this->OrderBy == $this->KeyName) ? $this->Table["table_name"].".".$this->OrderBy : $this->OrderBy) : "";

                 $result = $GLOBALS["db"]->query("select ".((!empty($this->Options["objects_options_show_fields"])) ? $this->Table["table_name"].".".$this->KeyName.",".$this->Options["objects_options_show_fields"] : "*")." from ".$this->Table["table_name"]." ".((is_array($joins) && sizeof($joins)>0) ? implode(" ", $joins) : "")."$where $order $limit");
                 if ($_GET["deb"] == 1) echo "<br>"."select ".((!empty($this->Options["objects_options_show_fields"])) ? $this->Table["table_name"].".".$this->KeyName.",".$this->Options["objects_options_show_fields"] : "*")." from ".$this->Table["table_name"]." ".((is_array($joins) && sizeof($joins)>0) ? implode(" ", $joins) : "")."$where $order $limit"."<br>".mysql_error()."<br>";
                 if (mysql_error()) {
                    return $this->OnError("dbError");
                 }
                 $rows = $GLOBALS["db"]->fetchall($result);
                 foreach($rows as $row) {

                 foreach($row as $key => $value) {
                    if ($this->Fields[$key]["field_type"] == "date") {
                                 $temp = explode("-", ($value == "0000-00-00") ? '' : $value);
                                 $row[$key."_year"] = $temp[0];
                                 $row[$key."_month"] = $temp[1];
                                 $row[$key."_day"] = $temp[2];
                                 $row[$key] = $temp[2].".".$temp[1].".".$temp[0];
                    }
                    if ($this->Fields[$key]["field_type"] == "datetime") {

                                 $temp1 = explode(" ", ($value == "0000-00-00 00:00:00") ? '' : $value);
                                 $temp = explode("-", $temp1[0]);
                                 $temp2 = explode(":", $temp1[1]);
                                 $row[$key."_year"] = $temp[0];
                                 $row[$key."_month"] = $temp[1];
                                 $row[$key."_day"] = $temp[2];
                                 $row[$key."_hour"] = $temp2[0];
                                 $row[$key."_minute"] = $temp2[1];
                    }

                    if ($this->Fields[$key]["field_type"] == "time") {
                                 $temp = explode(":", ($value == "00:00:00") ? '' : $value);
                                 $row[$key."_hour"] = $temp[0];
                                 $row[$key."_minute"] = $temp[1];
                    }
                 }

                 }
                 return $rows;
        }


        function OnError($code) {
                    $this->Error = true;
                    switch ($code) {
                     case ("dbError") : $this->ErrorMessage = "Ошибка чтения из базы данных."; break;
		             case ("dbWriteError") : $this->ErrorMessage = "Ошибка записи в базу данных."; break;
                     case ("ObjTableNotExists") : $this->ErrorMessage = "Указанный объект не существует."; break;
                     case ("TableNotExists") : $this->ErrorMessage = "Реальная таблица для указанного объекта не существует или содержит ошибки."; break;
                     case ("ObjFieldsNotExists") : $this->ErrorMessage = "Нет описания полей для указанного объекта."; break;
                     case ("ObjEmpty") : $this->ErrorMessage = "Не верно указан ID объекта."; break;
                     case ("ObjNotExists") : $this->ErrorMessage = "Объект с указаным ID не найден."; break;
                     case (1) : $this->ErrorMessage = "Не указана таблица для объекта."; break;
                    }

                    if ($this->DieOnError) die($this->ErrorMessage);
                    return false;
        }


      function Activate($id = 0) {
               if ($id == 0) return false;
               if (is_array($id)) {
                  if (sizeof($id) > 0) {
                    foreach($id as $key => $value) $id[$key] = addslashes($value);
                    @mysql_query("UPDATE ".$this->Table["table_name"]." SET ".$this->Table["table_name"]."_active = 'checked' WHERE ".$this->KeyName." IN ('".implode("','", $id)."')");
                  }
               } else {
                  @mysql_query("UPDATE ".$this->Table["table_name"]." SET ".$this->Table["table_name"]."_active = 'checked' WHERE ".$this->KeyName." = '".$id."'");
               }
               if (mysql_error()) {
                    return $this->OnError("dbError");
               }
      }

      function DeActivate($id = 0) {
               if ($id == 0) return false;
               if (is_array($id)) {
                  if (sizeof($id) > 0) {
                    foreach($id as $key => $value) $id[$key] = addslashes($value);
                    @mysql_query("UPDATE ".$this->Table["table_name"]." SET ".$this->Table["table_name"]."_active = '' WHERE ".$this->KeyName." IN ('".implode("','", $id)."')");
                  }
               } else {
                  @mysql_query("UPDATE ".$this->Table["table_name"]." SET ".$this->Table["table_name"]."_active = '' WHERE ".$this->KeyName." = '".$id."'");
               }
               if (mysql_error()) {
                    return $this->OnError("dbError");
               }
      }

      function Delete($id = 0) {
               if ($id === 0) return false;
               if (is_array($id)) {
                  if (sizeof($id) > 0) {
                    foreach($id as $key => $value) $id[$key] = addslashes($value);
                    @mysql_query("DELETE FROM ".$this->Table["table_name"]." WHERE ".$this->KeyName." IN ('".implode("','", $id)."')");
                  }
               } else {
                  @mysql_query("DELETE FROM ".$this->Table["table_name"]." WHERE ".$this->KeyName." = '".$id."'");
               }
               if (mysql_error()) {
                    return $this->OnError("dbError");
               }
      }


      function SaveObject($array) {
                 if (!$this->CheckObject($array)) {
                    $this->KeyValue = $_POST[$this->KeyName];
                    return false;
                 }
                 $fields = explode(",", $this->Options["objects_options_edit_fields"]);
                 if ($_POST[$this->KeyName] > 0) {
                    $sql = Array();
                    foreach($fields as $field) {
                        if ($this->Fields[$field]["field_type"] == "date")
				$_POST[$field] = $_POST[$field."_year"]."-".$_POST[$field."_month"]."-".$_POST[$field."_day"];
                        if ($this->Fields[$field]["field_type"] == "datetime")
				$_POST[$field] = $_POST[$field."_year"]."-".$_POST[$field."_month"]."-".$_POST[$field."_day"]." ".$_POST[$field."_hour"].":".$_POST[$field."_minute"].":00";
                        if ($this->Fields[$field]["field_type"] == "time")
				$_POST[$field] = $_POST[$field."_hour"].":".$_POST[$field."_minute"].":00";

//			$_POST[$field]=htmlspecialchars($_POST[$field],ENT_QUOTES,cp1251);
			$_POST[$field] = preg_replace('/[\']/im','\&\#039',$_POST[$field]);


                        //if ($this->KeyName != $field) $sql[] = $field." = '".$_POST[$field]."'";
                        if ($this->KeyName != $field) $sql[] = $field." = '{".$field."}'";
                    }
                    if (is_array($this->Fields["updated"])) $sql[] = "updated = NOW()";
                    if (is_array($this->Fields["updated_by"])) $sql[] = "updated_by = '".$_SESSION["user"]->users_id."'";
//		    $result = $GLOBALS["db"]->query("UPDATE ".$this->table["Table_name"]." SET ".implode(",\n", $sql)." WHERE ".$this->KeyName." = '".$_POST[$this->KeyName]."'", $_POST);
		    $result = $GLOBALS["db"]->query("update ".$this->Table["table_name"]." set ".implode(",", $sql)." where ".$this->KeyName." = '".$_POST[$this->KeyName]."'", $_POST);


                    //echo "<br>"."update ".$this->Table["table_name"]." set ".implode(",", $sql)." where ".$this->KeyName." = '".$_POST[$this->KeyName]."'"."<br>".mysql_error()."<br>";

//echo "<br><pre>"."update ".$this->Table["table_name"]." set ".implode(",\n", $sql)." where ".$this->KeyName." = '".$_POST[$this->KeyName]."'"."\n<br>\n".mysql_error()."\n</pre>\n<br>\n";
//echo "<br>1<pre>\n";
//echo $this->table["Table_name"];
//echo "\n2\n";
//echo $this->Table["table_name"];
//echo "\n</pre>\n";

                    $this->KeyValue = $_POST[$this->KeyName];
                 } else {
                    $sql = Array();
                    foreach($fields as $field) {
                       if ($this->Fields[$field]["field_type"] == "date") $_POST[$field] = $_POST[$field."_year"]."-".$_POST[$field."_month"]."-".$_POST[$field."_day"];
                       if ($this->Fields[$field]["field_type"] == "datetime") $_POST[$field] = $_POST[$field."_year"]."-".$_POST[$field."_month"]."-".$_POST[$field."_day"]." ".$_POST[$field."_hour"].":".$_POST[$field."_minute"].":00";
                       if ($this->Fields[$field]["field_type"] == "time") $_POST[$field] = $_POST[$field."_hour"].":".$_POST[$field."_minute"].":00";
                       if ($this->KeyName != $field && !($this->Table["table_name"]."_date"==$field && empty($_POST[$this->Table["table_name"]."_date"]))) {
                                $sql[0][] = $field;
                                $sql[1][] = "'{".$field."}'";
                       }
                    }
                    if (is_array($this->Fields[$this->Table["table_name"]."_date"]) && empty($_POST[$this->Table["table_name"]."_date"])) {
                        $sql[0][] = $this->Table["table_name"]."_date";
                        $sql[1][] = "NOW()";
                    }
                    $result = $GLOBALS["db"]->query("insert into ".$this->Table["table_name"]." (".implode(",", $sql[0]).") values (".implode(",", $sql[1]).")", $_POST);
                    //echo "<br>"."insert into ".$this->Table["table_name"]." (".implode(",", $sql[0]).") values (".implode(",", $sql[1]).")"."<br>".mysql_error()."<br>";
                    $this->KeyValue = @mysql_insert_id();
                 }
                 if (mysql_error()) {
                    return $this->OnError("dbWriteError");
                 }
                 return true;
      }

      function CheckObject($array) {
               if (!is_array($this->Options["objects_options_required_fields"]) || sizeof($this->Options["objects_options_required_fields"]) == 0) return true;
               foreach($this->Options["objects_options_required_fields"] as $require) {
                      @eval("\$check = \$_POST['".$require["field_name"]."'] ".$require["as"].";");
                      if (!$check) {
                         $this->Error = true;
                         $this->ErrorMessage = $require["errormsg"];
                         return false;
                      }
               }
               return true;
      }
}
?>