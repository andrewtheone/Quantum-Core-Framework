<table>
<?php 
foreach($data->form as $field)
    {
    echo "<tr><td>".$field['name']."</td><td>";
        if( $field['type'] == 'simplelist' )
            {
            echo "<select name='".$field['field_name']."'>";
            if( is_object($field['ds']) )
                {
                while( $r = $field['ds']->row() )
                    echo "<option value='".$r->{$field['ds_prim']}."'>".$r->{$field['ds_name']}."</option>";
                }else
                {
                foreach( $field['ds'] as $k=>$v )
                    echo "<option value='".$k."'>".$v."</option>";
                }
            echo "</select>";
            }else
            echo "<input type='text' value='".$field['value']."' name='".$field['field_name']."'></td></tr>";
    }
?>
</table>