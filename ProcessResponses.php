<?php
//  Developed By: Michael Jacobson
//  Developed Date: 2014-01-16
//  Description: PHP version of Processes user's answers to questionnaire form and is to be inserted into database table 'Responses'.
//
//  Uses SQL Server 2008 R2 with Server name 'SQLEXPRESS_2008'.
?>
<!doctype html>
<html>

<head>
    <meta charset="utf-8" />
    <title>Demo</title>
</head>
<body style="font-family:Arial;margin-left:40px;">

<?php
    $nQuestions = $_POST['NumberOfQuestions'];    // Number of questions in this questionnaire. It is dynamic.

    // Open a database connection for inserting new responce records into table 'responses', one for each question.
    $serverName = ".\SQLEXPRESS_2008";
    $connectionInfo = array( "Database"=>"SurveyMonkeys");
    $conn = sqlsrv_connect( $serverName, $connectionInfo);
    if ($conn === false)
    {
        echo 'Connection failed!<br />';
        die("<pre>".print_r(sqlsrv_errors(), true));
    }

    for ($i=1; $i<=$nQuestions; $i++)
    {
        // For each answered question, noted by defined $_POST[] request-form entry.
        if( isset($_POST['q' . $i]) )
        {
            // Set up and execute the Insert SQL query.
            $sqlInsert = "INSERT INTO Responses (AnswerId) VALUES (" . $_POST['q' . $i] . ")";
            $stmtInsert = sqlsrv_query( $conn, $sqlInsert );
            if (!$stmtInsert)   // Failed INSERT
            {
                echo "At question " . $i . ", INSERT into table Responses failed!<br />";
            }
            else  // Sucessful INSERT, must free $stmtInsert instant.
            {
                sqlsrv_free_stmt( $stmtInsert );
            }
        }
    }
    // Closes the SQL Server connection.
    sqlsrv_close( $conn );
?>

    <h2>Thank-you for submitting Survey Monkey Customer Questionnaire in PHP</h2>
    <h3>Assent Compliance</h3>
    <br />
    <a href="Default.htm">Back to main page</a><br />
    <br />
    <a href="SurveyResults.php">Survey Results Report</a>
</body>
</html>

