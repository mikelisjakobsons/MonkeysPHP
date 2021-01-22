<?php
//  Developed By: Michael Jacobson
//  Developed Date: 2014-01-16
//  Description: PHP version of Description: Displays the survey results with answers count for various answer given for each question.
//
//  Uses SQL Server 2008 R2 with Server name 'SQLEXPRESS_2008'.
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
    <h1>Survey Monkey Customer Questionnaire Results in PHP</h1>
    <h3>Assent Compliance</h3>

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
    // Uses an aggregate SQL statement in counting number of responses for each distinct question/answer pair.
    $strSQL = "SELECT Questions.QuestionId, Questions.Question, Answers.AnswerId, Answers.Answer, Count(Responses.AnswerId) AS NbrResponses " .
              "FROM Questions " .
              "INNER JOIN Answers ON Questions.QuestionId = Answers.QuestionId " .
              "LEFT OUTER JOIN Responses ON Answers.AnswerId = Responses.AnswerId " .
              "GROUP BY Questions.QuestionId, Questions.Question, Answers.AnswerId, Answers.Answer " .
              "ORDER BY Questions.QuestionID, Answers.AnswerId";

    // Establish a recordset cursor by querying an aggregate SELECT-SQL statement.
    $recordset = sqlsrv_query( $conn, $strSQL );     //run the query.
    if( $recordset === false )
    {
        echo 'SQL Query failed!<br />';
        die( print_r( sqlsrv_errors(), true));
    }

    $iQuestion = 0;
    while( $row = sqlsrv_fetch_array( $recordset, SQLSRV_FETCH_ASSOC))    // While not EOF of recordset...
    {
        if ($row['QuestionId'] != $iQuestion)     // Upon change of question, display its question in bold.
        {
            $iQuestion = $row['QuestionId'];
            echo "<br />";
            echo "<b>" . $iQuestion . ". &nbsp; " . $row['Question'] . "</b><br />";
        }
        echo $row['Answer'] . " &nbsp; &nbsp; &nbsp; " . $row['NbrResponses'] . " responses";
        echo "<br />";
    }

    // Closes the recordset and its SQL Server connection.
    sqlsrv_free_stmt( $recordset );
    sqlsrv_close( $conn );
?>
<br />
<a href="Default.htm">Back to main page</a>
</body>
</html>