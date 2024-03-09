<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Dashboard</title>
</head>
<body>

    <h1>Admin Dashboard</h1>

    <?php
        $servername = "localhost";
        $username = "root";
        $password = "";
        $dbname = "coursereg";
        
        // Create connection
        $conn = new mysqli($servername, $username, $password, $dbname);
        
        // Check connection
        if ($conn->connect_error) {
            die("Connection failed: " . $conn->connect_error);
        }

        $query = "SHOW TABLES";
        $result = $conn->query($query);

        if ($result) {
            echo '<ul>';
            while ($row = $result->fetch_row()) {
                $tableName = $row[0];
                echo '<li><a href="?table=' . $tableName . '">' . $tableName . '</a></li>';
            }
            echo '</ul>';

            if (isset($_GET['table'])) {
                $selectedTable = $_GET['table'];

                $selectQuery = "SELECT * FROM $selectedTable";
                $tableResult = $conn->query($selectQuery);

                if ($tableResult) {
                    echo '<h2>Table: ' . $selectedTable . '</h2>';
                    echo '<form method="post" action="?table=' . $selectedTable . '">';
                    echo '<table border="1">';
                
                    // Fetch column names from the selected table
                    $columnsQuery = "SHOW COLUMNS FROM $selectedTable";
                    $columnsResult = $conn->query($columnsQuery);
                
                    if ($columnsResult) {
                        echo '<tr>';
                        while ($column = $columnsResult->fetch_assoc()) {
                            echo '<th>' . $column['Field'] . '</th>';
                        }
                        echo '</tr>';
                
                        // Fetch and display data
                        while ($tableRow = $tableResult->fetch_assoc()) {
                            echo '<tr>';
                            foreach ($tableRow as $column => $value) {
                                echo '<td><input type="text" name="' . $column . '[]" value="' . $value . '"></td>';
                            }
                            echo '</tr>';
                        }
                
                        echo '</table>';
                        echo '<input type="submit" value="Update Changes">';
                        echo '</form>';
                    } else {
                        echo 'Error fetching column names from the selected table';
                    }
                } else {
                    echo 'Error fetching data from the selected table';
                }
                
            }
        } else {
            echo 'Error fetching table names from the database: ' . $conn->error;
        }

       

        if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_GET['table'])) {
            $selectedTable = $_GET['table'];
        
            // Fetch column names from the selected table
            $columnsQuery = "SHOW COLUMNS FROM $selectedTable";
            $columnsResult = $conn->query($columnsQuery);
        
            // Prepare the update query
            $updateQuery = "UPDATE $selectedTable SET ";
        
            if ($columnsResult) {
                $whereCondition = '';
        
                while ($column = $columnsResult->fetch_assoc()) {
                    $columnName = $column['Field'];
                    // Update each column with its corresponding value from the form
                    $updateQuery .= "$columnName='" . $conn->real_escape_string($_POST[$columnName][0]) . "', ";
                    
                    // Assuming 'id' and 'name' as the composite key columns (adjust based on your table)
                    if ($columnName == 'id' || $columnName == 'name') {
                        $whereCondition .= "$columnName='" . $conn->real_escape_string($_POST[$columnName][0]) . "' AND ";
                    }
                }
        
                // Remove the trailing comma and space from the update query
                $updateQuery = rtrim($updateQuery, ', ');
        
                // Remove the trailing 'AND' from the WHERE condition
                $whereCondition = rtrim($whereCondition, ' AND ');
        
                // Add the WHERE condition
                $updateQuery .= " WHERE $whereCondition";
        
                // Close the $columnsResult to free up resources
                $columnsResult->close();
        
                // Execute the update query
        
                // Redirect to the same page to refresh the data
                header("Location: ?table=$selectedTable");
                exit();
            } else {
                echo 'Error fetching column names from the selected table';
            }
        }
         $conn->close();
        
    ?>

</body>
</html>
