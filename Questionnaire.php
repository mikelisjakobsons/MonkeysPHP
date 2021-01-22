<?php
//  Developed By: Michael Jacobson
//  Developed Date: 2014-01-16
//  Description: PHP version of Case Scenario. Questionnaire form for users to fill it out and to submit it.
//
//  Uses SQL Server 2008 R2 with Server name 'SQLEXPRESS_2008'.
//
//  NOTE: Must set permissions for BUILTIN\Users on database SurveyMonkeys: 
//        In Microsoft SQL Server Management Studio, right-click on database node 'SurveyMonkeys', click properties, click permissions
//        grant BUILTIN\Users a Connect, Select, Insert, Update, Delete, and Execute. 
?>

<html>
<head>
  <style>
  TD {color:#008000;}
  </style>
  <meta HTTP-EQUIV="Content-Type" Content="text/html; charset=Windows-1252">
  <title>PHP Questionnaire</title>
</head>
<body style="font-family:Arial;margin-left:40px;">
    <h1>Survey Monkey Customer Questionnaire in PHP</h1>
    <h3>Assent Compliance</h3>

    <form action="ProcessResponses.php" method="POST">
<?php
    // Connects to database 'SurveyMonkeys' on Microsoft SQL Server 2008 R2 server.
    $serverName = ".\SQLEXPRESS_2008";
    $connectionInfo = array( "Database"=>"SurveyMonkeys");
    $conn = sqlsrv_connect( $serverName, $connectionInfo);
    if ($conn === false)
    {
        echo 'Connection failed!<br />';
        die("<pre>".print_r(sqlsrv_errors(), true));
    }
    // Establish a recordset cursor by querying SELECT-SQL statement.
    $strSQL = "SELECT Question, Answers.* FROM Questions INNER JOIN Answers ON Questions.QuestionId = Answers.QuestionId ORDER BY QuestionID, AnswerId";
    $recordset = sqlsrv_query( $conn, $strSQL );     //run the query.
    if( $recordset === false )
    {
        echo 'SQL Query failed!<br />';
        die( print_r( sqlsrv_errors(), true));
    }

    $iQuestion = 0;
    while( $row = sqlsrv_fetch_array( $recordset, SQLSRV_FETCH_ASSOC))   // While not EOF of recordset...
    {
        if ($row['QuestionId'] != $iQuestion)     // Upon change of question, display its question in bold.
        {
            $iQuestion = $row['QuestionId'];
            echo "<br />";
            echo "<b>" . $iQuestion . ". &nbsp; " . $row['Question'] . "</b><br />";
        }
        echo "<input Id=\"r" . $row['AnswerId'] . "\" Name=\"q" . $row['QuestionId'] . "\" type=\"radio\" value=\"" . $row['AnswerId'] . "\">" . $row['Answer'] . "</input>";
        echo "<br />";
    }

    // Closes the recordset and its SQL Server connection.
    sqlsrv_free_stmt( $recordset );
    sqlsrv_close( $conn );
?>
        <input type="hidden" name="NumberOfQuestions" value="<?=$iQuestion?>">
	<br />
        <input type="submit" id="submitId" name="submit" value="Submit" style="font-weight:bold;"> 
    </form> 
<br />
</body>
</html>