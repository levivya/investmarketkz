<?
//**************************************************************************************************************
//  ���������� �� ���� ��������� ������ ��� false, ���� ��� ���������� � �����, ��������� ������ ��� ��� ������
//
//  ������ ���� �������� ���������� � MySQL �����
//  ���������� assoc array ���� ������ �������
//             false ���� ��� ���������� � �����, ��������� ������ ��� ��� ������
//**************************************************************************************************************

function get_proxy() {
   $res = @mysql_query("select * from proxy where proxy_deleted <> 'checked' order by rand() limit 1");
   if (!mysql_error()) {
      if (mysql_num_rows($res) > 0) {
        $proxy = mysql_fetch_assoc($res);
        if ($proxy["proxy_id"] > 0) return $proxy;
      }
   } else {
      echo "<p><font color=ff0000>Error get proxy (no MySQL connect or MySQL error).</font></p>";
   }
   return false;
}
?>