The `enovision-slim-dependencies` package (not public) is used to check if a record has dependencies in other tables. You check the dependencies in case you want to delete a record.
After the check you can decided if you want to persuit the record deletion.

### How it works
All the dependencies are registered in an XML file named 'dependencies.xml'. A sample XML can be found in the package 'config' folder.

The structure is as following:
```
<?xml version="1.0" encoding="utf-8"?>
<dependencies xmlns="http://example.org/dependencies">
    <schema>
        <source>source_table_name</source>
        <targets>
            <target>
                <table>target_table_name</table>
                <field>field_to_check</field>
                <alias>Alias text for the message</alias>
                <where>active = 1</where>
            </target>
            <target>
                <table>another_target_table_name</table>
                <field>field_to_check</field>
                <alias>Some other alias</alias>
                <where>type = 'N' and active = 1</where>
            </target>
        </targets>
    </schema>
    <schema>
        <source>table_with_group</source>
        <targets>
            <target>
                <table>target_table</table>
                <field>some_field_a</field>
                <group>some_group</group>
                <alias>Some alias for this target</alias>
                <where>active = 1</where>
            </target>
            <target>
                <table>target_table</table>
                <field>some_field_b</field>
                <group>another_group</group>
                <alias>Some alias for this target</alias>
                <where>active = 1</where>
            </target>
        </targets>
    </schema>
</dependencies>
```
### Container dependency

The depencency object is bound to the $app->container as a dependency.

```
$container['dependencies'] = function ($container) {

    $dependencies = new \Enovision\Slim\Dependencies\Dependencies(
        
        // the container itself (required)
        $container,
        
        // XML path (required)
        INC_ROOT . '/config/dependencies/dependencies.xml',
        
        // callback function for getting the selection dynamically
        // sample below is based on Illuminate database support
        // (required)
        function (\Enovision\Slim\Dependencies\Classes\Target $t, $group = null, $value = '', $value2 = null, $value3 = null) {
            if ((!empty($group) && !empty($t->group)) && $group !== $t->group  ) {
                return 0;
            };

            $sql = sprintf("
               select %s
               from %s
               where %s = '$value'
               %s
               %s
               %s
            ", $t->field, $t->table, $t->field,
                !empty($t->where) ? "and {$t->where}" : '',
                !empty($t->field2) && !empty($value2) ? "and {$t->field2} = '$value2'" : '',
                !empty($t->field3) && !empty($value3) ? "and {$t->field3} = '$value3'" : ''
            );

            $result = DB::select(DB::raw($sql));

            return count($result);
        },
        
        // language (default = 'en'), omittable
        'en',
        
        // language path (default language files in package, omittable)
        null,
        
        // delimiter (default '<br/>', omittable, only use this when you want another delimiter
        '<br/>'
    );


    return $dependencies;
};
```

### How to check the dependency for a table record

Sample:
```
$this->get('/dependencies', function ($request, $response) {

    // value to compare in other tables field defined in XML
    $keyValueSource = '837b982eef2c65946844b094939ddfd2';
        
    // check the dependency, parameters: source_table, group, key
    $result = $this->dependencies->check('zs_patient', null, $keyValueSource );

    if ($result->hasDependencies()) {
       echo 'I have dependencies, it stops here!'
       echo $result->getHtmlMessages();
    } else {
       // do what you like, maybe delete the record?
    };

    // Sample with group

	// This means: find in the XML the 'parameters' schema and find entries with same group
    // and check the dependency according to the given criteria, with source value '004'
    $result = $this->dependencies->check('parameters', 'dozent', '004' );

    if ($result->hasDependencies()) {
     	echo 'I have dependencies, it stops here!'
        echo '<ul>';
        foreach($result->getMessages() as $message) {
         	echo "<li>$message</li>";
        }
        echo '</ul>';
    } else {
       	// do what you like, maybe delete the record?
    };
});
```

### Locale (languages)

You can find the locale files in the 'locale' folder in the package. Currently English and German is available. If you need another language
you can create it anywhere in your application. Just copy on of the files to your new location, like: 'config/dependencies/locale/nl' and keep the name
of the file the same (lang.php).

What remains is that you change the dependency injection parameters mentioned earlier in this chapter.
